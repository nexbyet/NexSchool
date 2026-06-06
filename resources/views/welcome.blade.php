<!DOCTYPE html>
<html lang="gu">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>NexSchool - સ્કૂલ મેનેજમેન્ટ સિસ્ટમ</title>
    <script src="https://cdn.tailwindcss.com"></script>
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
                        <a href="{{ route('dashboard') }}" class="px-5 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition font-medium text-sm">ડેશબોર્ડ</a>
                    @else
                        <a href="{{ route('login') }}" class="text-gray-700 hover:text-indigo-600 transition font-medium text-sm">લોગિન</a>
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
                <p class="text-xl text-gray-600 mb-2">સ્માર્ટ સ્કૂલ મેનેજમેન્ટ સિસ્ટમ</p>
                <p class="text-gray-500 mb-12 max-w-xl mx-auto">વિદ્યાર્થીઓ, શિક્ષકો, વર્ગો અને વિષયોનું સંચાલન કરો. સરળ, ઝડપી અને આધુનિક.</p>

                <div class="flex items-center justify-center gap-4">
                    @auth
                        <a href="{{ route('dashboard') }}" class="px-8 py-3 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition font-medium shadow-lg shadow-indigo-200">ડેશબોર્ડ પર જાઓ</a>
                    @else
                        <a href="{{ route('login') }}" class="px-8 py-3 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition font-medium shadow-lg shadow-indigo-200">લોગિન કરો</a>
                    @endauth
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mt-24 max-w-5xl mx-auto">
                <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100">
                    <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center mb-4 mx-auto">
                        <i class="lni lni-user-multiple-4 text-2xl text-blue-600"></i>
                    </div>
                    <h3 class="font-semibold text-gray-900 mb-2">વિદ્યાર્થી વ્યવસ્થાપન</h3>
                    <p class="text-sm text-gray-500">વિદ્યાર્થીઓની માહિતી, હાજરી અને પ્રગતિ ટ્રેક કરો</p>
                </div>
                <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100">
                    <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center mb-4 mx-auto">
                        <i class="lni lni-user-4 text-2xl text-green-600"></i>
                    </div>
                    <h3 class="font-semibold text-gray-900 mb-2">શિક્ષક વ્યવસ્થાપન</h3>
                    <p class="text-sm text-gray-500">શિક્ષકોની નિમણૂક, સમયપત્રક અને વિષયો</p>
                </div>
                <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100">
                    <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center mb-4 mx-auto">
                        <i class="lni lni-buildings-1 text-2xl text-purple-600"></i>
                    </div>
                    <h3 class="font-semibold text-gray-900 mb-2">વર્ગ વ્યવસ્થાપન</h3>
                    <p class="text-sm text-gray-500">વર્ગો, વિભાગો અને સમયપત્રક ગોઠવો</p>
                </div>
            </div>

            <div class="mt-16 text-center text-sm text-gray-400">
                &copy; {{ date('Y') }} NexSchool. બધા હકો સુરક્ષિત.
            </div>
        </main>
    </div>
</body>
</html>
