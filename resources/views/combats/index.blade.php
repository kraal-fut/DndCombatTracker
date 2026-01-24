@extends('layouts.app')

@section('title', 'Combats')

@section('content')
    <div class="space-y-6">
        <div class="flex justify-between items-center">
            <h1 class="text-3xl font-bold">Combat Encounters</h1>
            @can('create', App\Models\Combat::class)
                <a href="{{ route('combats.create') }}"
                    class="bg-red-600 hover:bg-red-700 text-white px-6 py-3 rounded-md font-medium transition">
                    + Create Combat
                </a>
            @endcan
        </div>

        @if($combats->isEmpty())
            <div class="bg-gray-800 rounded-lg p-12 text-center">
                <p class="text-gray-400 text-lg mb-4">No combat encounters yet.</p>
                <a href="{{ route('combats.create') }}"
                    class="inline-block bg-red-600 hover:bg-red-700 text-white px-6 py-3 rounded-md font-medium transition">
                    Create First Combat
                </a>
            </div>
        @else
            <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                @foreach($combats as $combat)
                    <div class="bg-gray-800 rounded-lg p-6 shadow-lg border border-gray-700 hover:border-red-500 transition">
                        <div class="flex justify-between items-start mb-4">
                            <h3 class="text-xl font-bold">{{ $combat->name }}</h3>
                            <span
                                class="px-2 py-1 text-xs rounded {{ $combat->status->value === 'active' ? 'bg-green-900 text-green-100' : ($combat->status->value === 'paused' ? 'bg-yellow-900 text-yellow-100' : 'bg-gray-700 text-gray-300') }}">
                                {{ $combat->status->label() }}
                            </span>
                        </div>

                        <div class="space-y-2 mb-4">
                            <p class="text-gray-400">Round: <span class="text-white font-medium">{{ $combat->current_round }}</span>
                            </p>
                            <p class="text-gray-400">Characters: <span
                                    class="text-white font-medium">{{ $combat->characters->count() }}</span></p>
                        </div>

                        <div class="flex space-x-2">
                            <a href="{{ route('combats.show', $combat) }}"
                                class="flex-1 bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded text-center transition">
                                View
                            </a>
                            <form action="{{ route('combats.destroy', $combat) }}" method="POST" class="flex-1"
                                onsubmit="return confirm('Are you sure you want to delete this combat?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="w-full bg-gray-700 hover:bg-gray-600 text-white px-4 py-2 rounded transition">
                                    Delete
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
@endsection