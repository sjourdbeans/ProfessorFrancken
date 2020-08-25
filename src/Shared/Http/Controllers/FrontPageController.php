<?php

declare(strict_types=1);

namespace Francken\Shared\Http\Controllers;

use DateTimeImmutable;
use DateTimeZone;
use Francken\Association\Activities\Activity;
use Francken\Association\FranckenVrij\Edition;
use Francken\Association\News\News;
use Illuminate\View\View;

class FrontPageController extends Controller
{
    public function index() : View
    {
        $today = new DateTimeImmutable(
            'now', new DateTimeZone('Europe/Amsterdam')
        );

        $latestEdition = Edition::query()
            ->latestEdition()
            ->first();

        $news = News::recent()->limit(3)->get();

        $activities = Activity::query()
             ->with(['signUpSettings', 'signUps'])
             ->withCount(['comments'])
            ->after($today)
            ->orderBy('start_date', 'asc')
            ->limit(5)
            ->get();

        return view('homepage/homepage', [
            'news' => $news,
            'activities' => $activities,
            'latest_edition' => $latestEdition,
        ]);
    }
}