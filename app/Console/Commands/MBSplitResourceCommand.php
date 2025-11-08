<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

class MBSplitResourceCommand extends Command
{
    /**
     * La firma del comando (come viene chiamato).
     * {name} è l'argomento (es. User)
     */
    protected $signature = 'MB:splitresource {directory}';

    /**
     * La descrizione del comando.
     */
    protected $description = 'Crea file Table e Form separati per una risorsa, data la sua directory';

    /**
     * @var Filesystem
     */
    protected $files;

    /**
     * Crea una nuova istanza del comando.
     */
    public function __construct(Filesystem $files)
    {
        parent::__construct();
        $this->files = $files;
    }

    /**
     * Esegue la logica del comando.
     */
    public function handle()
    {
        // 1. Ottieni il nome della directory (es. "Categories" o "UserResource")
        $directoryName = Str::studly(class_basename($this->argument('directory')));

        // 2. Definisci il percorso base
        $baseResourcePath = app_path('Filament/Resources/' . $directoryName);

        // 3. Controlla se la directory della risorsa esiste
        if (!$this->files->isDirectory($baseResourcePath)) {
            $this->error("La directory della risorsa {$directoryName} non esiste in app/Filament/Resources/");
            return 1; // Termina con errore
        }

        // 4. Indovina i nomi del modello
        // es. "Categories" -> singular "Category"
        // es. "UserResource" -> singular "User" (rimuovendo "Resource")
        $modelName = Str::studly(Str::singular(str_replace('Resource', '', $directoryName))); // "Category"
        $pluralModelName = Str::pluralStudly($modelName); // "Categories"
        $resourceName = $modelName . 'Resource'; // "CategoryResource"

        // Controlla che il file risorsa esista davvero lì
        if (!$this->files->exists($baseResourcePath . '/' . $resourceName . '.php')) {
            $this->error("File risorsa {$resourceName}.php non trovato in {$baseResourcePath}/");
            $this->comment("Assicurati che il nome della directory sia corretto e che il modello ({$modelName}) sia corrivo.");
            return 1;
        }

        // 5. Definisci i percorsi per i nuovi file
        $tablesDir = $baseResourcePath . '/Tables';
        $schemasDir = $baseResourcePath . '/Schemas';

        // 6. Definisci i nomi dei file e delle classi
        $tableClassName = $pluralModelName . 'Table';   // CategoriesTable
        $schemaClassName = $pluralModelName . 'Form'; // CategoriesForm

        $tablePath = $tablesDir . '/' . $tableClassName . '.php';
        $schemaPath = $schemasDir . '/' . $schemaClassName . '.php';

        // 7. Controlla se i file esistono già
        if ($this->files->exists($tablePath) || $this->files->exists($schemaPath)) {
            $this->error('I file Table o Schema esistono già.');
            return 1;
        }

        // 8. Crea le directory
        $this->files->makeDirectory($tablesDir, 0755, true, true);
        $this->files->makeDirectory($schemasDir, 0755, true, true);

        // 9. Prendi il contenuto degli stub
        $tableStub = $this->getStubContent('split-resource.table.stub');
        $schemaStub = $this->getStubContent('split-resource.schema.stub');

        // 10. Definisci i namespace
        $baseNamespace = 'App\\Filament\\Resources\\' . $directoryName; // Es. App\Filament\Resources\Categories
        $tableNamespace = $baseNamespace . '\\Tables';                  // Es. App\Filament\Resources\Categories\Tables
        $schemaNamespace = $baseNamespace . '\\Schemas';                // Es. App\Filament\Resources\Categories\Schemas

        // 11. Sostituisci i placeholder
        $tableContent = $this->replacePlaceholders($tableStub, $tableNamespace, $tableClassName, $modelName);
        $schemaContent = $this->replacePlaceholders($schemaStub, $schemaNamespace, $schemaClassName, $modelName);

        // 12. Scrivi i file
        $this->files->put($tablePath, $tableContent);
        $this->files->put($schemaPath, $schemaContent);

        $this->info("File {$tableClassName}.php e {$schemaClassName}.php creati con successo in {$directoryName}!");
        $this->comment("Ricorda di aggiornare la tua risorsa {$resourceName} per usare queste nuove classi.");

        return 0; // Termina con successo
    }

    /**
     * Carica il contenuto di uno stub.
     */
    protected function getStubContent(string $stubName): string
    {
        return $this->files->get(__DIR__ . '/Stubs/' . $stubName);
    }

    /**
     * Sostituisce i placeholder comuni negli stub.
     */
    protected function replacePlaceholders(string $stub, string $namespace, string $class, string $model): string
    {
        return str_replace(
            ['{{ namespace }}', '{{ class }}', '{{ model }}'],
            [$namespace, $class, $model],
            $stub
        );
    }
}
