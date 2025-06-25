<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use App\Models\ScheduledSocialPost;

class PublishScheduledPost implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $post;

    public function __construct(ScheduledSocialPost $post)
    {
        $this->post = $post;
    }

    public function handle()
    {
        $response = Http::post("https://graph.facebook.com/{$this->post->meta_page_id}/feed", [
            'message' => $this->post->message,
            'access_token' => $this->post->access_token
        ])->json();

        $this->post->posted = true;
        $this->post->save();
    }
}
