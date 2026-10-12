<?php

namespace App\Domains\Tickets\UI\Filament\Resources\Tickets\RelationManagers;

use App\Domains\Tickets\Infrastructure\Eloquent\Models\FsmTicket;
use DomainException;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Support\Exceptions\Cancel;
use Filament\Support\Exceptions\Halt;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Illuminate\Support\Arr;
use Illuminate\Validation\ValidationException;
use League\Flysystem\FilesystemException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;

class EvidencesRelationManager extends RelationManager
{
    protected static string $relationship = 'evidences';

    protected static ?string $title = 'Evidencias del Ticket';

    protected static string|null|\BackedEnum $icon = 'heroicon-o-camera';

    public function isReadOnly(): bool
    {
        return false;
    }

    public function exception(Throwable $e, callable $stopPropagation): void
    {
        if (
            $e instanceof ValidationException
            || $e instanceof Halt
            || $e instanceof Cancel
            || $e instanceof AuthorizationException
            || $e instanceof HttpExceptionInterface
        ) {
            return;
        }

        [$title, $body] = match (true) {
            $e instanceof DomainException => [
                'No se pudo guardar',
                $e->getMessage(),
            ],
            $e instanceof FilesystemException => [
                'No se pudieron leer las fotos',
                'Las fotos seleccionadas ya no están disponibles. Vuelve a tomarlas o seleccionarlas.',
            ],
            $e instanceof QueryException => [
                'No se pudo guardar la evidencia',
                'Faltan datos obligatorios o hay datos inválidos. Revisa la información e inténtalo de nuevo.',
            ],
            default => [
                'Ocurrió un error inesperado',
                'No se guardaron los cambios. Inténtalo de nuevo.',
            ],
        };

        if (! $e instanceof DomainException) {
            report($e);
        }

        try {
            $this->unmountAction();
        } catch (Throwable) {
        }

        if (config('app.debug') && ! $e instanceof DomainException) {
            $detail = $e instanceof QueryException
                ? ($e->errorInfo[2] ?? $e->getMessage())
                : $e->getMessage();

            $body .= ' | Detalle: ' . str($detail)->before('(Connection:')->trim()->limit(400);
        }

        Notification::make()
            ->danger()
            ->title($title)
            ->body($body)
            ->persistent()
            ->send();

        $stopPropagation();
    }

    protected function photoUpload(string $name): FileUpload
    {
        return FileUpload::make($name)
            ->image()
            ->disk(FsmTicket::EVIDENCE_DISK)
            ->directory(FsmTicket::EVIDENCE_DIRECTORY)
            ->visibility('public')
            ->maxSize(10240)
            ->automaticallyResizeImagesMode('contain')
            ->automaticallyResizeImagesToWidth('1920')
            ->automaticallyResizeImagesToHeight('1920')
            ->validationMessages([
                'image' => 'El archivo debe ser una imagen.',
                'max' => 'Cada foto debe pesar máximo 10 MB.',
                'required' => 'Agrega una foto.',
                'required_without' => 'Toma o selecciona al menos una foto.',
            ])
            ->columnSpanFull();
    }

    protected function cameraAndLocationAttributes(FileUpload $component): array
    {
        $locationPath = str($component->getStatePath())
            ->beforeLast('.')
            ->append('.location')
            ->replaceMatches('/[^A-Za-z0-9_.\-]/', '')
            ->toString();

        $script = <<<'JS'
            const setCapture = () => $el
                .querySelectorAll('input[type=file]')
                .forEach((input) => input.setAttribute('capture', 'environment'));
            setCapture();
            new MutationObserver(setCapture).observe($el, { childList: true, subtree: true });

            if (window.isSecureContext && navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    (pos) => $wire.set(
                        '__LOCATION_PATH__',
                        pos.coords.latitude.toFixed(6) + ', ' + pos.coords.longitude.toFixed(6),
                        false,
                    ),
                    () => {},
                    { enableHighAccuracy: true, timeout: 15000, maximumAge: 60000 },
                );
            }
        JS;

        return [
            'x-init' => str_replace('__LOCATION_PATH__', $locationPath, $script),
        ];
    }

    protected function locationHidden(): Hidden
    {
        return Hidden::make('location')
            ->dehydrateStateUsing(fn ($state) => FsmTicket::sanitizeEvidenceLocation($state));
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                $this->photoUpload('file_path')
                    ->label('Fotografía')
                    ->helperText('Toca para tomar una foto o elegirla de tu galería.')
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('file_path')
            ->emptyStateHeading('Sin evidencias')
            ->emptyStateDescription('Sube fotos con el botón "Subir Evidencias".')
            ->columns([
                ImageColumn::make('file_path')
                    ->label('Vista Previa')
                    ->disk(FsmTicket::EVIDENCE_DISK)
                    ->square()
                    ->url(fn ($record): ?string => FsmTicket::evidenceUrl($record->file_path))
                    ->openUrlInNewTab(),

                TextColumn::make('file_type')
                    ->label('Tipo')
                    ->badge()
                    ->placeholder('—')
                    ->searchable(),

                TextColumn::make('location')
                    ->label('Ubicación')
                    ->placeholder('Sin ubicación')
                    ->url(fn ($record): ?string => filled($record->location)
                        ? 'https://www.google.com/maps?q=' . urlencode($record->location)
                        : null)
                    ->openUrlInNewTab()
                    ->toggleable(),

                IconColumn::make('is_out_of_bounds')
                    ->label('Fuera de Límite')
                    ->boolean()
                    ->trueIcon('heroicon-o-exclamation-triangle')
                    ->falseIcon('heroicon-o-check-circle')
                    ->trueColor('danger')
                    ->falseColor('success'),

                TextColumn::make('created_at')
                    ->label('Subida')
                    ->dateTime('d/m/Y H:i')
                    ->placeholder('—')
                    ->sortable()
                    ->toggleable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->headerActions([
                CreateAction::make()
                    ->label('Subir Evidencias')
                    ->icon('heroicon-o-camera')
                    ->modalHeading('Subir evidencias')
                    ->modalSubmitActionLabel('Guardar')
                    ->createAnother(false)
                    ->schema([
                        $this->photoUpload('camera_photos')
                            ->label('Tomar fotos con la cámara')
                            ->placeholder('Toca aquí para abrir la cámara')
                            ->helperText('Toca para abrir la cámara. Repite para agregar más fotos. Tu ubicación se registra automáticamente.')
                            ->multiple()
                            ->maxFiles(10)
                            ->appendFiles()
                            ->panelLayout('grid')
                            ->storeFiles(false)
                            ->extraAttributes(fn (FileUpload $component): array => $this->cameraAndLocationAttributes($component))
                            ->requiredWithout('gallery_photos'),

                        $this->photoUpload('gallery_photos')
                            ->label('Elegir de la galería')
                            ->placeholder('Toca aquí para elegir fotos')
                            ->helperText('Selecciona una o varias fotos de tu celular.')
                            ->multiple()
                            ->maxFiles(10)
                            ->appendFiles()
                            ->panelLayout('grid')
                            ->storeFiles(false)
                            ->requiredWithout('camera_photos'),

                        $this->locationHidden(),
                    ])
                    ->using(function (array $data): Model {
                        $ticket = $this->getOwnerRecord();

                        $files = [
                            ...array_values(Arr::wrap($data['camera_photos'] ?? [])),
                            ...array_values(Arr::wrap($data['gallery_photos'] ?? [])),
                        ];

                        return $ticket->addEvidencesFromUploads(
                            files: $files,
                            location: $data['location'] ?? null,
                        )->last();
                    })
                    ->successNotificationTitle('Evidencias guardadas'),
            ])
            ->recordActions([
                EditAction::make()
                    ->mutateDataUsing(function (array $data): array {
                        $ticket = $this->getOwnerRecord();
                        $ticket->ensureCanReceiveEvidences();

                        $data = Arr::only($data, ['file_path']);

                        $data['client_uuid'] = $ticket->client_uuid;
                        $data['s3_key'] = $data['file_path'] ?? null;
                        $data['file_type'] = FsmTicket::detectEvidenceMimeType($data['file_path'] ?? null);

                        return $data;
                    })
                    ->after(function (Model $record): void {
                        if ($record->wasChanged('file_path')) {
                            FsmTicket::deleteEvidenceFile($record->getPrevious()['file_path'] ?? null);
                        }
                    }),

                DeleteAction::make()
                    ->after(fn (Model $record) => FsmTicket::deleteEvidenceFile($record->file_path)),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->after(fn (Collection $records) => $records->each(
                            fn (Model $record) => FsmTicket::deleteEvidenceFile($record->file_path)
                        )),
                ]),
            ]);
    }
}
