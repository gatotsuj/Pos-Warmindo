<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Receipt - {{ $transaction->invoice_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Courier New', monospace;
            font-size: 12px;
            width: 280px;
            margin: 0 auto;
            padding: 10px;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .font-bold {
            font-weight: bold;
        }

        .mb-2 {
            margin-bottom: 8px;
        }

        .mb-4 {
            margin-bottom: 16px;
        }

        .border-dashed {
            border-top: 1px dashed #000;
            padding-top: 8px;
            margin-top: 8px;
        }

        .flex {
            display: flex;
            justify-content: space-between;
        }

        .item {
            margin-bottom: 4px;
        }

        @media print {
            body {
                width: 80mm;
            }

            .no-print {
                display: none;
            }
        }
    </style>
</head>

<body>
    {{-- Header --}}
    <div class="text-center mb-4">

        {{-- Logo atau Inisial --}}
        @if (optional($receiptSettings)->logo)
            <div style="margin-bottom:8px;">
                <img src="{{ asset('storage/' . $receiptSettings->logo) }}"
                    style="max-height:60px; max-width:120px; object-fit:contain;">
            </div>
        @else
            @php
                $storeName = optional($receiptSettings)->store_name ?? 'TOKO SEJAHTERA';
                $initials = strtoupper(
                    collect(explode(' ', $storeName))->map(fn($word) => substr($word, 0, 1))->take(2)->implode(''),
                );
            @endphp

            <div
                style="
        width:60px;
        height:60px;
        line-height:60px;
        margin:0 auto 8px;
        border-radius:50%;
        border:2px solid #000;
        color:#000;
        font-weight:bold;
        font-size:20px;
        text-align:center;
        background:#fff;">
                {{ $initials }}
            </div>
        @endif

        {{-- Store Name --}}
        <div class="font-bold" style="font-size: 16px;">
            {{ $storeName ?? (optional($receiptSettings)->store_name ?? 'POS SYSTEM') }}
        </div>

        @if (optional($receiptSettings)->store_address)
            <div>{{ $receiptSettings->store_address }}</div>
        @else
            <div>Jl. Contoh No. 123</div>
        @endif

        @if (optional($receiptSettings)->store_phone)
            <div>Telp: {{ $receiptSettings->store_phone }}</div>
        @else
            <div>Telp: 021-1234567</div>
        @endif

        @if (optional($receiptSettings)->header_line_1)
            <div>{{ $receiptSettings->header_line_1 }}</div>
        @endif

        @if (optional($receiptSettings)->header_line_2)
            <div>{{ $receiptSettings->header_line_2 }}</div>
        @endif
    </div>

    <div class="border-dashed mb-2">
        <div class="flex"><span>No:</span><span>{{ $transaction->invoice_number }}</span></div>
        <div class="flex"><span>Tanggal:</span><span>{{ $transaction->created_at->format('d/m/Y H:i') }}</span></div>
        <div class="flex"><span>Kasir:</span><span>{{ $transaction->user->name }}</span></div>
    </div>

    {{-- Items --}}
    <div class="border-dashed mb-2">
        @foreach ($transaction->items as $item)
            <div class="item">
                <div>{{ $item->product_name }}</div>
                <div class="flex">
                    <span>{{ $item->quantity }} x {{ number_format($item->product_price, 0, ',', '.') }}</span>
                    <span>{{ number_format($item->subtotal, 0, ',', '.') }}</span>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Totals --}}
    <div class="border-dashed">
        <div class="flex"><span>Subtotal</span><span>{{ number_format($transaction->subtotal, 0, ',', '.') }}</span>
        </div>
        @if ($transaction->discount_amount > 0)
            <div class="flex"><span>Diskon
                    ({{ $transaction->discount_percent }}%)</span><span>-{{ number_format($transaction->discount_amount, 0, ',', '.') }}</span>
            </div>
        @endif
        <div class="flex"><span>Pajak
                ({{ $transaction->tax_percent }}%)</span><span>{{ number_format($transaction->tax_amount, 0, ',', '.') }}</span>
        </div>
        <div class="flex font-bold" style="font-size: 14px; margin-top: 8px;">
            <span>TOTAL</span><span>Rp {{ number_format($transaction->grand_total, 0, ',', '.') }}</span>
        </div>
        <div class="flex mt-2"><span>Bayar
                ({{ ucfirst($transaction->payment_method) }})</span><span>{{ number_format($transaction->paid_amount, 0, ',', '.') }}</span>
        </div>
        @if ($transaction->change_amount > 0)
            <div class="flex">
                <span>Kembali</span><span>{{ number_format($transaction->change_amount, 0, ',', '.') }}</span>
            </div>
        @endif
    </div>

    {{-- Footer --}}
    <div class="text-center border-dashed" style="margin-top: 16px;">
        <div class="mb-2">
            {{ optional($receiptSettings)->footer_line_1 ?? 'Terima Kasih' }}
        </div>
        <div style="font-size: 10px;">
            {{ optional($receiptSettings)->footer_line_2 ?? 'Barang yang sudah dibeli tidak dapat dikembalikan' }}
        </div>
    </div>

    {{-- Print Button --}}
    <div class="text-center no-print" style="margin-top: 20px;">
        <button onclick="window.print()" style="padding: 10px 20px; cursor: pointer;">🖨️ Print Receipt</button>
    </div>
</body>

</html>
