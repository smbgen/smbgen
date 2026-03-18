{{-- Marketing Footer — standalone, no CMS dependency --}}
<footer class="bg-slate-900 border-t border-slate-800">

    <div class="max-w-6xl mx-auto px-6 pt-14 pb-10">
        <div class="flex flex-col md:flex-row justify-between items-start gap-10">

            {{-- Brand --}}
            <div class="max-w-xs">
                <a href="/" class="flex items-center gap-2.5 mb-4 group">
                    <div class="w-7 h-7 bg-blue-600 rounded-lg flex items-center justify-center shrink-0">
                        <svg class="w-3.5 h-3.5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                    </div>
                    <span class="font-extrabold text-white">smbgen</span>
                </a>
                <p class="text-slate-500 text-sm leading-relaxed">
                    Build fast. Deliver beautifully. Grow aggressively.
                </p>
            </div>

            {{-- Link columns --}}
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-10 text-sm">

                <div>
                    <h4 class="text-white font-bold mb-4 text-xs uppercase tracking-widest">Platform</h4>
                    <div class="flex flex-col gap-2.5 text-slate-500">
                        <a href="#platform" class="hover:text-white transition-colors">Rapid Dev</a>
                        <a href="#cloud"    class="hover:text-white transition-colors">Cloud Delivery</a>
                        <a href="#services" class="hover:text-white transition-colors">CMS</a>
                    </div>
                </div>

                <div>
                    <h4 class="text-white font-bold mb-4 text-xs uppercase tracking-widest">Services</h4>
                    <div class="flex flex-col gap-2.5 text-slate-500">
                        <a href="#services" class="hover:text-white transition-colors">Design</a>
                        <a href="#services" class="hover:text-white transition-colors">Automation</a>
                        <a href="#growth"   class="hover:text-white transition-colors">Growth</a>
                    </div>
                </div>

                <div>
                    <h4 class="text-white font-bold mb-4 text-xs uppercase tracking-widest">Company</h4>
                    <div class="flex flex-col gap-2.5 text-slate-500">
                        @if(Route::has('blog.index'))
                            <a href="{{ route('blog.index') }}" class="hover:text-white transition-colors">Blog</a>
                        @endif
                        <a href="/contact" class="hover:text-white transition-colors">Contact</a>
                        <a href="{{ route('login') }}" class="hover:text-white transition-colors">Sign in</a>
                        <a href="{{ route('register') }}" class="hover:text-white transition-colors">Get started</a>
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- Bottom bar --}}
    <div class="border-t border-slate-800">
        <div class="max-w-6xl mx-auto px-6 py-5 flex flex-col sm:flex-row justify-between items-center gap-3">
            <p class="text-slate-600 text-xs">
                &copy; {{ date('Y') }} smbgen. All rights reserved.
            </p>
            <div class="flex gap-5 text-xs text-slate-600">
                @if(Route::has('legal.privacy'))
                    <a href="{{ route('legal.privacy') }}" class="hover:text-slate-400 transition-colors">Privacy</a>
                @endif
                @if(Route::has('legal.eula'))
                    <a href="{{ route('legal.eula') }}" class="hover:text-slate-400 transition-colors">Terms</a>
                @endif
            </div>
        </div>
    </div>

</footer>
