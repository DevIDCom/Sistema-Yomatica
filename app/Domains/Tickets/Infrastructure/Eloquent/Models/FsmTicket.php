<?php

namespace App\Domains\Tickets\Infrastructure\Eloquent\Models;

use App\Domains\Clientes\Infraestructure\Eloquent\Models\CrmClient;
use App\Domains\Clientes\Infraestructure\Eloquent\Models\CrmContact;
use App\Domains\Proyectos\Infraestructure\Eloquent\Models\FsmProject;
use App\Domains\Sucursales\Infraestructure\Eloquent\Models\CrmArea;
use App\Domains\Tickets\Domain\Observers\TicketObserver;
use App\Domains\Tickets\Domain\StateMachines\Ticket\TicketState;
use App\Domains\Users\Models\User;
use App\Models\FsmTicketChecklist;
use App\Models\FsmTicketMaterial;
use Database\Factories\FsmTicketFactory;
use DomainException;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Spatie\ModelStates\HasStates;
use Throwable;

#[ObservedBy(TicketObserver::class)]
class FsmTicket extends Model
{
    use HasStates, SoftDeletes, HasFactory;

    protected static function newFactory(): FsmTicketFactory
    {
        return FsmTicketFactory::new();
    }
    public const EVIDENCE_DISK = 'public';

    public const EVIDENCE_DIRECTORY = 'ticket-evidences';

    protected static function booted(): void
    {
        static::creating(function (self $ticket) {
            $ticket->code ??= 'TCK-' . now()->format('Y') . '-' . str_pad(
                    (string) (static::withTrashed()->whereYear('created_at', now()->year)->count() + 1),
                    5, '0', STR_PAD_LEFT
                );
        });

        static::forceDeleting(function (self $ticket) {
            $ticket->evidences()
                ->pluck('file_path')
                ->each(fn (?string $path) => static::deleteEvidenceFile($path));
        });
    }

    protected $fillable = [
        'code', 'client_uuid', 'project_id', 'area_id', 'contact_id',
        'assigned_tech_id', 'created_by', 'title', 'description',
        'status', 'priority', 'is_geofence_valid',
        'economic_weight', 'scheduled_at', 'due_at', 'check_in_at',
        'check_out_at', 'started_at', 'started_by',
        'finished_at', 'finished_by', 'resolution',
    ];

    protected $casts = [
        'status'            => TicketState::class,
        'is_geofence_valid' => 'boolean',
        'economic_weight'   => 'decimal:2',
        'scheduled_at'      => 'datetime',
        'due_at'            => 'datetime',
        'check_in_at'       => 'datetime',
        'check_out_at'      => 'datetime',
        'started_at'        => 'datetime',
        'finished_at'       => 'datetime',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(CrmClient::class, 'client_uuid', 'uuid');
    }

    public function contact(): BelongsTo
    {
        return $this->belongsTo(CrmContact::class, 'contact_id');
    }

    public function technician(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_tech_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(FsmProject::class, 'project_id');
    }

    public function area(): BelongsTo
    {
        return $this->belongsTo(CrmArea::class, 'area_id');
    }

    public function checklist(): HasOne
    {
        return $this->hasOne(FsmTicketChecklist::class, 'ticket_id');
    }

    public function materials(): HasMany
    {
        return $this->hasMany(FsmTicketMaterial::class, 'ticket_id');
    }

    public function evidences(): HasMany
    {
        return $this->hasMany(Evidence::class, 'ticket_id');
    }

    public function starter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'started_by');
    }

    public function finisher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'finished_by');
    }

    public function addEvidencesFromUploads(iterable $files, ?string $location = null, ?bool $isOutOfBounds = null): Collection
    {
        $this->ensureCanReceiveEvidences();

        $paths = [];

        try {
            foreach ($files as $file) {
                if (! $file instanceof UploadedFile) {
                    continue;
                }

                try {
                    if (method_exists($file, 'exists') && ! $file->exists()) {
                        continue;
                    }
                } catch (Throwable) {
                    continue;
                }

                $path = $file->storePublicly(static::EVIDENCE_DIRECTORY, ['disk' => static::EVIDENCE_DISK]);

                if (is_string($path) && $path !== '') {
                    $paths[] = $path;
                }
            }
        } catch (Throwable $e) {
            foreach ($paths as $path) {
                static::deleteEvidenceFile($path);
            }

            throw $e;
        }

        if ($paths === []) {
            throw new DomainException('No se pudo leer ninguna de las fotos. Vuelve a tomarlas o seleccionarlas e inténtalo de nuevo.');
        }

        return $this->addEvidences($paths, $location, $isOutOfBounds);
    }

    public function addEvidences(array $paths, ?string $location = null, ?bool $isOutOfBounds = null): Collection
    {
        $paths = array_values(array_filter($paths, fn ($path) => is_string($path) && trim($path) !== ''));

        try {
            $this->ensureCanReceiveEvidences();

            if ($paths === []) {
                throw new DomainException('Agrega al menos una foto.');
            }

            $location = static::sanitizeEvidenceLocation($location);

            $isOutOfBounds ??= $this->isLocationOutsideGeofence($location);

            return DB::transaction(fn () => collect($paths)
                ->map(fn (string $path) => $this->evidences()->create([
                    'client_uuid'      => $this->client_uuid,
                    'file_path'        => $path,
                    's3_key'           => $path,
                    'file_type'        => static::detectEvidenceMimeType($path),
                    'location'         => $location,
                    'is_out_of_bounds' => $isOutOfBounds,
                ])));
        } catch (Throwable $e) {
            foreach ($paths as $path) {
                static::deleteEvidenceFile($path);
            }

            throw $e;
        }
    }

    public function ensureCanReceiveEvidences(): void
    {
        if (! $this->exists) {
            throw new DomainException('Guarda el ticket antes de agregar evidencias.');
        }

        if (blank($this->client_uuid)) {
            throw new DomainException('Este ticket no tiene un cliente asignado. Asígnale uno antes de subir evidencias.');
        }
    }

    public static function sanitizeEvidenceLocation(mixed $location): ?string
    {
        if (! is_scalar($location)) {
            return null;
        }

        $point = static::parseEvidenceCoordinates(trim((string) $location));

        return $point
            ? sprintf('%.6F, %.6F', $point['lat'], $point['lng'])
            : null;
    }

    public static function parseEvidenceCoordinates(?string $location): ?array
    {
        if (blank($location)) {
            return null;
        }

        $parts = array_map('trim', explode(',', $location));

        if (count($parts) !== 2 || ! is_numeric($parts[0]) || ! is_numeric($parts[1])) {
            return null;
        }

        [$lat, $lng] = [(float) $parts[0], (float) $parts[1]];

        if ($lat < -90 || $lat > 90 || $lng < -180 || $lng > 180) {
            return null;
        }

        return ['lat' => $lat, 'lng' => $lng];
    }

    public function isLocationOutsideGeofence(?string $location): bool
    {
        try {
            $point = static::parseEvidenceCoordinates($location);
            $area = $this->area;

            if (! $point || ! $area) {
                return false;
            }

            $centerLat = $area->latitude ?? null;
            $centerLng = $area->longitude ?? null;
            $radius = $area->geofence_radius ?? null;

            if (! is_numeric($centerLat) || ! is_numeric($centerLng) || ! is_numeric($radius) || $radius <= 0) {
                return false;
            }

            return static::distanceInMeters($point['lat'], $point['lng'], (float) $centerLat, (float) $centerLng) > (float) $radius;
        } catch (Throwable $e) {
            report($e);

            return false;
        }
    }

    public static function distanceInMeters(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $earthRadius = 6371000;

        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);

        $a = sin($dLat / 2) ** 2
            + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLng / 2) ** 2;

        return $earthRadius * 2 * atan2(sqrt($a), sqrt(1 - $a));
    }

    public static function detectEvidenceMimeType(?string $path): ?string
    {
        if (blank($path)) {
            return null;
        }

        try {
            $disk = Storage::disk(static::EVIDENCE_DISK);

            return $disk->exists($path) ? (mb_substr((string) $disk->mimeType($path), 0, 50) ?: null) : null;
        } catch (Throwable) {
            return null;
        }
    }

    public static function deleteEvidenceFile(?string $path): void
    {
        if (! is_string($path) || blank($path)) {
            return;
        }

        try {
            Storage::disk(static::EVIDENCE_DISK)->delete($path);
        } catch (Throwable $e) {
            report($e);
        }
    }

    public static function evidenceUrl(?string $path): ?string
    {
        return filled($path) ? Storage::disk(static::EVIDENCE_DISK)->url($path) : null;
    }
}
