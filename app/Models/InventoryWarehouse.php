<?php

namespace App\Models;

use App\Domains\Users\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InventoryWarehouse extends Model {
    public $timestamps = false;
    protected $fillable = ['name', 'assigned_tech_id', 'type'];

    public function assignedTech(): BelongsTo { return $this->belongsTo(User::class, 'assigned_tech_id'); }
    public function stocks(): HasMany { return $this->hasMany(InventoryStock::class, 'warehouse_id'); }
    public function movements(): HasMany { return $this->hasMany(InventoryMovement::class, 'warehouse_id'); }
}
