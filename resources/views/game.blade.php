<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laravel Guessing Game</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 min-h-screen flex items-center justify-center p-4 text-gray-100">
    <div class="absolute top-4 left-4">
        <a href="{{ route('game.dashboard') }}" 
           class="px-4 py-2 bg-gradient-to-r from-gray-600 to-gray-700 text-white rounded-lg font-semibold hover:from-gray-700 hover:to-gray-800 transition shadow-lg">
            ← Back to Dashboard
        </a>
    </div>

    <div class="absolute top-4 right-4 flex items-center gap-4">
        <span class="text-gray-300 font-semibold">👤 {{ Auth::user()->name }}</span>
        <a href="{{ route('logout') }}" 
           class="px-4 py-2 bg-gradient-to-r from-red-600 to-red-700 text-white rounded-lg font-semibold hover:from-red-700 hover:to-red-800 transition shadow-lg">
            Logout
        </a>
    </div>

    <div class="bg-gray-800 p-8 rounded-lg shadow-2xl w-full max-w-6xl border border-gray-700">
        <h1 class="text-4xl font-bold mb-6 text-center text-white">🎮 Guess the Word! 🎮</h1>
        
        <!-- Top Stats Bar -->
        <div class="grid grid-cols-3 gap-4 mb-6">
            <div class="text-center p-4 bg-gray-700 rounded-lg border border-gray-600">
                <p class="text-gray-400 font-semibold text-sm">Category</p>
                <p class="text-2xl font-bold text-cyan-400 mt-2">{{ session('category') }}</p>
            </div>
            
            <!-- Lives Counter -->
            <div class="p-4 bg-gray-700 rounded-lg border-2 border-purple-500">
                <p class="text-sm font-semibold text-purple-300 mb-2">❤️ Lives</p>
                <p class="text-3xl font-bold {{ session('lives', 3) === 0 ? 'text-red-400' : 'text-purple-400' }}">{{ session('lives', 3) }}/3</p>
                <div class="w-full bg-gray-600 rounded-full h-3 mt-2">
                    <div class="bg-gradient-to-r from-purple-500 to-purple-600 h-3 rounded-full transition-all" style="width: {{ (session('lives', 3) / 3) * 100 }}%"></div>
                </div>
            </div>
            
            <!-- Mistakes Counter -->
            <div class="p-4 bg-gray-700 rounded-lg border-2 border-yellow-500">
                <p class="text-sm font-semibold text-yellow-300 mb-2">⚠️ Mistakes</p>
                <p class="text-3xl font-bold">{{ session('mistakes', 0) }}<span class="text-lg text-gray-400">/6</span></p>
                <div class="w-full bg-gray-600 rounded-full h-3 mt-2">
                    <div class="bg-gradient-to-r from-yellow-500 to-orange-600 h-3 rounded-full transition-all" style="width: {{ (session('mistakes', 0) / 6) * 100 }}%"></div>
                </div>
            </div>
        </div>
        <!-- Word Display and Hint -->
        <div class="grid grid-cols-2 gap-6 mb-8">
            <div class="flex flex-col justify-center">
                <div class="text-6xl tracking-widest font-mono font-bold text-center p-6 bg-gray-700 rounded-lg border-3 border-cyan-500 min-h-24 flex items-center justify-center text-cyan-400 shadow-lg">
                    {{ session('hint') }}
                </div>
            </div>
            
            <div class="flex flex-col justify-center">
                @if(session('word_hint'))
                    <div class="p-5 bg-gray-700 rounded-lg border-2 border-blue-500 h-full flex flex-col justify-center shadow-lg">
                        <p class="text-sm font-semibold text-blue-300 mb-3">💡 Hint:</p>
                        <p class="text-blue-200 font-medium text-lg">{{ session('word_hint') }}</p>
                    </div>
                @else
                    <div class="p-5 bg-gray-700 rounded-lg border-2 border-gray-600 shadow-lg"></div>
                @endif
            </div>
        </div>

        <!-- Status Messages -->
        <div class="mb-6">
            @if(session('status'))
                <div class="p-4 text-green-300 font-bold bg-gray-700 rounded-lg border-2 border-green-500 text-center shadow-lg">{{ session('status') }}</div>
            @endif

            @if(session('error'))
                <div class="p-4 text-red-300 bg-gray-700 rounded-lg border-2 border-red-500 text-center shadow-lg">{{ session('error') }}</div>
            @endif

            @if(session('gameOver'))
                <div class="p-4 text-white font-bold bg-gray-700 rounded-lg text-lg border-2 border-red-600 text-center shadow-lg">
                    ☠️ {{ session('gameOver') }}
                </div>
            @endif
        </div>

        <!-- Guessing Sections -->
        <div class="grid grid-cols-2 gap-6 mb-6">
            <!-- Letter Guessing Section -->
            <div class="border-2 border-gray-600 rounded-lg p-6 bg-gray-700 shadow-lg">
                <h2 class="text-xl font-semibold mb-4 text-white">🔤 Guess by Letter</h2>
                
                @if(session('letterStatus'))
                    <div class="mb-3 p-3 text-green-300 font-semibold bg-gray-600 rounded-lg text-sm border border-green-500 shadow-md">
                        {{ session('letterStatus') }}
                    </div>
                @endif

                @if(session('letterError'))
                    <div class="mb-3 p-3 text-red-300 bg-gray-600 rounded-lg text-sm border border-red-500 shadow-md">
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
                               placeholder="Enter a letter"
                               class="w-full border-2 border-gray-500 bg-gray-600 text-white p-3 rounded-lg focus:outline-none focus:border-cyan-400 text-center text-2xl uppercase font-bold placeholder-gray-400"
                               style="letter-spacing: 0.2rem;"
                               {{ $isGameOver ? 'disabled' : '' }}>
                        <p class="text-xs text-gray-400 mt-2">A single letter (A-Z)</p>
                    </div>
                    
                    <button type="submit" {{ $isGameOver ? 'disabled' : '' }} class="w-full bg-gradient-to-r from-green-600 to-green-700 text-white py-3 rounded-lg hover:from-green-700 hover:to-green-800 transition font-semibold text-lg {{ $isGameOver ? 'cursor-not-allowed opacity-50' : '' }} shadow-lg">
                        Guess Letter
                    </button>
                </form>
            </div>

            <!-- Full Word Guess Section -->
            <div class="border-2 border-gray-600 rounded-lg p-6 bg-gray-700 flex flex-col justify-between shadow-lg">
                <div>
                    <h2 class="text-xl font-semibold mb-4 text-white">📝 Or Guess the Full Word</h2>
                    <form action="{{ route('game.guess') }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <input type="text" name="guess" placeholder="Enter the full word"
                                   class="w-full border-2 border-gray-500 bg-gray-600 text-white p-3 rounded-lg focus:outline-none focus:border-cyan-400 text-center text-xl font-semibold uppercase placeholder-gray-400">
                            <p class="text-xs text-gray-400 mt-2">Type the complete word</p>
                        </div>
                        
                        <button type="submit" class="w-full bg-gradient-to-r from-blue-600 to-blue-700 text-white py-3 rounded-lg hover:from-blue-700 hover:to-blue-800 transition font-semibold text-lg shadow-lg">
                            Submit Full Guess
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="text-center">
            <a href="{{ route('game.reset') }}" class="text-sm text-gray-400 hover:text-gray-200 underline font-medium transition">
                🔄 New Word / Reset Game
            </a>
        </div>
    </div>

</body>
</html>