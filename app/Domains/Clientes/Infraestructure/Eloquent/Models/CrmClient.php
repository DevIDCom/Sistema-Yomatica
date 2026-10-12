<?php

namespace App\Domains\Clientes\Infraestructure\Eloquent\Models;

use App\Domains\Cotizaciones\Infraestructure\Eloquent\Models\CpqQuote;
use App\Domains\Facturas\Infraestructure\Eloquent\Models\BillingInvoice;
use App\Domains\Proyectos\Infraestructure\Eloquent\Models\FsmProject;
use App\Domains\Sucursales\Infraestructure\Eloquent\Models\CrmBranch;
use App\Domains\Tickets\Infrastructure\Eloquent\Models\Evidence;
use Database\Factories\CrmClientFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class CrmClient extends Model
{
    use SoftDeletes;
    use HasFactory;

    protected static function newFactory(): CrmClientFactory{
        return CrmClientFactory::new();
    }

    protected $fillable = [
        'uuid',
        'business_name',
        'tax_id',
        'tax_regime',
        'zip_code',
        'type',
        'email',
        'phone',
        'cfdi_use'
    ];

    protected static function booted(): void
    {
        static::creating(function (CrmClient $client) {
            if (empty($client->uuid)) {
                $client->uuid = (string) Str::uuid();
            }
        });
    }

    public function branches(): HasMany { return $this->hasMany(CrmBranch::class, 'client_id'); }
    public function contacts(): HasMany { return $this->hasMany(CrmContact::class, 'client_id'); }
    public function quotes(): HasMany { return $this->hasMany(CpqQuote::class, 'client_id'); }
    public function projects(): HasMany { return $this->hasMany(FsmProject::class, 'client_id'); }
    public function invoices(): HasMany { return $this->hasMany(BillingInvoice::class, 'client_id'); }
    public function evidences(): HasMany { return $this->hasMany(Evidence::class, 'client_id'); }
}
