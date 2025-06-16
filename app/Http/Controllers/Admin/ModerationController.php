<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlockedContent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ModerationController extends Controller
{
    /**
     * Display a listing of blocked content and the form to add more.
     */
    public function index()
    {
        $blockedContent = BlockedContent::orderBy('created_at', 'desc')->paginate(15);
        return view('admin.moderation.index', compact('blockedContent'));
    }

    /**
     * Store a newly blocked content record in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'tmdb_id' => 'required|integer|unique:blocked_content,tmdb_id',
            'type' => 'required|string|in:movie,tv',
            'reason' => 'nullable|string|max:255',
        ]);

        BlockedContent::create($request->all());

        // Clear the cache so the blocklist is immediately updated across the site
        Cache::forget('blocked_content_ids');

        return back()->with('success', 'Content (ID: ' . $request->tmdb_id . ') has been successfully blocked.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BlockedContent $blockedContent)
    {
        $blockedContent->delete();

        // Clear the cache so the blocklist is immediately updated
        Cache::forget('blocked_content_ids');

        return back()->with('success', 'Blocked content has been successfully removed.');
    }
}