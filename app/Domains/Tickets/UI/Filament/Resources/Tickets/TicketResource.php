<?php

namespace App\Domains\Tickets\UI\Filament\Resources\Tickets;

use App\Domains\Tickets\Infrastructure\Eloquent\Models\FsmTicket;
use App\Domains\Tickets\UI\Filament\Resources\Tickets\Pages\CreateTicket;
use App\Domains\Tickets\UI\Filament\Resources\Tickets\Pages\EditTicket;
use App\Domains\Tickets\UI\Filament\Resources\Tickets\Pages\ListTickets;
use App\Domains\Tickets\UI\Filament\Resources\Tickets\Pages\ViewTicket;
use App\Domains\Tickets\UI\Filament\Resources\Tickets\RelationManagers\EvidencesRelationManager;
use App\Domains\Tickets\UI\Filament\Resources\Tickets\RelationManagers\MaterialsRelationManager;
use App\Domains\Tickets\UI\Filament\Resources\Tickets\Schemas\TicketForm;
use App\Domains\Tickets\UI\Filament\Resources\Tickets\Tables\TicketsTable;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class TicketResource extends Resource
{
    protected static ?string $model = FsmTicket::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-ticket';
    protected static ?string $modelLabel = 'Ticket';
    protected static ?string $pluralModelLabel = 'Tickets';
    protected static string|null|\UnitEnum $navigationGroup = 'Soporte';
    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'Ticket';

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        $user = auth()->user();

        if (! $user) {
            return $query;
        }

        if ($user->hasAnyRole(['super_admin', 'admin'])) {
            return $query;
        }

        return $query->where('assigned_tech_id', $user->id);
    }

    public static function form(Schema $schema): Schema
    {
        return TicketForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TicketsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            MaterialsRelationManager::class,
            EvidencesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListTickets::route('/'),
            'create' => CreateTicket::route('/create'),
            'view'   => ViewTicket::route('/{record}'),
            'edit'   => EditTicket::route('/{record}/edit'),
        ];
    }
}
