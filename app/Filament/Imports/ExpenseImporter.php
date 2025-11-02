<?php

namespace App\Filament\Imports;

use App\Models\Expense;
use App\Models\Category;
use Filament\Actions\Imports\Exceptions\RowImportFailedException;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Number;
use Illuminate\Database\Eloquent\Model;

class ExpenseImporter extends Importer
{
    protected static ?string $model = Expense::class;

    protected static ?string $slug = 'expenses';

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('name')
                ->label(__(self::$slug . '.form.name'))
                ->requiredMapping()
                ->rules(['required', 'max:255']),

            // CSV header: "category" (nome) oppure "category_id" (id)
            ImportColumn::make('category')
                ->label(__(self::$slug . '.form.category'))
                ->requiredMapping()
                ->rules(['required']),

            ImportColumn::make('amount')
                ->label(__(self::$slug . '.form.amount'))
                ->requiredMapping()
                ->numeric()
                ->rules(['required', 'numeric']),

            ImportColumn::make('date')
                ->label(__(self::$slug . '.form.date'))
                ->requiredMapping()
                ->rules(['required', 'date']),
        ];
    }

    public function resolveRecord(): Expense
    {
        return new Expense();
    }

    public function fillRecord(): void
    {
        Log::info('ExpenseImporter::fillRecord called', [
            'import_id' => $this->import->id ?? null,
            'row_number' => $this->rowNumber ?? null,
            'data' => $this->data,
        ]);

        $categoryId = $this->data['category_id'] ?? null;

        if (empty($categoryId) && ! empty($this->data['category'])) {
            $categoryName = trim((string) $this->data['category']);
            $category = Category::where('name', $categoryName)->first();

            if (! $category) {
                Log::warning('ExpenseImporter: categoria non trovata', [
                    'category' => $categoryName,
                    'import_id' => $this->import->id ?? null,
                    'row_number' => $this->rowNumber ?? null,
                ]);

                throw new RowImportFailedException("Categoria non trovata: {$categoryName}");
            }

            $categoryId = $category->id;
        }

        if (empty($categoryId)) {
            Log::warning('ExpenseImporter: category_id mancante o non risolvibile', [
                'data' => $this->data,
                'import_id' => $this->import->id ?? null,
                'row_number' => $this->rowNumber ?? null,
            ]);

            throw new RowImportFailedException('Category_id mancante o non risolvibile.');
        }

        $importUserId = $this->import->user_id ?? null;
        $userId = $this->data['user_id'] ?? $importUserId ?? Auth::id();

        $this->record->fill([
            'name' => $this->data['name'] ?? null,
            'category_id' => $categoryId,
            'amount' => $this->data['amount'] ?? null,
            'date' => $this->data['date'] ?? null,
            'user_id' => $userId,
        ]);

        Log::info('ExpenseImporter::fillRecord populated record', [
            'record' => $this->record->getAttributes(),
            'import_id' => $this->import->id ?? null,
            'row_number' => $this->rowNumber ?? null,
        ]);
    }

    protected function afterSave(): void
    {
        if ($this->record instanceof Model) {
            Log::info('ExpenseImporter::afterSave saved record', [
                'id' => $this->record->id,
                'attributes' => $this->record->toArray(),
                'import_id' => $this->import->id ?? null,
            ]);
        } else {
            Log::info('ExpenseImporter::afterSave record not Model', [
                'import_id' => $this->import->id ?? null,
            ]);
        }
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        // dd('ExpenseImporter::getCompletedNotificationBody called', $import);
        $body = 'Your expense import has completed and ' . Number::format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . Number::format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}
