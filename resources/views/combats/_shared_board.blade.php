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
                <a href="{{ route('combats.shared.add-character', $share->share_token) }}" 
                   class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded transition">
                    + Add Character
                </a>
            </div>
        </div>
    </div>

    <!-- Initiative Order -->
    <div class="bg-gray-800 overflow-hidden shadow-lg sm:rounded-lg border border-gray-700">
        <div class="p-6">
            <h3 class="text-lg font-semibold text-white mb-4">Initiative Order</h3>
            
            @if($combat->characters->isEmpty())
                <p class="text-gray-400 text-center py-8">No characters in combat yet.</p>
            @else
                <div class="space-y-3">
                    @foreach($combat->characters as $index => $character)
                        @php
                            $isCurrentTurn = $index === $combat->current_turn_index;
                            $isOwnCharacter = $userCharacters->contains('id', $character->id);
                            $isPlayerCharacter = $character->user_id !== null;
                        @endphp
                        
                        <div class="p-4 rounded-lg transition
                            {{ $isCurrentTurn ? 'bg-red-900 border-2 border-red-500' : 'bg-gray-700 border border-gray-600' }}
                            {{ $isOwnCharacter ? 'ring-2 ring-blue-500' : '' }}">
                            <div class="flex justify-between items-start">
                                <div class="flex-1">
                                    <div class="flex items-center gap-3">
                                        <span class="text-2xl font-bold text-white">{{ $character->initiative }}</span>
                                        <div>
                                            <h4 class="font-semibold text-white">
                                                {{ $character->name }}
                                                @if($isOwnCharacter)
                                                    <span class="text-xs bg-blue-600 text-white px-2 py-0.5 rounded">Your Character</span>
                                                @endif
                                                @if($isCurrentTurn)
                                                    <span class="text-xs bg-red-600 text-white px-2 py-0.5 rounded ml-1">Current Turn</span>
                                                @endif
                                            </h4>
                                            
                                            @if($isOwnCharacter || $isPlayerCharacter)
                                                <!-- Full stats for own characters and other player characters -->
                                                <div class="flex gap-4 mt-2 text-sm">
                                                    <span class="text-gray-300">
                                                        HP: <span class="font-semibold text-white">{{ $character->current_hp }}/{{ $character->max_hp }}</span>
                                                    </span>
                                                    <span class="text-gray-300">
                                                        AC: <span class="font-semibold text-white">{{ $character->armor_class }}</span>
                                                    </span>
                                                </div>
                                                
                                                <!-- HP Bar -->
                                                <div class="mt-2 w-full bg-gray-600 rounded-full h-2">
                                                    @php
                                                        $hpPercentage = ($character->current_hp / $character->max_hp) * 100;
                                                        $barColor = $hpPercentage > 50 ? 'bg-green-500' : ($hpPercentage > 25 ? 'bg-yellow-500' : 'bg-red-500');
                                                    @endphp
                                                    <div class="{{ $barColor }} h-2 rounded-full transition-all" style="width: {{ $hpPercentage }}%"></div>
                                                </div>
                                            @else
                                                <!-- Limited info for DM NPCs -->
                                                <p class="text-sm text-gray-400 mt-1">NPC/Monster</p>
                                            @endif

                                            @if($character->conditions->isNotEmpty())
                                                <div class="flex flex-wrap gap-1 mt-2">
                                                    @foreach($character->conditions as $condition)
                                                        <span class="px-2 py-0.5 text-xs bg-purple-900 text-purple-300 rounded">
                                                            {{ $condition->name }}
                                                        </span>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                @if($isOwnCharacter)
                                    <a href="{{ route('combats.shared.add-character', $share->share_token) }}?edit={{ $character->id }}" 
                                       class="ml-4 text-blue-400 hover:text-blue-300 text-sm font-semibold">
                                        Edit
                                    </a>
                                @endif
                            </div>

                            @if($isOwnCharacter && $character->notes)
                                <div class="mt-3 pt-3 border-t border-gray-600">
                                    <p class="text-sm text-gray-300">{{ $character->notes }}</p>
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
