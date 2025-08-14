<?php

declare(strict_types=1);

namespace App\Filament\Client\Pages;

use App\Filament\Components\Fields\TextInput\TextInput;
use BackedEnum;
use CodeWithDennis\FilamentLucideIcons\Enums\LucideIcon;
use Features\Token\Models\Token;
use Features\User\Actions\CreateTokenAction;
use Features\User\Models\User;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use UnitEnum;

class ApiTokens extends Page implements HasTable
{
    use InteractsWithTable;

    protected string $view = 'filament.client.pages.api-tokens';

    protected static string|BackedEnum|null $navigationIcon = LucideIcon::Key;

    protected static string|UnitEnum|null $navigationGroup = 'Developers';

    protected CreateTokenAction $create_token_action;

    public function boot(CreateTokenAction $create_token_action): void
    {
        $this->create_token_action = $create_token_action;
    }

    public function table(Table $table): Table
    {
        /** @var ?User $user */
        $user = auth()->user();
        abort_if($user === null, 404);

        return $table
            ->query(Token::query())
            ->columns([
                TextColumn::make('name'),
                TextColumn::make('token')
                    ->copyable()
                    ->copyMessage('token copied')
                    ->formatStateUsing(fn (string $state): string => app()->isProduction() ? Str::mask($state, '*', 10) : $state),
            ])
            ->headerActions([
                CreateAction::make()
                    ->schema([
                        TextInput::make('name')
                            ->required(),
                    ])
                    ->action(function (array $data) use ($user): void {
                        $this->create_token_action->handle($user, $data['name']);
                    }),
            ])
            ->recordActions([
                DeleteAction::make(),
            ])
            ->toolbarActions([
                DeleteBulkAction::make(),
            ]);
    }

    public function getBreadcrumbs(): array
    {
        return [
            ApiTokens::getUrl() => 'Api Tokens',
            'List',
        ];
    }
}
