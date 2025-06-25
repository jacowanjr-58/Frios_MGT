<?php

namespace App\Services\MetaAPI;

use Illuminate\Support\Facades\Http;

class MetaService
{
    protected $accessToken;
    protected $pageId;

    public function __construct($accessToken, $pageId)
    {
        $this->accessToken = $accessToken;
        $this->pageId = $pageId;
    }

    public function createPost($message)
    {
        return Http::post("https://graph.facebook.com/{$this->pageId}/feed", [
            'message' => $message,
            'access_token' => $this->accessToken
        ])->json();
    }
}
