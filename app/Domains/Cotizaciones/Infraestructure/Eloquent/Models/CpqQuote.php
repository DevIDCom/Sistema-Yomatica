<?php

namespace App\Domains\Cotizaciones\Infraestructure\Eloquent\Models;

use App\Domains\Clientes\Infraestructure\Eloquent\Models\CrmClient;
use App\Domains\Cotizaciones\Domain\StateMachines\Quote\QuoteState;
use App\Domains\Proyectos\Infraestructure\Eloquent\Models\FsmProject;
use App\Domains\Sucursales\Infraestructure\Eloquent\Models\CrmBranch;
use App\Models\CpqQuoteItem;
use App\Models\StatusHistory;
use Database\Factories\CpqQuoteFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Spatie\ModelStates\HasStates;

class CpqQuote extends Model {
    protected $fillable = ['uuid', 'parent_id', 'client_id', 'branch_id', 'status', 'currency', 'exchange_rate', 'total'];
    use HasStates;
    use HasFactory;

    protected static function newFactory(): CpqQuoteFactory {
        return CpqQuoteFactory::new();
    }
    protected function casts(): array
    {
        return [
            'status' => QuoteState::class,
        ];
    }
    public function client(): BelongsTo { return $this->belongsTo(CrmClient::class, 'client_id'); }
    public function branch(): BelongsTo { return $this->belongsTo(CrmBranch::class, 'branch_id'); }
    public function parentQuote(): BelongsTo { return $this->belongsTo(CpqQuote::class, 'parent_id'); }
    public function childQuotes(): HasMany { return $this->hasMany(CpqQuote::class, 'parent_id'); }
    public function items(): HasMany { return $this->hasMany(CpqQuoteItem::class, 'quote_id'); }
    public function project(): HasOne { return $this->hasOne(FsmProject::class, 'quote_id'); }
    public function statusHistories(): HasMany { return $this->hasMany(StatusHistory::class, 'quote_id'); }
}
