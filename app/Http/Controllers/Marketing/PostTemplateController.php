<?php

namespace App\Http\Controllers\Marketing;

use App\Models\PostTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PostTemplateController
{
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string',
        ]);

        PostTemplate::create([
            'title' => $request->title,
            'body' => $request->body,
            'created_by' => Auth::id()
        ]);

        return redirect()->back()->with('message', 'Template saved.');
    }

    public function index()
    {
        $templates = PostTemplate::latest()->get();
        return view('franchise_admin.marketing.select-template', compact('templates'));
    }
}
