<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ReceiptSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReceiptSettingController extends Controller
{
    /**
     * Show the receipt settings form.
     */
    public function edit(): View
    {
        $settings = ReceiptSetting::first() ?? new ReceiptSetting([
            'store_name'       => 'TOKO SEJAHTERA',
            'store_address'    => 'Jl. Contoh No. 123',
            'store_phone'      => '021-1234567',
            'footer_line_1'    => 'Terima Kasih',
            'footer_line_2'    => 'Barang yang sudah dibeli tidak dapat dikembalikan',
            'tax_percent'      => 11,
            'tax_enabled'      => true,
            'discount_enabled' => true,
        ]);

        return view('admin.receipt-settings.edit', compact('settings'));
    }

    /**
     * Update the receipt settings.
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'store_name'       => ['required', 'string', 'max:100'],
            'store_address'    => ['nullable', 'string', 'max:255'],
            'store_phone'      => ['nullable', 'string', 'max:50'],
            'header_line_1'    => ['nullable', 'string', 'max:100'],
            'header_line_2'    => ['nullable', 'string', 'max:100'],
            'footer_line_1'    => ['nullable', 'string', 'max:100'],
            'footer_line_2'    => ['nullable', 'string', 'max:100'],
            'tax_percent'      => ['required', 'numeric', 'min:0', 'max:100'],
            'tax_enabled'      => ['sometimes', 'boolean'],
            'discount_enabled' => ['sometimes', 'boolean'],
        ]);

        // Checkboxes tidak dikirim saat tidak dicentang — set default false
        $validated['tax_enabled']      = $request->boolean('tax_enabled');
        $validated['discount_enabled'] = $request->boolean('discount_enabled');

        $settings = ReceiptSetting::first();

        if (! $settings) {
            $settings = new ReceiptSetting();
        }

        $settings->fill($validated);
        $settings->save();

        return redirect()
            ->route('admin.receipt-settings.edit')
            ->with('success', 'Pengaturan struk berhasil disimpan.');
    }
}
