<?php

namespace App\Http\Controllers;

use App\Models\GameSession;
use Illuminate\Http\Request;

class GameController extends Controller
{
    private $categories = [
        'Animal' => ['Lion', 'Monkey', 'Cat', 'Dog', 'Elephant', 'Giraffe', 'Zebra', 'Kangaroo', 'Panda', 'Dolphin'],
        'Programming' => ['Java', 'Python', 'Javascript', 'Ruby', 'PHP', 'Csharp', 'Go', 'Swift', 'Kotlin', 'Rust'],
        'Food' => ['Pizza', 'Burger', 'Pasta', 'Sushi', 'Taco', 'Salad', 'Steak', 'Ice Cream', 'Sandwich']
    ];

    private $keyboardRows = [
        'row1' => ['Q', 'W', 'E', 'R', 'T', 'Y', 'U', 'I', 'O', 'P'],
        'row2' => ['A', 'S', 'D', 'F', 'G', 'H', 'J', 'K', 'L'],
        'row3' => ['Z', 'X', 'C', 'V', 'B', 'N', 'M']
    ];

    public function index()
    {
        // If no word is picked, start a new game
        if (!session()->has('word')) {
            $this->reset();
        }

        return view('game', ['keyboardRows' => $this->keyboardRows]);
    }

    public function guess(Request $request)
    {
        $userGuess = strtolower($request->input('guess'));
        $actualWord = strtolower(session('word'));

        if ($userGuess === $actualWord) {
            session()->flash('status', 'Correct! It was ' . session('word') . ' 🎉');
            $this->reset(); // Pick a new word for next time
        } else {
            session()->flash('error', 'Wrong guess! Try again.');
        }

        return redirect()->route('game.index');
    }

    public function guessLetter(Request $request)
    {
        $letter = strtolower($request->input('letter'));
        $actualWord = strtolower(session('word'));
        
        // Validate: only single letter
        if (strlen($letter) !== 1 || !ctype_alpha($letter)) {
            session()->flash('letterError', 'Please enter a single letter only.');
            return redirect()->route('game.index');
        }

        $guessedLetters = session('guessed_letters', []);
        $incorrectLetters = session('incorrect_letters', []);
        $mistakes = session('mistakes', 0);
        $lives = session('lives', 3);
        $gameSessionId = session('game_session_id');

        // Check if letter was already guessed
        if (in_array($letter, $guessedLetters)) {
            session()->flash('letterError', 'You already guessed this letter!');
            return redirect()->route('game.index');
        }

        if (strpos($actualWord, $letter) !== false) {
            // Correct letter - add to guessed letters and update hint
            $guessedLetters[] = $letter;
            session(['guessed_letters' => $guessedLetters]);
            
            // Update hint
            $this->updateHint();
            
            // Check if word is complete
            $hint = session('hint');
            if (strpos($hint, '_') === false) {
                session()->flash('status', 'Correct! It was ' . session('word') . ' 🎉');
                $this->reset();
            } else {
                session()->flash('letterStatus', '✓ Correct! "' . strtoupper($letter) . '" is in the word.');
            }
        } else {
            // Wrong letter
            $incorrectLetters[] = $letter;
            $guessedLetters[] = $letter;
            $mistakes++;
            session(['guessed_letters' => $guessedLetters, 'incorrect_letters' => $incorrectLetters, 'mistakes' => $mistakes]);
            
            if ($mistakes >= 6) {
                // Deduct one life from game session
                if ($gameSessionId) {
                    $gameSession = GameSession::find($gameSessionId);
                    if ($gameSession) {
                        $gameSession->deductLife();
                        $lives = $gameSession->lives;
                    }
                }
                
                session(['lives' => $lives]);

                if ($lives > 0) {
                    // Lost this round, but still have lives left
                    session()->flash('gameOver', 'Game Over! The word was: ' . session('word') . ' ❌ Lives left: ' . $lives);
                    $this->reset();
                    return redirect()->route('game.index');
                } else {
                    // No lives left - close game session and redirect to dashboard
                    session()->forget(['word', 'category', 'hint', 'guessed_letters', 'incorrect_letters', 'mistakes', 'lives', 'game_session_id']);
                    return redirect()->route('game.dashboard')->with('error', 'Game Over! No lives left. Create a new session to play again!');
                }
            } else {
                session()->flash('letterError', '✗ Wrong! "' . strtoupper($letter) . '" is not in the word. (Mistakes: ' . $mistakes . '/6)');
            }
        }

        return redirect()->route('game.index');
    }

    private function updateHint()
    {
        $word = strtolower(session('word'));
        $guessedLetters = session('guessed_letters', []);
        $hint = '';

        for ($i = 0; $i < strlen($word); $i++) {
            if (in_array($word[$i], $guessedLetters)) {
                $hint .= strtoupper($word[$i]) . ' ';
            } else {
                $hint .= '_ ';
            }
        }

        session(['hint' => $hint]);
    }

    public function reset()
    {
        $categoryName = array_rand($this->categories);
        $wordList = $this->categories[$categoryName];
        $word = $wordList[array_rand($wordList)];
        
        // Preserve lives and game_session_id while resetting the game
        $lives = session('lives', 3);
        $gameSessionId = session('game_session_id');

        session([
            'word' => $word,
            'category' => $categoryName,
            'hint' => str_repeat('_ ', strlen($word)),
            'guessed_letters' => [],
            'incorrect_letters' => [],
            'mistakes' => 0,
            'lives' => $lives,
            'game_session_id' => $gameSessionId
        ]);

        return redirect()->route('game.index');
    }
}
