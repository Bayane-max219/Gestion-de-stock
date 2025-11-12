<?php

namespace App\Http\Controllers;

use App\Models\CashRegister;
use App\Models\CashTransaction;
use App\Http\Requests\CashRegisterRequest;
use App\Http\Requests\CashTransactionRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\CashTransactionsExport;
use Barryvdh\DomPDF\Facade\PDF;
use Carbon\Carbon;

class CashRegisterController extends Controller
{
    public function index(Request $request)
    {
        $query = CashRegister::with(['user', 'store'])
            ->when($request->filled('store_id'), function ($q) use ($request) {
                $q->where('store_id', $request->store_id);
            })
            ->when($request->filled('status'), function ($q) use ($request) {
                $q->where('status', $request->status);
            })
            ->when($request->filled('date_from'), function ($q) use ($request) {
                $q->whereDate('opening_date', '>=', $request->date_from);
            })
            ->when($request->filled('date_to'), function ($q) use ($request) {
                $q->whereDate('opening_date', '<=', $request->date_to);
            });

        $registers = $query->latest()->paginate($request->per_page ?? 15);

        return response()->json($registers);
    }

    public function store(CashRegisterRequest $request)
    {
        // Check if there's already an open register
        $currentRegister = CashRegister::getCurrentRegister($request->store_id);
        if ($currentRegister) {
            return response()->json([
                'message' => 'There is already an open cash register for this store',
            ], 422);
        }

        DB::beginTransaction();

        try {
            $register = CashRegister::openRegister([
                'store_id' => $request->store_id,
                'user_id' => auth()->id(),
                'opening_balance' => $request->opening_balance,
                'notes' => $request->notes,
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Cash register opened successfully',
                'register' => $register,
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error opening cash register',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function show(CashRegister $cashRegister)
    {
        $cashRegister->load([
            'user',
            'store',
            'transactions' => function ($query) {
                $query->with(['user', 'reference'])->latest();
            },
        ]);

        $summary = $cashRegister->getDailySummary();

        return response()->json([
            'register' => $cashRegister,
            'summary' => $summary,
        ]);
    }

    public function close(CashRegisterRequest $request, CashRegister $cashRegister)
    {
        DB::beginTransaction();

        try {
            $cashRegister->closeRegister([
                'actual_closing_balance' => $request->actual_closing_balance,
                'notes' => $request->notes,
                'confirm_difference' => $request->confirm_difference,
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Cash register closed successfully',
                'register' => $cashRegister->load(['user', 'store']),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error closing cash register',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function addTransaction(CashTransactionRequest $request, CashRegister $cashRegister)
    {
        DB::beginTransaction();

        try {
            $transaction = $cashRegister->recordTransaction([
                'user_id' => auth()->id(),
                'type' => $request->type,
                'amount' => $request->amount,
                'description' => $request->description,
                'payment_method' => $request->payment_method,
                'reference_type' => $request->reference_type,
                'reference_id' => $request->reference_id,
                'notes' => $request->notes,
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Transaction recorded successfully',
                'transaction' => $transaction->load(['user', 'reference']),
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error recording transaction',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getTransactions(Request $request, CashRegister $cashRegister)
    {
        $transactions = $cashRegister->getTransactionHistory([
            'type' => $request->type,
            'date_from' => $request->date_from,
            'date_to' => $request->date_to,
            'user_id' => $request->user_id,
            'per_page' => $request->per_page,
        ]);

        return response()->json($transactions);
    }

    public function getCurrentRegister(Request $request)
    {
        $register = CashRegister::getCurrentRegister($request->store_id);

        if (!$register) {
            return response()->json([
                'message' => 'No open cash register found',
            ], 404);
        }

        $register->load(['user', 'store']);
        $summary = $register->getDailySummary();

        return response()->json([
            'register' => $register,
            'summary' => $summary,
        ]);
    }

    public function export(Request $request, CashRegister $cashRegister)
    {
        return Excel::download(
            new CashTransactionsExport($cashRegister->id, $request->all()),
            "transactions-{$cashRegister->id}.xlsx"
        );
    }

    public function printSummary(CashRegister $cashRegister)
    {
        $cashRegister->load(['user', 'store', 'transactions.user', 'transactions.reference']);
        
        $summary = $cashRegister->getDailySummary();

        $pdf = PDF::loadView('pdf.cash-register-summary', [
            'register' => $cashRegister,
            'summary' => $summary,
            'company' => [
                'name' => config('app.name'),
                'address' => config('app.address'),
                'phone' => config('app.phone'),
                'email' => config('app.email'),
            ],
        ]);

        return $pdf->download("register-summary-{$cashRegister->id}.pdf");
    }

    public function getDailySummary(Request $request)
    {
        $date = $request->date ?? now()->toDateString();
        
        $registers = CashRegister::with(['user', 'store', 'transactions'])
            ->where('store_id', $request->store_id)
            ->whereDate('opening_date', $date)
            ->get();

        $summary = [
            'total_openings' => $registers->count(),
            'total_transactions' => $registers->sum(function ($register) {
                return $register->transactions->count();
            }),
            'total_sales' => $registers->sum(function ($register) {
                return $register->total_sales;
            }),
            'total_purchases' => $registers->sum(function ($register) {
                return $register->total_purchases;
            }),
            'total_income' => $registers->sum(function ($register) {
                return $register->total_income;
            }),
            'total_expenses' => $registers->sum(function ($register) {
                return $register->total_expense;
            }),
            'net_movement' => $registers->sum(function ($register) {
                return $register->transactions->sum('amount');
            }),
            'registers' => $registers->map(function ($register) {
                return [
                    'id' => $register->id,
                    'store' => $register->store->name,
                    'cashier' => $register->user->name,
                    'opening_balance' => $register->opening_balance,
                    'current_balance' => $register->current_balance,
                    'difference' => $register->difference,
                    'status' => $register->status,
                    'opening_date' => $register->opening_date->format('H:i'),
                    'closing_date' => $register->closing_date?->format('H:i'),
                ];
            }),
        ];

        return response()->json($summary);
    }
}