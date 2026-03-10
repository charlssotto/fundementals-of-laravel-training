<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class GameController extends Controller
{
    private $categories = [
        'Animal' => ['Lion', 'Monkey', 'Cat'],
        'Programming' => ['Java', 'Python', 'Javascript']
    ];

    public function index()
    {
        // If no word is picked, start a new game
        if (!session()->has('word')) {
            $this->reset();
        }

        return view('game');
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

        // Validate input is a single letter
        $request->validate([
            'letter' => 'required|string|size:1|regex:/^[a-z]$/i'
        ]);

        $actualWord = strtolower(session('word'));
        $guessedLetters = session('guessed_letters', []);
        $hint = session('hint');

        // Check if letter already guessed
        if (in_array($letter, $guessedLetters)) {
            session()->flash('letter_error', 'You already guessed this letter!');
        } elseif (strpos($actualWord, $letter) !== false) {
            // Letter is in the word - update hint
            $guessedLetters[] = $letter;
            $hint = $this->updateHint($actualWord, $guessedLetters);
            session(['guessed_letters' => $guessedLetters, 'hint' => $hint]);
            session()->flash('letter_status', "Correct! The letter '$letter' is in the word! 🎉");

            // Check if word is complete
            if (strpos($hint, '_') === false) {
                session()->flash('status', 'Congratulations! You found the word: ' . session('word') . ' 🎊');
                $this->reset();
            }
        } else {
            // Letter is not in the word
            $guessedLetters[] = $letter;
            session(['guessed_letters' => $guessedLetters]);
            session()->flash('letter_error', "Wrong! The letter '$letter' is not in the word. ❌");
        }

        return redirect()->route('game.index');
    }

    private function updateHint($word, $guessedLetters)
    {
        $hint = '';
        for ($i = 0; $i < strlen($word); $i++) {
            if (in_array($word[$i], $guessedLetters)) {
                $hint .= $word[$i] . ' ';
            } else {
                $hint .= '_ ';
            }
        }
        return $hint;
    }

    public function reset()
    {
        $categoryName = array_rand($this->categories);
        $wordList = $this->categories[$categoryName];
        $word = $wordList[array_rand($wordList)];

        session([
            'word' => $word,
            'category' => $categoryName,
            'hint' => str_repeat('_ ', strlen($word)),
            'guessed_letters' => []
        ]);

        return redirect()->route('game.index');
    }
}
