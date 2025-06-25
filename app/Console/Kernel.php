<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Models\ScheduledSocialPost;
use App\Jobs\PublishScheduledPost;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule): void
    {
        $schedule->call(function () {
            $now = now();
            $posts = ScheduledSocialPost::where('posted', false)
                ->where('scheduled_for', '<=', $now)
                ->get();

            foreach ($posts as $post) {
                PublishScheduledPost::dispatch($post);
            }
        })->everyMinute();
    }
}
