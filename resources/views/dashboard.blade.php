<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Game Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="p-6">
        <!-- Header -->
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-4xl font-bold text-gray-800">Welcome, {{ Auth::user()->name }}! 👋</h1>
                <p class="text-gray-600 mt-2">Your Game Sessions</p>
            </div>
            <div class="flex items-center gap-4">
                <a href="{{ route('game.session.create') }}" 
                   class="px-6 py-3 bg-blue-500 text-white rounded-lg font-semibold hover:bg-blue-600 transition">
                    + New Game Session
                </a>
                <a href="{{ route('logout') }}" 
                   class="px-4 py-2 bg-red-500 text-white rounded-lg font-semibold hover:bg-red-600 transition">
                    Logout
                </a>
            </div>
        </div>

        <!-- Success Message -->
        @if(session('success'))
            <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 text-green-700 rounded">
                {{ session('success') }}
            </div>
        @endif

        <!-- Game Sessions Grid -->
        @if($gameSessions->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($gameSessions as $session)
                    <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition">
                        <h3 class="text-xl font-bold text-gray-800 mb-2">{{ $session->name }}</h3>
                        <p class="text-gray-600 mb-2">
                            <span class="inline-block px-3 py-1 rounded-full text-sm font-semibold
                                {{ $session->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                {{ ucfirst($session->status) }}
                            </span>
                        </p>
                        <p class="text-sm text-gray-500 mb-4">Created: {{ $session->created_at->format('M d, Y') }}</p>
                        <a href="{{ route('game.session.show', $session) }}" 
                           class="inline-block w-full text-center px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 transition font-semibold">
                            Play Session
                        </a>
                    </div>
                @endforeach
            </div>
        @else
            <div class="bg-white rounded-lg shadow-md p-12 text-center">
                <p class="text-gray-600 text-lg mb-6">You haven't created any game sessions yet.</p>
                <a href="{{ route('game.session.create') }}" 
                   class="inline-block px-6 py-3 bg-blue-500 text-white rounded-lg font-semibold hover:bg-blue-600 transition">
                    Create Your First Game Session
                </a>
            </div>
        @endif
    </div>
</body>
</html>
