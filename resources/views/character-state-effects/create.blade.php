@extends('layouts.app')

@section('title', 'Add State Effect')

@section('content')
<div class="max-w-2xl mx-auto">
    <h1 class="text-3xl font-bold mb-8">Add State Effect to {{ $character->name }}</h1>

    <form action="{{ route('characters.state-effects.store', $character) }}" method="POST" class="bg-gray-800 rounded-lg p-6 shadow-lg space-y-4">
        @csrf

        <div>
            <label for="name" class="block text-sm font-medium mb-2">Effect Name</label>
            <input 
                type="text" 
                name="name" 
                id="name" 
                value="{{ old('name') }}"
                class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 text-white"
                required
            >
            @error('name')
                <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label for="modifier_type" class="block text-sm font-medium mb-2">Modifier Type</label>
                <select 
                    name="modifier_type" 
                    id="modifier_type"
                    class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 text-white"
                    required
                >
                    @foreach($modifierTypes as $type)
                        <option value="{{ $type->value }}" @selected(old('modifier_type') === $type->value)>
                            {{ $type->label() }}
                        </option>
                    @endforeach
                </select>
                @error('modifier_type')
                    <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="value" class="block text-sm font-medium mb-2">Value</label>
                <input 
                    type="number" 
                    name="value" 
                    id="value" 
                    value="{{ old('value', 0) }}"
                    class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 text-white"
                    required
                >
                @error('value')
                    <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div>
            <label for="advantage_state" class="block text-sm font-medium mb-2">Advantage State</label>
            <select 
                name="advantage_state" 
                id="advantage_state"
                class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 text-white"
                required
            >
                @foreach($advantageStates as $state)
                    <option value="{{ $state->value }}" @selected(old('advantage_state') === $state->value)>
                        {{ $state->label() }}
                    </option>
                @endforeach
            </select>
            @error('advantage_state')
                <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="description" class="block text-sm font-medium mb-2">Description (Optional)</label>
            <textarea 
                name="description" 
                id="description"
                rows="3"
                class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 text-white"
            >{{ old('description') }}</textarea>
            @error('description')
                <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="duration_rounds" class="block text-sm font-medium mb-2">Duration (Rounds, Optional)</label>
            <input 
                type="number" 
                name="duration_rounds" 
                id="duration_rounds" 
                value="{{ old('duration_rounds') }}"
                class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 text-white"
                min="1"
            >
            @error('duration_rounds')
                <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex space-x-4">
            <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-md font-medium transition">
                Add State Effect
            </button>
            <a href="{{ route('combats.show', $character->combat) }}" class="bg-gray-700 hover:bg-gray-600 text-white px-6 py-2 rounded-md font-medium transition">
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection
