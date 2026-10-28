<!DOCTYPE html>
<html lang="gu">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>NexSchool - àª¸à«àª•à«‚àª² àª®à«‡àª¨à«‡àªœàª®à«‡àª¨à«àªŸ àª¸àª¿àª¸à«àªŸàª®</title>
    <link rel="stylesheet" href="{{ asset('css/app-1.1.8.css') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz@14..32&family=Anek+Gujarati:wght@300;400;500;600;700;800&family=Gabarito:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Anek Gujarati', 'Inter', 'Gabarito', sans-serif; }
    </style>
</head>
<body class="bg-gradient-to-br from-indigo-50 via-white to-purple-50 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <header class="flex items-center justify-between py-6">
            <div class="flex items-center gap-2">
                <div class="w-10 h-10 bg-indigo-600 rounded-xl flex items-center justify-center">
                    <span class="text-white font-bold text-lg">N</span>
                </div>
                <span class="text-xl font-bold text-gray-900">NexSchool</span>
            </div>
            <nav class="flex items-center gap-4">
                @if (Route::has('login'))
                    @auth
                        <a href="{{ route('dashboard') }}" class="px-5 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition font-medium text-sm">àª¡à«‡àª¶àª¬à«‹àª°à«àª¡</a>
                    @else
                        <a href="{{ route('login') }}" class="text-gray-700 hover:text-indigo-600 transition font-medium text-sm">àª²à«‹àª—àª¿àª¨</a>
                    @endauth
                @endif
            </nav>
        </header>

        <main class="py-20 text-center">
            <div class="max-w-3xl mx-auto">
                <div class="w-20 h-20 bg-indigo-600 rounded-2xl flex items-center justify-center mx-auto mb-8 shadow-lg shadow-indigo-200">
                    <span class="text-white font-bold text-3xl">N</span>
                </div>
                <h1 class="text-5xl font-bold text-gray-900 mb-4">NexSchool</h1>
                <p class="text-xl text-gray-600 mb-2">àª¸à«àª®àª¾àª°à«àªŸ àª¸à«àª•à«‚àª² àª®à«‡àª¨à«‡àªœàª®à«‡àª¨à«àªŸ àª¸àª¿àª¸à«àªŸàª®</p>
                <p class="text-gray-500 mb-12 max-w-xl mx-auto">àªµàª¿àª¦à«àª¯àª¾àª°à«àª¥à«€àª“, àª¶àª¿àª•à«àª·àª•à«‹, àªµàª°à«àª—à«‹ àª…àª¨à«‡ àªµàª¿àª·àª¯à«‹àª¨à«àª‚ àª¸àª‚àªšàª¾àª²àª¨ àª•àª°à«‹. àª¸àª°àª³, àªàª¡àªªà«€ àª…àª¨à«‡ àª†àª§à«àª¨àª¿àª•.</p>

                <div class="flex items-center justify-center gap-4">
                    @auth
                        <a href="{{ route('dashboard') }}" class="px-8 py-3 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition font-medium shadow-lg shadow-indigo-200">àª¡à«‡àª¶àª¬à«‹àª°à«àª¡ àªªàª° àªœàª¾àª“</a>
                    @else
                        <a href="{{ route('login') }}" class="px-8 py-3 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition font-medium shadow-lg shadow-indigo-200">àª²à«‹àª—àª¿àª¨ àª•àª°à«‹</a>
                    @endauth
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mt-24 max-w-5xl mx-auto">
                <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100">
                    <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center mb-4 mx-auto">
                        <i class="lni lni-user-multiple-4 text-2xl text-blue-600"></i>
                    </div>
                    <h3 class="font-semibold text-gray-900 mb-2">àªµàª¿àª¦à«àª¯àª¾àª°à«àª¥à«€ àªµà«àª¯àªµàª¸à«àª¥àª¾àªªàª¨</h3>
                    <p class="text-sm text-gray-500">àªµàª¿àª¦à«àª¯àª¾àª°à«àª¥à«€àª“àª¨à«€ àª®àª¾àª¹àª¿àª¤à«€, àª¹àª¾àªœàª°à«€ àª…àª¨à«‡ àªªà«àª°àª—àª¤àª¿ àªŸà«àª°à«‡àª• àª•àª°à«‹</p>
                </div>
                <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100">
                    <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center mb-4 mx-auto">
                        <i class="lni lni-user-4 text-2xl text-green-600"></i>
                    </div>
                    <h3 class="font-semibold text-gray-900 mb-2">àª¶àª¿àª•à«àª·àª• àªµà«àª¯àªµàª¸à«àª¥àª¾àªªàª¨</h3>
                    <p class="text-sm text-gray-500">àª¶àª¿àª•à«àª·àª•à«‹àª¨à«€ àª¨àª¿àª®àª£à«‚àª•, àª¸àª®àª¯àªªàª¤à«àª°àª• àª…àª¨à«‡ àªµàª¿àª·àª¯à«‹</p>
                </div>
                <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100">
                    <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center mb-4 mx-auto">
                        <i class="lni lni-buildings-1 text-2xl text-purple-600"></i>
                    </div>
                    <h3 class="font-semibold text-gray-900 mb-2">àªµàª°à«àª— àªµà«àª¯àªµàª¸à«àª¥àª¾àªªàª¨</h3>
                    <p class="text-sm text-gray-500">àªµàª°à«àª—à«‹, àªµàª¿àª­àª¾àª—à«‹ àª…àª¨à«‡ àª¸àª®àª¯àªªàª¤à«àª°àª• àª—à«‹àª àªµà«‹</p>
                </div>
            </div>

            <div class="mt-16 text-center text-sm text-gray-400">
                &copy; {{ date('Y') }} NexSchool. àª¬àª§àª¾ àª¹àª•à«‹ àª¸à«àª°àª•à«àª·àª¿àª¤.
            </div>
        </main>
    </div>
</body>
</html>
