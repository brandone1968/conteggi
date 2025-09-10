<?php

namespace App\Filament\Imports;

use App\Models\Expense;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Number;
use Illuminate\Support\Facades\Auth;
use App\Models\Category;
use Illuminate\Support\Facades\Log;


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
                ->rules(['required']),
            ImportColumn::make('category_id')
                ->label(__(self::$slug . '.table.category'))
                ->requiredMapping()
                ->rules(['required']),
            ImportColumn::make('amount')
                ->label(__(self::$slug . '.form.amount'))
                ->requiredMapping()
                ->rules(['required']),
            ImportColumn::make('user')
                ->requiredMapping()
                ->relationship()
                ->rules(['required']),
            ImportColumn::make('date')
                ->label(__(self::$slug . '.form.date'))
                ->requiredMapping()
                ->rules(['required', 'date']),
        ];
    }

    // public function resolveRecord(): ?Expense
    // {
    //     Log::info('resolveRecord è stato richiamato!');
    //     return new Expense();
    // }

    public function resolveRecord(): ?Expense
    {
        Log::info('resolveRecord è stato richiamato!');
        Log::info('category_id->' . $this->data['category_id']);
        Log::info('name->' . $this->data['name']);

        $category = Category::where('name', $this->data['category_id'])->first();
        if ($category) {
            $this->data['category_id'] = $category->id;
        } else {
            // Gestisci il caso in cui la categoria non esiste
            // Potresti lanciare un'eccezione, saltare la riga, o assegnare un ID di default
            Log::info('Categoria non trovata per name->' . $this->data['category_id']);
            $this->data['category_id'] = 0; // O un ID di default
        }
        // $this->data['user_id'] = Auth::id();

        // return Expense::firstOrNew([
        //     // $this->data['user_id'] => 3,
        //     // 3 => $this->data['user_id'],
        //     $this->data['user_id'] => 3,
        // ]);

        $this->data['user_id'] = 3;
        return new Expense();
    }



    public static function getCompletedNotificationBody(Import $import): string
    {
        Log::info('getCompletedNotificationBody è stato richiamato!');
        $body = 'Your expense import has completed and ' . Number::format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . Number::format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}
