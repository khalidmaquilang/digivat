<?php

declare(strict_types=1);

namespace App\Features\Shared\Models\Traits;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Support\Str;

trait HasUuidsTrait
{
    use HasUuids;

    public function newUniqueId(): string
    {
        return $this->getPrefixId().Str::uuid7()->toString();
    }

    protected function getPrefixId(): ?string
    {
        return null;
    }

    protected function isValidUniqueId(mixed $value): bool
    {
        if (! is_string($value)) {
            return false;
        }

        $stringValue = $value;
        if (str_contains($stringValue, '-')) {
            $parts = explode('-', $stringValue, 2);

            return Str::isUuid($parts[1]);
        }

        return Str::isUuid($stringValue);
    }
}
