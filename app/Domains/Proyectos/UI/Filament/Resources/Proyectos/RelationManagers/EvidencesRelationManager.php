<?php

namespace App\Domains\Proyectos\UI\Filament\Resources\Proyectos\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class EvidencesRelationManager extends RelationManager
{
    protected static string $relationship = 'evidences';

    protected static ?string $title = 'Evidencias';

    protected static ?string $recordTitleAttribute = 'file_path';

    protected function projectIsDraft(): bool
    {
        return (string) $this->getOwnerRecord()->status === 'draft';
    }

    public function getTableDescription(): ?string
    {
        return $this->projectIsDraft()
            ? 'El proyecto está en borrador. Actívalo para registrar evidencias.'
            : null;
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([

                TextInput::make('file_path')
                    ->label('Ruta del archivo')
                    ->placeholder('Ej: evidencias/2026/proyecto-12/antes.jpg')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),

                TextInput::make('s3_key')
                    ->label('Llave S3')
                    ->placeholder('Ej: fsm/evidences/8f3a91c2-antes.jpg')
                    ->maxLength(255)
                    ->columnSpanFull(),

                Select::make('file_type')
                    ->label('Tipo de archivo')
                    ->options([
                        'image' => 'Imagen',
                        'pdf'   => 'PDF',
                    ])
                    ->default('image')
                    ->native(false),

                TextInput::make('location')
                    ->label('Ubicación GPS')
                    ->placeholder('Ej: 19.432608, -99.133209')
                    ->maxLength(255),

                Select::make('ticket_id')
                    ->label('Ticket relacionado')
                    ->relationship(
                        name: 'ticket',
                        titleAttribute: 'title',
                        modifyQueryUsing: fn ($query) => $query->where('project_id', $this->getOwnerRecord()->getKey()),
                    )
                    ->searchable()
                    ->preload()
                    ->native(false)
                    ->placeholder('Sin ticket'),

                Select::make('invoice_id')
                    ->label('Factura relacionada')
                    ->relationship(
                        name: 'invoice',
                        titleAttribute: 'uuid',
                        modifyQueryUsing: fn ($query) => $query->where('project_id', $this->getOwnerRecord()->getKey()),
                    )
                    ->searchable()
                    ->preload()
                    ->native(false)
                    ->placeholder('Sin factura'),

                Toggle::make('is_out_of_bounds')
                    ->label('Fuera de geocerca')
                    ->helperText('Se marca cuando la evidencia se capturó fuera del perímetro autorizado.')
                    ->columnSpanFull(),
            ])
            ->columns(2);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('file_path')
            ->columns([
                TextColumn::make('ticket.technician.name')
                    ->label('Técnico')
                    ->placeholder('Sin ticket')
                    ->searchable(),
                TextColumn::make('file_path')
                    ->label('Archivo')
                    ->searchable()
                    ->limit(45)
                    ->tooltip(fn ($state) => $state)
                    ->fontFamily('mono'),

                TextColumn::make('file_type')
                    ->label('Tipo')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'image' => 'success',
                        'video' => 'info',
                        'pdf'   => 'danger',
                        default => 'gray',
                    }),

                TextColumn::make('ticket.title')
                    ->label('Ticket')
                    ->placeholder('Sin ticket')
                    ->limit(30)
                    ->wrap(),

                TextColumn::make('invoice.uuid')
                    ->label('Factura')
                    ->placeholder('Sin factura')
                    ->limit(13)
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('location')
                    ->label('Ubicación')
                    ->placeholder('Sin GPS')
                    ->copyable()
                    ->toggleable(isToggledHiddenByDefault: true),

                IconColumn::make('is_out_of_bounds')
                    ->label('Fuera de geocerca')
                    ->boolean()
                    ->trueIcon('heroicon-o-exclamation-triangle')
                    ->trueColor('danger')
                    ->falseIcon('heroicon-o-check-circle')
                    ->falseColor('success'),
            ])
            ->filters([
                TernaryFilter::make('is_out_of_bounds')
                    ->label('Fuera de geocerca')
                    ->placeholder('Todas')
                    ->trueLabel('Solo fuera de geocerca')
                    ->falseLabel('Solo dentro de geocerca'),
            ])
            ->headerActions([
                //
            ])
            ->recordActions([
              //
            ])
            ->recordActions([
                ViewAction::make(),
            ])
            ->toolbarActions([
              //
            ])
            ->emptyStateHeading('Sin evidencias')
            ->emptyStateDescription('Las fotos y documentos capturados en campo aparecerán aquí.');
    }
}
