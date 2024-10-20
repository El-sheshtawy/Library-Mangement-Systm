<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\DownloadResource;
use App\Models\Download;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DownloadController extends Controller
{
    /**
     * Display a listing of the user's downloads.
     */
    public function index(Request $request)
    {
        $this->authorize('view', Download::class);

        $user = Auth::user();

        // Fetch the authenticated user's downloads with related books
        $downloads = Download::where('user_id', $user->id)->with('book')->get();

        // Return the downloads as a collection of DownloadResource
        return DownloadResource::collection($downloads);
    }
}
