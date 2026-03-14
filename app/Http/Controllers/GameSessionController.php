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

            $hints = [
                'lion' => 'King of the jungle, roars loudly',
                'monkey' => 'Swings from trees and eats bananas',
                'cat' => 'Purrs and has whiskers',
                'dog' => 'Barks and wags its tail',
                'elephant' => 'Has a long trunk and large ears',
                'giraffe' => 'Tallest land animal with a long neck',
                'zebra' => 'Black and white striped horse-like animal',
                'kangaroo' => 'Australian animal that hops',
                'panda' => 'Black and white bear that eats bamboo',
                'dolphin' => 'Smart ocean mammal that squeaks',
                'java' => 'Island in Indonesia, also a programming language',
                'python' => 'Snake-like programming language',
                'javascript' => 'Language for web browsers',
                'ruby' => 'Red precious gemstone and a programming language',
                'php' => 'Server-side scripting language for web',
                'csharp' => 'Programming language by Microsoft (C#)',
                'go' => 'Simple programming language created by Google',
                'swift' => 'Fast programming language for Apple devices',
                'kotlin' => 'Modern programming language for Java',
                'rust' => 'Programming language known for safety and performance',
                'pizza' => 'Italian dish with cheese and toppings on a round base',
                'burger' => 'Sandwich with meat patty and buns',
                'pasta' => 'Italian noodles, often served with sauce',
                'sushi' => 'Japanese dish with rice and seafood',
                'taco' => 'Mexican dish with meat in a shell',
                'salad' => 'Dish with mixed vegetables',
                'steak' => 'Grilled meat cut from beef',
                'ice cream' => 'Cold sweet frozen dessert',
                'sandwich' => 'Two slices of bread with filling between'
            ];
            
            $categoryName = array_rand($categories);
            $wordList = $categories[$categoryName];
            $word = $wordList[array_rand($wordList)];

            $wordHint = $hints[strtolower($word)] ?? 'No hint available';

            session([
                'word' => $word,
                'category' => $categoryName,
                'hint' => str_repeat('_ ', strlen($word)),
                'guessed_letters' => [],
                'incorrect_letters' => [],
                'mistakes' => 0,
                'word_hint' => $wordHint,
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

    public function destroy(GameSession $gameSession)
    {
        // Check if user owns this game session
        if (auth()->user()->id !== $gameSession->user_id) {
            abort(403, 'Unauthorized to delete this game session.');
        }

        $name = $gameSession->name;
        $gameSession->delete();

        return redirect()->route('game.dashboard')
                       ->with('success', 'Game session "' . $name . '" deleted successfully!');
    }
}

