<?php

namespace App\Repositories\Contracts;

use App\Models\ReceiptSetting;

interface ReceiptSettingRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Get the first receipt settings or return a new instance with default values.
     */
    public function getSettingsOrNew(): ReceiptSetting;

    /**
     * Save/update the receipt settings.
     */
    public function saveSettings(array $data): ReceiptSetting;
}
