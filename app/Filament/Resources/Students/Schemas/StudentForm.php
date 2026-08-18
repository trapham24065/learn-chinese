<?php

namespace App\Filament\Resources\Students\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Validation\Rules\Password;

class StudentForm
{
    public static function make(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')
                ->label('Họ và tên')
                ->required()
                ->maxLength(255),

            TextInput::make('email')
                ->label('Email')
                ->email()
                ->required()
                ->maxLength(255)
                ->unique(ignoreRecord: true),

            TextInput::make('password')
                ->label('Mật khẩu')
                ->password()
                ->revealable()
                ->dehydrateStateUsing(fn (string $state): string => bcrypt($state))
                ->dehydrated(fn (?string $state): bool => filled($state))
                ->required(fn (string $operation): bool => $operation === 'create')
                ->rule(Password::default())
                ->helperText('Để trống nếu không muốn thay đổi mật khẩu (khi chỉnh sửa).'),
        ]);
    }
}
