<?php

namespace App\Domains\Clientes\UI\Filament\Resources\Clientes\RelationManagers;

use App\Domains\Sucursales\Infraestructure\Eloquent\Models\CrmBranch;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class SucursalesRelationManager extends RelationManager
{
    protected static string $relationship = 'branches';

    protected static ?string $title = 'Sucursales';

    protected static ?string $recordTitleAttribute = 'name';

    protected function splitCoordinates(array $data): array
    {
        $lat = $data['latitude'] ?? null;
        $lng = $data['longitude'] ?? null;

        unset($data['latitude'], $data['longitude']);

        return [
            $data,
            filled($lat) ? (float) $lat : null,
            filled($lng) ? (float) $lng : null,
        ];
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nombre de la sucursal')
                    ->placeholder('Ej. Sucursal Norte')
                    ->required()
                    ->minLength(3)
                    ->maxLength(255)
                    ->validationMessages([
                        'required' => 'Escribe el nombre de la sucursal.',
                        'min'      => 'El nombre es demasiado corto.',
                    ])
                    ->columnSpanFull(),

                Textarea::make('address')
                    ->label('Dirección')
                    ->placeholder('Ej. Av. Insurgentes Sur 123, Col. Centro, C.P. 06000')
                    ->rows(3)
                    ->maxLength(500)
                    ->columnSpanFull(),

                TextInput::make('latitude')
                    ->label('Latitud')
                    ->placeholder('Ej. 19.432608')
                    ->helperText('Opcional. Captura ambas coordenadas o ninguna.')
                    ->numeric()
                    ->minValue(-90)
                    ->maxValue(90)
                    ->step('0.000001')
                    ->requiredWith('longitude')
                    ->validationMessages([
                        'numeric'       => 'La latitud debe ser un número decimal.',
                        'min'           => 'La latitud va de -90 a 90.',
                        'max'           => 'La latitud va de -90 a 90.',
                        'required_with' => 'Captura ambas coordenadas o ninguna.',
                    ])
                    // Columna calculada: la persiste el modelo, no Filament.
                    ->dehydrated(false),

                TextInput::make('longitude')
                    ->label('Longitud')
                    ->placeholder('Ej. -99.133209')
                    ->numeric()
                    ->minValue(-180)
                    ->maxValue(180)
                    ->step('0.000001')
                    ->requiredWith('latitude')
                    ->validationMessages([
                        'numeric'       => 'La longitud debe ser un número decimal.',
                        'min'           => 'La longitud va de -180 a 180.',
                        'max'           => 'La longitud va de -180 a 180.',
                        'required_with' => 'Captura ambas coordenadas o ninguna.',
                    ])
                    ->dehydrated(false),
            ])
            ->columns(2);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->modifyQueryUsing(fn (Builder $query) => $query->withCoordinates())
            ->columns([
                TextColumn::make('name')
                    ->label('Sucursal')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('address')
                    ->label('Dirección')
                    ->placeholder('Sin dirección')
                    ->searchable()
                    ->limit(50)
                    ->tooltip(fn ($state) => $state),

                TextColumn::make('latitude')
                    ->label('Coordenadas')
                    ->placeholder('Sin ubicación')
                    ->formatStateUsing(fn ($record) => $record->latitude && $record->longitude
                        ? number_format((float) $record->latitude, 6) . ', ' . number_format((float) $record->longitude, 6)
                        : null)
                    ->fontFamily('mono')
                    ->copyable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('areas_count')
                    ->label('Áreas')
                    ->counts('areas')
                    ->badge(),

                TextColumn::make('contacts_count')
                    ->label('Contactos')
                    ->counts('contacts')
                    ->badge(),
            ])
            ->defaultSort('name')
            ->headerActions([
                CreateAction::make()
                    ->label('Nueva sucursal')
                    ->using(function (array $data): CrmBranch {
                        [$attributes, $lat, $lng] = $this->splitCoordinates($data);

                        /** @var CrmBranch $branch */
                        $branch = $this->getRelationship()->make($attributes);
                        $branch->setCoordinates($lat, $lng);
                        $branch->save();

                        return $branch->refresh();
                    }),
            ])
            ->recordActions([
                EditAction::make()
                    ->using(function (CrmBranch $record, array $data): CrmBranch {
                        [$attributes, $lat, $lng] = $this->splitCoordinates($data);

                        $record->fill($attributes);
                        $record->setCoordinates($lat, $lng);
                        $record->save();

                        return $record->refresh();
                    }),

                DeleteAction::make()
                    ->before(function (CrmBranch $record, DeleteAction $action) {
                        if ($record->areas()->exists() || $record->contacts()->exists()) {
                            Notification::make()
                                ->title('La sucursal tiene registros asociados')
                                ->body('Elimina primero sus áreas y contactos.')
                                ->warning()
                                ->send();

                            $action->halt();
                        }
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('Sin sucursales')
            ->emptyStateDescription('Registra las sucursales de este cliente.');
    }
}
