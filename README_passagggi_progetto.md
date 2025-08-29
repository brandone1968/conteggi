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
sail php artisan make:model Expense -m








