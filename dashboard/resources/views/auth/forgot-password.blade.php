<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password — Npontu Support Tracker</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="min-h-screen bg-gradient-to-br from-[#0d3d22] via-[#1B6B3A] to-[#12492A] flex items-center justify-center p-4">
<div class="w-full max-w-md">
    <div class="text-center mb-8">
        <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-white/10 backdrop-blur-sm border border-white/20 mb-4">
            <span class="text-3xl font-black text-[#F5C518]">N</span>
        </div>
        <h1 class="text-2xl font-bold text-white">Forgot your password?</h1>
        <p class="text-green-200 text-sm mt-1">Enter your email and we'll send you a reset link.</p>
    </div>

    <div class="bg-white rounded-2xl shadow-2xl p-8">
        @if(session('status'))
        <div class="mb-5 p-4 bg-green-50 border border-green-200 rounded-xl">
            <p class="text-sm font-semibold text-green-700 flex items-center gap-2">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                {{ session('status') }}
            </p>
        </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
            @csrf
            <div>
                <label for="email" class="block text-sm font-semibold text-gray-700 mb-1">Email Address</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus
                       class="block w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-2.5 text-sm focus:ring-2 focus:ring-[#1B6B3A] focus:border-[#1B6B3A] outline-none @error('email') border-red-400 @enderror">
                @error('email')
                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit"
                    class="w-full py-3 bg-[#1B6B3A] hover:bg-[#12492A] text-white font-bold rounded-xl transition-all text-sm shadow-lg">
                Send Reset Link
            </button>

            <div class="text-center">
                <a href="{{ route('login') }}" class="text-sm text-[#1B6B3A] font-semibold hover:underline">
                    ← Back to login
                </a>
            </div>
        </form>
    </div>
    <p class="text-center text-green-300 text-xs mt-6">© {{ date('Y') }} Npontu Technologies</p>
</div>
</body>
</html>
