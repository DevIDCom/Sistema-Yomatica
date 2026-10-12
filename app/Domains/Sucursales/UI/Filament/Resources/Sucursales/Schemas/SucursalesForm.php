<?php

namespace App\Domains\Sucursales\UI\Filament\Resources\Sucursales\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SucursalesForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Información de la Sucursal')
                    ->description('Ubicación operativa o planta del cliente para atención de tickets de servicio.')
                    ->columnSpanFull()
                    ->columns(1)
                    ->schema([
                        Select::make('client_id')
                            ->label('Cliente')
                            ->relationship('client', 'business_name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->columnSpanFull(),

                        TextInput::make('name')
                            ->label('Nombre de la Sucursal / Planta')
                            ->placeholder('Ej: Planta Norte, Corporativo Centro, CEDIS')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),

                        Textarea::make('address')
                            ->label('Dirección Completa')
                            ->placeholder('Calle, número, colonia, código postal, ciudad y estado')
                            ->rows(3)
                            ->required()
                            ->columnSpanFull(),

                        TextInput::make('location')
                            ->label('Coordenadas PostGIS / Geolocalización')
                            ->placeholder('Ej: POINT(-106.08889 28.63528)')
                            ->helperText('Formato WKT (Longitud Latitud) para el cálculo de geocercas en PostGIS.')
                            ->nullable()
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
