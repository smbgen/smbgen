@php use Illuminate\Support\Str; @endphp
@extends('layouts.guest')

@section('content')

<div class="relative w-full flex justify-center items-center px-3 min-h-screen">

  {{-- Animated particle overlay (canvas) --}}
  <div class="login-lines-overlay absolute inset-0 z-0 pointer-events-none" aria-hidden="true">
    <canvas id="loginParticles" class="absolute inset-0 w-full h-full" aria-hidden="true"></canvas>
  </div>

  {{-- Login Card --}}
  <div class="px-4 w-full max-w-md z-10">
    <div class="bg-gray-800/90 border border-gray-700 rounded-xl shadow-xl p-6">
      <div class="space-y-6">
      <h2 class="text-center text-2xl font-bold text-gray-100">{{ config('app.company_name', 'CLIENTBRIDGE') }}</h2>

  <form method="POST" action="{{ route('login') }}" class="space-y-4">
    @csrf

    <div>
      <label for="email" class="block text-sm font-medium text-gray-300 mb-2">EMAIL ADDRESS</label>
      <input type="email" id="email" name="email" class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg text-gray-100 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" required autofocus>
      @error('email')
        <div class="text-red-400 text-sm mt-1">{{ $message }}</div>
      @enderror
    </div>

    <div>
      <label for="password" class="block text-sm font-medium text-gray-300 mb-2">PASSWORD</label>
      <div x-data="{ show: false }" class="relative">
        <input type="password" x-bind:type="show ? 'text' : 'password'" id="password" name="password" class="w-full pr-12 px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg text-gray-100 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
        <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 px-3 flex items-center text-gray-300 hover:text-white z-10" aria-label="Toggle password visibility">
          <svg x-show="!show" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="2">
            <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12Z"/>
            <circle cx="12" cy="12" r="3"/>
          </svg>
          <svg x-show="show" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="2">
            <path d="M3 3l18 18"/>
            <path d="M10.58 10.58A3 3 0 0 0 12 15a3 3 0 0 0 2.42-4.42"/>
            <path d="M9.88 4.24A10.94 10.94 0 0 1 12 4c7 0 11 8 11 8a20.3 20.3 0 0 1-3.06 4.38"/>
            <path d="M6.61 6.61A10.94 10.94 0 0 0 1 12s4 8 11 8a10.94 10.94 0 0 0 5.39-1.61"/>
          </svg>
        </button>
      </div>
      @error('password')
        <div class="text-red-400 text-sm mt-1">{{ $message }}</div>
      @enderror
    </div>

    <div>
      <button type="submit" class="w-full btn-primary py-3">LOGIN</button>
    </div>
  </form>
  
  <div class="mt-3 text-center">
    <a href="{{ route('password.request') }}" class="text-sm text-blue-400 hover:underline">Forgot your password?</a>
  </div>
  
  <div class="flex items-center gap-3 mt-1 mb-3">
    <span class="h-px bg-gray-700 flex-1"></span>
    <span class="text-xs uppercase tracking-wider text-gray-400">OR</span>
    <span class="h-px bg-gray-700 flex-1"></span>
  </div>
    
  {{-- Google OAuth Link --}}
  <div class="mt-4">
    <a href="{{ route('auth.google.redirect') }}" class="w-full inline-flex items-center justify-center px-4 py-3 border border-gray-600 rounded-lg text-gray-300 hover:text-white hover:bg-gray-700 transition-colors">
      <svg width="18" height="18" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" class="mr-2">
        <path fill="#FFC107" d="M43.611,20.083H42V20H24v8h11.303c-1.649,4.657-6.08,8-11.303,8c-6.627,0-12-5.373-12-12c0-6.627,5.373-12,12-12c3.059,0,5.842,1.154,7.961,3.039l5.657-5.657C34.046,6.053,29.268,4,24,4C12.955,4,4,12.955,4,24c0,11.045,8.955,20,20,20c11.045,0,20-8.955,20-20C44,22.659,43.862,21.35,43.611,20.083z"/>
        <path fill="#FF3D00" d="M6.306,14.691l6.571,4.819C14.655,15.108,18.961,12,24,12c3.059,0,5.842,1.154,7.961,3.039l5.657-5.657C34.046,6.053,29.268,4,24,4C16.318,4,9.656,8.337,6.306,14.691z"/>
        <path fill="#4CAF50" d="M24,44c5.166,0,9.86-1.977,13.409-5.192l-6.19-5.238C29.211,35.091,26.715,36,24,36c-5.202,0-9.619-3.317-11.283-7.946l-6.522,5.025C9.505,39.556,16.227,44,24,44z"/>
        <path fill="#1976D2" d="M43.611,20.083H42V20H24v8h11.303c-0.792,2.237-2.231,4.166-4.087,5.571c0.001-0.001,0.002-0.001,0.003-0.002l6.19,5.238C36.971,39.205,44,34,44,24C44,22.659,43.862,21.35,43.611,20.083z"/>
      </svg>
      Continue with Google
    </a>
  </div>
      </div>
    </div>
  </div>

  {{-- Footer --}}
  <footer class="absolute bottom-0 w-full text-center pb-3 text-gray-400 text-sm z-10">
    <div class="opacity-75">
      © {{ date('Y') }} {{ config('app.company_name', 'CLIENTBRIDGE') }} ·
      <a href="https://clientbridge.app/privacy_policy.php" class="text-gray-300 hover:text-white underline">Privacy</a> ·
      <a href="https://clientbridge.app/terms_service.php" class="text-gray-300 hover:text-white underline">Terms</a>
    </div>
  </footer>

</div>

{{-- Toasts (consolidated) --}}
<div class="fixed top-0 right-0 p-3 z-50 max-w-xs pointer-events-auto">
  <div id="secureSessionToast" class="bg-gray-800 text-white border border-gray-600 rounded-lg shadow-lg w-full opacity-100 pointer-events-none" role="alert">
    <div class="bg-gray-700 text-white border-b-0 py-1 px-2 rounded-t-lg flex items-center">
      <strong class="text-xs">Session protection enabled</strong>
      <small class="text-xs ml-auto text-right">    <strong>CLIENTBRIDGE</strong><br> cloud monitoring</small>
    </div>
    <div class="text-xs py-2 px-3">
      <div>IP: {{ request()->ip() }}</div>
      <div>Browser: {{ Str::limit(request()->header('User-Agent'), 38) }}</div>
      <div class="mt-2">Session ID: <span id="sessionIdInline">{{ Str::uuid() }}</span></div>
    </div>
  </div>

  <a id="sessionPill" href="https://clientbridge.app" target="_blank" rel="noopener noreferrer" aria-label="Open clientbridge.app in a new tab" class="hidden bg-green-600 text-white text-xs rounded-full px-2 py-0.5 shadow-lg mt-2 text-center transform origin-center pointer-events-auto cursor-pointer">
    <strong>CLIENTBRIDGE</strong>
  </a>
</div>

@endsection

@push('scripts')
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
  (function () {
    const canvas = document.getElementById('loginParticles');
    if (!canvas) { return; }
    const mediaReduced = window.matchMedia('(prefers-reduced-motion: reduce)');
    const ctx = canvas.getContext && canvas.getContext('2d');
    if (!ctx) { return; }
    let DPR = Math.max(1, window.devicePixelRatio || 1);
    function resize() { const rect = canvas.getBoundingClientRect(); canvas.width = Math.round(rect.width * DPR); canvas.height = Math.round(rect.height * DPR); canvas.style.width = rect.width + 'px'; canvas.style.height = rect.height + 'px'; ctx.setTransform(DPR, 0, 0, DPR, 0, 0); }
    const COUNT = 900; const particles = []; const globalVelocity = { x: -0.008, y: -0.005 }; const cursorParticles = []; let mouse = { x: null, y: null };
    window.addEventListener('mousemove', (e) => { const rect = canvas.getBoundingClientRect(); mouse.x = (e.clientX - rect.left); mouse.y = (e.clientY - rect.top); for (let i = 0; i < 4; i++) cursorParticles.push(makeCursorParticle(mouse.x, mouse.y)); });
    window.addEventListener('touchmove', (e) => { if (!e.touches || !e.touches[0]) return; const rect = canvas.getBoundingClientRect(); mouse.x = (e.touches[0].clientX - rect.left); mouse.y = (e.touches[0].clientY - rect.top); for (let i = 0; i < 3; i++) cursorParticles.push(makeCursorParticle(mouse.x, mouse.y)); }, { passive: true });
    function rand(min, max) { return Math.random() * (max - min) + min; }
    function makeParticle(w, h) { return { x: rand(0, w), y: rand(0, h), r: rand(0.9, 2.2), vx: rand(-0.18, 0.18), vy: rand(-0.18, 0.18), phase: rand(0, Math.PI * 2), speed: rand(0.12, 0.5), opacity: rand(0.05, 0.22) }; }
    function makeCursorParticle(x, y) { const angle = rand(0, Math.PI * 2); const speed = rand(0.3, 1.2); return { x: x + rand(-6, 6), y: y + rand(-6, 6), r: rand(1.2, 2.8), vx: Math.cos(angle) * speed, vy: Math.sin(angle) * speed, life: Math.floor(rand(60, 140)), opacity: rand(0.25, 0.85) }; }
    function init() { DPR = Math.max(1, window.devicePixelRatio || 1); resize(); particles.length = 0; for (let i = 0; i < COUNT; i++) particles.push(makeParticle(canvas.width / DPR, canvas.height / DPR)); }
    function drawFrame(now) { ctx.clearRect(0, 0, canvas.width, canvas.height); ctx.fillStyle = 'rgba(59,130,246,0.06)'; ctx.fillRect(0, 0, canvas.width / DPR, canvas.height / DPR); for (let p of particles) { p.phase += 0.018 * p.speed; p.x += p.vx * p.speed + Math.sin(p.phase) * 0.12 + globalVelocity.x; p.y += p.vy * p.speed + Math.cos(p.phase * 0.7) * 0.08 + globalVelocity.y; if (p.x < -10) p.x = canvas.width / DPR + 10; if (p.x > canvas.width / DPR + 10) p.x = -10; if (p.y < -10) p.y = canvas.height / DPR + 10; if (p.y > canvas.height / DPR + 10) p.y = -10; ctx.beginPath(); ctx.fillStyle = `rgba(255,255,255,${p.opacity})`; ctx.arc(p.x, p.y, p.r, 0, Math.PI * 2); ctx.fill(); }
      for (let i = cursorParticles.length - 1; i >= 0; i--) { const cp = cursorParticles[i]; cp.x += cp.vx * 0.6 + globalVelocity.x * 1.6; cp.y += cp.vy * 0.6 + globalVelocity.y * 1.6; cp.life -= 1; if (cp.life <= 0) { cursorParticles.splice(i, 1); continue; } ctx.beginPath(); ctx.fillStyle = `rgba(255,255,255,${Math.max(0.06, cp.opacity * (cp.life / 120))})`; ctx.arc(cp.x, cp.y, cp.r, 0, Math.PI * 2); ctx.fill(); }
      ctx.save(); ctx.globalCompositeOperation = 'destination-out'; ctx.fillStyle = 'black'; const fontSize = Math.max(40, Math.min(140, (canvas.width / DPR) * 0.09)); ctx.font = `700 ${fontSize}px Inter, system-ui, -apple-system, 'Segoe UI', Roboto, 'Helvetica Neue', Arial`; ctx.textAlign = 'center'; ctx.textBaseline = 'middle'; const textX = (canvas.width / DPR) * 0.72; const textY = (canvas.height / DPR) * 0.88; try { ctx.fillText(@json(config('app.company_name', 'CLIENTBRIDGE')), textX, textY); } catch (e) { } ctx.restore(); }
    let animationId = null; if (mediaReduced.matches) { init(); drawFrame(); return; } window.addEventListener('resize', () => { init(); }); init(); animationId = requestAnimationFrame(function loop(now){ drawFrame(now); animationId = requestAnimationFrame(loop); }); document.addEventListener('visibilitychange', () => { if (document.hidden) { if (animationId) cancelAnimationFrame(animationId); } else { animationId = requestAnimationFrame(function loop(now){ drawFrame(now); animationId = requestAnimationFrame(loop); }); } });
  })();
</script>
@endpush

@push('styles')
<style>
  @media (max-width: 576px) { .toast { font-size: 0.75rem; } }
  .login-lines-overlay { z-index: 0; mix-blend-mode: normal; }
  #loginParticles { display: block; width: 100%; height: 100%; }
  @media (max-width: 420px) { .login-lines-overlay { opacity: 0.6; } }
  .bg-gray-800\/90 { position: relative; z-index: 10; }
</style>
@endpush

@push('styles')
<style>
  /* Toast sizing for small screens (unchanged) */
  @media (max-width: 576px) {
    .toast { font-size: 0.75rem; }
  }

  /* Canvas particle overlay */
  .login-lines-overlay { z-index: 0; mix-blend-mode: normal; }
  #loginParticles { display: block; width: 100%; height: 100%; }

  /* Slightly dim the overlay on very small screens for legibility */
  @media (max-width: 420px) {
    .login-lines-overlay { opacity: 0.6; }
  }

  /* Ensure the login card remains above the animation */
  .bg-gray-800\/90 { position: relative; z-index: 10; }
</style>
@endpush
