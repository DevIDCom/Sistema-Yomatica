<?php

namespace App\Domains\Sucursales\Infraestructure\Eloquent\Models;

use App\Domains\Clientes\Infraestructure\Eloquent\Models\CrmClient;
use App\Domains\Clientes\Infraestructure\Eloquent\Models\CrmContact;
use App\Domains\Cotizaciones\Infraestructure\Eloquent\Models\CpqQuote;
use Database\Factories\CrmBranchFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class CrmBranch extends Model
{
    use HasFactory;

    protected $fillable = ['client_id', 'name', 'address'];

    protected static function newFactory(): CrmBranchFactory{
        return CrmBranchFactory::new();
    }
    public function scopeWithCoordinates(Builder $query): Builder
    {
        return $query
            ->addSelect($this->getTable() . '.*')
            ->selectRaw('ST_Y(location::geometry) AS latitude')
            ->selectRaw('ST_X(location::geometry) AS longitude');
    }
    public function setCoordinates(?float $lat, ?float $lng): void
    {
        $this->location = (filled($lat) && filled($lng))
            ? DB::raw(sprintf('ST_SetSRID(ST_MakePoint(%F, %F), 4326)', $lng, $lat))
            : null;
    }
    public function hasCoordinates(): bool
    {
        return filled($this->latitude) && filled($this->longitude);
    }


    public function client(): BelongsTo
    {
        return $this->belongsTo(CrmClient::class, 'client_id');
    }

    public function areas(): HasMany
    {
        return $this->hasMany(CrmArea::class, 'branch_id');
    }

    public function contacts(): HasMany
    {
        return $this->hasMany(CrmContact::class, 'branch_id');
    }

    public function quotes(): HasMany
    {
        return $this->hasMany(CpqQuote::class, 'branch_id');
    }
}
