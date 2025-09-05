# Progetto Conteggi


<h3 align="left">Languages and Tools:</h3>
<p align="left"> <a href="https://git-scm.com/" target="_blank" rel="noreferrer"> <img src="https://www.vectorlogo.zone/logos/git-scm/git-scm-icon.svg" alt="git" width="40" height="40"/> </a> <a href="https://www.w3.org/html/" target="_blank" rel="noreferrer"> <img src="https://raw.githubusercontent.com/devicons/devicon/master/icons/html5/html5-original-wordmark.svg" alt="html5" width="40" height="40"/> </a> <a href="https://laravel.com/" target="_blank" rel="noreferrer"> <img src="https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/laravel/laravel-original.svg" alt="laravel" width="40" height="40" /> </a> <a href="https://www.linux.org/" target="_blank" rel="noreferrer"> <img src="https://raw.githubusercontent.com/devicons/devicon/master/icons/linux/linux-original.svg" alt="linux" width="40" height="40"/> </a> <a href="https://sqlite.org//" target="_blank" rel="noreferrer"> <img src="https://www.vectorlogo.zone/logos/sqlite/sqlite-ar21.svg" alt="SQLite" width="80" height="40"/> </a> <a href="https://www.php.net" target="_blank" rel="noreferrer"> <img src="https://raw.githubusercontent.com/devicons/devicon/master/icons/php/php-original.svg" alt="php" width="60" height="40"/> </a> <a href="https://tailwindcss.com/" target="_blank" rel="noreferrer"> <img src="https://www.vectorlogo.zone/logos/tailwindcss/tailwindcss-icon.svg" alt="tailwind" width="40" height="40"/> </a> </p>


## Crazione App di base
cd Sviluppo/Laravel
laravel new
quindi inserisci il nome del progetto
es:  conteggi
nessun starter kit per il progetto del tutorial, quindi seleziona 
none 
SQLite
no npm install and npm

Modificare il composer per impostare la versione corretta di php e eventualmente per impostrazioni db
cd conteggi

Per Filament
composer require filament/filament:"^4.0"

Per la debug bar
composer require barryvdh/laravel-debugbar --dev
 
php artisan sail:install
 
Togli la spunta da Mysql con il tasto spazio se usi SQLite
 
sail up -d
sail npm install
(sail php artisan migrate NON SERVE se usi SQLite)
 
sail php artisan filament:install --panels
sail php artisan make:filament-user
 
sail npm install && sail npm run build
sail npm run dev
 
sail composer require spatie/laravel-permission
sail php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
 
sail  php artisan migrate
 
aggiungi nel modello User.php
// The User model requires this trait (se da errore fai cercare da VSCode il percorso giusto di Spatie)
use HasRoles;
 
Imposta il logo e/o il nome del brand in:
app/Providers/Filament/AdminPanelProvider.php
con
    ->brandName('Conteggi');
 // ->brandLogo(asset('images/logo.png'));

Le immagini mettile in public/images (se la certella images non esiste creala)

## Creare repository e portare l'App su GitHub
Inizializza il repository locale  
git init  
git add .  
git commit -m "primo commit"  

Creare nuovo repository su GitHub  
Dal sito di GitHub cliccare sul tasto verde New oppure in alto a destra, c`è il tasto + che apre la tendina New repository  
poi assegna il nome, e basta,  
copia il Quick setup  
git remote add origin https://github.com/brandone1968/conteggi.git  
git branch -M main  
git push -u origin main  

## Creazione Migrazione  Modello 
sail php artisan make:model Category -m
sail php artisan make:model Expense -m

## Esecuzione Migrazioni
sail php artisan migrate

## Creazione risorse Filament
sail php artisan make:filament-resource Customer
### di tipo modale Modale
sail php artisan make:filament-resource Customer --simple
#### con creazione autoamtica Form e Table a partire dal Modello 
sail php artisan make:filament-resource Customer --generate

## Procedo quindi con:
sail php artisan make:filament-resource Category --simple --generate
sail php artisan make:filament-resource Expense --simple --generate

## Raggruppamento degli elementi di navigazione
Per raggruppare sotto lo stesso menu un gruppo di risorse filament, aggiungere in ogni risorsa:

use UnitEnum;

protected static string | UnitEnum | null $navigationGroup = 'Settings';

## Aggiungere Laravel Boost 
sail composer require laravel/boost --dev
sail php artisan boost:install

VSCode
1 Open the Command Palette (Cmd+Shift+P or Ctrl+Shift+P)
2 Press enter on "MCP: List Servers"
3 Arrow to laravel-boost and press enter
4 Choose 'Start server' and you're good to go!

## Togliere icone a tutti gli elementi di un gruppo e aggiungerla al gruppo
Impostare in tutte le risorse del gruppo:  
protected static string|BackedEnum|null $navigationIcon = null;  

## Raggruppare gli elementi di navigazione in un gruppo
protected static string | UnitEnum | null $navigationGroup = 'Settings';

## Impostare multilingua
Se vuoi semplicemente impostare la lingua italiana come principale:  
in app.php  
<?php
'locale' => 'it',
'fallback_locale' => 'it',
// ...existing code...  

e nel file .env  
APP_LOCALE=it
APP_FALLBACK_LOCALE=it  

poi crea una cartella Lang\it e metti dentro le etichette, es:  

<?php

return [
    'model-label' => 'Categoria',
    'plural-model-label' => 'Categorie',
    'navigation-label' => 'Categorie',
    'form' => [
        'name' => 'Nome',
        'date' => 'Data',
    ],
    'table' => [
        'name' => 'Nome',
    ],
];

e nelle risorse:  

    protected static ?string $slug = 'categories';

    public static function getModelLabel(): string
    {
        return __(self::$slug . '.model-label');
    }

    public static function getPluralModelLabel(): string
    {
        return __(self::$slug . '.plural-model-label');
    }  


Per gestire invece più lingue usa il plugin bezhansalleh/filament-language-switch:  

sail composer require bezhansalleh/filament-language-switch
sail php artisan vendor:publish --tag="filament-language-switch-config"





