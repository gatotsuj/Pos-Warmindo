@extends('layouts.app')

@section('title', 'Laporan')

@section('content')
<h2 class="text-2xl font-bold mb-6">Dashboard Laporan</h2>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    {{-- Hari Ini --}}
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="font-semibold mb-4">Hari Ini</h3>

        <p class="text-sm text-gray-500">Revenue</p>
        <p class="text-xl font-bold mb-2">
            Rp {{ number_format($today['revenue'], 0, ',', '.') }}
        </p>

        <p class="text-sm text-gray-600">
            {{ $today['transactions'] }} transaksi • {{ $today['items'] }} item
        </p>

        <a href="{{ route('admin.reports.daily') }}"
           class="inline-block mt-4 px-4 py-2 bg-blue-600 text-white rounded-lg">
            Lihat Laporan Harian
        </a>
    </div>

    {{-- Bulan Ini --}}
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="font-semibold mb-4">Bulan Ini</h3>

        <p class="text-sm text-gray-500">Revenue</p>
        <p class="text-xl font-bold mb-2">
            Rp {{ number_format($month['revenue'], 0, ',', '.') }}
        </p>

        <p class="text-sm text-gray-600">
            {{ $month['transactions'] }} transaksi • {{ $month['items'] }} item
        </p>

        <a href="{{ route('admin.reports.monthly') }}"
           class="inline-block mt-4 px-4 py-2 bg-green-600 text-white rounded-lg">
            Lihat Laporan Bulanan
        </a>
    </div>
</div>
@endsection
