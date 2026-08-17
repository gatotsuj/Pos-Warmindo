<?php

namespace App\Repositories\Eloquent;

use App\Models\ReceiptSetting;
use App\Repositories\Contracts\ReceiptSettingRepositoryInterface;

class ReceiptSettingRepository extends BaseRepository implements ReceiptSettingRepositoryInterface
{
    public function __construct(ReceiptSetting $model)
    {
        parent::__construct($model);
    }

    public function getSettingsOrNew(): ReceiptSetting
    {
        return $this->model->newQuery()->first() ?? new ReceiptSetting([
            'store_name'       => 'TOKO SEJAHTERA',
            'store_address'    => 'Jl. Contoh No. 123',
            'store_phone'      => '021-1234567',
            'footer_line_1'    => 'Terima Kasih',
            'footer_line_2'    => 'Barang yang sudah dibeli tidak dapat dikembalikan',
            'tax_percent'      => 11,
            'tax_enabled'      => true,
            'discount_enabled' => true,
        ]);
    }

    public function saveSettings(array $data): ReceiptSetting
    {
        $settings = $this->model->newQuery()->first();

        if (!$settings) {
            $settings = new ReceiptSetting();
        }

        $settings->fill($data);
        $settings->save();

        return $settings;
    }
}
