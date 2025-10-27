<?php

namespace App\Filament\Imports;

use App\Models\Expense;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Exceptions\RowImportFailedException;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Number;
use Illuminate\Support\Facades\Auth;
use App\Models\Category;
use Illuminate\Support\Facades\Log;
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
            ImportColumn::make('category')
                ->label(__(self::$slug . '.form.category'))
                ->requiredMapping()
                ->relationship(resolveUsing: 'name')
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

    public function resolveRecord(): ?Expense
    {
        Log::info('Tentativo di risoluzione record con i seguenti dati:', $this->data);

        // Usiamo fill() per popolare il modello con i dati validati dal CSV.
        // Aggiungiamo manualmente l'ID dell'utente loggato.
        // Filament gestirà la risoluzione della relazione 'category' in 'category_id'.
        return (new Expense)->fill(
            array_merge($this->data, ['user_id' => auth()->id()])
        );
    }

    protected function afterSave(Model $record): void
    {
        // LOG 2: Conferma che il record è stato salvato con successo
        Log::info('Record spesa salvato con successo:', $record->toArray());
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
