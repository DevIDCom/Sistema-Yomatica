<?php

namespace App\Domains\Proyectos\UI\Filament\Resources\Proyectos\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class MilestonesRelationManager extends RelationManager
{
    protected static string $relationship = 'milestones';

    protected static ?string $title = 'Hitos de Facturación';

    protected static ?string $recordTitleAttribute = 'name';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nombre del hito')
                    ->placeholder('Ej: Anticipo 30% a la firma')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),

                TextInput::make('percentage')
                    ->label('Porcentaje')
                    ->placeholder('Ej: 30')
                    ->numeric()
                    ->suffix('%')
                    ->minValue(0)
                    ->maxValue(100)
                    ->step(0.01)
                    ->required(),

                TextInput::make('amount')
                    ->label('Monto')
                    ->placeholder('Ej: 45000.00')
                    ->numeric()
                    ->prefix('$')
                    ->minValue(0)
                    ->step(0.01)
                    ->required(),

                Select::make('status')
                    ->label('Estatus')
                    ->options([
                        'pending'  => 'Pendiente',
                        'invoiced' => 'Facturado',
                        'paid'     => 'Pagado',
                    ])
                    ->default('pending')
                    ->native(false)
                    ->required(),

                DatePicker::make('due_date')
                    ->label('Fecha compromiso')
                    ->placeholder('Selecciona una fecha')
                    ->displayFormat('d/m/Y')
                    ->native(false),
            ])
            ->columns(2);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name')
                    ->label('Hito')
                    ->searchable()
                    ->wrap(),

                TextColumn::make('percentage')
                    ->label('%')
                    ->numeric(decimalPlaces: 2)
                    ->suffix('%')
                    ->sortable(),

                TextColumn::make('amount')
                    ->label('Monto')
                    ->money('MXN')
                    ->weight('bold')
                    ->sortable()
                    ->summarize(\Filament\Tables\Columns\Summarizers\Sum::make()->money('MXN')),

                TextColumn::make('status')
                    ->label('Estatus')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'paid'     => 'success',
                        'invoiced' => 'info',
                        default    => 'gray',
                    }),

                TextColumn::make('due_date')
                    ->label('Vence')
                    ->date('d/m/Y')
                    ->placeholder('Sin fecha')
                    ->sortable(),

                TextColumn::make('invoice.uuid')
                    ->label('Factura')
                    ->placeholder('No facturado')
                    ->limit(13)
                    ->fontFamily('mono'),
            ])
            ->defaultSort('due_date')
            ->headerActions([
                CreateAction::make()->label('Nuevo hito'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make()
                    ->visible(fn ($record) => ! $record->invoice()->exists()),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('Sin hitos de facturación')
            ->emptyStateDescription('Define cómo se facturará este proyecto por etapas.');
    }
}
