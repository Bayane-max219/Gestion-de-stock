<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Http\Requests\ClientRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ClientsExport;
use Barryvdh\DomPDF\Facade\PDF;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        $query = Client::with(['sales' => function ($query) {
                $query->select('id', 'client_id', 'total', 'payment_status');
            }])
            ->when($request->filled('status'), function ($q) use ($request) {
                $q->where('status', $request->status);
            })
            ->when($request->filled('city'), function ($q) use ($request) {
                $q->where('city', 'like', "%{$request->city}%");
            })
            ->when($request->filled('search'), function ($q) use ($request) {
                $q->where(function ($query) use ($request) {
                    $query->where('name', 'like', "%{$request->search}%")
                        ->orWhere('email', 'like', "%{$request->search}%")
                        ->orWhere('phone', 'like', "%{$request->search}%")
                        ->orWhere('code', 'like', "%{$request->search}%");
                });
            })
            ->when($request->filled('overdue'), function ($q) {
                $q->whereHas('sales', function ($query) {
                    $query->where('payment_status', '!=', 'paid')
                        ->whereRaw('DATEDIFF(NOW(), sale_date) > payment_terms');
                });
            });

        $clients = $query->latest()->paginate($request->per_page ?? 15);

        return response()->json($clients);
    }

    public function store(ClientRequest $request)
    {
        DB::beginTransaction();

        try {
            $client = new Client($request->validated());
            $client->generateClientCode();
            $client->save();

            DB::commit();

            return response()->json([
                'message' => 'Client created successfully',
                'client' => $client,
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error creating client',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function show(Client $client)
    {
        $client->load(['sales' => function ($query) {
            $query->with('items.product')
                ->select('id', 'client_id', 'invoice_number', 'sale_date', 'total', 'payment_status')
                ->latest();
        }]);

        return response()->json($client);
    }

    public function update(ClientRequest $request, Client $client)
    {
        DB::beginTransaction();

        try {
            $client->update($request->validated());

            DB::commit();

            return response()->json([
                'message' => 'Client updated successfully',
                'client' => $client,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error updating client',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy(Client $client)
    {
        if ($client->sales()->exists()) {
            return response()->json([
                'message' => 'Cannot delete client with sales history',
            ], 422);
        }

        $client->delete();

        return response()->json([
            'message' => 'Client deleted successfully',
        ]);
    }

    public function history(Request $request, Client $client)
    {
        $filters = $request->only(['status', 'date_from', 'date_to', 'per_page']);
        
        $salesHistory = $client->getSalesHistory($filters);
        $paymentHistory = $client->getPaymentHistory($filters);

        return response()->json([
            'sales' => $salesHistory,
            'payments' => $paymentHistory,
            'summary' => [
                'total_sales' => $client->total_sales,
                'total_paid' => $client->total_paid,
                'total_due' => $client->total_due,
                'available_credit' => $client->available_credit,
                'overdue_amount' => $client->getOverdueSales()->sum('total'),
            ],
        ]);
    }

    public function export(Request $request)
    {
        return Excel::download(new ClientsExport($request->all()), 'clients.xlsx');
    }

    public function printStatement(Client $client)
    {
        $client->load(['sales' => function ($query) {
            $query->with(['items.product', 'payments'])
                ->where('payment_status', '!=', 'paid')
                ->latest();
        }]);

        $pdf = PDF::loadView('pdf.client-statement', [
            'client' => $client,
            'company' => [
                'name' => config('app.name'),
                'address' => config('app.address'),
                'phone' => config('app.phone'),
                'email' => config('app.email'),
            ],
        ]);

        return $pdf->download("statement-{$client->code}.pdf");
    }

    public function overdue()
    {
        $clients = Client::whereHas('sales', function ($query) {
                $query->where('payment_status', '!=', 'paid')
                    ->whereRaw('DATEDIFF(NOW(), sale_date) > payment_terms');
            })
            ->with(['sales' => function ($query) {
                $query->where('payment_status', '!=', 'paid')
                    ->whereRaw('DATEDIFF(NOW(), sale_date) > payment_terms')
                    ->select('id', 'client_id', 'invoice_number', 'sale_date', 'total', 'payment_status');
            }])
            ->get()
            ->map(function ($client) {
                return [
                    'id' => $client->id,
                    'code' => $client->code,
                    'name' => $client->name,
                    'phone' => $client->phone,
                    'email' => $client->email,
                    'total_overdue' => $client->sales->sum('total'),
                    'oldest_invoice_date' => $client->sales->min('sale_date'),
                    'overdue_invoices' => $client->sales->count(),
                ];
            });

        return response()->json($clients);
    }

    public function updateStatus(Request $request, Client $client)
    {
        $request->validate([
            'status' => 'required|in:active,inactive,blacklisted',
        ]);

        $client->status = $request->status;
        $client->save();

        return response()->json([
            'message' => 'Client status updated successfully',
            'client' => $client,
        ]);
    }
}