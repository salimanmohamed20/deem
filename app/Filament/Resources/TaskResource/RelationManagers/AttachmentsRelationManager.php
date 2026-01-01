<?php

namespace App\Filament\Resources\TaskResource\RelationManagers;

use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Actions;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;

class AttachmentsRelationManager extends RelationManager
{
    protected static string $relationship = 'attachments';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Forms\Components\FileUpload::make('file_path')
                    ->label('File')
                    ->disk('public')
                    ->directory('task-attachments')
                    ->acceptedFileTypes(['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'image/jpeg', 'image/png', 'application/zip'])
                    ->maxSize(10240)
                    ->required()
                    ->storeFileNamesIn('file_name')
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('file_name')
            ->columns([
                Tables\Columns\TextColumn::make('file_name'),
                Tables\Columns\TextColumn::make('file_type'),
                Tables\Columns\TextColumn::make('file_size')
                    ->formatStateUsing(fn($state) => $state ? number_format($state / 1024, 2) . ' KB' : '-'),
                Tables\Columns\TextColumn::make('uploader.user.name')
                    ->label('Uploaded By'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Actions\CreateAction::make()
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['uploaded_by'] = auth()->user()->employee?->id;
                        
                        $filePath = $data['file_path'];
                        
                        // Get file metadata if file exists
                        if ($filePath && Storage::disk('public')->exists($filePath)) {
                            $data['file_size'] = Storage::disk('public')->size($filePath);
                            $data['file_type'] = Storage::disk('public')->mimeType($filePath);
                        } else {
                            $data['file_size'] = 0;
                            $data['file_type'] = 'application/octet-stream';
                        }
                        
                        return $data;
                    }),
            ])
            ->actions([
                Actions\Action::make('download')
                    ->label('Download')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->url(fn($record) => Storage::disk('public')->url($record->file_path))
                    ->openUrlInNewTab(),
                Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
