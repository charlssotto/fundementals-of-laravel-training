<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $gameSession->name }} - Game History</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 min-h-screen text-gray-100">
    <div class="p-6">
        <!-- Header -->
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-4xl font-bold text-white">Game History</h1>
                <p class="text-gray-400 mt-2">Session: <span class="font-bold text-blue-400">{{ $gameSession->name }}</span></p>
            </div>
            <div class="flex items-center gap-4">
                <a href="{{ route('game.dashboard') }}" 
                   class="px-6 py-3 bg-gradient-to-r from-gray-600 to-gray-700 text-white rounded-lg font-semibold hover:from-gray-700 hover:to-gray-800 transition shadow-lg">
                    ← Back to Dashboard
                </a>
                <a href="{{ route('logout') }}" 
                   class="px-6 py-3 bg-gradient-to-r from-red-500 to-red-600 text-white rounded-lg font-semibold hover:from-red-600 hover:to-red-700 transition shadow-lg">
                    Logout
                </a>
            </div>
        </div>

        <!-- Game History List -->
        @if(count($gameHistory) > 0)
            <div class="space-y-4">
                @foreach($gameHistory as $index => $round)
                    <div class="bg-gray-800 rounded-lg shadow-lg p-6 hover:shadow-2xl transition border border-gray-700" style="min-height: 200px;">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-xl font-bold text-white">Round {{ $index + 1 }}</h3>
                            <span class="inline-block px-4 py-2 rounded-full text-sm font-semibold
                                {{ $round['result'] === 'won' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ ucfirst($round['result']) }}
                            </span>
                        </div>

                        <div class="grid grid-cols-3 gap-4 mb-4">
                            <!-- Left: Word and Date -->
                            <div class="space-y-3">
                                <div>
                                    <p class="text-sm text-gray-400">Word</p>
                                    <p class="text-lg font-semibold text-white">{{ strtoupper($round['word']) }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-400">Date</p>
                                    <p class="text-lg font-semibold text-white">{{ \Carbon\Carbon::parse($round['created_at'])->format('M d, Y H:i') }}</p>
                                </div>
                            </div>

                            <!-- Middle: Guessed and Incorrect Letters -->
                            <div class="flex flex-col items-center">
                                <!-- Guessed Letters -->
                                <div class="mb-3 flex flex-col items-center">
                                    <p class="text-sm font-semibold text-gray-300 mb-2">Guessed Letters:</p>
                                    <div class="flex flex-wrap gap-2 justify-center">
                                        @forelse($round['guessed_letters'] as $letter)
                                            @if(in_array($letter, $round['incorrect_letters']))
                                                <span class="px-3 py-1 rounded-full text-sm font-semibold bg-red-100 text-red-800 line-through">
                                                    {{ strtoupper($letter) }}
                                                </span>
                                            @else
                                                <span class="px-3 py-1 rounded-full text-sm font-semibold bg-green-100 text-green-800">
                                                    {{ strtoupper($letter) }}
                                                </span>
                                            @endif
                                        @empty
                                            <p class="text-gray-500 text-sm">No letters guessed</p>
                                        @endforelse
                                    </div>
                                </div>

                                <!-- Incorrect Letters -->
                                @if(count($round['incorrect_letters']) > 0)
                                    <div class="flex flex-col items-center">
                                        <p class="text-sm font-semibold text-gray-300 mb-2">Incorrect Letters:</p>
                                        <div class="flex flex-wrap gap-2 justify-center">
                                            @foreach($round['incorrect_letters'] as $letter)
                                                <span class="px-3 py-1 rounded-full text-sm font-semibold bg-red-100 text-red-800">
                                                    {{ strtoupper($letter) }}
                                                </span>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <!-- Right: Category and Mistakes -->
                            <div class="space-y-3">
                                <div>
                                    <p class="text-sm text-gray-400">Category</p>
                                    <p class="text-lg font-semibold text-white">{{ $round['category'] }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-400">Mistakes</p>
                                    <p class="text-lg font-semibold text-orange-400">{{ $round['mistakes'] }}/6</p>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="bg-gray-800 rounded-lg shadow-lg p-12 text-center border border-gray-700">
                <p class="text-gray-400 text-lg mb-6">No game history available yet. Start playing to see your history!</p>
                <a href="{{ route('game.session.show', $gameSession) }}" 
                   class="inline-block px-6 py-3 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-lg font-semibold hover:from-blue-600 hover:to-blue-700 transition shadow-lg">
                    Play Game
                </a>
            </div>
        @endif
    </div>
</body>
</html>
