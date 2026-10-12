<?php

namespace App\Providers;

use App\Domains\Cotizaciones\Domain\Policies\CpqQuotePolicy;
use App\Domains\Cotizaciones\Infraestructure\Eloquent\Models\CpqQuote;
use App\Domains\Tickets\Domain\Policies\FsmTicketPolicy;
use App\Domains\Tickets\Infrastructure\Eloquent\Models\FsmTicket;
use App\Domains\Users\Models\User;
use BezhanSalleh\FilamentShield\Commands\SuperAdminCommand;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }
    public function boot(): void{
        Gate::policy(FsmTicket::class, FsmTicketPolicy::class);
        Gate::policy(CpqQuote::class, CpqQuotePolicy::class);

    }
}
