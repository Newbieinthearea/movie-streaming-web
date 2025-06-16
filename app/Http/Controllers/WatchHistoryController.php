<?php

namespace App\Http\Controllers;

use App\Models\WatchHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WatchHistoryController extends Controller
{
    /**
     * Remove the specified watch history item from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\WatchHistory  $watchHistory
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request, WatchHistory $watchHistory)
    {
        // Authorization Check
        if (Auth::id() !== $watchHistory->user_id) {
            abort(403, 'Unauthorized action.');
        }

        $watchHistory->delete();

        // Check if the request is an AJAX request
        if ($request->wantsJson()) {
            return response()->json(['status' => 'success', 'message' => 'Item removed from history.']);
        }

        // Fallback for non-AJAX requests
        return back()->with('success', 'Item removed from your watch history.');
    }
}