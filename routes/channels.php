<?php

use App\Domains\Tickets\Infrastructure\Eloquent\Models\FsmTicket;
use App\Domains\Users\Models\User;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Domains.Users.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});
Broadcast::channel('tickets', function (User $user) {
    return $user->can('viewAny', FsmTicket::class);
});
