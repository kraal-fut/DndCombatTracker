@extends('layouts.app')

@section('title', 'Create Combat')

@section('content')
<div class="max-w-2xl mx-auto">
    <h1 class="text-3xl font-bold mb-8">Create New Combat</h1>

    <form action="{{ route('combats.store') }}" method="POST" class="bg-gray-800 rounded-lg p-6 shadow-lg">
        @csrf

        <div class="mb-6">
            <label for="name" class="block text-sm font-medium mb-2">Combat Name</label>
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

        <div class="flex space-x-4">
            <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-md font-medium transition">
                Create Combat
            </button>
            <a href="{{ route('combats.index') }}" class="bg-gray-700 hover:bg-gray-600 text-white px-6 py-2 rounded-md font-medium transition">
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection
