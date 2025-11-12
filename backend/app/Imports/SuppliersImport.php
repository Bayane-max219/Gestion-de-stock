<?php

namespace App\Imports;

use App\Models\Supplier;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class SuppliersImport implements ToModel, WithHeadingRow, WithValidation, WithBatchInserts, WithChunkReading
{
    protected $rowCount = 0;
    protected $errors = [];

    public function model(array $row)
    {
        try {
            $this->rowCount++;

            return new Supplier([
                'name' => $row['name'],
                'email' => $row['email'] ?? null,
                'phone' => $row['phone'] ?? null,
                'address' => $row['address'] ?? null,
                'tax_number' => $row['tax_number'] ?? null,
                'is_active' => $row['is_active'] ?? true,
                'notes' => $row['notes'] ?? null,
            ]);
        } catch (\Exception $e) {
            $this->errors[] = [
                'row' => $this->rowCount,
                'error' => $e->getMessage()
            ];
            Log::error('Error importing supplier', [
                'row' => $row,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string'],
            'tax_number' => ['nullable', 'string', 'max:50'],
            'is_active' => ['nullable', 'boolean'],
            'notes' => ['nullable', 'string'],
        ];
    }

    public function customValidationMessages()
    {
        return [
            'name.required' => 'The supplier name is required',
            'name.max' => 'The supplier name cannot exceed 255 characters',
            'email.email' => 'Please provide a valid email address',
            'phone.max' => 'The phone number cannot exceed 20 characters',
            'tax_number.max' => 'The tax number cannot exceed 50 characters',
        ];
    }

    public function getRowCount(): int
    {
        return $this->rowCount;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function batchSize(): int
    {
        return 100;
    }

    public function chunkSize(): int
    {
        return 100;
    }
}