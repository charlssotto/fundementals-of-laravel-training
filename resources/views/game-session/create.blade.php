<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Game Session</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center p-4">
    <div class="bg-white p-8 rounded-lg shadow-lg w-full max-w-md">
        <h1 class="text-3xl font-bold text-gray-800 mb-6 text-center">Create New Game Session</h1>
        
        <form action="{{ route('game.session.store') }}" method="POST" class="space-y-4">
            @csrf
            
            <div>
                <label for="name" class="block text-gray-700 font-semibold mb-2">Session Name</label>
                <input type="text" 
                       id="name" 
                       name="name" 
                       placeholder="e.g., My First Game"
                       class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-blue-500"
                       required
                       autofocus
                       value="{{ old('name') }}">
                @error('name')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <button type="submit" 
                    class="w-full px-6 py-3 bg-blue-500 text-white rounded-lg font-semibold hover:bg-blue-600 transition">
                Create Session
            </button>
            
            <a href="{{ route('game.dashboard') }}" 
               class="block w-full text-center px-6 py-3 bg-gray-300 text-gray-700 rounded-lg font-semibold hover:bg-gray-400 transition">
                Cancel
            </a>
        </form>
    </div>
</body>
</html>
