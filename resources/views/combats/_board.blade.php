<div id="combat-board" class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold">{{ $combat->name }}</h1>
            <p class="text-gray-400 mt-1">Round {{ $combat->current_round }} | Status: {{ $combat->status->label() }}
            </p>
        </div>
        @can('update', $combat)
            <div class="flex space-x-2">
                @if($combat->status === App\Enums\CombatStatus::Active)
                    <form action="{{ route('combats.pause', $combat) }}" method="POST">
                        @csrf
                        <button type="submit"
                            class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-md transition">
                            Pause
                        </button>
                    </form>
                @elseif($combat->status === App\Enums\CombatStatus::Paused)
                    <form action="{{ route('combats.resume', $combat) }}" method="POST">
                        @csrf
                        <button type="submit"
                            class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md transition">
                            Resume
                        </button>
                    </form>
                @elseif($combat->status === App\Enums\CombatStatus::Preparation)
                    <form action="{{ route('combats.start', $combat) }}" method="POST">
                        @csrf
                        <button type="submit"
                            class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md transition font-bold">
                            Start Combat
                        </button>
                    </form>
                @endif

                @if($combat->status !== App\Enums\CombatStatus::Completed)
                    <form action="{{ route('combats.end', $combat) }}" method="POST"
                        onsubmit="return confirm('Are you sure you want to end this combat?')">
                        @csrf
                        <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md transition">
                            End Combat
                        </button>
                    </form>
                @endif
            </div>
        @endcan
    </div>

    <!-- Share Management -->
    @can('update', $combat)
        <div x-data="{ open: false }" class="bg-gray-800 rounded-lg shadow-lg border border-gray-700 overflow-hidden">
            <button @click="open = !open"
                class="w-full flex justify-between items-center px-6 py-4 text-left hover:bg-gray-750 transition duration-150">
                <h3 class="text-lg font-semibold text-white">Share Combat with Players</h3>
                <svg :class="open ? 'rotate-180' : ''" class="w-5 h-5 text-gray-400 transition-transform duration-200"
                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </button>

            <div x-show="open" x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 transform -translate-y-2"
                x-transition:enter-end="opacity-100 transform translate-y-0"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 transform translate-y-0"
                x-transition:leave-end="opacity-0 transform -translate-y-2" class="px-6 pb-6 space-y-4">

                @php
                    $activeShare = $combat->getActiveShare();
                @endphp

                @if(session('share_url'))
                    <div class="bg-green-900 border border-green-700 text-green-300 px-4 py-3 rounded">
                        Share link generated! Copy the link below.
                    </div>
                @endif

                @if($activeShare)
                    <div class="space-y-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">Share Link:</label>
                            <div class="flex gap-2">
                                <input type="text" id="share-link"
                                    value="{{ route('combats.shared', $activeShare->share_token) }}" readonly
                                    class="flex-1 rounded-md bg-gray-900 border-gray-600 text-white shadow-sm">
                                <button onclick="copyShareLink()"
                                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md transition">
                                    Copy
                                </button>
                            </div>
                            <p class="mt-1 text-xs text-gray-400">Players can use this link to view and join the combat</p>
                        </div>

                        <div class="flex gap-2">
                            <form action="{{ route('combats.share.revoke', $combat) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    onclick="return confirm('Are you sure? This will invalidate the current share link.')"
                                    class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-md transition text-sm">
                                    Revoke Link
                                </button>
                            </form>

                            <form action="{{ route('combats.share.regenerate', $combat) }}" method="POST">
                                @csrf
                                <button type="submit"
                                    onclick="return confirm('This will create a new link and invalidate the old one.')"
                                    class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-md transition text-sm">
                                    Regenerate Link
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <form action="{{ route('combats.share.generate', $combat) }}" method="POST">
                        @csrf
                        <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md transition">
                            Generate Share Link
                        </button>
                    </form>
                    <p class="mt-2 text-sm text-gray-400">Create a shareable link for players to view and join this combat</p>
                @endif
            </div>
        </div>
    @endcan

    <div class="bg-gray-800 rounded-lg p-6 shadow-lg">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold">Initiative Order</h2>
            @can('update', $combat)
                <div class="flex space-x-2">
                    @can('create', App\Models\CombatCharacter::class)
                        <a href="{{ route('combats.characters.create', $combat) }}"
                            class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md transition">
                            Add Character
                        </a>
                    @endcan
                    @if($combat->characters->isNotEmpty())
                        @if($combat->status === App\Enums\CombatStatus::Paused || $combat->status === App\Enums\CombatStatus::Completed)
                            <form action="{{ route('combats.resume', $combat) }}" method="POST">
                                @csrf
                                <button type="submit"
                                    class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-md transition font-semibold">
                                    {{ $combat->current_round === 1 ? 'Start Combat' : 'Resume Combat' }}
                                </button>
                            </form>
                        @endif

                        @if($combat->status === App\Enums\CombatStatus::Active)
                            <form action="{{ route('combats.next-turn', $combat) }}" method="POST">
                                @csrf
                                <button type="submit"
                                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md transition">
                                    Next Turn
                                </button>
                            </form>
                            <form action="{{ route('combats.next-round', $combat) }}" method="POST">
                                @csrf
                                <button type="submit"
                                    class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-md transition">
                                    Next Round
                                </button>
                            </form>
                        @endif

                        <form action="{{ route('combats.characters.destroy-all', $combat) }}" method="POST"
                            onsubmit="return confirm('Remove all characters?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="bg-gray-700 hover:bg-gray-600 text-white px-4 py-2 rounded-md transition">
                                Remove All
                            </button>
                        </form>
                    @endif
                </div>
            @endcan
        </div>

        @if($combat->characters->isEmpty())
            <p class="text-gray-400 text-center py-8">No characters in combat yet.</p>
        @else
                <div class="space-y-4">
                    @foreach($combat->characters as $index => $character)
                            @php
                                $isCurrentTurn = $index === $combat->current_turn_index;
                                $isOwnCharacter = auth()->check() && $character->user_id === auth()->id();
                                $isPlayerCharacter = $character->user_id !== null;
                                $isDM = auth()->check() && $combat->user_id === auth()->id();
                                $isAdmin = auth()->check() && auth()->user()->isAdmin();
                                $canManage = $isOwnCharacter || $isDM || $isAdmin;
                            @endphp

                            <div class="rounded-lg p-4 transition
                                        {{ $isCurrentTurn ? 'bg-red-900 border-2 border-red-500' : 'bg-gray-800 border border-gray-700' }}
                                        {{ $isOwnCharacter ? 'ring-2 ring-blue-500' : '' }}">

                                <div class="flex justify-between items-start mb-3">
                                    <div class="flex items-center space-x-4">
                                        <div
                                            class="text-3xl font-bold {{ $isCurrentTurn ? 'text-white' : 'text-red-500' }} w-12 text-center">
                                            {{ $character->initiative }}
                                        </div>
                                        <div class="flex-1">
                                            <h3 class="text-xl font-bold text-white">
                                                {{ $character->name }}
                                                @if($character->is_player)
                                                    <span class="text-blue-400 text-sm ml-1">(Player)</span>
                                                @endif
                                                @if($isOwnCharacter)
                                                    <span class="text-xs bg-blue-600 text-white px-2 py-0.5 rounded ml-1">Your
                                                        Character</span>
                                                @endif
                                                @if($isCurrentTurn)
                                                    <span class="text-red-400 text-sm ml-1">← Current Turn</span>
                                                @endif
                                            </h3>

                                            @can('viewStats', $character)
                                                    <div class="flex items-center gap-4 mt-1">
                                                        <div class="flex items-center gap-2">
                                                            <span class="text-green-400 font-semibold">HP:
                                                                {{ $character->current_hp }}/{{ $character->max_hp }}</span>
                                                            @can('updateHp', $character)
                                                                <div class="flex gap-1 ml-2">
                                                                    <form
                                                                        action="{{ route('combats.characters.update-hp', [$combat, $character]) }}"
                                                                        method="POST" class="inline-flex items-center gap-1">
                                                                        @csrf
                                                                        <input type="hidden" name="change_type" value="damage">
                                                                        <input type="number" name="hp_change" placeholder="DMG" min="1"
                                                                            class="w-16 px-1 py-0.5 text-xs bg-gray-900 border border-gray-600 rounded focus:outline-none focus:ring-1 focus:ring-red-500 text-white">
                                                                        <button type="submit"
                                                                            class="px-2 py-0.5 text-xs bg-red-600 hover:bg-red-700 rounded transition text-white font-bold">-</button>
                                                                    </form>
                                                                    <form
                                                                        action="{{ route('combats.characters.update-hp', [$combat, $character]) }}"
                                                                        method="POST" class="inline-flex items-center gap-1">
                                                                        @csrf
                                                                        <input type="hidden" name="change_type" value="heal">
                                                                        <input type="number" name="hp_change" placeholder="HEAL" min="1"
                                                                            class="w-16 px-1 py-0.5 text-xs bg-gray-900 border border-gray-600 rounded focus:outline-none focus:ring-1 focus:ring-green-500 text-white">
                                                                        <button type="submit"
                                                                            class="px-2 py-0.5 text-xs bg-green-600 hover:bg-green-700 rounded transition text-white font-bold">+</button>
                                                                    </form>
                                                                </div>
                                                            @endcan
                                                        </div>
                                                    </div>
                                                </div>
                                            @else
                                            <p class="text-sm text-gray-500 mt-1">Stats hidden</p>
                                        @endcan
                                    </div>
                                </div>
                                <div class="flex items-center space-x-2">
                                    @can('update', $character)
                                        <a href="{{ route('combats.characters.edit', [$combat, $character]) }}"
                                            class="text-blue-400 hover:text-blue-300 text-sm font-semibold">Edit</a>
                                    @endcan
                                    @if(auth()->check() && ($isOwnCharacter || $isAdmin || $isDM))
                                        <form action="{{ route('combats.characters.destroy', [$combat, $character]) }}" method="POST"
                                            onsubmit="return confirm('Remove this character?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="text-red-400 hover:text-red-300 text-sm font-semibold">Remove</button>
                                        </form>
                                    @endif
                                </div>
                            </div>

                            @can('viewStats', $character)
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                                    <!-- Conditions -->
                                    <div class="bg-gray-900/50 rounded p-3 border border-gray-700">
                                        <div class="flex justify-between items-center mb-2">
                                            <h4 class="font-semibold text-xs text-gray-300 uppercase">Conditions</h4>
                                            @if($canManage)
                                                <a href="{{ route('characters.conditions.create', $character) }}"
                                                    class="text-blue-400 hover:text-blue-300 text-xs text-white">Add</a>
                                            @endif
                                        </div>
                                        @if($character->conditions->isEmpty())
                                            <p class="text-gray-600 text-xs italic">None</p>
                                        @else
                                            <div class="space-y-1">
                                                @foreach($character->conditions as $condition)
                                                    <div class="flex justify-between items-center bg-gray-800 rounded px-2 py-1">
                                                        <span class="text-xs text-white">
                                                            <span
                                                                class="inline-block w-2 h-2 rounded-full bg-{{ $condition->condition_type->color() }}-500 mr-1"></span>
                                                            {{ $condition->getDisplayName() }}
                                                        </span>
                                                        @if($canManage)
                                                            <form action="{{ route('characters.conditions.destroy', [$character, $condition]) }}"
                                                                method="POST" class="inline">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit"
                                                                    class="text-red-400 hover:text-red-300 text-xs text-white">×</button>
                                                            </form>
                                                        @endif
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>

                                    <!-- State Effects -->
                                    <div class="bg-gray-900/50 rounded p-3 border border-gray-700">
                                        <div class="flex justify-between items-center mb-2">
                                            <h4 class="font-semibold text-xs text-gray-300 uppercase">State Effects</h4>
                                            @if($canManage)
                                                <a href="{{ route('characters.state-effects.create', $character) }}"
                                                    class="text-blue-400 hover:text-blue-300 text-xs text-white">Add</a>
                                            @endif
                                        </div>
                                        @if($character->stateEffects->isEmpty())
                                            <p class="text-gray-600 text-xs italic">None</p>
                                        @else
                                            <div class="space-y-1">
                                                @foreach($character->stateEffects as $effect)
                                                    <div class="flex justify-between items-center bg-gray-800 rounded px-2 py-1">
                                                        <span class="text-xs text-white">
                                                            {{ $effect->name }}
                                                            @if($effect->value !== 0) ({{ $effect->value > 0 ? '+' : '' }}{{ $effect->value }})
                                                            @endif
                                                        </span>
                                                        @if($canManage)
                                                            <form action="{{ route('characters.state-effects.destroy', [$character, $effect]) }}"
                                                                method="POST" class="inline">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit"
                                                                    class="text-red-400 hover:text-red-300 text-xs text-white">×</button>
                                                            </form>
                                                        @endif
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Reactions -->
                                    <div class="bg-gray-900/50 rounded p-3 border border-gray-700">
                                        <div class="flex justify-between items-center mb-2">
                                            <h4 class="font-semibold text-xs text-gray-300 uppercase">Reactions</h4>
                                            @if($canManage)
                                                <a href="{{ route('characters.reactions.create', $character) }}"
                                                    class="text-blue-400 hover:text-blue-300 text-xs text-white">Add</a>
                                            @endif
                                        </div>
                                        @if($character->reactions->isEmpty())
                                            <p class="text-gray-600 text-xs italic">None</p>
                                        @else
                                            @php $hasUsedReaction = $character->hasUsedReaction(); @endphp
                                            <div class="space-y-1">
                                                @foreach($character->reactions as $reaction)
                                                    <div
                                                        class="flex justify-between items-center bg-gray-800 rounded px-2 py-1 {{ $hasUsedReaction && !$reaction->is_used ? 'opacity-50' : '' }}">
                                                        <span
                                                            class="text-xs {{ $reaction->is_used ? 'line-through text-gray-500' : 'text-white' }}">
                                                            {{ $reaction->name }}
                                                        </span>
                                                        <div class="flex space-x-1">
                                                            @if($canManage)
                                                                @if($reaction->is_used)
                                                                    <form action="{{ route('characters.reactions.reset', [$character, $reaction]) }}"
                                                                        method="POST" class="inline">
                                                                        @csrf
                                                                        <button type="submit" class="text-green-400 hover:text-green-300 text-xs">↺</button>
                                                                    </form>
                                                                @elseif(!$hasUsedReaction)
                                                                    <form action="{{ route('characters.reactions.use', [$character, $reaction]) }}"
                                                                        method="POST" class="inline">
                                                                        @csrf
                                                                        <button type="submit"
                                                                            class="text-yellow-400 hover:text-yellow-300 text-xs">✓</button>
                                                                    </form>
                                                                @endif
                                                                <form action="{{ route('characters.reactions.destroy', [$character, $reaction]) }}"
                                                                    method="POST" class="inline">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" class="text-red-400 hover:text-red-300 text-xs">×</button>
                                                                </form>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endcan

                            @if($isOwnCharacter && $character->notes)
                                <div class="mt-3 pt-3 border-t border-gray-600">
                                    <p class="text-sm text-gray-300 italic">{{ $character->notes }}</p>
                                </div>
                            @endif
                        </div>
                    @endforeach
            </div>
        @endif
</div>
</div>