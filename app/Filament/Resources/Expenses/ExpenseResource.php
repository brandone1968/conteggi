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

    protected static ?string $slug = 'expense';

    protected static string | UnitEnum | null $navigationGroup = 'Settings';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->label(_(self::$slug . '.form.name')),
                Select::make('category_id')
                    ->relationship('category', 'name')
                    ->required(),
                TextInput::make('amount')
                    ->required(),
                Hidden::make('user_id')
                    ->default(Auth::id()),
                DatePicker::make('date')
                    ->required()
                    ->default(now())
                    ->label(_(self::$slug . '.form.date')),
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
                    ->label(_(self::$slug . '.table.name')),
                TextColumn::make('expenses.id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('amount')
                    ->searchable(),
                TextColumn::make('user_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('date')
                    ->date()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
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
