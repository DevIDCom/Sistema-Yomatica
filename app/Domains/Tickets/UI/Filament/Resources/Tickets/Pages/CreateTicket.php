<?php

namespace App\Domains\Tickets\UI\Filament\Resources\Tickets\Pages;

use App\Domains\Tickets\Infrastructure\Services\TicketAssignmentNotifier;
use App\Domains\Tickets\UI\Filament\Resources\Tickets\TicketResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTicket extends CreateRecord
{
    protected static string $resource = TicketResource::class;

}
