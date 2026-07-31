<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password — Npontu Support Tracker</title>
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
        <h1 class="text-2xl font-bold text-white">Set your new password</h1>
        <p class="text-green-200 text-sm mt-1">Choose a strong password to secure your account.</p>
    </div>

    <div class="bg-white rounded-2xl shadow-2xl p-8">
        <form method="POST" action="{{ route('password.update') }}" class="space-y-5">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">

            <div>
                <label for="email" class="block text-sm font-semibold text-gray-700 mb-1">Email Address</label>
                <input type="email" id="email" name="email" value="{{ old('email', request()->email) }}" required autofocus
                       class="block w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-2.5 text-sm focus:ring-2 focus:ring-[#1B6B3A] focus:border-[#1B6B3A] outline-none @error('email') border-red-400 @enderror">
                @error('email')
                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password" class="block text-sm font-semibold text-gray-700 mb-1">New Password</label>
                <input type="password" id="password" name="password" required
                       class="block w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-2.5 text-sm focus:ring-2 focus:ring-[#1B6B3A] focus:border-[#1B6B3A] outline-none @error('password') border-red-400 @enderror">
                @error('password')
                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 mb-1">Confirm New Password</label>
                <input type="password" id="password_confirmation" name="password_confirmation" required
                       class="block w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-2.5 text-sm focus:ring-2 focus:ring-[#1B6B3A] focus:border-[#1B6B3A] outline-none">
            </div>

            {{-- Password strength hints --}}
            <div class="bg-blue-50 border border-blue-100 rounded-xl p-3 text-xs text-blue-700 space-y-1">
                <p class="font-semibold text-blue-800 mb-1">Password requirements:</p>
                <p>• At least 8 characters long</p>
                <p>• Mix of uppercase, lowercase, and numbers recommended</p>
                <p>• Avoid using personal information</p>
            </div>

            <button type="submit"
                    class="w-full py-3 bg-[#1B6B3A] hover:bg-[#12492A] text-white font-bold rounded-xl transition-all text-sm shadow-lg">
                Reset Password
            </button>
        </form>
    </div>
    <p class="text-center text-green-300 text-xs mt-6">© {{ date('Y') }} Npontu Technologies</p>
</div>
</body>
</html>
