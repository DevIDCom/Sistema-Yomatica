<?php

namespace App\Domains\Sucursales\Infraestructure\Eloquent\Models;

use App\Domains\Tickets\Infrastructure\Eloquent\Models\FsmTicket;
use Database\Factories\CrmAreaFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class
CrmArea extends Model {
    use HasFactory;

    protected static function newFactory(): CrmAreaFactory
    {
        return CrmAreaFactory::new();
    }

    public $timestamps = false;
    protected $fillable = ['branch_id', 'name', 'floor_level'];

    public function branch(): BelongsTo { return $this->belongsTo(CrmBranch::class, 'branch_id'); }
    public function tickets(): HasMany { return $this->hasMany(FsmTicket::class, 'area_id'); }
}
