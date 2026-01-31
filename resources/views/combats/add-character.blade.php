@extends('layouts.app')

@section('header')
    <h2 class="font-semibold text-xl text-gray-100 leading-tight">
        Add Character to {{ $combat->name }}
    </h2>
@endsection

@section('content')
    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-gray-800 overflow-hidden shadow-lg sm:rounded-lg border border-gray-700">
                <div class="p-6">
                    <form method="POST" action="{{ route('combats.shared.store-character', $share->share_token) }}">
                        @csrf

                        <div class="mb-4">
                            <label for="name" class="block text-sm font-medium text-gray-300">Character Name</label>
                            <input type="text" name="name" id="name" value="{{ old('name') }}" required
                                class="mt-1 block w-full rounded-md bg-gray-900 border-gray-600 text-white shadow-sm focus:border-red-500 focus:ring-red-500">
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="initiative" class="block text-sm font-medium text-gray-300">Initiative Roll</label>
                            <input type="number" name="initiative" id="initiative" value="{{ old('initiative') }}" required
                                min="1" max="30"
                                class="mt-1 block w-full rounded-md bg-gray-900 border-gray-600 text-white shadow-sm focus:border-red-500 focus:ring-red-500">
                            @error('initiative')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-xs text-gray-400">Roll d20 + your initiative modifier</p>
                        </div>

                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <label for="max_hp" class="block text-sm font-medium text-gray-300">Max HP</label>
                                <input type="number" name="max_hp" id="max_hp" value="{{ old('max_hp') }}" required min="1"
                                    class="mt-1 block w-full rounded-md bg-gray-900 border-gray-600 text-white shadow-sm focus:border-red-500 focus:ring-red-500">
                                @error('max_hp')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="current_hp" class="block text-sm font-medium text-gray-300">Current HP
                                    (optional)</label>
                                <input type="number" name="current_hp" id="current_hp" value="{{ old('current_hp') }}"
                                    min="0"
                                    class="mt-1 block w-full rounded-md bg-gray-900 border-gray-600 text-white shadow-sm focus:border-red-500 focus:ring-red-500">
                                @error('current_hp')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-xs text-gray-400">Defaults to Max HP</p>
                            </div>
                            <div class="mb-4">
                                <label for="armor_class" class="block text-sm font-medium text-gray-300">Armor Class</label>
                                <input type="number" name="armor_class" id="armor_class" value="{{ old('armor_class') }}"
                                    required min="1" max="30"
                                    class="mt-1 block w-full rounded-md bg-gray-900 border-gray-600 text-white shadow-sm focus:border-red-500 focus:ring-red-500">
                                @error('armor_class')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="border-t border-gray-700 pt-6 mt-6">
                                <h3 class="text-lg font-semibold mb-4 text-white">Damage Modifiers</h3>

                                <div class="space-y-6">
                                    <!-- Resistances -->
                                    <div>
                                        <label class="block text-sm font-medium mb-3 text-blue-400">Resistances (Half
                                            Damage)</label>
                                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                                            @foreach(\App\Enums\DamageType::cases() as $type)
                                                <label class="flex items-center space-x-2 text-sm text-gray-300">
                                                    <input type="checkbox" name="resistances[]" value="{{ $type->value }}"
                                                        @checked(is_array(old('resistances')) && in_array($type->value, old('resistances')))>
                                                    <span>{{ $type->label() }}</span>
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>

                                    <!-- Immunities -->
                                    <div>
                                        <label class="block text-sm font-medium mb-3 text-green-400">Immunities (No
                                            Damage)</label>
                                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                                            @foreach(\App\Enums\DamageType::cases() as $type)
                                                <label class="flex items-center space-x-2 text-sm text-gray-300">
                                                    <input type="checkbox" name="immunities[]" value="{{ $type->value }}"
                                                        @checked(is_array(old('immunities')) && in_array($type->value, old('immunities')))>
                                                    <span>{{ $type->label() }}</span>
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>

                                    <!-- Vulnerabilities -->
                                    <div>
                                        <label class="block text-sm font-medium mb-3 text-red-400">Vulnerabilities (Double
                                            Damage)</label>
                                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                                            @foreach(\App\Enums\DamageType::cases() as $type)
                                                <label class="flex items-center space-x-2 text-sm text-gray-300">
                                                    <input type="checkbox" name="vulnerabilities[]" value="{{ $type->value }}"
                                                        @checked(is_array(old('vulnerabilities')) && in_array($type->value, old('vulnerabilities')))>
                                                    <span>{{ $type->label() }}</span>
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center justify-between mt-8">
                                <a href="{{ route('combats.shared', $share->share_token) }}"
                                    class="text-gray-400 hover:text-gray-200">Cancel</a>
                                <button type="submit"
                                    class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-6 rounded transition">
                                    Add Character
                                </button>
                            </div>
                    </form>
                </div>
            </div>

            <div class="mt-4 bg-gray-800 border border-gray-700 rounded-lg p-4">
                <p class="text-sm text-gray-400">
                    <strong class="text-white">Tip:</strong> You can add multiple characters to the same combat
                    (e.g., your main character and an animal companion).
                </p>
            </div>
        </div>
    </div>
@endsection