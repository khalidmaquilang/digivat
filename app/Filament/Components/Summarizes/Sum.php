<?php

declare(strict_types=1);

namespace App\Filament\Components\Summarizes;

use Filament\Tables\Columns\Summarizers\Sum as BaseSum;

class Sum extends BaseSum
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->label('Total');
        $this->money('PHP', divideBy: 100);
    }
}
