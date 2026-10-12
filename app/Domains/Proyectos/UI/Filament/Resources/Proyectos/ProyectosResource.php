<?php

namespace App\Domains\Proyectos\UI\Filament\Resources\Proyectos;

use App\Domains\Proyectos\Infraestructure\Eloquent\Models\FsmProject;
use App\Domains\Proyectos\UI\Filament\Resources\Proyectos\Pages\CreateProyectos;
use App\Domains\Proyectos\UI\Filament\Resources\Proyectos\Pages\EditProyectos;
use App\Domains\Proyectos\UI\Filament\Resources\Proyectos\Pages\ListProyectos;
use App\Domains\Proyectos\UI\Filament\Resources\Proyectos\RelationManagers\EvidencesRelationManager;
use App\Domains\Proyectos\UI\Filament\Resources\Proyectos\RelationManagers\InvoicesRelationManager;
use App\Domains\Proyectos\UI\Filament\Resources\Proyectos\RelationManagers\MilestonesRelationManager;
use App\Domains\Proyectos\UI\Filament\Resources\Proyectos\RelationManagers\TicketsRelationManager;
use App\Domains\Proyectos\UI\Filament\Resources\Proyectos\Schemas\ProyectosForm;
use App\Domains\Proyectos\UI\Filament\Resources\Proyectos\Tables\ProyectosTable;
use BackedEnum;
use Filament\Resources\RelationManagers\RelationGroup;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ProyectosResource extends Resource
{
    protected static ?string $model = FsmProject::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;
    protected static ?string $modelLabel = 'Proyecto';
    protected static ?string $pluralModelLabel = 'Proyectos';
    protected static string|null|\UnitEnum $navigationGroup = 'Administracion';
    protected static ?int $navigationSort = 1;


    protected static ?string $recordTitleAttribute = 'Proyectos';

    public static function form(Schema $schema): Schema
    {
        return ProyectosForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ProyectosTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            TicketsRelationManager::class,
            EvidencesRelationManager::class,
            RelationGroup::make('Finanzas', [
                MilestonesRelationManager::class,
                InvoicesRelationManager::class,
            ]),
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListProyectos::route('/'),
            'create' => CreateProyectos::route('/create'),
            'edit' => EditProyectos::route('/{record}/edit'),
        ];
    }
}
