<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Log In — Securing the Digital Mine</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css'])
</head>
<body class="h-full flex items-center justify-center bg-[#0c0c0e] text-[#ededef] font-sans antialiased">
    <div class="w-full max-w-md p-8 bg-[#17171a] border border-[#2e2e33] rounded-3xl shadow-2xl space-y-6">
        
        <!-- Header -->
        <div class="text-center space-y-2">
            <div class="inline-flex p-3 rounded-2xl bg-[#10b981]/10 text-[#10b981] border border-[#10b981]/20">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/>
                </svg>
            </div>
            <h2 class="text-2xl font-bold tracking-tight text-white">Welcome Back</h2>
            <p class="text-xs text-gray-400">Sign in to manage your organization's intrusion shields.</p>
        </div>

        @if($errors->any())
            <div class="p-4 bg-red-500/10 border border-red-500/20 text-red-400 text-xs rounded-xl space-y-1">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form action="{{ route('login') }}" method="POST" class="space-y-4">
            @csrf
            
            <div class="space-y-1.5">
                <label class="text-xs font-semibold text-gray-300">Email Address</label>
                <input type="email" name="email" value="{{ old('email') }}" required class="w-full bg-[#0c0c0e] border border-[#2e2e33] rounded-xl px-4 py-2.5 text-sm text-white focus:border-[#10b981] focus:outline-none placeholder-gray-600" placeholder="name@company.com">
            </div>

            <div class="space-y-1.5">
                <div class="flex justify-between items-center">
                    <label class="text-xs font-semibold text-gray-300">Password</label>
                </div>
                <input type="password" name="password" required class="w-full bg-[#0c0c0e] border border-[#2e2e33] rounded-xl px-4 py-2.5 text-sm text-white focus:border-[#10b981] focus:outline-none placeholder-gray-600" placeholder="••••••••">
            </div>

            <div class="flex items-center justify-between text-xs">
                <label class="flex items-center gap-2 cursor-pointer select-none text-gray-400">
                    <input type="checkbox" name="remember" class="rounded bg-[#0c0c0e] border-[#2e2e33] text-[#10b981] focus:ring-0">
                    Remember me
                </label>
            </div>

            <button type="submit" class="w-full py-3 bg-[#10b981] hover:bg-[#0da672] text-white rounded-xl text-sm font-semibold transition cursor-pointer">
                Log In
            </button>
        </form>

        <div class="text-center text-xs text-gray-500 border-t border-[#2e2e33]/50 pt-4">
            Need to register your organization? 
            <a href="{{ route('signup') }}" class="text-[#10b981] hover:underline font-semibold ml-1">Create Account</a>
        </div>

    </div>
</body>
</html>
