@extends('layouts.app')

@section('title', 'Edit Character')

@section('content')
    <div class="max-w-2xl mx-auto">
        <h1 class="text-3xl font-bold mb-8">Edit {{ $character->name }}</h1>

        <form action="{{ route('combats.characters.update', [$combat, $character]) }}" method="POST"
            class="bg-gray-800 rounded-lg p-6 shadow-lg space-y-4">
            @csrf
            @method('PATCH')

            <div>
                <label for="name" class="block text-sm font-medium mb-2">Character Name</label>
                <input type="text" name="name" id="name" value="{{ old('name', $character->name) }}"
                    class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 text-white"
                    required>
                @error('name')
                    <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="initiative" class="block text-sm font-medium mb-2">Initiative</label>
                <input type="number" name="initiative" id="initiative"
                    value="{{ old('initiative', $character->initiative) }}"
                    class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 text-white"
                    required min="1">
                @error('initiative')
                    <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="max_hp" class="block text-sm font-medium mb-2">Max HP</label>
                    <input type="number" name="max_hp" id="max_hp" value="{{ old('max_hp', $character->max_hp) }}"
                        class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 text-white"
                        min="1">
                    @error('max_hp')
                        <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="current_hp" class="block text-sm font-medium mb-2">Current HP</label>
                    <input type="number" name="current_hp" id="current_hp"
                        value="{{ old('current_hp', $character->current_hp) }}"
                        class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 text-white"
                        min="0">
                    @error('current_hp')
                        <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>


            <div class="flex items-center">
                <input type="checkbox" name="is_player" id="is_player" value="1" @checked(old('is_player', $character->is_player))
                    class="w-4 h-4 text-red-600 bg-gray-700 border-gray-600 rounded focus:ring-red-500"
                    onchange="document.getElementById('player_assignment').style.display = this.checked ? 'block' : 'none'">
                <label for="is_player" class="ml-2 text-sm font-medium">Player Character</label>
            </div>

            <div id="player_assignment" style="display: {{ old('is_player', $character->is_player) ? 'block' : 'none' }}"
                class="mt-4">
                <label for="user_id" class="block text-sm font-medium mb-2">Assign to Player</label>
                <select name="user_id" id="user_id"
                    class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 text-white">
                    <option value="">-- Select Player --</option>
                    @foreach(\App\Models\User::where('role', 'player')->get() as $player)
                        <option value="{{ $player->id }}" @selected(old('user_id', $character->user_id) == $player->id)>
                            {{ $player->name }} ({{ $player->email }})
                        </option>
                    @endforeach
                </select>
                @error('user_id')
                    <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="border-t border-gray-700 pt-6 mt-6">
                <h3 class="text-lg font-semibold mb-4">Damage Modifiers</h3>

                <div class="space-y-6">
                    <!-- Resistances -->
                    <div>
                        <label class="block text-sm font-medium mb-3 text-blue-400">Resistances (Half Damage)</label>
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                            @foreach(\App\Enums\DamageType::cases() as $type)
                                <label class="flex items-center space-x-2 text-sm">
                                    <input type="checkbox" name="resistances[]" value="{{ $type->value }}"
                                        @checked(is_array(old('resistances', $character->resistances)) && in_array($type->value, old('resistances', $character->resistances)))>
                                    <span>{{ $type->label() }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <!-- Immunities -->
                    <div>
                        <label class="block text-sm font-medium mb-3 text-green-400">Immunities (No Damage)</label>
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                            @foreach(\App\Enums\DamageType::cases() as $type)
                                <label class="flex items-center space-x-2 text-sm">
                                    <input type="checkbox" name="immunities[]" value="{{ $type->value }}"
                                        @checked(is_array(old('immunities', $character->immunities)) && in_array($type->value, old('immunities', $character->immunities)))>
                                    <span>{{ $type->label() }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <!-- Vulnerabilities -->
                    <div>
                        <label class="block text-sm font-medium mb-3 text-red-400">Vulnerabilities (Double Damage)</label>
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                            @foreach(\App\Enums\DamageType::cases() as $type)
                                <label class="flex items-center space-x-2 text-sm">
                                    <input type="checkbox" name="vulnerabilities[]" value="{{ $type->value }}"
                                        @checked(is_array(old('vulnerabilities', $character->vulnerabilities)) && in_array($type->value, old('vulnerabilities', $character->vulnerabilities)))>
                                    <span>{{ $type->label() }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <div class="border-t border-gray-700 pt-6 mt-6">
                <h3 class="text-lg font-semibold mb-4">Condition Immunities</h3>

                <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                    @foreach(\App\Enums\ConditionType::cases() as $type)
                        @continue($type === \App\Enums\ConditionType::Custom)
                        <label class="flex items-center space-x-2 text-sm">
                            <input type="checkbox" name="condition_immunities[]" value="{{ $type->value }}"
                                @checked(is_array(old('condition_immunities', $character->condition_immunities)) && in_array($type->value, old('condition_immunities', $character->condition_immunities)))>
                            <span>{{ $type->label() }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="flex space-x-4">
                <button type="submit"
                    class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-md font-medium transition">
                    Update Character
                </button>
                <a href="{{ route('combats.show', $combat) }}"
                    class="bg-gray-700 hover:bg-gray-600 text-white px-6 py-2 rounded-md font-medium transition">
                    Cancel
                </a>
            </div>
        </form>
    </div>
@endsection