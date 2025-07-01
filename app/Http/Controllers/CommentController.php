<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Site;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class CommentController extends Controller
{
    use AuthorizesRequests;

    public function store(Request $request, Site $site)
    {
        $this->authorize('addComment', $site);

        $request->validate([
            'comment' => 'required|string',
        ]);

        $site->comments()->create([
            'user_id' => Auth::id(),
            'comment' => $request->comment,
        ]);

        return redirect()->back()->with('success', 'Komentar berhasil ditambahkan.');
    }
}