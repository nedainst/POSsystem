@extends('layouts.app')

@section('title', 'Kustomisasi')
@section('page-title', 'Kustomisasi Website')
@section('page-description', 'Sesuaikan tampilan dan pengaturan toko Anda')

@section('content')
<form method="POST" action="{{ route('settings.update') }}" enctype="multipart/form-data">
    @csrf @method('PUT')

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column -->
        <div class="lg:col-span-2 space-y-6">
            <!-- General Settings -->
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                <h3 class="text-sm font-bold text-gray-800 mb-4 flex items-center gap-2">
                    <i data-lucide="store" class="w-4 h-4 text-primary-500"></i> Informasi Toko
                </h3>
                <div class="space-y-4">
                    @foreach($groups['general'] as $setting)
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ $setting->label }}</label>
                            @if($setting->type === 'text')
                                <input type="text" name="{{ $setting->key }}" value="{{ $setting->value }}"
                                       class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm outline-none focus:ring-2 focus:ring-primary-500">
                            @elseif($setting->type === 'textarea')
                                <textarea name="{{ $setting->key }}" rows="3"
                                          class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm outline-none focus:ring-2 focus:ring-primary-500 resize-none">{{ $setting->value }}</textarea>
                            @elseif($setting->type === 'image')
                                <input type="file" name="{{ $setting->key }}" accept="image/*"
                                       class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-medium file:bg-primary-50 file:text-primary-600">
                                @if($setting->value)
                                    <img src="{{ Storage::url($setting->value) }}" class="mt-2 w-20 h-20 rounded-xl object-cover">
                                @endif
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Appearance Settings -->
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                <h3 class="text-sm font-bold text-gray-800 mb-4 flex items-center gap-2">
                    <i data-lucide="palette" class="w-4 h-4 text-primary-500"></i> Tampilan & Warna
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @foreach($groups['appearance'] as $setting)
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ $setting->label }}</label>
                            @if($setting->type === 'color')
                                <div class="flex items-center gap-3">
                                    <input type="color" name="{{ $setting->key }}" value="{{ $setting->value }}"
                                           class="w-10 h-10 rounded-lg border border-gray-200 cursor-pointer p-0.5">
                                    <input type="text" value="{{ $setting->value }}"
                                           class="flex-1 px-4 py-2.5 border border-gray-200 rounded-xl text-sm bg-gray-50 outline-none" readonly>
                                </div>
                            @elseif($setting->type === 'boolean')
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="checkbox" name="{{ $setting->key }}" value="1"
                                           {{ $setting->value ? 'checked' : '' }}
                                           class="w-4 h-4 rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                                    <span class="text-sm text-gray-600">Aktifkan</span>
                                </label>
                            @else
                                <input type="text" name="{{ $setting->key }}" value="{{ $setting->value }}"
                                       class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm outline-none focus:ring-2 focus:ring-primary-500">
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- POS Settings -->
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                <h3 class="text-sm font-bold text-gray-800 mb-4 flex items-center gap-2">
                    <i data-lucide="monitor" class="w-4 h-4 text-primary-500"></i> Pengaturan Kasir
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @foreach($groups['pos'] as $setting)
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ $setting->label }}</label>
                            @if($setting->type === 'boolean')
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="checkbox" name="{{ $setting->key }}" value="1"
                                           {{ $setting->value ? 'checked' : '' }}
                                           class="w-4 h-4 rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                                    <span class="text-sm text-gray-600">Aktifkan</span>
                                </label>
                            @else
                                <input type="text" name="{{ $setting->key }}" value="{{ $setting->value }}"
                                       class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm outline-none focus:ring-2 focus:ring-primary-500">
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Receipt Settings -->
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                <h3 class="text-sm font-bold text-gray-800 mb-4 flex items-center gap-2">
                    <i data-lucide="receipt" class="w-4 h-4 text-primary-500"></i> Pengaturan Struk
                </h3>
                <div class="space-y-4">
                    @foreach($groups['receipt'] as $setting)
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ $setting->label }}</label>
                            @if($setting->type === 'textarea')
                                <textarea name="{{ $setting->key }}" rows="2"
                                          class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm outline-none focus:ring-2 focus:ring-primary-500 resize-none">{{ $setting->value }}</textarea>
                            @else
                                <input type="text" name="{{ $setting->key }}" value="{{ $setting->value }}"
                                       class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm outline-none focus:ring-2 focus:ring-primary-500">
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Right Column - Preview -->
        <div>
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 sticky top-20">
                <h3 class="text-sm font-bold text-gray-800 mb-4 flex items-center gap-2">
                    <i data-lucide="eye" class="w-4 h-4 text-primary-500"></i> Live Preview
                </h3>

                <!-- Color Preview -->
                <div class="space-y-3">
                    <div class="rounded-xl overflow-hidden border border-gray-200">
                        <div class="h-12 bg-sidebar flex items-center px-4 gap-2">
                            <div class="w-6 h-6 bg-white/20 rounded-md"></div>
                            <div class="h-2 w-16 bg-white/40 rounded"></div>
                        </div>
                        <div class="p-4 bg-gray-50">
                            <div class="flex gap-2 mb-3">
                                <div class="h-8 flex-1 bg-primary-500 rounded-lg"></div>
                                <div class="h-8 flex-1 bg-primary-600 rounded-lg"></div>
                                <div class="h-8 flex-1 bg-primary-700 rounded-lg"></div>
                            </div>
                            <div class="space-y-2">
                                <div class="h-3 bg-gray-200 rounded w-3/4"></div>
                                <div class="h-3 bg-gray-200 rounded w-1/2"></div>
                            </div>
                        </div>
                    </div>

                    <p class="text-xs text-gray-400 text-center">
                        Perubahan warna akan terlihat setelah disimpan
                    </p>
                </div>

                <button type="submit" class="w-full mt-4 bg-primary-600 hover:bg-primary-700 text-white py-3 rounded-xl text-sm font-bold transition shadow-lg shadow-primary-500/25 flex items-center justify-center gap-2">
                    <i data-lucide="save" class="w-4 h-4"></i>
                    Simpan Pengaturan
                </button>
            </div>
        </div>
    </div>
</form>
@endsection
