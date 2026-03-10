<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laravel Guessing Game</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 h-screen flex items-center justify-center">

    <div class="bg-white p-8 rounded-lg shadow-md w-96 text-center">
        <h1 class="text-2xl font-bold mb-4">Guess the Word!</h1>
        
        <p class="text-gray-600">Category: <span class="font-bold text-blue-600">{{ session('category') }}</span></p>
        <div class="text-3xl tracking-widest my-6 font-mono">
            {{ session('hint') }}
        </div>

        @if(session('status'))
            <div class="mb-4 text-green-600 font-bold">{{ session('status') }}</div>
        @endif

        @if(session('error'))
            <div class="mb-4 text-red-500">{{ session('error') }}</div>
        @endif

        <!-- Letter Guess Section -->
        <div class="bg-blue-50 p-4 rounded mb-6 border border-blue-200">
            <h2 class="text-lg font-semibold mb-3 text-gray-700">Guess a Letter</h2>
            
            @if(session('letter_status'))
                <div class="mb-3 text-green-600 font-bold">{{ session('letter_status') }}</div>
            @endif

            @if(session('letter_error'))
                <div class="mb-3 text-red-500">{{ session('letter_error') }}</div>
            @endif

            <form action="{{ route('game.guess_letter') }}" method="POST" class="space-y-3">
                @csrf
                <input type="text" name="letter" maxlength="1" autofocus required
                       placeholder="Enter a letter"
                       class="w-full border-2 border-gray-300 p-2 rounded focus:outline-none focus:border-blue-500 text-center uppercase"
                       inputmode="text">
                
                @error('letter')
                    <div class="text-red-500 text-sm">{{ $message }}</div>
                @enderror

                <button type="submit" class="w-full bg-green-500 text-white py-2 rounded hover:bg-green-600 transition">
                    Guess Letter
                </button>
            </form>

            @if(session('guessed_letters'))
                <div class="mt-4 text-sm">
                    <p class="text-gray-600 font-semibold">Guessed Letters:</p>
                    <div class="flex flex-wrap gap-2 mt-2">
                        @foreach(session('guessed_letters') as $guessedLetter)
                            <span class="bg-blue-200 text-blue-800 px-2 py-1 rounded">{{ strtoupper($guessedLetter) }}</span>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <!-- Full Word Guess Section -->
        <div class="bg-purple-50 p-4 rounded border border-purple-200">
            <h2 class="text-lg font-semibold mb-3 text-gray-700">Or Guess the Full Word</h2>
            <form action="{{ route('game.guess') }}" method="POST" class="space-y-3">
                @csrf
                <input type="text" name="guess" autofocus 
                       placeholder="Enter the word"
                       class="w-full border-2 border-gray-300 p-2 rounded focus:outline-none focus:border-purple-500 text-center">
                
                <button type="submit" class="w-full bg-purple-500 text-white py-2 rounded hover:bg-purple-600 transition">
                    Submit Word
                </button>
            </form>
        </div>

        <a href="{{ route('game.reset') }}" class="block mt-6 text-sm text-gray-400 hover:text-gray-600 underline">
            New Word / Reset
        </a>
    </div>

</body>
</html>