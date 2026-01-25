<?php

namespace App\Filament\Resources\TaskResource\RelationManagers;

use Filament\Actions;
use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables;
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
                    ->acceptedFileTypes([
                        'application/pdf',
                        'application/msword',
                        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                        'application/vnd.ms-excel',
                        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                        'image/jpeg',
                        'image/png',
                        'image/gif',
                        'image/webp',
                        'application/zip',
                        'text/plain',
                    ])
             
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
                Tables\Columns\ViewColumn::make('preview')
                    ->label('')
                    ->view('filament.tables.columns.attachment-preview')
                    ->width('60px'),
                Tables\Columns\TextColumn::make('file_name')
                    ->label('File Name')
                    ->weight('bold')
                    ->searchable()
                    ->limit(40)
                    ->tooltip(fn ($record) => $record->file_name)
                    ->width('300px'),
                Tables\Columns\TextColumn::make('file_size')
                    ->label('Size')
                    ->formatStateUsing(fn ($state) => $this->formatFileSize($state))
                    ->color('gray')
                    ->width('100px'),
                Tables\Columns\TextColumn::make('uploader.user.name')
                    ->label('Uploaded By')
                    ->color('gray')
                    ->width('150px'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Date')
                    ->since()
                    ->color('gray')
                    ->width('120px'),
            ])
            ->filters([])
            ->headerActions([
                Actions\CreateAction::make()
                    ->label('Upload File')
                    ->icon('heroicon-o-arrow-up-tray')
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['uploaded_by'] = auth()->user()->employee?->id;
                        
                        $filePath = $data['file_path'];
                        
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
                Actions\Action::make('preview')
                    ->label('Preview')
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    ->url(fn ($record) => $record ? route('attachments.task.preview', $record) : null)
                    ->openUrlInNewTab()
                    ->visible(fn ($record) => $record && $this->isPreviewable($record->file_type)),
                Actions\Action::make('download')
                    ->label('Download')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->url(fn ($record) => $record ? route('attachments.task.download', $record) : null)
                    ->openUrlInNewTab(),
                Actions\DeleteAction::make()
                    ->icon('heroicon-o-trash'),
            ])
            ->bulkActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('No attachments')
            ->emptyStateDescription('Upload files to attach them to this task.')
            ->emptyStateIcon('heroicon-o-paper-clip');
    }

    protected function isImage(?string $mimeType): bool
    {
        if (!$mimeType) return false;
        return str_starts_with($mimeType, 'image/');
    }

    protected function isPreviewable(?string $mimeType): bool
    {
        if (!$mimeType) return false;
        
        $previewableTypes = [
            'image/jpeg', 'image/png', 'image/gif', 'image/webp',
            'application/pdf',
        ];
        
        return in_array($mimeType, $previewableTypes);
    }

    protected function getFileIcon(?string $mimeType): string
    {
        if (!$mimeType) return 'heroicon-o-document';
        
        return match (true) {
            str_starts_with($mimeType, 'image/') => 'heroicon-o-photo',
            str_contains($mimeType, 'pdf') => 'heroicon-o-document-text',
            str_contains($mimeType, 'word') => 'heroicon-o-document-text',
            str_contains($mimeType, 'excel') || str_contains($mimeType, 'spreadsheet') => 'heroicon-o-table-cells',
            str_contains($mimeType, 'zip') => 'heroicon-o-archive-box',
            str_contains($mimeType, 'text') => 'heroicon-o-document',
            default => 'heroicon-o-document',
        };
    }

    protected function formatFileSize(?int $bytes): string
    {
        if (!$bytes) return '0 B';
        
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;
        
        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }
}
