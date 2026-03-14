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
                   class="px-6 py-3 bg-red-500 text-white rounded-lg font-semibold hover:bg-red-600 transition">
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
                        
                        <!-- Lives Display -->
                        <div class="mb-3 p-2 bg-purple-50 rounded border border-purple-200">
                            <p class="text-sm font-semibold text-purple-700">
                                ❤️ Lives: <span class="{{ $session->lives === 0 ? 'text-red-600 font-bold' : 'text-purple-600' }}">{{ $session->lives }}/3</span>
                            </p>
                            <div class="w-full bg-purple-200 rounded-full h-2 mt-1">
                                <div class="bg-purple-600 h-2 rounded-full transition-all" style="width: {{ ($session->lives / 3) * 100 }}%"></div>
                            </div>
                        </div>
                        
                        <p class="text-sm text-gray-500 mb-4">Created: {{ $session->created_at->format('M d, Y') }}</p>
                        
                        <div class="space-y-2">
                            @if($session->lives > 0)
                                <a href="{{ route('game.session.show', $session) }}" 
                                   class="inline-block w-full text-center px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 transition font-semibold">
                                    Play Session
                                </a>
                            @else
                                <button disabled 
                                        class="w-full px-4 py-2 bg-gray-300 text-gray-600 rounded cursor-not-allowed font-semibold">
                                    Game Over - No Lives Left
                                </button>
                            @endif
                            <a href="{{ route('game.session.history', $session) }}" 
                               class="inline-block w-full text-center px-4 py-2 bg-indigo-500 text-white rounded hover:bg-indigo-600 transition font-semibold">
                                📊 History
                            </a>
                            <button type="button" 
                                    onclick="openDeleteModal('{{ $session->id }}', '{{ $session->name }}')"
                                    class="w-full px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600 transition font-semibold">
                                🗑️ Delete
                            </button>
                        </div>
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

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-xl p-6 max-w-sm mx-4">
            <h3 class="text-lg font-bold text-gray-800 mb-2">Delete Game Session</h3>
            <p class="text-gray-600 mb-6">
                Are you sure you want to delete the game session "<span id="sessionName" class="font-semibold"></span>"? This action cannot be undone.
            </p>
            
            <div class="flex gap-3">
                <button type="button" 
                        onclick="closeDeleteModal()"
                        class="flex-1 px-4 py-2 bg-gray-300 text-gray-800 rounded hover:bg-gray-400 transition font-semibold">
                    Cancel
                </button>
                <form id="deleteForm" method="POST" class="flex-1">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                            class="w-full px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600 transition font-semibold">
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
