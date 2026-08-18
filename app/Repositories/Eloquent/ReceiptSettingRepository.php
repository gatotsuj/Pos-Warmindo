<?php

namespace App\Repositories\Eloquent;

use App\Models\ReceiptSetting;
use App\Repositories\Contracts\ReceiptSettingRepositoryInterface;
use App\Support\CurrentTenant;
use Illuminate\Support\Facades\Cache;

class ReceiptSettingRepository extends BaseRepository implements ReceiptSettingRepositoryInterface
{
    public function __construct(ReceiptSetting $model)
    {
        parent::__construct($model);
    }

    public function getSettingsOrNew(): ReceiptSetting
    {
        $tenantId = CurrentTenant::id() ?? 1;
        $cacheKey = "tenant_{$tenantId}_receipt_settings";

        return Cache::store('redis')->remember($cacheKey, 3600, function () {
            return $this->model->newQuery()->first() ?? new ReceiptSetting([
                'store_name'       => 'TOKO SEJAHTERA',
                'store_address'    => 'Jl. Contoh No. 123',
                'store_phone'      => '021-1234567',
                'footer_line_1'    => 'Terima Kasih',
                'footer_line_2'    => 'Barang yang sudah dibeli tidak dapat dikembalikan',
                'tax_percent'      => 11,
                'tax_enabled'      => true,
                'discount_enabled' => true,
                'is_cash_enabled'  => true,
                'is_qris_enabled'  => true,
                'is_card_enabled'  => true,
            ]);
        });
    }

    public function saveSettings(array $data): ReceiptSetting
    {
        $settings = $this->model->newQuery()->first();

        if (!$settings) {
            $settings = new ReceiptSetting();
        }

        $settings->fill($data);
        $settings->save();

        $tenantId = CurrentTenant::id() ?? 1;
        Cache::store('redis')->forget("tenant_{$tenantId}_receipt_settings");

        return $settings;
    }
}
