@php
  use Illuminate\Support\Str;

  $tenancyEnabled = (bool) ($tenancyEnabled ?? config('app.tenancy_enabled', false));
  $trialUrl = rtrim((string) config('app.url'), '/').route('trial.show', absolute: false);
  $googleLoginUrl = route('auth.google.redirect');
@endphp
@extends('layouts.guest')

@section('content')

<div class="relative w-full flex justify-center items-center px-3 min-h-screen bg-gradient-to-br from-gray-100 via-slate-50 to-blue-100/40 dark:from-gray-900 dark:via-gray-900 dark:to-blue-950/30">

  {{-- Animated particle overlay (canvas) --}}
  <div class="login-lines-overlay absolute inset-0 z-0 pointer-events-none" aria-hidden="true">
    <canvas id="loginParticles" class="absolute inset-0 w-full h-full" aria-hidden="true"></canvas>
  </div>

  <div class="px-4 w-full max-w-6xl z-10">
    <div class="grid gap-6 lg:grid-cols-[minmax(0,1.05fr)_minmax(320px,0.95fr)] lg:items-stretch">
      <div class="bg-white/90 dark:bg-gray-800/90 border border-gray-200 dark:border-gray-700 rounded-[28px] shadow-xl p-6 sm:p-8">
        <div class="space-y-6">
          <div class="space-y-3">
            <div class="inline-flex items-center gap-2 rounded-full border border-blue-200 bg-blue-50 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.18em] text-blue-700 dark:border-blue-500/30 dark:bg-blue-500/10 dark:text-blue-200">
              {{ $tenancyEnabled ? 'Existing Workspace' : 'Organization Login' }}
            </div>
            <div>
              <h2 class="text-3xl font-bold tracking-tight text-gray-900 dark:text-gray-100">Welcome back</h2>
              <p class="mt-2 max-w-xl text-sm leading-6 text-gray-600 dark:text-gray-300">Sign in with your email and password, or take the faster path with one-click Google login if your account is already connected.</p>
            </div>
          </div>

  <form method="POST" action="{{ route('login') }}" class="space-y-4">
    @csrf

    <div>
      <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">EMAIL ADDRESS</label>
      <input type="email" id="email" name="email" value="{{ old('email', request('email')) }}" class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-gray-100 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" required autofocus>
      @error('email')
        <div class="text-red-400 text-sm mt-1">{{ $message }}</div>
      @enderror
    </div>

    <div>
      <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">PASSWORD</label>
      <div x-data="{ show: false }" class="relative">
        <input type="password" x-bind:type="show ? 'text' : 'password'" id="password" name="password" class="w-full pr-12 px-3 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-gray-100 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
        <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 px-3 flex items-center text-gray-500 dark:text-gray-300 hover:text-gray-700 dark:hover:text-white z-10" aria-label="Toggle password visibility">
          <svg x-show="!show" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12Z"/>
            <circle cx="12" cy="12" r="3"/>
          </svg>
          <svg x-show="show" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
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
      <button type="submit" class="w-full btn-primary py-3">{{ $tenancyEnabled ? 'Continue to workspace' : 'Sign in' }}</button>
    </div>
  </form>
  
  <div class="mt-3 text-center">
    <a href="{{ route('password.request') }}" class="text-sm text-blue-400 hover:underline">Forgot your password?</a>
  </div>

  @unless($tenancyEnabled)
  <div class="mt-2 text-center">
    <a href="{{ route('register') }}" class="text-sm text-blue-400 hover:underline">Create a new account</a>
  </div>
  @endunless
  
  <div class="flex items-center gap-3 mt-1 mb-3">
    <span class="h-px bg-gray-300 dark:bg-gray-700 flex-1"></span>
    <span class="text-xs uppercase tracking-wider text-gray-500 dark:text-gray-400">Faster Path</span>
    <span class="h-px bg-gray-300 dark:bg-gray-700 flex-1"></span>
  </div>
    
  {{-- Google OAuth Link --}}
  <div class="mt-4">
    <a href="{{ $googleLoginUrl }}" class="w-full inline-flex items-center justify-center px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
      <svg width="18" height="18" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" class="mr-2">
        <path fill="#FFC107" d="M43.611,20.083H42V20H24v8h11.303c-1.649,4.657-6.08,8-11.303,8c-6.627,0-12-5.373-12-12c0-6.627,5.373-12,12-12c3.059,0,5.842,1.154,7.961,3.039l5.657-5.657C34.046,6.053,29.268,4,24,4C12.955,4,4,12.955,4,24c0,11.045,8.955,20,20,20c11.045,0,20-8.955,20-20C44,22.659,43.862,21.35,43.611,20.083z"/>
        <path fill="#FF3D00" d="M6.306,14.691l6.571,4.819C14.655,15.108,18.961,12,24,12c3.059,0,5.842,1.154,7.961,3.039l5.657-5.657C34.046,6.053,29.268,4,24,4C16.318,4,9.656,8.337,6.306,14.691z"/>
        <path fill="#4CAF50" d="M24,44c5.166,0,9.86-1.977,13.409-5.192l-6.19-5.238C29.211,35.091,26.715,36,24,36c-5.202,0-9.619-3.317-11.283-7.946l-6.522,5.025C9.505,39.556,16.227,44,24,44z"/>
        <path fill="#1976D2" d="M43.611,20.083H42V20H24v8h11.303c-0.792,2.237-2.231,4.166-4.087,5.571c0.001-0.001,0.002-0.001,0.003-0.002l6.19,5.238C36.971,39.205,44,34,44,24C44,22.659,43.862,21.35,43.611,20.083z"/>
      </svg>
      One-click login with Google
    </a>
    <p class="mt-3 text-center text-xs leading-5 text-gray-500 dark:text-gray-400">Using Gmail or Google Workspace? Skip password friction and get into your workspace in one click.</p>
  </div>
        </div>
      </div>

      <div class="space-y-5">
        @if($tenancyEnabled)
          <div class="rounded-[28px] border border-slate-800 bg-slate-900 p-6 text-white shadow-xl shadow-slate-900/20">
            <div class="inline-flex items-center gap-2 rounded-full bg-white/10 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.18em] text-cyan-200">New Here?</div>
            <h3 class="mt-4 text-2xl font-semibold tracking-tight">Create a new workspace</h3>
            <p class="mt-3 text-sm leading-6 text-slate-300">If you are setting up smbgen for the first time, start with registration. You can launch your tenant, choose your subdomain, and come back to this screen anytime after setup.</p>
            <a href="{{ $trialUrl }}" class="mt-5 inline-flex items-center justify-center rounded-xl bg-cyan-400 px-4 py-3 text-sm font-semibold text-slate-950 transition-colors hover:bg-cyan-300">Start registration</a>
            <p class="mt-3 text-xs leading-5 text-slate-400">This is the best path for new businesses evaluating the platform.</p>
          </div>
        @else
          <div class="rounded-[28px] border border-slate-800 bg-slate-900 p-6 text-white shadow-xl shadow-slate-900/20">
            <div class="inline-flex items-center gap-2 rounded-full bg-white/10 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.18em] text-cyan-200">New Here?</div>
            <h3 class="mt-4 text-2xl font-semibold tracking-tight">Create a new account</h3>
            <p class="mt-3 text-sm leading-6 text-slate-300">Use registration to join your organization. If you already have an account, sign in above or use the password reset link if needed.</p>
            <a href="{{ route('register') }}" class="mt-5 inline-flex items-center justify-center rounded-xl bg-cyan-400 px-4 py-3 text-sm font-semibold text-slate-950 transition-colors hover:bg-cyan-300">Register account</a>
            <p class="mt-3 text-xs leading-5 text-slate-400">Standard organization login flow is active while multi-tenancy is disabled.</p>
          </div>
        @endif

        <div class="rounded-[28px] border border-gray-200 bg-white/85 p-6 shadow-lg shadow-slate-200/60 backdrop-blur-sm dark:border-gray-700 dark:bg-gray-800/85 dark:shadow-none">
          <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Flow Map</p>
          <div class="mt-4 space-y-4">
            <div class="flex gap-4">
              <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-blue-100 text-sm font-semibold text-blue-700 dark:bg-blue-500/20 dark:text-blue-200">1</div>
              <div>
                <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">Pick your path</p>
                <p class="mt-1 text-sm leading-6 text-gray-600 dark:text-gray-300">Returning users sign in here. New users start registration. Google users can use the one-click option.</p>
              </div>
            </div>
            <div class="flex gap-4">
              <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-violet-100 text-sm font-semibold text-violet-700 dark:bg-violet-500/20 dark:text-violet-200">2</div>
              <div>
                <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">Confirm access fast</p>
                <p class="mt-1 text-sm leading-6 text-gray-600 dark:text-gray-300">Use email and password for standard login, or Google for the fastest path when your account is already linked.</p>
              </div>
            </div>
            <div class="flex gap-4">
              <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-emerald-100 text-sm font-semibold text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-200">3</div>
              <div>
                <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">Land in the right dashboard</p>
                <p class="mt-1 text-sm leading-6 text-gray-600 dark:text-gray-300">Once authenticated, smbgen routes you into the correct workspace dashboard for your role and tenant.</p>
              </div>
            </div>
          </div>
        </div>

        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm leading-6 text-emerald-900 dark:border-emerald-500/20 dark:bg-emerald-500/10 dark:text-emerald-100">
          <span class="font-semibold">Google shortcut:</span> if your account already uses Google, the one-click login above is the fastest way in.
        </div>
      </div>
    </div>
  </div>

  {{-- Footer --}}
  <footer class="absolute bottom-0 w-full text-center pb-3 text-gray-500 dark:text-gray-400 text-sm z-10">
    <div class="opacity-75">
      © {{ date('Y') }} {{ config('app.company_name', config('app.name')) }} ·
      <a href="{{ config('app.url') }}/privacy-policy" class="text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white underline">Privacy</a> ·
      <a href="{{ config('app.url') }}/terms" class="text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white underline">Terms</a>
    </div>
  </footer>

</div>

{{-- Toasts (consolidated) --}}
<div class="fixed top-0 right-0 p-3 z-50 max-w-xs pointer-events-auto">
  <div id="secureSessionToast" class="bg-white dark:bg-gray-800 text-gray-900 dark:text-white border border-gray-200 dark:border-gray-600 rounded-lg shadow-lg w-full opacity-100 pointer-events-none" role="alert">
    <div class="bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white border-b-0 py-1 px-2 rounded-t-lg flex items-center">
      <strong class="text-xs">Session protection enabled</strong>
      <small class="text-xs ml-auto text-right">    <strong>{{ config('app.company_name', config('app.name')) }}</strong><br> cloud monitoring</small>
    </div>
    <div class="text-xs py-2 px-3">
      <div>IP: {{ request()->ip() }}</div>
      <div>Browser: {{ Str::limit(request()->header('User-Agent'), 38) }}</div>
      <div class="mt-2">Session ID: <span id="sessionIdInline">{{ Str::uuid() }}</span></div>
    </div>
  </div>

  <a id="sessionPill" href="{{ config('app.url') }}" target="_blank" rel="noopener noreferrer" aria-label="Open {{ config('app.name') }} in a new tab" class="hidden bg-green-600 text-white text-xs rounded-full px-2 py-0.5 shadow-lg mt-2 text-center transform origin-center pointer-events-auto cursor-pointer">
    <strong>{{ config('app.company_name', config('app.name')) }}</strong>
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
    function run() {
    const canvas = document.getElementById('loginParticles');
    if (!canvas) {
      return;
    }

    const mediaReduced = window.matchMedia('(prefers-reduced-motion: reduce)');
    const ctx = canvas.getContext && canvas.getContext('2d');
    if (!ctx) {
      return;
    }

    let DPR = Math.max(1, window.devicePixelRatio || 1);
    const COUNT = 900;
    const particles = [];
    const cursorParticles = [];
    const globalVelocity = { x: -0.008, y: -0.005 };
    let animationId = null;

    function isDarkTheme() {
      return document.documentElement.classList.contains('dark');
    }

    function getPalette() {
      if (isDarkTheme()) {
        return {
          wash: 'rgba(59,130,246,0.06)',
          particleRgb: '255,255,255',
          cursorRgb: '255,255,255',
          opacityBoost: 1,
        };
      }

      return {
        wash: 'rgba(0,0,0,0)',
        particleRgb: '30,41,59',
        cursorRgb: '15,23,42',
        opacityBoost: 2.8,
      };
    }

    function resize() {
      const rect = canvas.getBoundingClientRect();
      canvas.width = Math.round(rect.width * DPR);
      canvas.height = Math.round(rect.height * DPR);
      canvas.style.width = rect.width + 'px';
      canvas.style.height = rect.height + 'px';
      ctx.setTransform(DPR, 0, 0, DPR, 0, 0);
    }

    function rand(min, max) {
      return Math.random() * (max - min) + min;
    }

    function makeParticle(w, h) {
      return {
        x: rand(0, w),
        y: rand(0, h),
        r: rand(0.9, 2.2),
        vx: rand(-0.18, 0.18),
        vy: rand(-0.18, 0.18),
        phase: rand(0, Math.PI * 2),
        speed: rand(0.12, 0.5),
        opacity: rand(0.05, 0.22),
      };
    }

    function makeCursorParticle(x, y) {
      const angle = rand(0, Math.PI * 2);
      const speed = rand(0.3, 1.2);

      return {
        x: x + rand(-6, 6),
        y: y + rand(-6, 6),
        r: rand(1.2, 2.8),
        vx: Math.cos(angle) * speed,
        vy: Math.sin(angle) * speed,
        life: Math.floor(rand(60, 140)),
        opacity: rand(0.25, 0.85),
      };
    }

    function init() {
      DPR = Math.max(1, window.devicePixelRatio || 1);
      resize();
      particles.length = 0;

      for (let i = 0; i < COUNT; i++) {
        particles.push(makeParticle(canvas.width / DPR, canvas.height / DPR));
      }
    }

    function drawFrame() {
      const palette = getPalette();

      ctx.clearRect(0, 0, canvas.width, canvas.height);
      ctx.fillStyle = palette.wash;
      ctx.fillRect(0, 0, canvas.width / DPR, canvas.height / DPR);

      for (const p of particles) {
        p.phase += 0.018 * p.speed;
        p.x += p.vx * p.speed + Math.sin(p.phase) * 0.12 + globalVelocity.x;
        p.y += p.vy * p.speed + Math.cos(p.phase * 0.7) * 0.08 + globalVelocity.y;

        if (p.x < -10) {
          p.x = canvas.width / DPR + 10;
        }
        if (p.x > canvas.width / DPR + 10) {
          p.x = -10;
        }
        if (p.y < -10) {
          p.y = canvas.height / DPR + 10;
        }
        if (p.y > canvas.height / DPR + 10) {
          p.y = -10;
        }

        const particleOpacity = Math.min(0.48, p.opacity * palette.opacityBoost);
        ctx.beginPath();
        ctx.fillStyle = `rgba(${palette.particleRgb},${particleOpacity})`;
        ctx.arc(p.x, p.y, p.r, 0, Math.PI * 2);
        ctx.fill();
      }

      for (let i = cursorParticles.length - 1; i >= 0; i--) {
        const cp = cursorParticles[i];
        cp.x += cp.vx * 0.6 + globalVelocity.x * 1.6;
        cp.y += cp.vy * 0.6 + globalVelocity.y * 1.6;
        cp.life -= 1;

        if (cp.life <= 0) {
          cursorParticles.splice(i, 1);
          continue;
        }

        const cursorOpacity = Math.max(0.06, cp.opacity * (cp.life / 120) * palette.opacityBoost);
        ctx.beginPath();
        ctx.fillStyle = `rgba(${palette.cursorRgb},${cursorOpacity})`;
        ctx.arc(cp.x, cp.y, cp.r, 0, Math.PI * 2);
        ctx.fill();
      }

      ctx.save();
      ctx.globalCompositeOperation = 'destination-out';
      ctx.fillStyle = 'black';

      const fontSize = Math.max(20, Math.min(56, (canvas.width / DPR) * 0.04));
      ctx.font = `700 ${fontSize}px Inter, system-ui, -apple-system, 'Segoe UI', Roboto, 'Helvetica Neue', Arial`;
      ctx.textAlign = 'right';
      ctx.textBaseline = 'middle';

      const textX = (canvas.width / DPR) - 32;
      const textY = (canvas.height / DPR) * 0.88;

      try {
        ctx.fillText(@json(config('app.company_name', config('app.name'))), textX, textY);
      } catch (e) {
        // no-op
      }

      ctx.restore();
    }

    window.addEventListener('mousemove', (e) => {
      const rect = canvas.getBoundingClientRect();
      const x = e.clientX - rect.left;
      const y = e.clientY - rect.top;

      for (let i = 0; i < 4; i++) {
        cursorParticles.push(makeCursorParticle(x, y));
      }
    });

    window.addEventListener('touchmove', (e) => {
      if (!e.touches || !e.touches[0]) {
        return;
      }

      const rect = canvas.getBoundingClientRect();
      const x = e.touches[0].clientX - rect.left;
      const y = e.touches[0].clientY - rect.top;

      for (let i = 0; i < 3; i++) {
        cursorParticles.push(makeCursorParticle(x, y));
      }
    }, { passive: true });

    function startLoop() {
      animationId = requestAnimationFrame(function loop() {
        drawFrame();
        animationId = requestAnimationFrame(loop);
      });
    }

    function stopLoop() {
      if (animationId) {
        cancelAnimationFrame(animationId);
        animationId = null;
      }
    }

    init();

    if (mediaReduced.matches) {
      drawFrame();
      return;
    }

    startLoop();

    window.addEventListener('resize', () => {
      init();
    });

    document.addEventListener('visibilitychange', () => {
      if (document.hidden) {
        stopLoop();
      } else {
        startLoop();
      }
    });
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', run);
  } else {
    run();
  }
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

