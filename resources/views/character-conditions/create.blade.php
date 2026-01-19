@extends('layouts.app')

@section('title', 'Add Condition')

@section('content')
<div class="max-w-2xl mx-auto">
    <h1 class="text-3xl font-bold mb-8">Add Condition to {{ $character->name }}</h1>

    <form action="{{ route('characters.conditions.store', $character) }}" method="POST" class="bg-gray-800 rounded-lg p-6 shadow-lg space-y-4">
        @csrf

        <div>
            <label for="condition_type" class="block text-sm font-medium mb-2">Condition Type</label>
            <select 
                name="condition_type" 
                id="condition_type"
                class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 text-white"
                required
            >
                @foreach($conditionTypes as $type)
                    <option value="{{ $type->value }}" @selected(old('condition_type') === $type->value)>
                        {{ $type->label() }}
                    </option>
                @endforeach
            </select>
            @error('condition_type')
                <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div id="custom-name-field" style="display: none;">
            <label for="custom_name" class="block text-sm font-medium mb-2">Custom Condition Name</label>
            <input 
                type="text" 
                name="custom_name" 
                id="custom_name" 
                value="{{ old('custom_name') }}"
                class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 text-white"
            >
            @error('custom_name')
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
                Add Condition
            </button>
            <a href="{{ route('combats.show', $character->combat) }}" class="bg-gray-700 hover:bg-gray-600 text-white px-6 py-2 rounded-md font-medium transition">
                Cancel
            </a>
        </div>
    </form>
</div>

<script>
    document.getElementById('condition_type').addEventListener('change', function() {
        const customField = document.getElementById('custom-name-field');
        if (this.value === 'custom') {
            customField.style.display = 'block';
        } else {
            customField.style.display = 'none';
        }
    });
    
    if (document.getElementById('condition_type').value === 'custom') {
        document.getElementById('custom-name-field').style.display = 'block';
    }
</script>
@endsection
