<?php

namespace App\Http\Controllers\Marketing;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ScheduledSocialPost;

class ScheduledPostController
{
    public function indexFranchise(Request $request)
    {
        $query = ScheduledSocialPost::where('franchise_id', Auth::user()->franchise_id);

        if ($request->filled('status')) {
            $query->where('posted', $request->status === 'posted');
        }

        if ($request->filled('date')) {
            $query->whereDate('scheduled_for', $request->date);
        }

        $posts = $query->latest('scheduled_for')->get();

        return view('franchise_admin.marketing.scheduled-posts', compact('posts'));
    }

    public function indexCorporate(Request $request)
    {
        $query = ScheduledSocialPost::query();

        if ($request->filled('status')) {
            $query->where('posted', $request->status === 'posted');
        }

        if ($request->filled('date')) {
            $query->whereDate('scheduled_for', $request->date);
        }

        $posts = $query->latest('scheduled_for')->get();

        return view('corporate_admin.marketing.scheduled-posts', compact('posts'));
    }

    public function cancel($id)
    {
        $post = ScheduledSocialPost::findOrFail($id);
        $post->delete();

        return back()->with('message', 'Post canceled.');
    }

    public function reschedule(Request $request, $id)
    {
        $request->validate(['new_time' => 'required|date|after_or_equal:now']);
        $post = ScheduledSocialPost::findOrFail($id);
        $post->scheduled_for = $request->new_time;
        $post->save();

        return back()->with('message', 'Post rescheduled.');
    }
}
