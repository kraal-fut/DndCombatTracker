@extends('layouts.app')

@section('title', $combat->name)

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold">{{ $combat->name }}</h1>
            <p class="text-gray-400 mt-1">Round {{ $combat->current_round }} | Status: {{ $combat->status->label() }}</p>
        </div>
        <div class="flex space-x-2">
            @if($combat->status === App\Enums\CombatStatus::Active)
                <form action="{{ route('combats.pause', $combat) }}" method="POST">
                    @csrf
                    <button type="submit" class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-md transition">
                        Pause
                    </button>
                </form>
            @elseif($combat->status === App\Enums\CombatStatus::Paused)
                <form action="{{ route('combats.resume', $combat) }}" method="POST">
                    @csrf
                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md transition">
                        Resume
                    </button>
                </form>
            @endif
            
            @if($combat->status !== App\Enums\CombatStatus::Completed)
                <form action="{{ route('combats.end', $combat) }}" method="POST" onsubmit="return confirm('Are you sure you want to end this combat?')">
                    @csrf
                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md transition">
                        End Combat
                    </button>
                </form>
            @endif
        </div>
    </div>

    <div class="bg-gray-800 rounded-lg p-6 shadow-lg">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold">Initiative Order</h2>
            <div class="flex space-x-2">
                <a href="{{ route('combats.characters.create', $combat) }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md transition">
                    Add Character
                </a>
                @if($combat->characters->isNotEmpty())
                    <form action="{{ route('combats.next-turn', $combat) }}" method="POST">
                        @csrf
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md transition">
                            Next Turn
                        </button>
                    </form>
                    <form action="{{ route('combats.next-round', $combat) }}" method="POST">
                        @csrf
                        <button type="submit" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-md transition">
                            Next Round
                        </button>
                    </form>
                    <form action="{{ route('combats.characters.destroy-all', $combat) }}" method="POST" onsubmit="return confirm('Remove all characters?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="bg-gray-700 hover:bg-gray-600 text-white px-4 py-2 rounded-md transition">
                            Remove All
                        </button>
                    </form>
                @endif
            </div>
        </div>

        @if($combat->characters->isEmpty())
            <p class="text-gray-400 text-center py-8">No characters in combat yet.</p>
        @else
            <div class="space-y-4">
                @foreach($combat->characters as $index => $character)
                    <div class="bg-gray-700 rounded-lg p-4 {{ $index === $combat->current_turn_index ? 'ring-2 ring-red-500' : '' }}">
                        <div class="flex justify-between items-start mb-3">
                            <div class="flex items-center space-x-3">
                                <div class="text-2xl font-bold text-red-500">{{ $character->initiative }}</div>
                                <div class="flex-1">
                                    <h3 class="text-xl font-bold">
                                        {{ $character->name }}
                                        @if($character->is_player)
                                            <span class="text-blue-400 text-sm">(Player)</span>
                                        @endif
                                        @if($index === $combat->current_turn_index)
                                            <span class="text-red-400 text-sm">← Current Turn</span>
                                        @endif
                                    </h3>
                                    @if($character->current_hp !== null)
                                        @php
                                            $hpPercentage = $character->max_hp > 0 ? ($character->current_hp / $character->max_hp) * 100 : 0;
                                            $hpColor = $hpPercentage > 70 ? 'text-green-400' : ($hpPercentage > 30 ? 'text-yellow-400' : 'text-red-400');
                                        @endphp
                                        <div class="flex items-center gap-2 mt-1">
                                            <p class="{{ $hpColor }}">HP: {{ $character->current_hp }}/{{ $character->max_hp }}</p>
                                            <div class="flex gap-1">
                                                <form action="{{ route('combats.characters.update-hp', [$combat, $character]) }}" method="POST" class="inline-flex items-center gap-1">
                                                    @csrf
                                                    <input type="hidden" name="change_type" value="damage">
                                                    <input 
                                                        type="number" 
                                                        name="hp_change" 
                                                        placeholder="DMG"
                                                        min="1"
                                                        class="w-16 px-1 py-0.5 text-xs bg-gray-700 border border-gray-600 rounded focus:outline-none focus:ring-1 focus:ring-red-500 text-white"
                                                        required
                                                    >
                                                    <button type="submit" class="px-2 py-0.5 text-xs bg-red-600 hover:bg-red-700 rounded transition">-</button>
                                                </form>
                                                <form action="{{ route('combats.characters.update-hp', [$combat, $character]) }}" method="POST" class="inline-flex items-center gap-1">
                                                    @csrf
                                                    <input type="hidden" name="change_type" value="heal">
                                                    <input 
                                                        type="number" 
                                                        name="hp_change" 
                                                        placeholder="HEAL"
                                                        min="1"
                                                        class="w-16 px-1 py-0.5 text-xs bg-gray-700 border border-gray-600 rounded focus:outline-none focus:ring-1 focus:ring-green-500 text-white"
                                                        required
                                                    >
                                                    <button type="submit" class="px-2 py-0.5 text-xs bg-green-600 hover:bg-green-700 rounded transition">+</button>
                                                </form>
                                            </div>
                                        </div>
                                    @endif
                                    @if($character->armor_class)
                                        <p class="text-gray-400">AC: {{ $character->armor_class }}</p>
                                    @endif
                                </div>
                            </div>
                            <form action="{{ route('combats.characters.destroy', [$combat, $character]) }}" method="POST" onsubmit="return confirm('Remove this character?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-400 hover:text-red-300">Remove</button>
                            </form>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="bg-gray-800 rounded p-3">
                                <div class="flex justify-between items-center mb-2">
                                    <h4 class="font-semibold text-sm">Conditions</h4>
                                    <a href="{{ route('characters.conditions.create', $character) }}" class="text-blue-400 hover:text-blue-300 text-sm">Add</a>
                                </div>
                                @if($character->conditions->isEmpty())
                                    <p class="text-gray-500 text-xs">None</p>
                                @else
                                    <div class="space-y-1">
                                        @foreach($character->conditions as $condition)
                                            <div class="flex justify-between items-center bg-gray-700 rounded px-2 py-1">
                                                <span class="text-xs">
                                                    <span class="inline-block w-2 h-2 rounded-full bg-{{ $condition->condition_type->color() }}-500 mr-1"></span>
                                                    {{ $condition->getDisplayName() }}
                                                    @if($condition->duration_rounds)
                                                        ({{ $condition->duration_rounds }}r)
                                                    @endif
                                                </span>
                                                <form action="{{ route('characters.conditions.destroy', [$character, $condition]) }}" method="POST" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-400 hover:text-red-300 text-xs">×</button>
                                                </form>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>

                            <div class="bg-gray-800 rounded p-3">
                                <div class="flex justify-between items-center mb-2">
                                    <h4 class="font-semibold text-sm">State Effects</h4>
                                    <a href="{{ route('characters.state-effects.create', $character) }}" class="text-blue-400 hover:text-blue-300 text-sm">Add</a>
                                </div>
                                @if($character->stateEffects->isEmpty())
                                    <p class="text-gray-500 text-xs">None</p>
                                @else
                                    <div class="space-y-1">
                                        @foreach($character->stateEffects as $effect)
                                            <div class="flex justify-between items-center bg-gray-700 rounded px-2 py-1">
                                                <span class="text-xs">
                                                    {{ $effect->name }}
                                                    @if($effect->value !== 0)
                                                        ({{ $effect->value > 0 ? '+' : '' }}{{ $effect->value }})
                                                    @endif
                                                    @if($effect->advantage_state !== App\Enums\AdvantageState::Normal)
                                                        <span class="text-{{ $effect->advantage_state->color() }}-400">{{ $effect->advantage_state->label() }}</span>
                                                    @endif
                                                    @if($effect->duration_rounds)
                                                        ({{ $effect->duration_rounds }}r)
                                                    @endif
                                                </span>
                                                <form action="{{ route('characters.state-effects.destroy', [$character, $effect]) }}" method="POST" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-400 hover:text-red-300 text-xs">×</button>
                                                </form>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>

                            <div class="bg-gray-800 rounded p-3">
                                <div class="flex justify-between items-center mb-2">
                                    <h4 class="font-semibold text-sm">Reactions</h4>
                                    <a href="{{ route('characters.reactions.create', $character) }}" class="text-blue-400 hover:text-blue-300 text-sm">Add</a>
                                </div>
                                @if($character->reactions->isEmpty())
                                    <p class="text-gray-500 text-xs">None</p>
                                @else
                                    @php
                                        $hasUsedReaction = $character->hasUsedReaction();
                                    @endphp
                                    <div class="space-y-1">
                                        @foreach($character->reactions as $reaction)
                                            <div class="flex justify-between items-center bg-gray-700 rounded px-2 py-1 {{ $hasUsedReaction && !$reaction->is_used ? 'opacity-50' : '' }}">
                                                <span class="text-xs {{ $reaction->is_used ? 'line-through text-gray-500' : '' }} {{ $hasUsedReaction && !$reaction->is_used ? 'text-gray-600' : '' }}">
                                                    {{ $reaction->name }}
                                                    @if($hasUsedReaction && !$reaction->is_used)
                                                        <span class="text-gray-600 text-xs ml-1">(unavailable)</span>
                                                    @endif
                                                </span>
                                                <div class="flex space-x-1">
                                                    @if($reaction->is_used)
                                                        <form action="{{ route('characters.reactions.reset', [$character, $reaction]) }}" method="POST" class="inline">
                                                            @csrf
                                                            <button type="submit" class="text-green-400 hover:text-green-300 text-xs">↺</button>
                                                        </form>
                                                    @elseif(!$hasUsedReaction)
                                                        <form action="{{ route('characters.reactions.use', [$character, $reaction]) }}" method="POST" class="inline">
                                                            @csrf
                                                            <button type="submit" class="text-yellow-400 hover:text-yellow-300 text-xs">✓</button>
                                                        </form>
                                                    @else
                                                        <button type="button" disabled class="text-gray-600 text-xs cursor-not-allowed" title="Already used a reaction this round">✓</button>
                                                    @endif
                                                    <form action="{{ route('characters.reactions.destroy', [$character, $reaction]) }}" method="POST" class="inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="text-red-400 hover:text-red-300 text-xs">×</button>
                                                    </form>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection
