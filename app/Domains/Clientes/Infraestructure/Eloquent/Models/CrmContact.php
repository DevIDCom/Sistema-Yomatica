<?php

namespace App\Domains\Clientes\Infraestructure\Eloquent\Models;

use App\Domains\Sucursales\Infraestructure\Eloquent\Models\CrmBranch;
use Database\Factories\CrmContactFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CrmContact extends Model {
    use HasFactory;

    protected static function newFactory(): CrmContactFactory {
        return CrmContactFactory::new();
    }

    public $timestamps = false;
    protected $fillable = ['client_id', 'branch_id', 'name', 'phone', 'email', 'role_title'];

    public function client(): BelongsTo { return $this->belongsTo(CrmClient::class, 'client_id'); }
    public function branch(): BelongsTo { return $this->belongsTo(CrmBranch::class, 'branch_id'); }
}
