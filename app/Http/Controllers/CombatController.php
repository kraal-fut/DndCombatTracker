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
        $this->authorize('create', Combat::class);

        return view('combats.create');
    }

    public function store(StoreCombatRequest $request, CombatService $combatService): RedirectResponse
    {
        $this->authorize('create', Combat::class);

        $combat = $combatService->createCombat($request->validated('name'));
        $combat->update(['user_id' => auth()->id()]);

        return redirect()->route('combats.show', $combat)
            ->with('success', 'Combat created successfully!');
    }

    public function show(Combat $combat): View
    {
        $this->authorize('view', $combat);

        $combat->load([
            'characters.conditions',
            'characters.stateEffects',
            'characters.reactions',
        ]);

        return view('combats.show', compact('combat'));
    }

    public function start(Combat $combat, CombatService $combatService): RedirectResponse
    {
        $this->authorize('update', $combat);

        $combatService->startCombat($combat);

        return back()->with('success', 'Combat started!');
    }

    public function destroy(Combat $combat): RedirectResponse
    {
        $this->authorize('delete', $combat);

        $combat->delete();

        return redirect()->route('combats.index')
            ->with('success', 'Combat deleted successfully!');
    }

    public function nextTurn(Combat $combat, CombatService $combatService): RedirectResponse
    {
        $this->authorize('update', $combat);

        $combatService->nextTurn($combat);

        return back()->with('success', 'Advanced to next turn!');
    }

    public function nextRound(Combat $combat, CombatService $combatService): RedirectResponse
    {
        $this->authorize('update', $combat);

        $combatService->nextRound($combat);

        return back()->with('success', 'Advanced to next round!');
    }

    public function pause(Combat $combat, CombatService $combatService): RedirectResponse
    {
        $this->authorize('update', $combat);

        $combatService->pauseCombat($combat);

        return back()->with('success', 'Combat paused!');
    }

    public function resume(Combat $combat, CombatService $combatService): RedirectResponse
    {
        $this->authorize('update', $combat);

        $combatService->resumeCombat($combat);

        return back()->with('success', 'Combat resumed!');
    }

    public function end(Combat $combat, CombatService $combatService): RedirectResponse
    {
        $this->authorize('update', $combat);

        $combatService->endCombat($combat);

        return back()->with('success', 'Combat ended!');
    }

    public function generateShare(Combat $combat): RedirectResponse
    {
        $this->authorize('update', $combat);

        $share = $combat->generateShareLink();
        $shareUrl = route('combats.shared', $share->share_token);

        return back()->with('success', 'Share link generated!')
            ->with('share_url', $shareUrl);
    }

    public function revokeShare(Combat $combat): RedirectResponse
    {
        $this->authorize('update', $combat);

        $combat->revokeShare();

        return back()->with('success', 'Share link revoked!');
    }

    public function regenerateShare(Combat $combat): RedirectResponse
    {
        $this->authorize('update', $combat);

        $share = $combat->regenerateShareLink();
        $shareUrl = route('combats.shared', $share->share_token);

        return back()->with('success', 'New share link generated!')
            ->with('share_url', $shareUrl);
    }
}
