<div id="shared-combat-board">
    <!-- Combat Info -->
    <div class="bg-gray-800 overflow-hidden shadow-lg sm:rounded-lg border border-gray-700 mb-6">
        <div class="p-6">
            <div class="flex justify-between items-center">
                <div>
                    <h3 class="text-2xl font-bold text-white">{{ $combat->name }}</h3>
                    <p class="text-gray-400 mt-1">
                        Round {{ $combat->current_round }} •
                        <span class="px-2 py-1 text-xs font-semibold rounded-full
                            @if($combat->status->value === 'active') bg-green-900 text-green-300
                            @elseif($combat->status->value === 'paused') bg-yellow-900 text-yellow-300
                            @else bg-gray-600 text-gray-300
                            @endif">
                            {{ $combat->status->label() }}
                        </span>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Initiative Order -->
    <div class="bg-gray-800 overflow-hidden shadow-lg sm:rounded-lg border border-gray-700">
        <div class="p-6">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-bold text-white">Initiative Order</h3>
                <div class="flex space-x-2">
                    <a href="{{ route('combats.shared.add-character', $share->share_token) }}"
                        class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded transition text-sm">
                        Add Character
                    </a>
                    @can('update', $combat)
                        @if($combat->status === App\Enums\CombatStatus::Active)
                            <form action="{{ route('combats.next-turn', $combat) }}" method="POST">
                                @csrf
                                <button type="submit"
                                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md transition text-sm font-bold">
                                    Next Turn
                                </button>
                            </form>
                            <form action="{{ route('combats.next-round', $combat) }}" method="POST">
                                @csrf
                                <button type="submit"
                                    class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-md transition text-sm font-bold">
                                    Next Round
                                </button>
                            </form>
                        @endif
                        <form action="{{ route('combats.characters.destroy-all', $combat) }}" method="POST"
                            onsubmit="return confirm('Remove all characters?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="bg-gray-700 hover:bg-gray-600 text-white px-4 py-2 rounded-md transition text-sm font-bold">
                                Remove All
                            </button>
                        </form>
                    @endcan
                </div>
            </div>

            @if($combat->characters->isEmpty())
                <p class="text-gray-400 text-center py-8">No characters in combat yet.</p>
            @else
            <div class="space-y-4">
                @foreach($combat->characters as $index => $character)
                @php
                    $isCurrentTurn = $index === $combat->current_turn_index;
                    $isOwnCharacter = $userCharacters->contains('id', $character->id);
                    $isPlayerCharacter = $character->user_id !== null;
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

                                @if($isOwnCharacter || $isPlayerCharacter)
                                    <div class="flex items-center gap-4 mt-1">
                                        <div class="flex items-center gap-2">
                                            <span class="text-green-400 font-semibold">HP:
                                                {{ $character->current_hp }}/{{ $character->max_hp }}
                                                @if($character->temporary_hp > 0)
                                                    <span class="text-blue-400">(+{{ $character->temporary_hp }})</span>
                                                @endif
                                            </span>
                                            @can('updateHp', $character)
                                                <div class="flex gap-1 ml-2" x-data="{ damageModal: false }">
                                                    <!-- Damage Button -->
                                                    <button @click="damageModal = true"
                                                        class="px-2 py-0.5 text-xs bg-red-600 hover:bg-red-700 rounded transition text-white font-bold"
                                                        title="Deal Damage">DAM</button>

                                                    <!-- Damage Modal -->
                                                    <div x-show="damageModal"
                                                        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50"
                                                        x-cloak>
                                                        <div @click.away="damageModal = false"
                                                            class="bg-gray-800 border border-gray-700 rounded-lg shadow-xl w-full max-w-md p-6"
                                                            x-data="{ 
                                                                                entries: [{amount: '', type: 'bludgeoning'}],
                                                                                ignoreResist: false,
                                                                                addEntry() { this.entries.push({amount: '', type: 'bludgeoning'}) },
                                                                                removeEntry(index) { this.entries.splice(index, 1) }
                                                                            }">
                                                            <h3 class="text-xl font-bold mb-4">Apply Damage to
                                                                {{ $character->name }}
                                                            </h3>

                                                            <form
                                                                action="{{ route('combats.characters.update-hp', [$combat, $character]) }}"
                                                                method="POST">
                                                                @csrf
                                                                <input type="hidden" name="change_type" value="damage">

                                                                <div class="space-y-3 mb-4 max-h-60 overflow-y-auto pr-2">
                                                                    <template x-for="(entry, index) in entries" :key="index">
                                                                        <div class="flex gap-2">
                                                                            <input type="number" x-model="entry.amount"
                                                                                :name="'damages['+index+'][amount]'"
                                                                                placeholder="Amt"
                                                                                class="w-20 px-2 py-1 bg-gray-900 border border-gray-600 rounded text-white"
                                                                                required>
                                                                            <select x-model="entry.type"
                                                                                :name="'damages['+index+'][type]'"
                                                                                class="flex-1 px-2 py-1 bg-gray-900 border border-gray-600 rounded text-white">
                                                                                @foreach(\App\Enums\DamageType::cases() as $type)
                                                                                    <option value="{{ $type->value }}">
                                                                                        {{ $type->label() }}
                                                                                    </option>
                                                                                @endforeach
                                                                            </select>
                                                                            <button type="button" @click="removeEntry(index)"
                                                                                class="text-red-400 hover:text-red-300 px-2">×</button>
                                                                        </div>
                                                                    </template>
                                                                </div>

                                                                <button type="button" @click="addEntry()"
                                                                    class="text-sm text-blue-400 hover:text-blue-300 mb-4">+ Add
                                                                    Damage Type</button>

                                                                <div class="flex items-center mb-6">
                                                                    <input type="checkbox" name="ignore_resist"
                                                                        id="ignore_resist_shared_{{ $character->id }}"
                                                                        x-model="ignoreResist" value="1"
                                                                        class="w-4 h-4 text-red-600 bg-gray-700 border-gray-600 rounded focus:ring-red-500">
                                                                    <label :for="'ignore_resist_shared_{{ $character->id }}'"
                                                                        class="ml-2 text-sm">Ignore Resistance (Elemental
                                                                        Adept)</label>
                                                                </div>

                                                                <div class="flex justify-end gap-3">
                                                                    <button type="button" @click="damageModal = false"
                                                                        class="px-4 py-2 bg-gray-700 hover:bg-gray-600 rounded text-white">Cancel</button>
                                                                    <button type="submit"
                                                                        class="px-4 py-2 bg-red-600 hover:bg-red-700 rounded text-white font-bold">Apply
                                                                        Damage</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>

                                                    <form
                                                        action="{{ route('combats.characters.update-hp', [$combat, $character]) }}"
                                                        method="POST" class="inline-flex items-center gap-1 text-white">
                                                        @csrf
                                                        <input type="hidden" name="change_type" value="heal">
                                                        <input type="number" name="hp_change" placeholder="H" min="1"
                                                            class="w-12 px-1 py-0.5 text-xs bg-gray-900 border border-gray-600 rounded focus:outline-none focus:ring-1 focus:ring-green-500 text-white">
                                                        <button type="submit"
                                                            class="px-2 py-0.5 text-xs bg-green-600 hover:bg-green-700 rounded transition text-white font-bold"
                                                            title="Heal">+</button>
                                                    </form>
                                                    <form
                                                        action="{{ route('combats.characters.update-hp', [$combat, $character]) }}"
                                                        method="POST" class="inline-flex items-center gap-1 text-white">
                                                        @csrf
                                                        <input type="hidden" name="change_type" value="temporary">
                                                        <input type="number" name="hp_change" placeholder="T" min="1"
                                                            class="w-12 px-1 py-0.5 text-xs bg-gray-900 border border-gray-600 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 text-white">
                                                        <button type="submit"
                                                            class="px-2 py-0.5 text-xs bg-blue-600 hover:bg-blue-700 rounded transition text-white font-bold"
                                                            title="Set Temporary HP">T</button>
                                                    </form>
                                                </div>
                                            @endcan
                                            <div class="flex flex-wrap gap-2 mt-2">
                                                @if($character->resistances && count($character->resistances) > 0)
                                                    <div class="flex items-center gap-1">
                                                        <span class="text-[10px] uppercase font-bold text-blue-400">Res:</span>
                                                        @foreach($character->resistances as $res)
                                                            <span
                                                                class="px-1.5 py-0.5 bg-blue-900/30 text-blue-300 rounded text-[10px] border border-blue-800/50">
                                                                {{ ucfirst($res) }}
                                                            </span>
                                                        @endforeach
                                                    </div>
                                                @endif
                                                @if($character->immunities && count($character->immunities) > 0)
                                                    <div class="flex items-center gap-1">
                                                        <span class="text-[10px] uppercase font-bold text-green-400">Imm:</span>
                                                        @foreach($character->immunities as $imm)
                                                            <span
                                                                class="px-1.5 py-0.5 bg-green-900/30 text-green-300 rounded text-[10px] border border-green-800/50">
                                                                {{ ucfirst($imm) }}
                                                            </span>
                                                        @endforeach
                                                    </div>
                                                @endif
                                                @if($character->vulnerabilities && count($character->vulnerabilities) > 0)
                                                    <div class="flex items-center gap-1">
                                                        <span class="text-[10px] uppercase font-bold text-red-400">Vul:</span>
                                                        @foreach($character->vulnerabilities as $vul)
                                                            <span
                                                                class="px-1.5 py-0.5 bg-red-900/30 text-red-300 rounded text-[10px] border border-red-800/50">
                                                                {{ ucfirst($vul) }}
                                                            </span>
                                                        @endforeach
                                                    </div>
                                                @endif
                                                @if($character->condition_immunities && count($character->condition_immunities) > 0)
                                                    <div class="flex items-center gap-1">
                                                        <span
                                                            class="text-[10px] uppercase font-bold text-purple-400">CImm:</span>
                                                        @foreach($character->condition_immunities as $cimm)
                                                            <span
                                                                class="px-1.5 py-0.5 bg-purple-900/30 text-purple-300 rounded text-[10px] border border-purple-800/50">
                                                                {{ ucfirst($cimm) }}
                                                            </span>
                                                        @endforeach
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-gray-400 text-sm">AC: {{ $character->armor_class }}</div>
                                @else
                                                        <p class="text-sm text-gray-500 mt-1">NPC/Monster</p>
                                                        @endcan
                                                    </div>
                                                </div>
                                                <div class="flex items-center space-x-2">
                                                    @if($isOwnCharacter)
                                                        <a href="{{ route('combats.shared.add-character', $share->share_token) }}?edit={{ $character->id }}"
                                                            class="text-blue-400 hover:text-blue-300 text-sm font-semibold">Edit</a>
                                                    @endif
                                                    @if(auth()->check() && ($isOwnCharacter || auth()->user()->isAdmin() || $combat->user_id === auth()->id()))
                                                        <form action="{{ route('combats.characters.destroy', [$combat, $character]) }}"
                                                            method="POST" onsubmit="return confirm('Remove this character?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit"
                                                                class="text-red-400 hover:text-red-300 text-sm font-semibold">Remove</button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </div>

                                            @if($isOwnCharacter || $isPlayerCharacter)
                                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                                                    <!-- Conditions -->
                                                    <div class="bg-gray-900/50 rounded p-3 border border-gray-700">
                                                        <div class="flex justify-between items-center mb-2">
                                                            <h4 class="font-semibold text-xs text-gray-300 uppercase">Conditions</h4>
                                                            @if($isOwnCharacter)
                                                                <a href="{{ route('characters.conditions.create', $character) }}"
                                                                    class="text-blue-400 hover:text-blue-300 text-xs">Add</a>
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
                                                                        @if($isOwnCharacter)
                                                                            <form
                                                                                action="{{ route('characters.conditions.destroy', [$character, $condition]) }}"
                                                                                method="POST" class="inline">
                                                                                @csrf
                                                                                @method('DELETE')
                                                                                <button type="submit" class="text-red-400 hover:text-red-300 text-xs">×</button>
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
                                                            @if($isOwnCharacter)
                                                                <a href="{{ route('characters.state-effects.create', $character) }}"
                                                                    class="text-blue-400 hover:text-blue-300 text-xs">Add</a>
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
                                                                            @if($effect->value !== 0)
                                                                            ({{ $effect->value > 0 ? '+' : '' }}{{ $effect->value }}) @endif
                                                                        </span>
                                                                        @if($isOwnCharacter)
                                                                            <form
                                                                                action="{{ route('characters.state-effects.destroy', [$character, $effect]) }}"
                                                                                method="POST" class="inline">
                                                                                @csrf
                                                                                @method('DELETE')
                                                                                <button type="submit" class="text-red-400 hover:text-red-300 text-xs">×</button>
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
                                                            @if($isOwnCharacter)
                                                                <a href="{{ route('characters.reactions.create', $character) }}"
                                                                    class="text-blue-400 hover:text-blue-300 text-xs">Add</a>
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
                                                                            @if($isOwnCharacter)
                                                                                @if($reaction->is_used)
                                                                                    <form
                                                                                        action="{{ route('characters.reactions.reset', [$character, $reaction]) }}"
                                                                                        method="POST" class="inline">
                                                                                        @csrf
                                                                                        <button type="submit"
                                                                                            class="text-green-400 hover:text-green-300 text-xs">↺</button>
                                                                                    </form>
                                                                                @elseif(!$hasUsedReaction)
                                                                                    <form action="{{ route('characters.reactions.use', [$character, $reaction]) }}"
                                                                                        method="POST" class="inline">
                                                                                        @csrf
                                                                                        <button type="submit"
                                                                                            class="text-yellow-400 hover:text-yellow-300 text-xs">✓</button>
                                                                                    </form>
                                                                                @endif
                                                                                <form
                                                                                    action="{{ route('characters.reactions.destroy', [$character, $reaction]) }}"
                                                                                    method="POST" class="inline">
                                                                                    @csrf
                                                                                    @method('DELETE')
                                                                                    <button type="submit"
                                                                                        class="text-red-400 hover:text-red-300 text-xs">×</button>
                                                                                </form>
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endif

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

    <!-- Info for players -->
    <div class="mt-6 bg-gray-800 border border-gray-700 rounded-lg p-4">
        <p class="text-sm text-gray-400">
            <strong class="text-white">Note:</strong> You can add multiple characters to this combat.
            You see full stats for all player characters, but only names for NPCs/monsters.
            The DM controls combat flow (turns and rounds).
        </p>
    </div>
</div>