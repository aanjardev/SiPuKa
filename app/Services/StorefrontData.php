<?php

namespace App\Services;

use App\Models\Branch;
use App\Models\CatalogSettings;

class StorefrontData
{
    private bool $settingsLoaded = false;

    private bool $branchLoaded = false;

    private ?CatalogSettings $settings = null;

    private ?Branch $branch = null;

    public function settings(): ?CatalogSettings
    {
        if (! $this->settingsLoaded) {
            $this->settings = CatalogSettings::first();
            $this->settingsLoaded = true;
        }

        return $this->settings;
    }

    public function branch(): ?Branch
    {
        if (! $this->branchLoaded) {
            $this->branch = Branch::with('jamOperasional')->where('is_active', true)->first();
            $this->branchLoaded = true;
        }

        return $this->branch;
    }
}
