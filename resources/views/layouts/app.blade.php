<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'AeronPay B2B Voucher & e-KYC SaaS Platform')</title>

    <!-- Google Fonts: Outfit & Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        display: ['Outfit', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            50: '#eef2ff',
                            100: '#e0e7ff',
                            500: '#6366f1',
                            600: '#4f46e5',
                            700: '#4338ca',
                            900: '#312e81',
                        },
                        cyanGlow: '#06b6d4',
                    }
                }
            }
        }
    </script>

    <!-- FontAwesome 6 Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Canvas Confetti -->
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #0b0f19;
            color: #f8fafc;
            background-image: 
                radial-gradient(at 0% 0%, rgba(99, 102, 241, 0.15) 0px, transparent 50%),
                radial-gradient(at 100% 0%, rgba(6, 182, 212, 0.12) 0px, transparent 50%),
                radial-gradient(at 50% 100%, rgba(168, 85, 247, 0.1) 0px, transparent 50%);
            background-attachment: fixed;
            min-height: 100vh;
        }

        .glass-panel {
            background: rgba(30, 41, 59, 0.7);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.4);
        }

        .glass-card {
            background: rgba(30, 41, 59, 0.5);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.06);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .glass-card:hover {
            transform: translateY(-4px);
            border-color: rgba(99, 102, 241, 0.4);
            box-shadow: 0 12px 30px -10px rgba(99, 102, 241, 0.3);
        }

        .glow-button {
            background: linear-gradient(135deg, #4f46e5 0%, #06b6d4 100%);
            box-shadow: 0 4px 20px rgba(79, 70, 229, 0.4);
            transition: all 0.3s ease;
        }

        .glow-button:hover {
            box-shadow: 0 6px 25px rgba(6, 182, 212, 0.6);
            transform: scale(1.02);
        }

        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: rgba(15, 23, 42, 0.6);
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: rgba(99, 102, 241, 0.4);
            border-radius: 9999px;
        }
    </style>
</head>
<body class="flex flex-col min-h-screen custom-scrollbar">

    <!-- Top Navigation Header -->
    <header class="sticky top-0 z-40 glass-panel border-b border-slate-800/80 px-4 md:px-8 py-3">
        <div class="max-w-7xl mx-auto flex items-center justify-between">
            <!-- Brand Logo -->
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-indigo-600 via-cyan-500 to-indigo-400 flex items-center justify-center text-white shadow-lg shadow-indigo-500/30">
                    <i class="fa-solid fa-bolt text-lg"></i>
                </div>
                <div>
                    <a href="{{ route('admin.dashboard') }}" class="font-display font-bold text-xl tracking-tight text-white flex items-center gap-2">
                        AeronPay <span class="text-xs px-2 py-0.5 rounded-full bg-indigo-500/20 text-indigo-300 font-semibold border border-indigo-500/30">SaaS Reseller</span>
                    </a>
                    <p class="text-[11px] text-slate-400 hidden sm:block">Digital e-KYC Merchant Onboarding & Gift Voucher Platform</p>
                </div>
            </div>

            <!-- Header Controls & Mode -->
            <div class="flex items-center gap-3">
                <!-- Mode Badge -->
                @php
                    $currentMode = \App\Models\Setting::get('aeronpay_mode', 'mock');
                @endphp
                <div class="hidden sm:flex items-center gap-2 px-3 py-1.5 rounded-lg border text-xs font-semibold {{ $currentMode === 'live' ? 'bg-emerald-500/10 text-emerald-400 border-emerald-500/30' : 'bg-amber-500/10 text-amber-400 border-amber-500/30' }}">
                    <span class="w-2 h-2 rounded-full {{ $currentMode === 'live' ? 'bg-emerald-400 animate-pulse' : 'bg-amber-400' }}"></span>
                    <span>{{ strtoupper($currentMode) }} MODE</span>
                </div>

                <!-- Role Switcher -->
                <form action="{{ route('switch.role') }}" method="POST" class="flex items-center bg-slate-900/80 p-1 rounded-xl border border-slate-800">
                    @csrf
                    <button type="submit" name="role" value="admin" class="px-3 py-1.5 rounded-lg text-xs font-semibold transition-all {{ request()->routeIs('admin.*') ? 'bg-indigo-600 text-white shadow-md' : 'text-slate-400 hover:text-white' }}">
                        <i class="fa-solid fa-user-shield mr-1"></i> Admin Panel
                    </button>
                    <button type="submit" name="role" value="merchant" class="px-3 py-1.5 rounded-lg text-xs font-semibold transition-all {{ request()->routeIs('merchant.*') ? 'bg-indigo-600 text-white shadow-md' : 'text-slate-400 hover:text-white' }}">
                        <i class="fa-solid fa-store mr-1"></i> Merchant Store
                    </button>
                </form>

                <!-- Settings Trigger Button -->
                <button onclick="openSettingsModal()" class="p-2.5 rounded-xl glass-card text-slate-300 hover:text-white hover:bg-slate-800/80 transition-all" title="AeronPay API Settings">
                    <i class="fa-solid fa-gear"></i>
                </button>
            </div>
        </div>
    </header>

    <!-- Main Content Container -->
    <main class="flex-1 max-w-7xl w-full mx-auto px-4 md:px-8 py-8">
        @if(session('success'))
            <div id="alert-success" class="mb-6 p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 flex items-center justify-between text-sm">
                <div class="flex items-center gap-3">
                    <i class="fa-solid fa-circle-check text-lg"></i>
                    <span>{{ session('success') }}</span>
                </div>
                <button onclick="this.parentElement.remove()" class="text-emerald-400 hover:text-white"><i class="fa-solid fa-xmark"></i></button>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Global Settings Modal -->
    <div id="settingsModal" class="fixed inset-0 z-50 hidden bg-slate-950/80 backdrop-blur-md flex items-center justify-center p-4">
        <div class="glass-panel w-full max-w-lg rounded-2xl p-6 border border-slate-700/80 relative shadow-2xl animate-fade-in">
            <div class="flex items-center justify-between pb-4 border-b border-slate-800">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-indigo-600/20 text-indigo-400 border border-indigo-500/30 flex items-center justify-center">
                        <i class="fa-solid fa-sliders"></i>
                    </div>
                    <div>
                        <h3 class="font-display font-bold text-lg text-white">AeronPay API Settings</h3>
                        <p class="text-xs text-slate-400">Configure Credentials & Mode</p>
                    </div>
                </div>
                <button onclick="closeSettingsModal()" class="text-slate-400 hover:text-white p-1"><i class="fa-solid fa-xmark text-lg"></i></button>
            </div>

            <form id="settingsForm" onsubmit="saveSettings(event)" class="mt-5 space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">API Execution Mode</label>
                    <div class="grid grid-cols-2 gap-3">
                        <label class="flex items-center justify-center gap-2 p-3 rounded-xl border border-slate-800 bg-slate-900/60 cursor-pointer hover:border-indigo-500/50 has-[:checked]:border-indigo-500 has-[:checked]:bg-indigo-600/10">
                            <input type="radio" name="aeronpay_mode" value="mock" {{ \App\Models\Setting::get('aeronpay_mode', 'mock') === 'mock' ? 'checked' : '' }} class="hidden">
                            <i class="fa-solid fa-vial text-amber-400"></i>
                            <span class="text-xs font-semibold text-slate-200">Mock / Sandbox</span>
                        </label>
                        <label class="flex items-center justify-center gap-2 p-3 rounded-xl border border-slate-800 bg-slate-900/60 cursor-pointer hover:border-indigo-500/50 has-[:checked]:border-indigo-500 has-[:checked]:bg-indigo-600/10">
                            <input type="radio" name="aeronpay_mode" value="live" {{ \App\Models\Setting::get('aeronpay_mode', 'mock') === 'live' ? 'checked' : '' }} class="hidden">
                            <i class="fa-solid fa-tower-cell text-emerald-400"></i>
                            <span class="text-xs font-semibold text-slate-200">Live Production</span>
                        </label>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">AeronPay Base URL</label>
                    <input type="text" name="aeronpay_base_url" value="{{ \App\Models\Setting::get('aeronpay_base_url', 'https://api.aeronpay.in/api/serviceapi-prod') }}" class="w-full bg-slate-900/90 border border-slate-800 rounded-xl px-3.5 py-2.5 text-xs text-white focus:outline-none focus:border-indigo-500" required>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Client ID (`client-id` Header)</label>
                    <input type="text" name="aeronpay_client_id" value="{{ \App\Models\Setting::get('aeronpay_client_id', '') }}" placeholder="Enter unique Client ID" class="w-full bg-slate-900/90 border border-slate-800 rounded-xl px-3.5 py-2.5 text-xs text-white focus:outline-none focus:border-indigo-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Client Secret (`client-secret` Header)</label>
                    <input type="password" name="aeronpay_client_secret" value="{{ \App\Models\Setting::get('aeronpay_client_secret', '') }}" placeholder="Enter Client Secret key" class="w-full bg-slate-900/90 border border-slate-800 rounded-xl px-3.5 py-2.5 text-xs text-white focus:outline-none focus:border-indigo-500">
                </div>

                <div class="pt-3 flex items-center justify-end gap-3 border-t border-slate-800">
                    <button type="button" onclick="closeSettingsModal()" class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-400 hover:text-white">Cancel</button>
                    <button type="submit" class="glow-button px-5 py-2 rounded-xl text-xs font-semibold text-white">Save Settings</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Footer -->
    <footer class="border-t border-slate-800/80 py-6 text-center text-xs text-slate-500">
        <p>&copy; {{ date('Y') }} AeronPay SaaS Reseller Platform. Digital e-KYC & Instant Gift Vouchers Engine.</p>
    </footer>

    <script>
        function openSettingsModal() {
            document.getElementById('settingsModal').classList.remove('hidden');
        }
        function closeSettingsModal() {
            document.getElementById('settingsModal').classList.add('hidden');
        }
        function saveSettings(e) {
            e.preventDefault();
            const form = document.getElementById('settingsForm');
            const formData = new FormData(form);

            fetch("{{ route('admin.settings.update') }}", {
                method: "POST",
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    location.reload();
                } else {
                    alert("Error: " + data.message);
                }
            })
            .catch(err => alert("Failed to save settings: " + err));
        }
    </script>

    @stack('scripts')
</body>
</html>
