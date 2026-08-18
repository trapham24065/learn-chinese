<?php

namespace App\Filament\Resources\Flashcards\Pages;

use App\Filament\Resources\Flashcards\FlashcardResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditFlashcard extends EditRecord
{
    protected static string $resource = FlashcardResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()->label('Xóa flashcard này'),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
