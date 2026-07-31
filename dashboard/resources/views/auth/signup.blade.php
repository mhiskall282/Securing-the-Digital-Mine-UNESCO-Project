<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sign Up — Securing the Digital Mine</title>
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
                    <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z"/>
                </svg>
            </div>
            <h2 class="text-2xl font-bold tracking-tight text-white">Create SaaS Tenant</h2>
            <p class="text-xs text-gray-400">Register your organization to access private device monitoring.</p>
        </div>

        @if($errors->any())
            <div class="p-4 bg-red-500/10 border border-red-500/20 text-red-400 text-xs rounded-xl space-y-1">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form action="{{ route('signup') }}" method="POST" class="space-y-4">
            @csrf
            
            <!-- Org Name -->
            <div class="space-y-1.5">
                <label class="text-xs font-semibold text-gray-300">Organization Name</label>
                <input type="text" name="org_name" value="{{ old('org_name') }}" required class="w-full bg-[#0c0c0e] border border-[#2e2e33] rounded-xl px-4 py-2.5 text-sm text-white focus:border-[#10b981] focus:outline-none placeholder-gray-600" placeholder="e.g. Acme Mining Corp">
            </div>

            <!-- Your Name -->
            <div class="space-y-1.5">
                <label class="text-xs font-semibold text-gray-300">Administrator Name</label>
                <input type="text" name="name" value="{{ old('name') }}" required class="w-full bg-[#0c0c0e] border border-[#2e2e33] rounded-xl px-4 py-2.5 text-sm text-white focus:border-[#10b981] focus:outline-none placeholder-gray-600" placeholder="e.g. John Doe">
            </div>

            <!-- Email -->
            <div class="space-y-1.5">
                <label class="text-xs font-semibold text-gray-300">Work Email</label>
                <input type="email" name="email" value="{{ old('email') }}" required class="w-full bg-[#0c0c0e] border border-[#2e2e33] rounded-xl px-4 py-2.5 text-sm text-white focus:border-[#10b981] focus:outline-none placeholder-gray-600" placeholder="name@company.com">
            </div>

            <!-- Passwords -->
            <div class="grid grid-cols-2 gap-4">
                <div class="space-y-1.5">
                    <label class="text-xs font-semibold text-gray-300">Password</label>
                    <input type="password" name="password" required class="w-full bg-[#0c0c0e] border border-[#2e2e33] rounded-xl px-4 py-2 text-sm text-white focus:border-[#10b981] focus:outline-none placeholder-gray-600" placeholder="••••••••">
                </div>
                <div class="space-y-1.5">
                    <label class="text-xs font-semibold text-gray-300">Confirm</label>
                    <input type="password" name="password_confirmation" required class="w-full bg-[#0c0c0e] border border-[#2e2e33] rounded-xl px-4 py-2 text-sm text-white focus:border-[#10b981] focus:outline-none placeholder-gray-600" placeholder="••••••••">
                </div>
            </div>

            <button type="submit" class="w-full py-3 bg-[#10b981] hover:bg-[#0da672] text-white rounded-xl text-sm font-semibold transition-all cursor-pointer mt-2">
                Register Tenant
            </button>
        </form>

        <div class="text-center text-xs text-gray-500 border-t border-[#2e2e33]/50 pt-4">
            Already registered? 
            <a href="{{ route('login') }}" class="text-[#10b981] hover:underline font-semibold ml-1">Log In</a>
        </div>

    </div>
</body>
</html>
