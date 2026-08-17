<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\Contracts\ReceiptSettingRepositoryInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReceiptSettingController extends Controller
{
    protected ReceiptSettingRepositoryInterface $receiptSettingRepo;

    public function __construct(ReceiptSettingRepositoryInterface $receiptSettingRepo)
    {
        $this->receiptSettingRepo = $receiptSettingRepo;
    }

    /**
     * Show the receipt settings form.
     */
    public function edit(): View
    {
        $settings = $this->receiptSettingRepo->getSettingsOrNew();

        return view('admin.receipt-settings.edit', compact('settings'));
    }

    /**
     * Update the receipt settings.
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'store_name'       => ['required', 'string', 'max:100'],
            'theme_color'      => ['nullable', 'string', 'in:red,green,blue,indigo,amber,slate'],
            'store_address'    => ['nullable', 'string', 'max:255'],
            'store_phone'      => ['nullable', 'string', 'max:50'],
            'header_line_1'    => ['nullable', 'string', 'max:100'],
            'header_line_2'    => ['nullable', 'string', 'max:100'],
            'footer_line_1'    => ['nullable', 'string', 'max:100'],
            'footer_line_2'    => ['nullable', 'string', 'max:100'],
            'tax_percent'      => ['required', 'numeric', 'min:0', 'max:100'],
            'tax_enabled'      => ['sometimes', 'boolean'],
            'discount_enabled' => ['sometimes', 'boolean'],
            'is_cash_enabled'  => ['sometimes', 'boolean'],
            'is_qris_enabled'  => ['sometimes', 'boolean'],
            'is_card_enabled'  => ['sometimes', 'boolean'],
            'store_logo'       => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        // Handle logo upload
        if ($request->hasFile('store_logo')) {
            $validated['store_logo'] = $request->file('store_logo')->store('logos', 'public');
        }

        // Checkboxes tidak dikirim saat tidak dicentang — set default false
        $validated['tax_enabled']      = $request->boolean('tax_enabled');
        $validated['discount_enabled'] = $request->boolean('discount_enabled');
        $validated['is_cash_enabled']  = $request->boolean('is_cash_enabled');
        $validated['is_qris_enabled']  = $request->boolean('is_qris_enabled');
        $validated['is_card_enabled']  = $request->boolean('is_card_enabled');

        $this->receiptSettingRepo->saveSettings($validated);

        return redirect()
            ->route('admin.receipt-settings.edit')
            ->with('success', 'Pengaturan struk berhasil disimpan.');
    }
}
