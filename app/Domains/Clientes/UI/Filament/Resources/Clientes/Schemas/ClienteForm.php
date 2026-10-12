<?php

namespace App\Domains\Clientes\UI\Filament\Resources\Clientes\Schemas;

use App\Domains\Cotizaciones\UI\Filament\Resources\Cotizaciones\CotizacionesResource;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
class ClienteForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Datos Fiscales (CFDI 4.0)')
                    ->description('Los datos deben coincidir exactamente con la Constancia de Situación Fiscal.')
                    ->columnSpanFull()
                    ->schema([
                        Select::make('type')
                            ->label('Tipo de Persona')
                            ->options([
                                'moral' => 'Persona Moral',
                                'fisica' => 'Persona Física',
                            ])
                            ->default('moral')
                            ->required(),

                        TextInput::make('business_name')
                            ->label('Nombre o Razón Social')
                            ->required()
                            ->maxLength(255)
                            ->extraInputAttributes(['style' => 'text-transform: uppercase;']),

                        TextInput::make('tax_id')
                            ->label('RFC')
                            ->required()
                            ->minLength(12)
                            ->maxLength(13)
                            ->unique(ignoreRecord: true)
                            ->validationMessages([
                                'unique' => 'El RFC ya está registrado.',
                                'required' => 'El RFC es obligatorio.',
                            ])
                            ->regex('/^[A-ZÑ&]{3,4}\d{6}[A-V1-9][A-Z1-9][0-9A]$/i')
                            ->extraInputAttributes(['style' => 'text-transform: uppercase;']),

                        TextInput::make('zip_code')
                            ->label('Código Postal Fiscal')
                            ->required()
                            ->numeric()
                            ->length(5),

                        TextInput::make('email')
                            ->label('Correo Electrónico')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->validationMessages([
                                'unique' => 'El Correo ya está registrado.',
                                'required' => 'El Correo es obligatorio.',
                            ])
                            ->maxLength(255),

                        TextInput::make('phone')
                            ->label('Teléfono')
                            ->tel()
                            ->required()
                            ->length(10),

                        Select::make('tax_regime')
                            ->label('Régimen Fiscal')
                            ->options([
                                '601' => '601 - General de Ley Personas Morales',
                                '603' => '603 - Personas Morales con Fines no Lucrativos',
                                '605' => '605 - Sueldos y Salarios e Ingresos Asimilados a Salarios',
                                '606' => '606 - Arrendamiento',
                                '612' => '612 - Personas Físicas con Actividades Empresariales y Profesionales',
                                '625' => '625 - Régimen de las Actividades Empresariales con ingresos a través de Plataformas Tecnológicas',
                                '626' => '626 - Régimen Simplificado de Confianza (RESICO)',
                            ])
                            ->required(),

                        Select::make('cfdi_use')
                            ->label('Uso de CFDI (Sugerido)')
                            ->required()
                            ->options([
                                'G01' => 'G01 - Adquisición de mercancías',
                                'G03' => 'G03 - Gastos en general',
                                'P01' => 'P01 - Por definir',
                                'CP01' => 'CP01 - Pagos',
                                'S01' => 'S01 - Sin efectos fiscales',
                            ])
                            ->default('G03'),
                    ])->columns(2)

            ]);
    }
}
