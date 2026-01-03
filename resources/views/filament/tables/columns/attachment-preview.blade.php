@php
    $record = $getRecord();
    $fileType = $record->file_type ?? '';
    $filePath = $record->file_path ?? '';
    $isImage = str_starts_with($fileType, 'image/');
    
    // Generate the correct URL for the image
    $imageUrl = $filePath ? asset('storage/' . $filePath) : '';
@endphp

<div class="w-12 h-12 rounded-lg overflow-hidden flex items-center justify-center flex-shrink-0
    @if(!$isImage)
        @if(str_contains($fileType, 'pdf'))
            bg-red-100 dark:bg-red-900/30
        @elseif(str_contains($fileType, 'word'))
            bg-blue-100 dark:bg-blue-900/30
        @elseif(str_contains($fileType, 'excel') || str_contains($fileType, 'spreadsheet'))
            bg-green-100 dark:bg-green-900/30
        @elseif(str_contains($fileType, 'zip'))
            bg-yellow-100 dark:bg-yellow-900/30
        @else
            bg-gray-100 dark:bg-gray-700
        @endif
    @else
        bg-gray-200 dark:bg-gray-700
    @endif
">
    @if($isImage && $filePath)
        <img 
            src="{{ $imageUrl }}" 
            alt="{{ $record->file_name }}"
            class="w-full h-full object-cover"
            onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
        >
        <div class="w-full h-full items-center justify-center bg-gray-100 dark:bg-gray-700" style="display: none;">
            <x-heroicon-o-photo class="w-5 h-5 text-gray-400" />
        </div>
    @elseif(str_contains($fileType, 'pdf'))
        <x-heroicon-o-document-text class="w-5 h-5 text-red-600 dark:text-red-400" />
    @elseif(str_contains($fileType, 'word'))
        <x-heroicon-o-document-text class="w-5 h-5 text-blue-600 dark:text-blue-400" />
    @elseif(str_contains($fileType, 'excel') || str_contains($fileType, 'spreadsheet'))
        <x-heroicon-o-table-cells class="w-5 h-5 text-green-600 dark:text-green-400" />
    @elseif(str_contains($fileType, 'zip'))
        <x-heroicon-o-archive-box class="w-5 h-5 text-yellow-600 dark:text-yellow-400" />
    @else
        <x-heroicon-o-document class="w-5 h-5 text-gray-600 dark:text-gray-400" />
    @endif
</div>
