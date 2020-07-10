<?php

declare(strict_types=1);

namespace Francken\Lustrum\Http\Controllers\Admin;

use Francken\Lustrum\Adtchievement;
use Francken\Lustrum\EarnedAdtchievement;
use Francken\Lustrum\Pirate;
use Francken\Lustrum\PirateCrew;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Webmozart\Assert\Assert;

class PirateAdtchievementsController
{
    public function store(PirateCrew $pirateCrew, Request $request) : RedirectResponse
    {
        $points = $request->input('points');

        if ($points !== null) {
            $points = (int)$points;
        }

        $adtchievement = Adtchievement::findOrFail($request->adtchievement_id);
        Assert::isInstanceOf($adtchievement, Adtchievement::class);

        $pirate = Pirate::where('member_id', $request->input('adtchievement.member_id'))
            ->firstOrFail();

        Assert::isInstanceOf($pirate, Pirate::class);

        $adtchievement->earnBy(
            $pirate,
            $points,
            $request->input('reason') ?? ''
        );

        return redirect()->action([PirateCrewController::class, 'index'], ['pirateCrew' => $pirateCrew->slug]);
    }

    public function remove(PirateCrew $pirateCrew, EarnedAdtchievement $adtchievement) : RedirectResponse
    {
        $adtchievement->delete();

        return redirect()->action([PirateCrewController::class, 'index'], ['pirateCrew' => $pirateCrew->slug]);
    }
}
