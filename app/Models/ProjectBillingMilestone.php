<?php

namespace App\Models;

use App\Domains\Facturas\Infraestructure\Eloquent\Models\BillingInvoice;
use App\Domains\Proyectos\Infraestructure\Eloquent\Models\FsmProject;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ProjectBillingMilestone extends Model {
    public $timestamps = false;
    protected $fillable = ['project_id', 'name', 'percentage', 'amount', 'status', 'due_date'];

    public function project(): BelongsTo { return $this->belongsTo(FsmProject::class, 'project_id'); }
    public function invoice(): HasOne { return $this->hasOne(BillingInvoice::class, 'milestone_id'); }
}
