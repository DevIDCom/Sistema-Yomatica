<?php

namespace App\Domains\Proyectos\Infraestructure\Eloquent\Models;

use App\Domains\Clientes\Infraestructure\Eloquent\Models\CrmClient;
use App\Domains\Cotizaciones\Infraestructure\Eloquent\Models\CpqQuote;
use App\Domains\Facturas\Infraestructure\Eloquent\Models\BillingInvoice;
use App\Domains\Proyectos\Domain\StateMachines\Project\ProjectState;
use App\Domains\Tickets\Infrastructure\Eloquent\Models\Evidence;
use App\Domains\Tickets\Infrastructure\Eloquent\Models\FsmTicket;
use App\Models\ProjectBillingMilestone;
use Database\Factories\FsmProjectFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class FsmProject extends Model
{
    use HasFactory;
    protected $fillable = [
        'quote_id',
        'client_id',
        'code',
        'name',
        'status',
        'total_budget',
        'financial_progress_percentage',
    ];

    protected static function newFactory(): FsmProjectFactory
    {
        return FsmProjectFactory::new();
    }

    protected $casts = [
        'status' => ProjectState::class,
    ];

    protected static function booted(): void
    {
        static::creating(function (FsmProject $project) {
            if (empty($project->code)) {
                $project->code = static::generateCode();
            }
        });
    }

    public function save(array $options = []): bool
    {
        if ($this->exists) {
            return parent::save($options);
        }

        return DB::transaction(fn () => parent::save($options));
    }
    public static function generateCode(): string
    {
        $prefix = 'PRJ-' . date('Y') . '-';

        $latestProject = static::query()
            ->where('code', 'LIKE', "{$prefix}%")
            ->lockForUpdate()
            ->orderByRaw('LENGTH(code) DESC')
            ->orderBy('code', 'desc')
            ->first();

        if (! $latestProject) {
            return $prefix . '0001';
        }

        $lastNumber = (int) Str::afterLast($latestProject->code, '-');

        return $prefix . str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
    }

    public function quote(): BelongsTo
    {
        return $this->belongsTo(CpqQuote::class, 'quote_id');
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(CrmClient::class, 'client_id');
    }

    public function milestones(): HasMany
    {
        return $this->hasMany(ProjectBillingMilestone::class, 'project_id');
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(FsmTicket::class, 'project_id');
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(BillingInvoice::class, 'project_id');
    }

    public function evidences(): HasMany
    {
        return $this->hasMany(Evidence::class, 'project_id');
    }
}
