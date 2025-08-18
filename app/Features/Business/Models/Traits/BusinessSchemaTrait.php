<?php

declare(strict_types=1);

namespace App\Features\Business\Models\Traits;

use App\Filament\Components\Fields\TextInput\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Utilities\Set;
use Illuminate\Support\Str;

trait BusinessSchemaTrait
{
    /**
     * @return array<\Filament\Schemas\Components\Component>
     */
    public static function schema(bool $disable_tin = false): array
    {
        return [
            TextInput::make('name')
                ->required()
                ->lazy()
                ->afterStateUpdated(fn (string $state, Set $set): mixed => $set('slug', Str::slug($state))),
            TextInput::make('slug')
                ->required()
                ->unique(ignoreRecord: true)
                ->alphaDash(),
            TextInput::make('tin_number')
                ->required()
                ->disabled($disable_tin)
                ->unique(),
            FileUpload::make('logo')
                ->image(),
            Select::make('creativeDomains')
                ->multiple()
                ->relationship(titleAttribute: 'name')
                ->required()
                ->preload(),
        ];
    }
}
