@extends('layouts.app')

@section('header')
    <h2 class="font-semibold text-xl text-gray-100 leading-tight">
        {{ __('Dashboard') }}
    </h2>
@endsection

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Welcome Message -->
            <div
                class="bg-gradient-to-r from-red-900 to-gray-800 overflow-hidden shadow-xl sm:rounded-lg mb-6 border border-gray-700">
                <div class="p-6">
                    <h3 class="text-2xl font-bold text-white mb-2">Welcome, {{ Auth::user()->name }}!</h3>
                    <p class="text-gray-300">
                        You are logged in as <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                @if(Auth::user()->role->value === 'admin') bg-red-100 text-red-800
                                @elseif(Auth::user()->role->value === 'dm') bg-purple-100 text-purple-800
                                @else bg-blue-100 text-blue-800
                                @endif">
                            {{ Auth::user()->role->label() }}
                        </span>
                    </p>
                </div>
            </div>

            <!-- Statistics Grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <!-- Total Combats -->
                <div class="bg-gray-800 overflow-hidden shadow-lg sm:rounded-lg border border-gray-700">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-red-900 rounded-md p-3">
                                <svg class="h-6 w-6 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-400 truncate">
                                        @if(Auth::user()->isAdmin())
                                            Total Combats
                                        @elseif(Auth::user()->isDM())
                                            Your Combats
                                        @else
                                            Your Combats
                                        @endif
                                    </dt>
                                    <dd class="text-3xl font-bold text-white">
                                        {{ $totalCombats }}
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Active Combats -->
                <div class="bg-gray-800 overflow-hidden shadow-lg sm:rounded-lg border border-gray-700">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-green-900 rounded-md p-3">
                                <svg class="h-6 w-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 10V3L4 14h7v7l9-11h-7z" />
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-400 truncate">Active Combats</dt>
                                    <dd class="text-3xl font-bold text-white">
                                        {{ $activeCombats }}
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Total Characters -->
                <div class="bg-gray-800 overflow-hidden shadow-lg sm:rounded-lg border border-gray-700">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-purple-900 rounded-md p-3">
                                <svg class="h-6 w-6 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-400 truncate">Total Characters</dt>
                                    <dd class="text-3xl font-bold text-white">
                                        {{ $totalCharacters }}
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Combats -->
            <div class="bg-gray-800 overflow-hidden shadow-lg sm:rounded-lg border border-gray-700">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-white mb-4">Recent Combats</h3>

                    @if($recentCombats->isEmpty())
                        <p class="text-gray-400 text-center py-8">No combats yet. Create your first combat to get started!</p>
                        <div class="text-center mt-4">
                            @can('create', \App\Models\Combat::class)
                                <a href="{{ route('combats.create') }}"
                                    class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg transition">
                                    Create Combat
                                </a>
                            @endcan
                        </div>
                    @else
                        <div class="space-y-3">
                            @foreach($recentCombats as $combat)
                                <a href="{{ route('combats.show', $combat) }}"
                                    class="block p-4 bg-gray-700 hover:bg-gray-650 rounded-lg transition border border-gray-600">
                                    <div class="flex justify-between items-center">
                                        <div>
                                            <h4 class="font-semibold text-white">{{ $combat->name }}</h4>
                                            <p class="text-sm text-gray-400">
                                                Round {{ $combat->current_round }} •
                                                {{ $combat->characters->count() }} characters •
                                                <span class="px-2 py-0.5 text-xs font-semibold rounded-full
                                                                @if($combat->status->value === 'active') bg-green-900 text-green-300
                                                                @elseif($combat->status->value === 'paused') bg-yellow-900 text-yellow-300
                                                                @else bg-gray-600 text-gray-300
                                                                @endif">
                                                    {{ $combat->status->label() }}
                                                </span>
                                            </p>
                                        </div>
                                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 5l7 7-7 7" />
                                        </svg>
                                    </div>
                                </a>
                            @endforeach
                        </div>

                        <div class="mt-4 text-center">
                            <a href="{{ route('combats.index') }}" class="text-red-400 hover:text-red-300 font-semibold">
                                View All Combats →
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                @can('create', \App\Models\Combat::class)
                    <div class="bg-gray-800 overflow-hidden shadow-lg sm:rounded-lg border border-gray-700 p-6">
                        <h3 class="text-lg font-semibold text-white mb-3">Quick Actions</h3>
                        <div class="space-y-2">
                            <a href="{{ route('combats.create') }}"
                                class="block w-full text-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg transition">
                                Create New Combat
                            </a>
                            <a href="{{ route('combats.index') }}"
                                class="block w-full text-center px-4 py-2 bg-gray-700 hover:bg-gray-600 text-white font-semibold rounded-lg transition">
                                View All Combats
                            </a>
                        </div>
                    </div>
                @endcan

                @if(Auth::user()->isAdmin())
                    <div class="bg-gray-800 overflow-hidden shadow-lg sm:rounded-lg border border-gray-700 p-6">
                        <h3 class="text-lg font-semibold text-white mb-3">Admin Tools</h3>
                        <div class="space-y-2">
                            <a href="{{ route('admin.dashboard') }}"
                                class="block w-full text-center px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white font-semibold rounded-lg transition">
                                Manage Users
                            </a>
                            <p class="text-sm text-gray-400 text-center">
                                {{ \App\Models\User::count() }} total users
                            </p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection