<?php

namespace App\Filament\Resources\Expenses;

use App\Filament\Resources\Expenses\Pages\ManageExpenses;
use App\Models\Expense;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Hidden;

class ExpenseResource extends Resource
{
    protected static ?string $model = Expense::class;

    protected static ?string $slug = 'expenses';

    public static function getModelLabel(): string
    {
        return __(self::$slug . '.model-label');
    }

    public static function getPluralModelLabel(): string
    {
        return __(self::$slug . '.plural-model-label');
    }

    protected static string | UnitEnum | null $navigationGroup = 'Impostazioni';

    // protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;
    protected static string|BackedEnum|null $navigationIcon = null;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->label(__(self::$slug . '.form.name')),
                Select::make('category_id')
                    ->label(__(self::$slug . '.table.category'))
                    ->relationship('category', 'name')
                    ->required(),
                TextInput::make('amount')
                    ->label(__(self::$slug . '.form.amount'))
                    ->required(),
                Hidden::make('user_id')
                    ->default(Auth::id()),
                DatePicker::make('date')
                    ->required()
                    ->default(now())
                    ->label(__(self::$slug . '.form.date')),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name')
                    ->sortable()
                    ->searchable()
                    ->label(__(self::$slug . '.table.name')),
                TextColumn::make('category.name')
                    ->label(__(self::$slug . '.table.category'))
                    ->sortable(),
                TextColumn::make('amount')
                    ->label(__(self::$slug . '.table.amount'))
                    ->numeric()
                    ->searchable(),
                TextColumn::make('user.name')
                    ->label(__(self::$slug . '.table.user'))
                    ->sortable(),
                TextColumn::make('date')
                    ->date()
                    ->label(__(self::$slug . '.table.date'))
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->label(__(self::$slug . '.table.created_at'))
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label(__(self::$slug . '.table.updated_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageExpenses::route('/'),
        ];
    }
}
