<?php

namespace App\Http\Controllers;

use App\Models\GameSession;
use Illuminate\Http\Request;

class GameSessionController extends Controller
{
    public function dashboard()
    {
        $gameSessions = auth()->user()->gameSessions()->get();
        return view('dashboard', compact('gameSessions'));
    }

    public function create()
    {
        return view('game-session.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $gameSession = auth()->user()->gameSessions()->create($validated);

        return redirect()->route('game.session.show', $gameSession)
                       ->with('success', 'Game session "' . $gameSession->name . '" created successfully!');
    }

    public function show(GameSession $gameSession)
    {
        // Check if user owns this game session
        if (auth()->user()->id !== $gameSession->user_id) {
            abort(403, 'Unauthorized access to this game session.');
        }

        // Check if game session still has lives
        if (!$gameSession->isPlayable()) {
            return view('game-session.show', compact('gameSession'))->with('error', 'This game session has no lives left. Create a new one to play again!');
        }
        
        // Initialize game session if not already started
        if (!session()->has('word')) {
            $categories = [
                'Animal' => ['Lion', 'Monkey', 'Cat', 'Dog', 'Elephant', 'Giraffe', 'Zebra', 'Kangaroo', 'Panda', 'Dolphin'],
                'Programming' => ['Java', 'Python', 'Javascript', 'Ruby', 'PHP', 'Csharp', 'Go', 'Swift', 'Kotlin', 'Rust'],
                'Food' => ['Pizza', 'Burger', 'Pasta', 'Sushi', 'Taco', 'Salad', 'Steak', 'Ice Cream', 'Sandwich']
            ];
            
            $categoryName = array_rand($categories);
            $wordList = $categories[$categoryName];
            $word = $wordList[array_rand($wordList)];

            session([
                'word' => $word,
                'category' => $categoryName,
                'hint' => str_repeat('_ ', strlen($word)),
                'guessed_letters' => [],
                'incorrect_letters' => [],
                'mistakes' => 0,
                'game_session_id' => $gameSession->id,
                'lives' => $gameSession->lives
            ]);
        }
        
        return view('game-session.show', compact('gameSession'));
    }

    public function history(GameSession $gameSession)
    {
        // Check if user owns this game session
        if (auth()->user()->id !== $gameSession->user_id) {
            abort(403, 'Unauthorized access to this game session.');
        }

        $gameHistory = $gameSession->game_history ?? [];
        
        return view('game-session.history', compact('gameSession', 'gameHistory'));
    }
}

