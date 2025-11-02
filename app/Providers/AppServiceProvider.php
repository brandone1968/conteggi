<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Filament\Actions\Imports\Models\Import as FilamentImport;
use Illuminate\Support\Facades\Auth;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // salva l'id dell'utente che avvia l'import direttamente sulla tabella imports
        FilamentImport::creating(function (FilamentImport $import): void {
            try {
                $import->user_id = Auth::id();
            } catch (\Throwable $e) {
                // non bloccare la richiesta
            }
        });
    }
}
