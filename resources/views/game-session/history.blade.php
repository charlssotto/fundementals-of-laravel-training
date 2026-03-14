<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $gameSession->name }} - Game History</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="p-6">
        <!-- Header -->
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-4xl font-bold text-gray-800">Game History</h1>
                <p class="text-gray-600 mt-2">Session: <span class="font-bold text-blue-600">{{ $gameSession->name }}</span></p>
            </div>
            <div class="flex items-center gap-4">
                <a href="{{ route('game.dashboard') }}" 
                   class="px-6 py-3 bg-gray-500 text-white rounded-lg font-semibold hover:bg-gray-600 transition">
                    ← Back to Dashboard
                </a>
                <a href="{{ route('logout') }}" 
                   class="px-6 py-3 bg-red-500 text-white rounded-lg font-semibold hover:bg-red-600 transition">
                    Logout
                </a>
            </div>
        </div>

        <!-- Game History List -->
        @if(count($gameHistory) > 0)
            <div class="space-y-4">
                @foreach($gameHistory as $index => $round)
                    <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-xl font-bold text-gray-800">Round {{ $index + 1 }}</h3>
                            <span class="inline-block px-4 py-2 rounded-full text-sm font-semibold
                                {{ $round['result'] === 'won' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ ucfirst($round['result']) }}
                            </span>
                        </div>

                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <p class="text-sm text-gray-600">Category</p>
                                <p class="text-lg font-semibold text-gray-800">{{ $round['category'] }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Word</p>
                                <p class="text-lg font-semibold text-gray-800">{{ strtoupper($round['word']) }}</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <p class="text-sm text-gray-600">Mistakes</p>
                                <p class="text-lg font-semibold text-orange-600">{{ $round['mistakes'] }}/6</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Date</p>
                                <p class="text-lg font-semibold text-gray-800">{{ \Carbon\Carbon::parse($round['created_at'])->format('M d, Y H:i') }}</p>
                            </div>
                        </div>

                        <!-- Guessed Letters -->
                        <div class="mb-4">
                            <p class="text-sm font-semibold text-gray-700 mb-2">Guessed Letters:</p>
                            <div class="flex flex-wrap gap-2">
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
                            <div>
                                <p class="text-sm font-semibold text-gray-700 mb-2">Incorrect Letters:</p>
                                <div class="flex flex-wrap gap-2">
                                    @foreach($round['incorrect_letters'] as $letter)
                                        <span class="px-3 py-1 rounded-full text-sm font-semibold bg-red-100 text-red-800">
                                            {{ strtoupper($letter) }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @else
            <div class="bg-white rounded-lg shadow-md p-12 text-center">
                <p class="text-gray-600 text-lg mb-6">No game history available yet. Start playing to see your history!</p>
                <a href="{{ route('game.session.show', $gameSession) }}" 
                   class="inline-block px-6 py-3 bg-blue-500 text-white rounded-lg font-semibold hover:bg-blue-600 transition">
                    Play Game
                </a>
            </div>
        @endif
    </div>
</body>
</html>
