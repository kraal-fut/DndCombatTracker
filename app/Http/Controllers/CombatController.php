<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCombatRequest;
use App\Models\Combat;
use App\Services\CombatService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CombatController extends Controller
{
    public function index(): View
    {
        $combats = Combat::with('characters')->latest()->get();
        
        return view('combats.index', compact('combats'));
    }

    public function create(): View
    {
        return view('combats.create');
    }

    public function store(StoreCombatRequest $request, CombatService $combatService): RedirectResponse
    {
        $combat = $combatService->createCombat($request->validated('name'));

        return redirect()->route('combats.show', $combat)
            ->with('success', 'Combat created successfully!');
    }

    public function show(Combat $combat): View
    {
        $combat->load([
            'characters.conditions',
            'characters.stateEffects',
            'characters.reactions',
        ]);

        return view('combats.show', compact('combat'));
    }

    public function destroy(Combat $combat): RedirectResponse
    {
        $combat->delete();

        return redirect()->route('combats.index')
            ->with('success', 'Combat deleted successfully!');
    }

    public function nextTurn(Combat $combat, CombatService $combatService): RedirectResponse
    {
        $combatService->nextTurn($combat);

        return back()->with('success', 'Advanced to next turn!');
    }

    public function nextRound(Combat $combat, CombatService $combatService): RedirectResponse
    {
        $combatService->nextRound($combat);

        return back()->with('success', 'Advanced to next round!');
    }

    public function pause(Combat $combat, CombatService $combatService): RedirectResponse
    {
        $combatService->pauseCombat($combat);

        return back()->with('success', 'Combat paused!');
    }

    public function resume(Combat $combat, CombatService $combatService): RedirectResponse
    {
        $combatService->resumeCombat($combat);

        return back()->with('success', 'Combat resumed!');
    }

    public function end(Combat $combat, CombatService $combatService): RedirectResponse
    {
        $combatService->endCombat($combat);

        return back()->with('success', 'Combat ended!');
    }
}
