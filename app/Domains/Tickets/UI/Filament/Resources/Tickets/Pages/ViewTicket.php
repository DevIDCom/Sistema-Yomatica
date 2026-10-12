<?php

namespace App\Domains\Tickets\UI\Filament\Resources\Tickets\Pages;

use App\Domains\Tickets\Domain\StateMachines\Ticket\TicketState;
use App\Domains\Tickets\Infrastructure\Eloquent\Models\FsmTicket;
use App\Domains\Tickets\UI\Filament\Resources\Tickets\TicketResource;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewTicket extends ViewRecord
{
    protected static string $resource = TicketResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('changeStatus')
                ->label('Cambiar Estado')
                ->icon('heroicon-o-arrow-path')
                ->color('primary')
                ->form([
                    Select::make('status')
                        ->label('Nuevo Estado Operativo')
                        ->options(TicketState::getSelectOptions())
                        ->disableOptionWhen(function (string $value, ?FsmTicket $record) {
                            if (! $record) return false;
                            $currentState = (string) $record->status;

                            return $value !== $currentState && ! $record->status->canTransitionTo($value);
                        })
                        ->native(false)
                        ->required(),
                ])
                ->fillForm(fn (FsmTicket $record): array => [
                    'status' => (string) $record->status,
                ])
                ->action(function (array $data, FsmTicket $record): void {
                    $record->update(['status' => $data['status']]);

                    Notification::make()
                        ->title('Estado actualizado correctamente')
                        ->success()
                        ->send();
                }),
        ];
    }
}
