<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laravel Guessing Game</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center p-4">

    <div class="bg-white p-8 rounded-lg shadow-md w-full max-w-md text-center">
        <h1 class="text-2xl font-bold mb-4">Guess the Word!</h1>
        
        <p class="text-gray-600 mb-2">Category: <span class="font-bold text-blue-600">{{ session('category') }}</span></p>
        
        <!-- Mistakes Counter -->
        <div class="mb-4 p-3 bg-yellow-50 rounded border-2 border-yellow-200">
            <p class="text-sm font-semibold text-yellow-800">Mistakes: 
                <span class="text-lg">{{ session('mistakes', 0) }}<span class="text-gray-500">/6</span></span>
            </p>
            <div class="w-full bg-yellow-200 rounded-full h-2 mt-2">
                <div class="bg-yellow-600 h-2 rounded-full transition-all" style="width: {{ (session('mistakes', 0) / 6) * 100 }}%"></div>
            </div>
        </div>
        <div class="text-4xl tracking-widest my-6 font-mono font-bold min-h-16 flex items-center justify-center">
            {{ session('hint') }}
        </div>

        @if(session('status'))
            <div class="mb-4 p-3 text-green-600 font-bold bg-green-50 rounded">{{ session('status') }}</div>
        @endif

        @if(session('error'))
            <div class="mb-4 p-3 text-red-500 bg-red-50 rounded">{{ session('error') }}</div>
        @endif

        @if(session('gameOver'))
            <div class="mb-4 p-3 text-white font-bold bg-red-600 rounded text-lg border-2 border-red-800">
                ☠️ {{ session('gameOver') }}
            </div>
        @endif

        <!-- Letter Guessing Section -->
        <div class="mb-6 border-t pt-6">
            <h2 class="text-lg font-semibold mb-3 text-gray-700">Guess by Letter</h2>
            
            @if(session('letterStatus'))
                <div class="mb-3 p-2 text-green-600 font-semibold bg-green-50 rounded text-sm">
                    {{ session('letterStatus') }}
                </div>
            @endif

            @if(session('letterError'))
                <div class="mb-3 p-2 text-red-500 bg-red-50 rounded text-sm">
                    {{ session('letterError') }}
                </div>
            @endif

            @php
                $isGameOver = session('mistakes', 0) >= 6;
            @endphp

            <form id="letterForm" action="{{ route('game.guessLetter') }}" method="POST" class="space-y-3" {{ $isGameOver ? 'style=opacity:0.5' : '' }}>
                @csrf
                <div>
                    <input type="text" id="letterInput" name="letter" maxlength="1" autofocus required
                           placeholder="Enter a letter or click below"
                           class="w-full border-2 border-gray-300 p-2 rounded focus:outline-none focus:border-green-500 text-center text-lg uppercase"
                           style="letter-spacing: 0.2rem;"
                           {{ $isGameOver ? 'disabled' : '' }}>
                    <p class="text-xs text-gray-500 mt-1">A single letter (A-Z)</p>
                </div>
                
                <!-- On-Screen Keyboard -->
                <div class="mt-4 p-3 bg-gray-50 rounded">
                    @php
                        $guessedLetters = session('guessed_letters', []);
                        $incorrectLetters = session('incorrect_letters', []);
                    @endphp
                    
                    @foreach($keyboardRows as $row)
                        <div class="flex gap-1 justify-center mb-1">
                            @foreach($row as $letter)
                                @php
                                    $isGuessed = in_array(strtolower($letter), $guessedLetters);
                                    $isIncorrect = in_array(strtolower($letter), $incorrectLetters);
                                @endphp
                                
                                @if($isGuessed)
                                    @if($isIncorrect)
                                        <button type="button" disabled 
                                                class="px-2 py-1 text-xs font-bold rounded bg-red-300 text-red-700 cursor-not-allowed opacity-50 line-through">
                                            {{ $letter }}
                                        </button>
                                    @else
                                        <button type="button" disabled 
                                                class="px-2 py-1 text-xs font-bold rounded bg-green-300 text-green-700 cursor-not-allowed opacity-75">
                                            {{ $letter }}
                                        </button>
                                    @endif
                                @else
                                    <button type="button" 
                                            onclick="guessLetterClick('{{ strtolower($letter) }}')"
                                            {{ $isGameOver ? 'disabled' : '' }}
                                            class="px-2 py-1 text-xs font-bold rounded bg-blue-400 text-white hover:bg-blue-500 transition active:scale-95 {{ $isGameOver ? 'cursor-not-allowed opacity-50' : '' }}">
                                        {{ $letter }}
                                    </button>
                                @endif
                            @endforeach
                        </div>
                    @endforeach
                </div>
                
                <button type="submit" {{ $isGameOver ? 'disabled' : '' }} class="w-full bg-green-500 text-white py-2 rounded hover:bg-green-600 transition font-semibold {{ $isGameOver ? 'cursor-not-allowed opacity-50' : '' }}">
                    Guess Letter
                </button>
            </form>

            <script>
                function guessLetterClick(letter) {
                    document.getElementById('letterInput').value = letter.toUpperCase();
                    document.getElementById('letterForm').submit();
                }
            </script>

            <!-- Display Guessed Letters -->
            @if(session('guessed_letters') && count(session('guessed_letters')) > 0)
                <div class="mt-4 p-3 bg-blue-50 rounded">
                    <p class="text-xs text-gray-600 mb-2">Guessed Letters:</p>
                    <div class="flex flex-wrap gap-2 justify-center">
                        @foreach(session('guessed_letters') as $g)
                            @if(!in_array($g, session('incorrect_letters', [])))
                                <span class="bg-green-200 text-green-800 px-2 py-1 rounded text-sm font-semibold">
                                    {{ strtoupper($g) }}
                                </span>
                            @else
                                <span class="bg-red-200 text-red-800 px-2 py-1 rounded text-sm font-semibold line-through">
                                    {{ strtoupper($g) }}
                                </span>
                            @endif
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Display Incorrect Letters -->
            @if(session('incorrect_letters') && count(session('incorrect_letters')) > 0)
                <div class="mt-3 p-3 bg-red-50 rounded">
                    <p class="text-xs text-gray-600 mb-2">Incorrect Guesses:</p>
                    <p class="text-sm font-semibold text-red-600">
                        {{ implode(', ', array_map('strtoupper', session('incorrect_letters'))) }}
                    </p>
                </div>
            @endif
        </div>

        <!-- Full Word Guess Section -->
        <div class="border-t pt-6">
            <h2 class="text-lg font-semibold mb-3 text-gray-700">Or Guess the Full Word</h2>
            <form action="{{ route('game.guess') }}" method="POST" class="space-y-3">
                @csrf
                <input type="text" name="guess" placeholder="Enter the full word"
                       class="w-full border-2 border-gray-300 p-2 rounded focus:outline-none focus:border-blue-500 text-center">
                
                <button type="submit" class="w-full bg-blue-500 text-white py-2 rounded hover:bg-blue-600 transition font-semibold">
                    Submit Full Guess
                </button>
            </form>
        </div>

        <a href="{{ route('game.reset') }}" class="block mt-6 text-sm text-gray-400 hover:text-gray-600 underline">
            New Word / Reset
        </a>
    </div>

</body>
</html>