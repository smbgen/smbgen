<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>{{ config('app.name', 'smbgen') }}</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    
    <!-- Scripts: let laravel-vite-plugin auto-detect dev server or built manifest -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
    
    <!-- Livewire Styles -->
    @livewireStyles
    
</head>
<body class="font-sans antialiased" style="--bg-color: {{ config('business.branding.background_color', '#1f2937') }}; --primary-color: {{ config('business.branding.primary_color', '#3B82F6') }}; --secondary-color: {{ config('business.branding.secondary_color', '#8B5CF6') }};">
    <div class="min-h-screen" style="background-color: var(--bg-color);">
        <div class="relative w-full flex justify-center items-center px-3 min-h-screen">

            {{-- Animated particle overlay (canvas) --}}
            <div class="login-lines-overlay absolute inset-0 z-0 pointer-events-none" aria-hidden="true">
                <canvas id="loginParticles" class="absolute inset-0 w-full h-full" aria-hidden="true"></canvas>
            </div>

            <div class="px-4 w-full max-w-md z-10 py-16 sm:px-6 lg:px-8">
                @if (session('success'))
                    <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                        {{ session('success') }}
                    </div>
                @endif

                @if (session('error'))
                    <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                        {{ session('error') }}
                    </div>
                @endif

                <div class="bg-white text-gray-900 rounded-lg shadow p-8 relative" style="z-index:10;">
                    @yield('content')
                </div>
            </div>

            {{-- Footer --}}
            <div class="absolute bottom-0 w-full z-10">
                <x-public-footer />
            </div>

            {{-- Toasts (consolidated) --}}
            <div class="fixed top-0 right-0 p-3 z-50 max-w-xs pointer-events-auto">
              <div id="secureSessionToast" class="bg-gray-800 text-white border border-gray-600 rounded-lg shadow-lg w-full opacity-100 pointer-events-none" role="alert">
                <div class="bg-gray-700 text-white border-b-0 py-1 px-2 rounded-t-lg flex items-center">
                  <strong class="text-xs">Session protection enabled</strong>
                  <small class="text-xs ml-auto text-right">    <strong>{{ config('app.name') }}</strong><br> cloud monitoring</small>
                </div>
                <div class="text-xs py-2 px-3 text-white/90">
                  <div>IP: {{ request()->ip() }}</div>
                  <div>Browser: {{ \Illuminate\Support\Str::limit(request()->header('User-Agent'), 38) }}</div>
                  <div class="mt-2">Session ID: <span id="sessionIdInline">{{ \Illuminate\Support\Str::uuid() }}</span></div>
                </div>
              </div>

              <a id="sessionPill" href="{{ config('app.url') }}" target="_blank" rel="noopener noreferrer" aria-label="Open {{ config('app.name') }} in a new tab" class="hidden bg-green-600 text-white text-xs rounded-full px-2 py-0.5 shadow-lg mt-2 text-center transform origin-center pointer-events-auto cursor-pointer">
                <strong>{{ config('app.name') }}</strong>
              </a>
            </div>

        </div>
    </div>

    @livewireScripts
    @stack('scripts')

    <style>
        /* Toast sizing for small screens (unchanged) */
        @media (max-width: 576px) { .toast { font-size: 0.75rem; } }

        /* Canvas particle overlay */
        .login-lines-overlay { z-index: 0; mix-blend-mode: normal; }
        #loginParticles { display: block; width: 100%; height: 100%; }

        /* Slightly dim the overlay on very small screens for legibility */
        @media (max-width: 420px) { .login-lines-overlay { opacity: 0.6; } }

        /* Ensure the login card remains above the animation */
        .bg-gray-800\/90 { position: relative; z-index: 10; }
    </style>

    <script>
            document.addEventListener('DOMContentLoaded', function () {
                const consolidated = document.getElementById('secureSessionToast');
                const sessionPill = document.getElementById('sessionPill');
                const sessionIdInline = document.getElementById('sessionIdInline');

                if (consolidated) consolidated.style.display = 'block';

                setTimeout(() => {
                    if (!sessionPill) return;
                    if (consolidated) {
                        consolidated.style.transition = 'opacity 400ms ease, transform 400ms ease, height 300ms ease, margin 300ms ease';
                        consolidated.style.opacity = '0';
                        consolidated.style.transform = 'translateY(-6px)';
                        setTimeout(() => { consolidated.style.display = 'none'; }, 420);
                    }
                    sessionPill.classList.remove('hidden');
                    sessionPill.style.transition = 'transform 400ms ease, padding 300ms ease, opacity 300ms ease';
                    sessionPill.style.transform = 'scale(1)';
                    setTimeout(() => { sessionPill.style.padding = '0.125rem 0.5rem'; sessionPill.style.transform = 'scale(0.85)'; }, 180);
                    setTimeout(() => { if (sessionIdInline) sessionIdInline.style.opacity = '0.6'; }, 1200);
                }, 4000);
            });
        </script>

        <script>
            // Canvas particle system for login background
            (function () {
                const canvas = document.getElementById('loginParticles');
                if (!canvas) { return; }
                const mediaReduced = window.matchMedia('(prefers-reduced-motion: reduce)');
                const ctx = canvas.getContext && canvas.getContext('2d');
                if (!ctx) { return; }
                let DPR = Math.max(1, window.devicePixelRatio || 1);
                function resize() {
                    const rect = canvas.getBoundingClientRect();
                    canvas.width = Math.round(rect.width * DPR);
                    canvas.height = Math.round(rect.height * DPR);
                    canvas.style.width = rect.width + 'px';
                    canvas.style.height = rect.height + 'px';
                    ctx.setTransform(DPR, 0, 0, DPR, 0, 0);
                }
                const COUNT = 900; const particles = []; const globalVelocity = { x: -0.008, y: -0.005 }; const cursorParticles = []; let mouse = { x: null, y: null }; let lastMouse = { t: Date.now() };
                window.addEventListener('mousemove', (e) => { const rect = canvas.getBoundingClientRect(); mouse.x = (e.clientX - rect.left); mouse.y = (e.clientY - rect.top); lastMouse.t = Date.now(); for (let i = 0; i < 4; i++) cursorParticles.push(makeCursorParticle(mouse.x, mouse.y)); });
                window.addEventListener('touchmove', (e) => { if (!e.touches || !e.touches[0]) return; const rect = canvas.getBoundingClientRect(); mouse.x = (e.touches[0].clientX - rect.left); mouse.y = (e.touches[0].clientY - rect.top); lastMouse.t = Date.now(); for (let i = 0; i < 3; i++) cursorParticles.push(makeCursorParticle(mouse.x, mouse.y)); }, { passive: true });
                function rand(min, max) { return Math.random() * (max - min) + min; }
                function makeParticle(w, h) { return { x: rand(0, w), y: rand(0, h), r: rand(0.9, 2.2), vx: rand(-0.18, 0.18), vy: rand(-0.18, 0.18), phase: rand(0, Math.PI * 2), speed: rand(0.12, 0.5), opacity: rand(0.05, 0.22) }; }
                function makeCursorParticle(x, y) { const angle = rand(0, Math.PI * 2); const speed = rand(0.3, 1.2); return { x: x + rand(-6, 6), y: y + rand(-6, 6), r: rand(1.2, 2.8), vx: Math.cos(angle) * speed, vy: Math.sin(angle) * speed, life: Math.floor(rand(60, 140)), opacity: rand(0.25, 0.85) }; }
                function init() { DPR = Math.max(1, window.devicePixelRatio || 1); resize(); particles.length = 0; for (let i = 0; i < COUNT; i++) particles.push(makeParticle(canvas.width / DPR, canvas.height / DPR)); }
                function drawFrame(now) { ctx.clearRect(0, 0, canvas.width, canvas.height); ctx.fillStyle = 'rgba(59,130,246,0.06)'; ctx.fillRect(0, 0, canvas.width / DPR, canvas.height / DPR); for (let p of particles) { p.phase += 0.018 * p.speed; p.x += p.vx * p.speed + Math.sin(p.phase) * 0.12 + globalVelocity.x; p.y += p.vy * p.speed + Math.cos(p.phase * 0.7) * 0.08 + globalVelocity.y; if (p.x < -10) p.x = canvas.width / DPR + 10; if (p.x > canvas.width / DPR + 10) p.x = -10; if (p.y < -10) p.y = canvas.height / DPR + 10; if (p.y > canvas.height / DPR + 10) p.y = -10; ctx.beginPath(); ctx.fillStyle = `rgba(255,255,255,${p.opacity})`; ctx.arc(p.x, p.y, p.r, 0, Math.PI * 2); ctx.fill(); }
                    for (let i = cursorParticles.length - 1; i >= 0; i--) { const cp = cursorParticles[i]; cp.x += cp.vx * 0.6 + globalVelocity.x * 1.6; cp.y += cp.vy * 0.6 + globalVelocity.y * 1.6; cp.life -= 1; if (cp.life <= 0) { cursorParticles.splice(i, 1); continue; } ctx.beginPath(); ctx.fillStyle = `rgba(255,255,255,${Math.max(0.06, cp.opacity * (cp.life / 120))})`; ctx.arc(cp.x, cp.y, cp.r, 0, Math.PI * 2); ctx.fill(); }
                    ctx.save(); ctx.globalCompositeOperation = 'destination-out'; ctx.fillStyle = 'black'; const fontSize = Math.max(40, Math.min(140, (canvas.width / DPR) * 0.09)); ctx.font = `700 ${fontSize}px Inter, system-ui, -apple-system, 'Segoe UI', Roboto, 'Helvetica Neue', Arial`; ctx.textAlign = 'center'; ctx.textBaseline = 'middle'; const textX = (canvas.width / DPR) * 0.72; const textY = (canvas.height / DPR) * 0.88; const companyName = {!! json_encode(config('app.company_name', 'smbgen')) !!}; ctx.fillText(companyName, textX, textY);
                    ctx.restore(); }
                let animationId = null; if (mediaReduced.matches) { init(); drawFrame(); return; } window.addEventListener('resize', () => { init(); }); init(); animationId = requestAnimationFrame(function loop(now){ drawFrame(now); animationId = requestAnimationFrame(loop); }); document.addEventListener('visibilitychange', () => { if (document.hidden) { if (animationId) cancelAnimationFrame(animationId); } else { animationId = requestAnimationFrame(function loop(now){ drawFrame(now); animationId = requestAnimationFrame(loop); }); } });
            })();
        </script>
</body>
</html>
