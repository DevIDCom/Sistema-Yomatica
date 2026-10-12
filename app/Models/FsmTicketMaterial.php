<?php

namespace App\Models;

use App\Domains\Tickets\Infrastructure\Eloquent\Models\FsmTicket;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FsmTicketMaterial extends Model {
    public $timestamps = false;
    protected $fillable = ['ticket_id', 'product_id', 'warehouse_id', 'quantity_used', 'unit_cost', 'client_updated_at', 'server_updated_at'];

    public function ticket(): BelongsTo { return $this->belongsTo(FsmTicket::class, 'ticket_id'); }
    public function product(): BelongsTo { return $this->belongsTo(CatalogProduct::class, 'product_id'); }
    public function warehouse(): BelongsTo { return $this->belongsTo(InventoryWarehouse::class, 'warehouse_id'); }
}
