@extends('layouts.app')

@section('title', 'Add Character')

@section('content')
    <div class="max-w-2xl mx-auto">
        <h1 class="text-3xl font-bold mb-8">Add Character to {{ $combat->name }}</h1>

        <form action="{{ route('combats.characters.store', $combat) }}" method="POST"
            class="bg-gray-800 rounded-lg p-6 shadow-lg space-y-4">
            @csrf

            <div>
                <label for="name" class="block text-sm font-medium mb-2">Character Name</label>
                <input type="text" name="name" id="name" value="{{ old('name') }}"
                    class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 text-white"
                    required>
                @error('name')
                    <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="initiative" class="block text-sm font-medium mb-2">Initiative</label>
                <input type="number" name="initiative" id="initiative" value="{{ old('initiative') }}"
                    class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 text-white"
                    required min="1">
                @error('initiative')
                    <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="max_hp" class="block text-sm font-medium mb-2">Max HP</label>
                    <input type="number" name="max_hp" id="max_hp" value="{{ old('max_hp') }}"
                        class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 text-white"
                        min="1">
                    @error('max_hp')
                        <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="current_hp" class="block text-sm font-medium mb-2">Current HP</label>
                    <input type="number" name="current_hp" id="current_hp" value="{{ old('current_hp') }}"
                        class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 text-white"
                        min="0">
                    @error('current_hp')
                        <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>


            <div class="flex items-center">
                <input type="checkbox" name="is_player" id="is_player" value="1" @checked(old('is_player'))
                    class="w-4 h-4 text-red-600 bg-gray-700 border-gray-600 rounded focus:ring-red-500"
                    onchange="document.getElementById('player_assignment').style.display = this.checked ? 'block' : 'none'">
                <label for="is_player" class="ml-2 text-sm font-medium">Player Character</label>
            </div>

            <div id="player_assignment" style="display: {{ old('is_player') ? 'block' : 'none' }}">
                <label for="user_id" class="block text-sm font-medium mb-2">Assign to Player</label>
                <select name="user_id" id="user_id"
                    class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 text-white">
                    <option value="">-- Select Player --</option>
                    @foreach(\App\Models\User::where('role', 'player')->get() as $player)
                        <option value="{{ $player->id }}" @selected(old('user_id') == $player->id)>
                            {{ $player->name }} ({{ $player->email }})
                        </option>
                    @endforeach
                </select>
                @error('user_id')
                    <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex space-x-4">
                <button type="submit"
                    class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-md font-medium transition">
                    Add Character
                </button>
                <a href="{{ route('combats.show', $combat) }}"
                    class="bg-gray-700 hover:bg-gray-600 text-white px-6 py-2 rounded-md font-medium transition">
                    Cancel
                </a>
            </div>
        </form>
    </div>
@endsection