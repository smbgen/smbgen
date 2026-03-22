{{-- ── smbgen Frontend Footer ──────────────────────────────────────── --}}
<footer class="bg-slate-950 border-t border-slate-800">

    <div class="max-w-6xl mx-auto px-6 pt-16 pb-10">
        <div class="flex flex-col lg:flex-row justify-between items-start gap-12">

            {{-- Brand column --}}
            <div class="max-w-sm">
                <a href="{{ route('home') }}" class="flex items-center gap-2.5 mb-5 group">
                    <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                    </div>
                    <span class="font-extrabold text-white text-lg">smbgen</span>
                </a>
                <p class="text-slate-400 text-sm leading-relaxed mb-4">
                    The next-generation AI-powered platform for small &amp; mid-market businesses.
                    Build fast. Deliver beautifully. Grow aggressively.
                </p>
                <div class="flex flex-wrap gap-2">
                    <span class="text-xs font-semibold text-slate-500 bg-slate-800 px-2.5 py-1 rounded-md">AI-Native</span>
                    <span class="text-xs font-semibold text-slate-500 bg-slate-800 px-2.5 py-1 rounded-md">Distributed</span>
                    <span class="text-xs font-semibold text-slate-500 bg-slate-800 px-2.5 py-1 rounded-md">SMB-First</span>
                </div>
            </div>

            {{-- Link columns --}}
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-10 text-sm">

                <div>
                    <h4 class="text-white font-bold mb-4 text-xs uppercase tracking-widest">Platform</h4>
                    <div class="flex flex-col gap-2.5 text-slate-500">
                        <a href="{{ route('home') }}#platform"  class="hover:text-white transition-colors">Rapid Dev</a>
                        <a href="{{ route('home') }}#platform"  class="hover:text-white transition-colors">Cloud Delivery</a>
                        <a href="{{ route('home') }}#platform"  class="hover:text-white transition-colors">CMS</a>
                        <a href="{{ route('home') }}#ai"        class="hover:text-white transition-colors">AI Engine</a>
                    </div>
                </div>

                <div>
                    <h4 class="text-white font-bold mb-4 text-xs uppercase tracking-widest">Services</h4>
                    <div class="flex flex-col gap-2.5 text-slate-500">
                        <a href="{{ route('home') }}#services" class="hover:text-white transition-colors">Web Design</a>
                        <a href="{{ route('home') }}#services" class="hover:text-white transition-colors">Social Media</a>
                        <a href="{{ route('home') }}#services" class="hover:text-white transition-colors">Email Marketing</a>
                        <a href="{{ route('home') }}#services" class="hover:text-white transition-colors">Lead Gen</a>
                    </div>
                </div>

                <div>
                    <h4 class="text-white font-bold mb-4 text-xs uppercase tracking-widest">Growth</h4>
                    <div class="flex flex-col gap-2.5 text-slate-500">
                        <a href="{{ route('home') }}#growth" class="hover:text-white transition-colors">Lead Capture</a>
                        <a href="{{ route('home') }}#growth" class="hover:text-white transition-colors">Lead Management</a>
                        <a href="{{ route('home') }}#growth" class="hover:text-white transition-colors">SEO</a>
                        <a href="{{ route('home') }}#growth" class="hover:text-white transition-colors">Paid Acquisition</a>
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

                <div>
                    <h4 class="text-white font-bold mb-4 text-xs uppercase tracking-widest">Partners</h4>
                    <div class="flex flex-col gap-2.5 text-slate-500">
                        <a href="{{ route('partners.l7') }}" class="hover:text-white transition-colors">L7 Labs</a>
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
