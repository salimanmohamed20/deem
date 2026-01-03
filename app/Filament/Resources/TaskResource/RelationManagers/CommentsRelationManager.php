<?php

namespace App\Filament\Resources\TaskResource\RelationManagers;

use App\Models\CommentAttachment;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;

class CommentsRelationManager extends RelationManager
{
    protected static string $relationship = 'comments';
    
    public array $pendingAttachments = [];

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Forms\Components\RichEditor::make('comment')
                    ->required()
                    ->maxLength(5000)
                    ->placeholder('Write your comment here...')
                    ->columnSpanFull(),
                Forms\Components\FileUpload::make('attachments_upload')
                    ->label('Attachments')
                    ->multiple()
                    ->disk('public')
                    ->directory('comment-attachments')
                    ->preserveFilenames()
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
                    ->maxSize(10240)
                    ->maxFiles(5)
                    ->columnSpanFull()
                    ->helperText('Max 5 files, 10MB each'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\Layout\Split::make([
                    Tables\Columns\Layout\Stack::make([
                        Tables\Columns\TextColumn::make('author.user.name')
                            ->label('Author')
                            ->weight('bold')
                            ->icon('heroicon-o-user-circle'),
                        Tables\Columns\TextColumn::make('created_at')
                            ->since()
                            ->color('gray')
                            ->size('sm'),
                    ])->space(1)->grow(false),
                    Tables\Columns\Layout\Stack::make([
                        Tables\Columns\TextColumn::make('comment')
                            ->html()
                            ->limit(150)
                            ->wrap(),
                        Tables\Columns\TextColumn::make('attachments_count')
                            ->counts('attachments')
                            ->label('Attachments')
                            ->badge()
                            ->color('info')
                            ->icon('heroicon-o-paper-clip')
                            ->visible(fn ($state) => $state > 0),
                    ])->space(2),
                ]),
            ])
            ->filters([])
            ->headerActions([
                Actions\CreateAction::make()
                    ->label('Add Comment')
                    ->icon('heroicon-o-chat-bubble-left-ellipsis')
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['employee_id'] = auth()->user()->employee?->id;
                        // Store attachments temporarily, remove from data to prevent DB error
                        $this->pendingAttachments = $data['attachments_upload'] ?? [];
                        unset($data['attachments_upload']);
                        return $data;
                    })
                    ->after(function ($record) {
                        // Get uploaded files stored in mutateFormDataUsing
                        $uploadedFiles = $this->pendingAttachments ?? [];
                        
                        if (!empty($uploadedFiles)) {
                            foreach ($uploadedFiles as $filePath) {
                                $fileName = basename($filePath);
                                
                                // The file path from FileUpload already includes the directory
                                $fileSize = Storage::disk('public')->exists($filePath) 
                                    ? Storage::disk('public')->size($filePath) 
                                    : 0;
                                $fileType = Storage::disk('public')->exists($filePath)
                                    ? Storage::disk('public')->mimeType($filePath)
                                    : 'application/octet-stream';
                                
                                CommentAttachment::create([
                                    'task_comment_id' => $record->id,
                                    'file_path' => $filePath,
                                    'file_name' => $fileName,
                                    'file_size' => $fileSize,
                                    'file_type' => $fileType,
                                ]);
                            }
                        }
                        
                        $this->pendingAttachments = [];
                    }),
            ])
            ->actions([
                Actions\Action::make('view')
                    ->label('View')
                    ->icon('heroicon-o-eye')
                    ->modalHeading(fn ($record) => 'Comment by ' . ($record->author?->user?->name ?? 'Unknown'))
                    ->modalContent(fn ($record) => view('filament.resources.task-resource.comment-modal', [
                        'comment' => $record,
                        'attachments' => $record->attachments,
                    ]))
                    ->modalFooterActions([])
                    ->modalSubmitAction(false)
                    ->modalCancelAction(false),
                Actions\EditAction::make()
                    ->icon('heroicon-o-pencil')
                    ->visible(fn ($record) => $record->employee_id === auth()->user()->employee?->id),
                Actions\DeleteAction::make()
                    ->icon('heroicon-o-trash')
                    ->visible(fn ($record) => $record->employee_id === auth()->user()->employee?->id),
            ])
            ->bulkActions([])
            ->emptyStateHeading('No comments yet')
            ->emptyStateDescription('Be the first to add a comment to this task.')
            ->emptyStateIcon('heroicon-o-chat-bubble-left-right');
    }
}
