<?php

namespace App\Domains\Proyectos\UI\Filament\Resources\Proyectos\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class InvoicesRelationManager extends RelationManager
{
    protected static string $relationship = 'invoices';

    protected static ?string $title = 'Facturas';

    protected static ?string $recordTitleAttribute = 'uuid';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Datos de la factura')
                    ->columns(2)
                    ->schema([
                        TextInput::make('uuid')
                            ->label('Folio interno')
                            ->placeholder('Ej: FAC-2026-0001')
                            ->required()
                            ->maxLength(255),

                        Select::make('milestone_id')
                            ->label('Hito facturado')
                            ->relationship(
                                name: 'milestone',
                                titleAttribute: 'name',
                                modifyQueryUsing: fn ($query) => $query->where('project_id', $this->getOwnerRecord()->getKey()),
                            )
                            ->searchable()
                            ->preload()
                            ->native(false),

                        TextInput::make('total')
                            ->label('Total')
                            ->placeholder('Ej: 45000.00')
                            ->numeric()
                            ->prefix('$')
                            ->minValue(0)
                            ->step(0.01)
                            ->required(),

                        Select::make('status')
                            ->label('Estatus')
                            ->options([
                                'draft'     => 'Borrador',
                                'stamped'   => 'Timbrada',
                                'paid'      => 'Pagada',
                                'cancelled' => 'Cancelada',
                            ])
                            ->default('draft')
                            ->native(false)
                            ->required(),
                    ]),

                Section::make('Datos fiscales (CFDI 4.0)')
                    ->description('Se llenan automáticamente al timbrar con Facturapi.')
                    ->columns(2)
                    ->schema([
                        TextInput::make('fiscal_uuid')
                            ->label('UUID Fiscal (SAT)')
                            ->placeholder('Ej: A1B2C3D4-E5F6-7890-ABCD-EF1234567890')
                            ->maxLength(36),
                        TextInput::make('facturapi_id')
                            ->label('ID Facturapi')
                            ->placeholder('Ej: 65f2a1b9c8d4e3f201234567')
                            ->maxLength(255),

                        Select::make('cfdi_use')
                            ->label('Uso de CFDI')
                            ->options([
                                'G01'  => 'G01 - Adquisición de mercancías',
                                'G03'  => 'G03 - Gastos en general',
                                'P01'  => 'P01 - Por definir',
                                'CP01' => 'CP01 - Pagos',
                                'S01'  => 'S01 - Sin efectos fiscales',
                            ])
                            ->default('G03')
                            ->native(false),

                        Select::make('tax_regime')
                            ->label('Régimen Fiscal')
                            ->options([
                                '601' => '601 - General de Ley Personas Morales',
                                '603' => '603 - Personas Morales con Fines no Lucrativos',
                                '605' => '605 - Sueldos y Salarios',
                                '606' => '606 - Arrendamiento',
                                '612' => '612 - Actividades Empresariales y Profesionales',
                                '626' => '626 - RESICO',
                            ])
                            ->native(false),

                        TextInput::make('receiver_zip_code')
                            ->label('C.P. del receptor')
                            ->placeholder('Ej: 64000')
                            ->numeric()
                            ->length(5),
                    ]),
            ]);
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('uuid')->label('Folio interno'),
                TextEntry::make('fiscal_uuid')->label('UUID Fiscal')->placeholder('Sin timbrar')->copyable(),
                TextEntry::make('milestone.name')->label('Hito')->placeholder('Sin hito'),
                TextEntry::make('client.business_name')->label('Cliente'),
                TextEntry::make('total')->label('Total')->money('MXN'),
                TextEntry::make('status')->label('Estatus')->badge(),
                TextEntry::make('cfdi_use')->label('Uso de CFDI'),
                TextEntry::make('tax_regime')->label('Régimen Fiscal'),
            ])
            ->columns(2);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('uuid')
            ->columns([
                TextColumn::make('uuid')
                    ->label('Folio')
                    ->searchable()
                    ->copyable()
                    ->fontFamily('mono')
                    ->limit(13),

                TextColumn::make('fiscal_uuid')
                    ->label('UUID Fiscal')
                    ->placeholder('Sin timbrar')
                    ->copyable()
                    ->fontFamily('mono')
                    ->limit(10)
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('milestone.name')
                    ->label('Hito')
                    ->placeholder('Sin hito')
                    ->wrap(),

                TextColumn::make('status')
                    ->label('Estatus')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'paid'      => 'success',
                        'stamped'   => 'info',
                        'cancelled' => 'danger',
                        default     => 'gray',
                    }),

                TextColumn::make('total')
                    ->label('Total')
                    ->money('MXN')
                    ->weight('bold')
                    ->sortable()
                    ->summarize(\Filament\Tables\Columns\Summarizers\Sum::make()->money('MXN')),
            ])
            ->headerActions([
                CreateAction::make()->label('Nueva factura'),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make()
                    ->visible(fn ($record) => blank($record->fiscal_uuid)),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('Sin facturas')
            ->emptyStateDescription('Aún no se ha emitido ninguna factura de este proyecto.');
    }
}
