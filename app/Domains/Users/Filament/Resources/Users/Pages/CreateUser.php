<?php

namespace App\Domains\Users\Filament\Resources\Users\Pages;

use App\Domains\Users\Filament\Resources\Users\UserResource;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;
}
