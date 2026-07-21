<?php

namespace App\Http\Controllers;

use App\Models\AiLog;
use App\Services\GeminiAiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AiAssistantController extends Controller
{
    public function index()
    {
        $userId = Auth::id();
        $aiLogs = AiLog::where('user_id', $userId)->latest()->take(10)->get();

        return view('ai.index', compact('aiLogs'));
    }

    public function ask(Request $request)
    {
        $request->validate([
            'prompt' => ['required', 'string', 'max:500'],
        ]);

        $userId = Auth::id();
        $aiService = new GeminiAiService();
        $response = $aiService->askFinancialAssistant($userId, $request->prompt);

        return redirect()->back()->with([
            'latest_query' => $request->prompt,
            'latest_answer' => $response['answer'],
        ]);
    }
}
