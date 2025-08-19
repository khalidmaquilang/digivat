<?php

declare(strict_types=1);

namespace App\Filament\Client\Pages;

use App\Features\Business\Models\Business;
use App\Features\Token\Actions\CreateTokenAction;
use App\Features\Token\Models\Token;
use App\Filament\Components\Fields\TextInput\TextInput;
use BackedEnum;
use CodeWithDennis\FilamentLucideIcons\Enums\LucideIcon;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Facades\Filament;
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
        return $table
            ->query(Token::query())
            ->columns([
                TextColumn::make('name'),
                TextColumn::make('token')
                    ->copyable()
                    ->copyableState(fn (string $state): string => $state)
                    ->copyMessage('token copied')
                    ->formatStateUsing(fn (string $state): string => app()->isProduction() ? Str::mask($state, '*', 10) : $state),
            ])
            ->headerActions([
                CreateAction::make()
                    ->schema([
                        TextInput::make('name')
                            ->required(),
                    ])
                    ->action(function (array $data): void {
                        /** @var ?Business $business */
                        $business = Filament::getTenant();
                        abort_if($business === null, 403);

                        $this->create_token_action->handle($business, $data['name']);
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
