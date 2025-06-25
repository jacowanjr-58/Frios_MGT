<?php

namespace App\Http\Controllers\Marketing;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use App\Models\FranchiseMetaToken;

class MetaOAuthController
{
    public function redirectToMeta()
    {
        $clientId = env('META_CLIENT_ID');
        $redirectUri = route('meta.callback');
        $scopes = 'pages_manage_posts,pages_read_engagement,pages_show_list';
        return redirect("https://www.facebook.com/v19.0/dialog/oauth?client_id={$clientId}&redirect_uri={$redirectUri}&scope={$scopes}");
    }

    public function handleCallback(Request $request)
    {
        $clientId = env('META_CLIENT_ID');
        $clientSecret = env('META_CLIENT_SECRET');
        $redirectUri = route('meta.callback');

        $response = Http::get('https://graph.facebook.com/v19.0/oauth/access_token', [
            'client_id' => $clientId,
            'redirect_uri' => $redirectUri,
            'client_secret' => $clientSecret,
            'code' => $request->code,
        ])->json();

        $pageData = Http::get('https://graph.facebook.com/me/accounts', [
            'access_token' => $response['access_token']
        ])->json();

        $page = $pageData['data'][0];

        FranchiseMetaToken::updateOrCreate(
            ['franchisee_id' => Auth::user()->franchisee_id],
            [
                'meta_page_id' => $page['id'],
                'meta_access_token' => $page['access_token'],
                'token_expires_at' => now()->addDays(60),
            ]
        );

        return redirect()->route('dashboard')->with('message', 'Meta account connected!');
    }
}
