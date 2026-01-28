@extends('layouts.app')

@section('title', 'Combats')

@section('content')
    <div class="space-y-6" x-data="{ combatToDelete: null, deleteUrl: '', showDeleteModal: false }">
        <div class="flex items-center space-x-4">
            <h1 class="text-3xl font-bold">Combat Encounters</h1>
            @can('create', App\Models\Combat::class)
                <a href="{{ route('combats.create') }}"
                    class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md font-medium transition text-sm">
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
                            @can('delete', $combat)
                                <button
                                    x-on:click="combatToDelete = '{{ $combat->name }}'; deleteUrl = '{{ route('combats.destroy', $combat) }}'; $dispatch('open-modal', 'confirm-combat-deletion')"
                                    class="flex-1 bg-gray-700 hover:bg-gray-600 text-white px-4 py-2 rounded transition">
                                    Delete
                                </button>
                            @endcan
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        <x-modal name="confirm-combat-deletion" focusable>
            <form :action="deleteUrl" method="POST" class="p-6 bg-gray-800 text-gray-100">
                @csrf
                @method('DELETE')

                <h2 class="text-lg font-medium text-white">
                    {{ __('Are you sure you want to delete this combat?') }}
                </h2>

                <p class="mt-1 text-sm text-gray-400">
                    <span
                        x-text="'Once ' + combatToDelete + ' is deleted, all of its resources and data will be permanently deleted.'"></span>
                </p>

                <div class="mt-6 flex justify-end gap-3">
                    <button type="button" x-on:click="$dispatch('close')"
                        class="inline-flex items-center px-4 py-2 bg-gray-700 border border-gray-600 rounded-md font-semibold text-xs text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:ring-offset-gray-800 disabled:opacity-25 transition ease-in-out duration-150">
                        {{ __('Cancel') }}
                    </button>

                    <button type="submit"
                        class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500 active:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 focus:ring-offset-gray-800 transition ease-in-out duration-150">
                        {{ __('Delete Combat') }}
                    </button>
                </div>
            </form>
        </x-modal>
    </div>
@endsection