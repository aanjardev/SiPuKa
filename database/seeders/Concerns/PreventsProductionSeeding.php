<?php

namespace Database\Seeders\Concerns;

trait PreventsProductionSeeding
{
    protected function guardAgainstProduction(): void
    {
        if (app()->environment('production') && !config('seeders.allow_destructive_in_production')) {
            throw new \RuntimeException(
                'Seeder destruktif diblokir di production. Set ALLOW_PRODUCTION_SEEDING=true hanya untuk eksekusi yang disengaja.'
            );
        }
    }
}
