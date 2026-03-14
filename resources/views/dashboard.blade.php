<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Game Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 min-h-screen text-gray-100">
    <div class="p-6">
        <!-- Header -->
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-4xl font-bold text-white">Welcome, {{ Auth::user()->name }}! 👋</h1>
                <p class="text-gray-400 mt-2">Your Game Sessions</p>
            </div>
            <div class="flex items-center gap-4">
                <a href="{{ route('game.session.create') }}" 
                   class="px-6 py-3 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-lg font-semibold hover:from-blue-600 hover:to-blue-700 transition shadow-lg">
                    + New Game Session
                </a>
                <a href="{{ route('logout') }}" 
                   class="px-6 py-3 bg-gradient-to-r from-red-500 to-red-600 text-white rounded-lg font-semibold hover:from-red-600 hover:to-red-700 transition shadow-lg">
                    Logout
                </a>
            </div>
        </div>

        <!-- Success Message -->
        @if(session('success'))
            <div class="mb-6 p-4 bg-green-900 border-l-4 border-green-400 text-green-200 rounded">
                {{ session('success') }}
            </div>
        @endif

        <!-- Game Sessions Grid -->
        @if($gameSessions->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($gameSessions as $session)
                    <div class="bg-gray-800 rounded-lg shadow-lg p-6 hover:shadow-2xl hover:bg-gray-750 transition border border-gray-700">
                        <h3 class="text-xl font-bold text-white mb-2">{{ $session->name }}</h3>
                        <p class="text-gray-400 mb-2">
                            <span class="inline-block px-3 py-1 rounded-full text-sm font-semibold
                                {{ $session->status === 'active' ? 'bg-green-900 text-green-300' : 'bg-gray-700 text-gray-300' }}">
                                {{ ucfirst($session->status) }}
                            </span>
                        </p>
                        
                        <!-- Lives Display -->
                        <div class="mb-3 p-2 bg-gray-700 rounded border border-purple-500">
                            <p class="text-sm font-semibold text-purple-300">
                                ❤️ Lives: <span class="{{ $session->lives === 0 ? 'text-red-400 font-bold' : 'text-purple-300' }}">{{ $session->lives }}/3</span>
                            </p>
                            <div class="w-full bg-gray-600 rounded-full h-2 mt-1">
                                <div class="bg-gradient-to-r from-purple-500 to-purple-600 h-2 rounded-full transition-all" style="width: {{ ($session->lives / 3) * 100 }}%"></div>
                            </div>
                        </div>
                        
                        <p class="text-sm text-gray-500 mb-4">Created: {{ $session->created_at->format('M d, Y') }}</p>
                        
                        <div class="space-y-2">
                            @if($session->lives > 0)
                                <a href="{{ route('game.session.show', $session) }}" 
                                   class="inline-block w-full text-center px-4 py-2 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded hover:from-blue-600 hover:to-blue-700 transition font-semibold shadow-md">
                                    Play Session
                                </a>
                            @else
                                <button disabled 
                                        class="w-full px-4 py-2 bg-gray-700 text-gray-500 rounded cursor-not-allowed font-semibold">
                                    Game Over - No Lives Left
                                </button>
                            @endif
                            <a href="{{ route('game.session.history', $session) }}" 
                               class="inline-block w-full text-center px-4 py-2 bg-gradient-to-r from-indigo-600 to-indigo-700 text-white rounded hover:from-indigo-700 hover:to-indigo-800 transition font-semibold shadow-md">
                                📊 History
                            </a>
                            <button type="button" 
                                    onclick="openDeleteModal('{{ $session->id }}', '{{ $session->name }}')"
                                    class="w-full px-4 py-2 bg-gradient-to-r from-red-600 to-red-700 text-white rounded hover:from-red-700 hover:to-red-800 transition font-semibold shadow-md">
                                🗑️ Delete
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="bg-gray-800 rounded-lg shadow-md p-12 text-center border border-gray-700">
                <p class="text-gray-400 text-lg mb-6">You haven't created any game sessions yet.</p>
                <a href="{{ route('game.session.create') }}" 
                   class="inline-block px-6 py-3 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-lg font-semibold hover:from-blue-600 hover:to-blue-700 transition shadow-lg">
                    Create Your First Game Session
                </a>
            </div>
        @endif
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="hidden fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50">
        <div class="bg-gray-800 rounded-lg shadow-2xl p-6 max-w-sm mx-4 border border-gray-700">
            <h3 class="text-lg font-bold text-white mb-2">Delete Game Session</h3>
            <p class="text-gray-300 mb-6">
                Are you sure you want to delete the game session "<span id="sessionName" class="font-semibold text-red-400"></span>"? This action cannot be undone.
            </p>
            
            <div class="flex gap-3">
                <button type="button" 
                        onclick="closeDeleteModal()"
                        class="flex-1 px-4 py-2 bg-gray-700 text-gray-200 rounded hover:bg-gray-600 transition font-semibold">
                    Cancel
                </button>
                <form id="deleteForm" method="POST" class="flex-1">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                            class="w-full px-4 py-2 bg-gradient-to-r from-red-600 to-red-700 text-white rounded hover:from-red-700 hover:to-red-800 transition font-semibold">
                        Delete
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openDeleteModal(sessionId, sessionName) {
            document.getElementById('sessionName').textContent = sessionName;
            document.getElementById('deleteForm').action = `/game-session/${sessionId}`;
            document.getElementById('deleteModal').classList.remove('hidden');
        }

        function closeDeleteModal() {
            document.getElementById('deleteModal').classList.add('hidden');
        }

        // Close modal when clicking outside of it
        document.getElementById('deleteModal').addEventListener('click', function(event) {
            if (event.target === this) {
                closeDeleteModal();
            }
        });
    </script>
</body>
</html>
