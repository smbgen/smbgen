<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Glasgow Design Build | Raleigh Home Renovation & Remodeling</title>
    <meta name="description" content="Glasgow Design Build is Raleigh, NC's premier expert for home renovation and remodeling, specializing in historic home restoration services.">

    {{-- Open Graph --}}
    <meta property="og:title" content="Glasgow Design Build | Raleigh Home Renovation & Remodeling">
    <meta property="og:description" content="Raleigh's premier home renovation and remodeling experts. Historic restoration, kitchens, baths, decks, and whole-home transformations.">
    <meta property="og:image" content="https://glasgowdb.com/wp-content/uploads/2024/02/GDB-color-logo.png">
    <meta property="og:type" content="website">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <style>
        :root {
            --gdb-navy:    #1F3A8A;
            --gdb-charcoal:#4A4A4A;
            --gdb-gold:    #D4A843;
            --bg-dark:     #0d1b2a;
            --bg-mid:      #0f2236;
            --surface:     rgba(255,255,255,.05);
            --border:      rgba(255,255,255,.10);
            --text-muted:  #94a3b8;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            font-family: 'Inter', sans-serif;
            background: var(--bg-dark);
            color: #f1f5f9;
        }

        /* ── Nav ─────────────────────────────────── */
        .gdb-nav {
            position: sticky;
            top: 0;
            z-index: 100;
            background: rgba(13,27,42,.92);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--border);
            padding: .9rem 1.5rem;
        }

        .gdb-nav .nav-logo img { height: 44px; object-fit: contain; }

        .gdb-nav .nav-links a {
            color: #cbd5e1;
            text-decoration: none;
            font-size: .9rem;
            font-weight: 500;
            transition: color .2s;
        }

        .gdb-nav .nav-links a:hover { color: #fff; }

        .gdb-nav .nav-cta {
            background: var(--gdb-navy);
            color: #fff !important;
            padding: .45rem 1.1rem;
            border-radius: 8px;
            font-weight: 600 !important;
            font-size: .85rem !important;
        }

        .gdb-nav .nav-cta:hover { background: #2a4fa8 !important; }

        /* ── Hero ────────────────────────────────── */
        .hero {
            min-height: 92vh;
            display: grid;
            place-items: center;
            padding: 5rem 1.5rem;
            background:
                radial-gradient(ellipse at 60% 0%, rgba(31,58,138,.35) 0%, transparent 55%),
                radial-gradient(ellipse at 20% 80%, rgba(212,168,67,.12) 0%, transparent 40%),
                linear-gradient(180deg, #0d1b2a 0%, #061020 100%);
            position: relative;
            overflow: hidden;
        }

        .hero::before {
            content: '';
            position: absolute;
            inset: 0;
            background: url('https://glasgowdb.com/wp-content/uploads/2024/02/avery-outside-after.jpg') center/cover no-repeat;
            opacity: .10;
        }

        .hero-content { position: relative; max-width: 1100px; width: 100%; }

        .hero-pill {
            display: inline-flex;
            align-items: center;
            gap: .5rem;
            background: rgba(31,58,138,.25);
            border: 1px solid rgba(31,58,138,.45);
            color: #93c5fd;
            border-radius: 999px;
            padding: .5rem 1rem;
            font-size: .85rem;
            font-weight: 500;
            margin-bottom: 1.5rem;
        }

        .hero-pill .dot {
            width: 7px; height: 7px;
            border-radius: 50%;
            background: #60a5fa;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%,100% { opacity: 1; }
            50%      { opacity: .4; }
        }

        .hero-logo {
            height: 80px;
            object-fit: contain;
            margin-bottom: 2rem;
            filter: brightness(0) invert(1);
        }

        .hero-title {
            font-size: clamp(2.6rem, 5.5vw, 4.8rem);
            font-weight: 900;
            letter-spacing: -.04em;
            line-height: 1.02;
            margin-bottom: 1.25rem;
        }

        .hero-title span { color: #60a5fa; }

        .hero-sub {
            font-size: 1.15rem;
            color: var(--text-muted);
            max-width: 660px;
            line-height: 1.85;
            margin-bottom: 2.5rem;
        }

        .hero-stats {
            display: flex;
            flex-wrap: wrap;
            gap: 2rem;
            margin-top: 3rem;
            padding-top: 2rem;
            border-top: 1px solid var(--border);
        }

        .hero-stat span { font-size: 2rem; font-weight: 800; color: #fff; }
        .hero-stat p    { font-size: .8rem; color: var(--text-muted); margin: 0; }

        /* ── Buttons ──────────────────────────────── */
        .btn-gdb-primary {
            background: var(--gdb-navy);
            color: #fff;
            border: none;
            border-radius: 10px;
            padding: .8rem 1.8rem;
            font-weight: 700;
            font-size: .95rem;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: .45rem;
            transition: background .2s, transform .15s;
        }

        .btn-gdb-primary:hover { background: #2a4fa8; color: #fff; transform: translateY(-1px); }

        .btn-gdb-outline {
            background: transparent;
            color: #cbd5e1;
            border: 1px solid rgba(255,255,255,.2);
            border-radius: 10px;
            padding: .8rem 1.8rem;
            font-weight: 600;
            font-size: .95rem;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: .45rem;
            transition: border-color .2s, color .2s;
        }

        .btn-gdb-outline:hover { border-color: rgba(255,255,255,.45); color: #fff; }

        /* ── Sections ─────────────────────────────── */
        .section { padding: 6rem 1.5rem; }
        .section-dark  { background: var(--bg-dark); }
        .section-mid   { background: var(--bg-mid); }
        .section-label {
            font-size: .75rem;
            font-weight: 700;
            letter-spacing: .12em;
            text-transform: uppercase;
            color: #60a5fa;
            margin-bottom: .6rem;
        }

        h2.section-title {
            font-size: clamp(1.8rem, 3vw, 2.6rem);
            font-weight: 800;
            letter-spacing: -.03em;
            margin-bottom: 1rem;
        }

        .section-desc { color: var(--text-muted); max-width: 620px; line-height: 1.8; }

        /* ── Cards ───────────────────────────────── */
        .gdb-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: 1.8rem;
            height: 100%;
            transition: border-color .2s, transform .2s;
        }

        .gdb-card:hover { border-color: rgba(96,165,250,.35); transform: translateY(-2px); }

        .gdb-card .card-icon {
            width: 48px; height: 48px;
            border-radius: 12px;
            background: rgba(31,58,138,.3);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.35rem;
            margin-bottom: 1.1rem;
        }

        .gdb-card h4 { font-size: 1.05rem; font-weight: 700; margin-bottom: .5rem; }
        .gdb-card p  { font-size: .9rem; color: var(--text-muted); line-height: 1.7; margin: 0; }

        /* ── Gallery ─────────────────────────────── */
        .gallery-grid {
            display: grid;
            grid-template-columns: repeat(12, 1fr);
            grid-template-rows: 260px 260px;
            gap: 12px;
        }

        .gallery-grid .g-item { border-radius: 16px; overflow: hidden; position: relative; }
        .gallery-grid .g-item img {
            width: 100%; height: 100%;
            object-fit: cover;
            transition: transform .4s;
        }
        .gallery-grid .g-item:hover img { transform: scale(1.04); }
        .gallery-grid .g-item .g-label {
            position: absolute;
            bottom: 0; left: 0; right: 0;
            background: linear-gradient(0deg, rgba(0,0,0,.7), transparent);
            padding: 1.2rem .9rem .7rem;
            font-size: .8rem;
            font-weight: 600;
            color: #fff;
        }

        .g1 { grid-column: 1/6; grid-row: 1/2; }
        .g2 { grid-column: 6/10; grid-row: 1/2; }
        .g3 { grid-column: 10/13; grid-row: 1/3; }
        .g4 { grid-column: 1/4; grid-row: 2/3; }
        .g5 { grid-column: 4/10; grid-row: 2/3; }

        @media (max-width: 768px) {
            .gallery-grid {
                grid-template-columns: 1fr 1fr;
                grid-template-rows: auto;
            }
            .g1,.g2,.g3,.g4,.g5 { grid-column: auto; grid-row: auto; height: 200px; }
        }

        /* ── Process ─────────────────────────────── */
        .process-step {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 18px;
            padding: 1.6rem;
            height: 100%;
            position: relative;
        }

        .process-step .step-num {
            width: 44px; height: 44px;
            border-radius: 50%;
            background: var(--gdb-navy);
            color: #fff;
            font-weight: 800;
            font-size: 1.05rem;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1rem;
        }

        .process-step h5 { font-weight: 700; font-size: 1rem; margin-bottom: .4rem; }
        .process-step p  { font-size: .88rem; color: var(--text-muted); margin: 0; }

        /* ── Testimonials ────────────────────────── */
        .testimonial-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: 2rem;
            height: 100%;
        }

        .testimonial-card .stars { color: #facc15; font-size: 1rem; margin-bottom: .75rem; }
        .testimonial-card blockquote { font-size: .95rem; color: #e2e8f0; line-height: 1.75; font-style: italic; margin: 0 0 1rem; }
        .testimonial-card .reviewer { font-size: .85rem; font-weight: 600; color: #94a3b8; }

        /* ── Team ────────────────────────────────── */
        .team-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 20px;
            overflow: hidden;
            height: 100%;
        }

        .team-card img { width: 100%; height: 220px; object-fit: cover; }
        .team-card .team-info { padding: 1.3rem; }
        .team-card h5 { font-weight: 700; font-size: 1rem; margin-bottom: .2rem; }
        .team-card p  { font-size: .85rem; color: var(--text-muted); margin: 0; }

        /* ── Blog ────────────────────────────────── */
        .blog-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 18px;
            overflow: hidden;
            height: 100%;
            text-decoration: none;
            display: block;
            transition: border-color .2s, transform .2s;
        }

        .blog-card:hover { border-color: rgba(96,165,250,.35); transform: translateY(-2px); }
        .blog-card img { width: 100%; height: 180px; object-fit: cover; }
        .blog-card .blog-body { padding: 1.3rem; }
        .blog-card .blog-cat { font-size: .72rem; font-weight: 700; text-transform: uppercase; letter-spacing: .1em; color: #60a5fa; margin-bottom: .5rem; }
        .blog-card h5 { font-size: .95rem; font-weight: 700; color: #f1f5f9; line-height: 1.45; margin: 0; }

        /* ── Certifications ──────────────────────── */
        .cert-pill {
            display: inline-flex;
            align-items: center;
            gap: .6rem;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: .7rem 1.2rem;
            font-size: .85rem;
            font-weight: 500;
            color: #cbd5e1;
            text-decoration: none;
            transition: border-color .2s, color .2s;
        }

        .cert-pill:hover { border-color: rgba(96,165,250,.4); color: #fff; }
        .cert-pill i { color: #60a5fa; font-size: 1rem; }

        /* ── CTA panel ───────────────────────────── */
        .cta-panel {
            background: linear-gradient(135deg, rgba(31,58,138,.25), rgba(212,168,67,.08));
            border: 1px solid rgba(31,58,138,.4);
            border-radius: 28px;
            padding: 3rem 2.5rem;
        }

        .cta-panel h2 { font-size: clamp(1.7rem,3vw,2.4rem); font-weight: 800; letter-spacing: -.03em; }

        /* ── Footer ──────────────────────────────── */
        footer {
            background: #060e18;
            border-top: 1px solid var(--border);
            padding: 3.5rem 1.5rem 2rem;
            color: var(--text-muted);
            font-size: .88rem;
        }

        footer a { color: #94a3b8; text-decoration: none; transition: color .2s; }
        footer a:hover { color: #fff; }

        .footer-logo { height: 50px; object-fit: contain; filter: brightness(0) invert(1); opacity: .7; }

        .social-link {
            width: 38px; height: 38px;
            border-radius: 10px;
            border: 1px solid var(--border);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #94a3b8;
            font-size: 1rem;
            transition: border-color .2s, color .2s;
        }

        .social-link:hover { border-color: rgba(96,165,250,.45); color: #fff; }
    </style>
</head>
<body>

    {{-- ── Nav ──────────────────────────────────────────────────────── --}}
    <nav class="gdb-nav">
        <div class="container d-flex align-items-center justify-content-between gap-3">
            <div class="nav-logo">
                <a href="/">
                    <img src="https://glasgowdb.com/wp-content/uploads/2024/02/GDB-White-logo.png" alt="Glasgow Design Build">
                </a>
            </div>
            <div class="nav-links d-none d-md-flex align-items-center gap-4">
                <a href="https://glasgowdb.com/nc-remodeling-expert/" target="_blank">Our Story</a>
                <a href="https://glasgowdb.com/nc-home-renovations/" target="_blank">Gallery</a>
                <a href="https://glasgowdb.com/builders-blog/" target="_blank">Blog</a>
                <a href="{{ route('contact') }}">Contact</a>
                @auth
                    <a href="{{ route('dashboard') }}" class="nav-cta">Dashboard</a>
                @else
                    <a href="{{ route('register') }}" class="nav-cta">Get Started</a>
                @endauth
            </div>
            <div class="d-flex d-md-none">
                <a href="tel:+19192442979" class="btn-gdb-outline py-2 px-3"><i class="bi bi-telephone-fill"></i></a>
            </div>
        </div>
    </nav>

    {{-- ── Hero ─────────────────────────────────────────────────────── --}}
    <section class="hero">
        <div class="hero-content">
            <div>
                <div class="hero-pill">
                    <span class="dot"></span>
                    Raleigh, NC &bull; Wake County &bull; Historic Home Restoration
                </div>
            </div>
            <img src="https://glasgowdb.com/wp-content/uploads/2024/02/GDB-White-logo.png"
                 alt="Glasgow Design Build" class="hero-logo d-none d-sm-block">
            <h1 class="hero-title">Experience the<br><span>Difference</span> with<br>Glasgow Design Build</h1>
            <p class="hero-sub">Raleigh's premier expert for home renovation and remodeling — specializing in historic home restoration, kitchens, baths, custom decks, and whole-home transformations. Fully licensed and insured.</p>
            <div class="d-flex flex-column flex-sm-row gap-3 align-items-start">
                <a href="https://calendly.com/glasgowdb" target="_blank" class="btn-gdb-primary">
                    <i class="bi bi-calendar2-check"></i> Book a Consultation
                </a>
                <a href="tel:+19192442979" class="btn-gdb-outline">
                    <i class="bi bi-telephone"></i> (919) 244-2979
                </a>
            </div>
            <div class="hero-stats">
                <div class="hero-stat">
                    <span>15+</span>
                    <p>Years of Experience</p>
                </div>
                <div class="hero-stat">
                    <span>200+</span>
                    <p>Projects Completed</p>
                </div>
                <div class="hero-stat">
                    <span>5★</span>
                    <p>Client Reviews</p>
                </div>
                <div class="hero-stat">
                    <span>HGTV</span>
                    <p>Featured 2017</p>
                </div>
            </div>
        </div>
    </section>

    {{-- ── Services ──────────────────────────────────────────────────── --}}
    <section class="section section-mid">
        <div class="container">
            <div class="row gy-5 align-items-center mb-5">
                <div class="col-lg-5">
                    <div class="section-label">What We Build</div>
                    <h2 class="section-title">Full-Spectrum Home Renovation</h2>
                    <p class="section-desc">From kitchen and bathroom remodels to attic conversions, screened porches, custom decks, and new construction — Glasgow Design Build delivers projects that honor your home's character and elevate everyday living.</p>
                    <a href="https://glasgowdb.com/wake-county-home-remodeling/" target="_blank" class="btn-gdb-primary mt-4">
                        Get a Free Estimate <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
                <div class="col-lg-7">
                    <img src="https://glasgowdb.com/wp-content/uploads/2024/02/avery-livingroom-after.jpg"
                         alt="Avery Home living room renovation" class="img-fluid rounded-4 shadow" style="width:100%; object-fit:cover; max-height:400px;">
                </div>
            </div>
            <div class="row row-cols-2 row-cols-md-4 g-3">
                <div class="col"><div class="gdb-card"><div class="card-icon">🏛️</div><h4>Historic Restoration</h4><p>Preserve character while upgrading systems and finishes for homes that feel timeless.</p></div></div>
                <div class="col"><div class="gdb-card"><div class="card-icon">🍳</div><h4>Kitchen Remodels</h4><p>Chef-ready kitchens with curated finishes, custom cabinetry, and smart layouts.</p></div></div>
                <div class="col"><div class="gdb-card"><div class="card-icon">🛁</div><h4>Bathroom Remodels</h4><p>Spa-inspired baths with zero-entry showers, custom tilework, and designer fixtures.</p></div></div>
                <div class="col"><div class="gdb-card"><div class="card-icon">🏠</div><h4>Whole Home</h4><p>Complete interior and exterior renovations that transform every room from the ground up.</p></div></div>
                <div class="col"><div class="gdb-card"><div class="card-icon">🌿</div><h4>Outdoor Living</h4><p>Custom decks and screened-in porches that expand your living space with style.</p></div></div>
                <div class="col"><div class="gdb-card"><div class="card-icon">🏗️</div><h4>Attic Conversions</h4><p>Transform unused attic space into functional bedrooms, offices, or bonus rooms.</p></div></div>
                <div class="col"><div class="gdb-card"><div class="card-icon">🏚️</div><h4>Finished Basements</h4><p>Livable, beautifully finished lower levels that add real square footage to your home.</p></div></div>
                <div class="col"><div class="gdb-card"><div class="card-icon">🔨</div><h4>New Construction</h4><p>Custom builds on your lot — designed and delivered by the Glasgow team from the ground up.</p></div></div>
            </div>
        </div>
    </section>

    {{-- ── Gallery ───────────────────────────────────────────────────── --}}
    <section class="section section-dark">
        <div class="container">
            <div class="text-center mb-5">
                <div class="section-label">Transformation Gallery</div>
                <h2 class="section-title">Built by Glasgow, Loved by Raleigh</h2>
                <p class="section-desc mx-auto">Every project tells a story of craftsmanship, collaboration, and care. Browse a selection of real transformations by the Glasgow team.</p>
            </div>
            <div class="gallery-grid">
                <div class="g-item g1">
                    <img src="https://glasgowdb.com/wp-content/uploads/2024/02/avery-outside-after.jpg" alt="Avery Home exterior renovation">
                    <div class="g-label">Avery Home — Full Exterior</div>
                </div>
                <div class="g-item g2">
                    <img src="https://glasgowdb.com/wp-content/uploads/2025/11/Pentz-Main-Bath-Remodel1.jpg" alt="Pentz Main Bath Remodel">
                    <div class="g-label">Pentz Main Bath — Spa Remodel</div>
                </div>
                <div class="g-item g3">
                    <img src="https://glasgowdb.com/wp-content/uploads/2024/02/avery-deck-after.jpg" alt="Avery custom deck">
                    <div class="g-label">Avery Home — Custom Deck</div>
                </div>
                <div class="g-item g4">
                    <img src="https://glasgowdb.com/wp-content/uploads/2024/02/avery-bathroom-after.jpg" alt="Avery bathroom renovation">
                    <div class="g-label">Avery Home — Bathroom</div>
                </div>
                <div class="g-item g5">
                    <img src="https://glasgowdb.com/wp-content/uploads/2024/02/avery-livingroom-after.jpg" alt="Avery living room renovation">
                    <div class="g-label">Avery Home — Living Room</div>
                </div>
            </div>
            <div class="text-center mt-4">
                <a href="https://glasgowdb.com/nc-home-renovations/" target="_blank" class="btn-gdb-outline">
                    View Full Gallery <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        </div>
    </section>

    {{-- ── Process ───────────────────────────────────────────────────── --}}
    <section class="section section-mid">
        <div class="container">
            <div class="text-center mb-5">
                <div class="section-label">How It Works</div>
                <h2 class="section-title">The Glasgow Process</h2>
                <p class="section-desc mx-auto">Every step is designed to keep you informed, confident, and excited about your renovation from first call to final walkthrough.</p>
            </div>
            <div class="row row-cols-1 row-cols-md-5 g-3">
                <div class="col">
                    <div class="process-step">
                        <div class="step-num">1</div>
                        <h5>Good Fit</h5>
                        <p>Initial consultation to align on vision, style, scope, and budget — making sure we're the right team for your project.</p>
                    </div>
                </div>
                <div class="col">
                    <div class="process-step">
                        <div class="step-num">2</div>
                        <h5>Estimate</h5>
                        <p>Detailed assessment of materials, labor, and timeframe with transparent pricing — no surprises.</p>
                    </div>
                </div>
                <div class="col">
                    <div class="process-step">
                        <div class="step-num">3</div>
                        <h5>Q &amp; A</h5>
                        <p>Open communication to address every question and concern before any work begins.</p>
                    </div>
                </div>
                <div class="col">
                    <div class="process-step">
                        <div class="step-num">4</div>
                        <h5>Contract</h5>
                        <p>Formal agreement covering scope, timelines, costs, and responsibilities — all in writing.</p>
                    </div>
                </div>
                <div class="col">
                    <div class="process-step">
                        <div class="step-num">5</div>
                        <h5>Permits</h5>
                        <p>We handle all required permits and code compliance so your project can begin on schedule.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ── Testimonials ─────────────────────────────────────────────── --}}
    <section class="section section-dark">
        <div class="container">
            <div class="text-center mb-5">
                <div class="section-label">Client Reviews</div>
                <h2 class="section-title">What Raleigh Homeowners Say</h2>
            </div>
            <div class="row row-cols-1 row-cols-md-3 g-4">
                <div class="col">
                    <div class="testimonial-card">
                        <div class="stars">★★★★★</div>
                        <blockquote>"Glasgow Ventures put a brand new deck on our house in June and my wife and I could not be happier with the outcome. The team is efficient and extremely professional."</blockquote>
                        <div class="reviewer">— Verified Client, Raleigh NC</div>
                    </div>
                </div>
                <div class="col">
                    <div class="testimonial-card">
                        <div class="stars">★★★★★</div>
                        <blockquote>"The Glasgow Design Team is spectacular. If you are looking for quality and professionalism, you will be delighted when you hire them."</blockquote>
                        <div class="reviewer">— Verified Client, Wake County NC</div>
                    </div>
                </div>
                <div class="col">
                    <div class="testimonial-card">
                        <div class="stars">★★★★★</div>
                        <blockquote>"Jonathan Brothers and his team of highly skilled builders and carpenters at Glasgow Design Build did a fantastic job building my new deck, screen porch, and renovating my kitchen."</blockquote>
                        <div class="reviewer">— Verified Client, Triangle Area NC</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ── Team ──────────────────────────────────────────────────────── --}}
    <section class="section section-mid">
        <div class="container">
            <div class="text-center mb-5">
                <div class="section-label">The Team</div>
                <h2 class="section-title">Crafting Excellence Together</h2>
                <p class="section-desc mx-auto">Founded by Jonathan and Walter Brothers, Glasgow Design Build has been a cornerstone of Raleigh's home renovation community since surviving the 2008 recession and growing into a Triangle-area institution.</p>
            </div>
            <div class="row row-cols-1 row-cols-sm-2 row-cols-md-4 g-4">
                <div class="col">
                    <div class="team-card">
                        <img src="https://glasgowdb.com/wp-content/uploads/2026/02/jonathan_brothers-2026.jpg" alt="Jonathan Brothers" onerror="this.src='https://glasgowdb.com/wp-content/uploads/2024/02/GDB-color-logo.png'; this.style.objectFit='contain'; this.style.padding='2rem';">
                        <div class="team-info">
                            <h5>Jonathan Brothers</h5>
                            <p>Owner &amp; Licensed Contractor — leads strategy and client relationships.</p>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="team-card">
                        <img src="https://glasgowdb.com/wp-content/uploads/2024/02/walter_brothers-2024sm.jpg" alt="Walter Brothers" onerror="this.src='https://glasgowdb.com/wp-content/uploads/2024/02/GDB-color-logo.png'; this.style.objectFit='contain'; this.style.padding='2rem';">
                        <div class="team-info">
                            <h5>Walter Brothers</h5>
                            <p>Managing Partner — oversees project delivery and construction execution.</p>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="team-card">
                        <img src="https://glasgowdb.com/wp-content/uploads/2024/02/debbi_miller-2024sm.jpg" alt="Debbi Miller" onerror="this.src='https://glasgowdb.com/wp-content/uploads/2024/02/GDB-color-logo.png'; this.style.objectFit='contain'; this.style.padding='2rem';">
                        <div class="team-info">
                            <h5>Debbi Miller</h5>
                            <p>Project Manager &amp; In-House Designer — coordinates design details and client communication.</p>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="team-card">
                        <img src="https://glasgowdb.com/wp-content/uploads/2024/02/wendy_russell.jpg" alt="Wendy Russell" onerror="this.src='https://glasgowdb.com/wp-content/uploads/2024/02/GDB-color-logo.png'; this.style.objectFit='contain'; this.style.padding='2rem';">
                        <div class="team-info">
                            <h5>Wendy Russell</h5>
                            <p>Communications Director — keeps every client conversation clear and on track.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ── Blog ──────────────────────────────────────────────────────── --}}
    <section class="section section-dark">
        <div class="container">
            <div class="d-flex justify-content-between align-items-end mb-5 flex-wrap gap-3">
                <div>
                    <div class="section-label">Builders Blog</div>
                    <h2 class="section-title mb-0">Renovation Advice &amp; Project News</h2>
                </div>
                <a href="https://glasgowdb.com/builders-blog/" target="_blank" class="btn-gdb-outline">
                    All Posts <i class="bi bi-arrow-right"></i>
                </a>
            </div>
            <div class="row row-cols-1 row-cols-md-4 g-4">
                <div class="col">
                    <a href="https://glasgowdb.com/2026/04/spring-renovation-planning-how-to-prepare-now-for-a-smoother-home-project-later/" target="_blank" class="blog-card">
                        <img src="https://glasgowdb.com/wp-content/uploads/2026/04/Spring-Renovation-Planning.jpg" alt="Spring Renovation Planning">
                        <div class="blog-body">
                            <div class="blog-cat">Professional Advice</div>
                            <h5>Spring Renovation Planning: How to Prepare Now for a Smoother Project Later</h5>
                        </div>
                    </a>
                </div>
                <div class="col">
                    <a href="https://glasgowdb.com/2026/04/bni-presentation-is-this-thursday-4-9/" target="_blank" class="blog-card">
                        <img src="https://glasgowdb.com/wp-content/uploads/2026/04/Jonathan-BNI-Presentation.jpg" alt="Jonathan BNI Presentation">
                        <div class="blog-body">
                            <div class="blog-cat">Community</div>
                            <h5>Jonathan's BNI Presentation — Featuring Glasgow Design Build</h5>
                        </div>
                    </a>
                </div>
                <div class="col">
                    <a href="https://glasgowdb.com/2026/02/behind-the-walls-in-mordecai-second-floor-demolition-what-we-found/" target="_blank" class="blog-card">
                        <img src="https://glasgowdb.com/wp-content/uploads/2026/02/Behind-the-Walls-in-Mordecai.png" alt="Behind the Walls in Mordecai">
                        <div class="blog-body">
                            <div class="blog-cat">Project Update</div>
                            <h5>Behind the Walls in Mordecai: Second-Floor Demolition &amp; What We Found</h5>
                        </div>
                    </a>
                </div>
                <div class="col">
                    <a href="https://glasgowdb.com/2026/01/winter-home-upgrades-that-pay-off-before-spring/" target="_blank" class="blog-card">
                        <img src="https://glasgowdb.com/wp-content/uploads/2026/01/Winter-Home-Upgrades-That-Pay-Off-Before-Spring.jpg" alt="Winter Home Upgrades">
                        <div class="blog-body">
                            <div class="blog-cat">Professional Advice</div>
                            <h5>Winter Home Upgrades That Pay Off Before Spring</h5>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- ── Certifications ────────────────────────────────────────────── --}}
    <section class="section section-mid">
        <div class="container">
            <div class="text-center mb-4">
                <div class="section-label">Credentials</div>
                <h2 class="section-title">Licensed, Insured &amp; Recognized</h2>
            </div>
            <div class="d-flex flex-wrap gap-3 justify-content-center">
                <a href="https://web.hbawake.com/Builder-Remodeling-Residential-Commerical/Glasgow-Design-Build,-LLC-2884" target="_blank" class="cert-pill">
                    <i class="bi bi-award-fill"></i> HBA Raleigh &amp; Wake County Member
                </a>
                <a href="https://www.nclbgc.org/license-search/?licenseName=glasgow" target="_blank" class="cert-pill">
                    <i class="bi bi-patch-check-fill"></i> NC Licensed General Contractor (NCLBGC)
                </a>
                <span class="cert-pill">
                    <i class="bi bi-shield-check"></i> Fully Insured &amp; Bonded
                </span>
                <a href="https://www.carymagazine.com/features/2023-maggy-awards-services/" target="_blank" class="cert-pill">
                    <i class="bi bi-trophy-fill"></i> Maggy Awards 2023 — Services Category
                </a>
                <span class="cert-pill">
                    <i class="bi bi-tv-fill"></i> HGTV Featured 2017
                </span>
                <span class="cert-pill">
                    <i class="bi bi-lightning-charge-fill"></i> Authorized Generac Dealer
                </span>
            </div>
        </div>
    </section>

    {{-- ── CTA ───────────────────────────────────────────────────────── --}}
    <section class="section section-dark">
        <div class="container">
            <div class="cta-panel">
                <div class="row align-items-center gy-4">
                    <div class="col-lg-7">
                        <div class="section-label">Ready to Start?</div>
                        <h2>Let's Talk About Your Next Home Transformation</h2>
                        <p style="color:var(--text-muted); max-width:520px; line-height:1.8;" class="mb-4">
                            Whether you're planning a historic restoration, a kitchen makeover, or a full whole-home remodel — the Glasgow Design Build team is ready to bring your vision to life.
                        </p>
                        <div class="d-flex flex-column flex-sm-row gap-3">
                            <a href="https://calendly.com/glasgowdb" target="_blank" class="btn-gdb-primary">
                                <i class="bi bi-calendar2-check"></i> Book a Free Consultation
                            </a>
                            <a href="{{ route('contact') }}" class="btn-gdb-outline">
                                <i class="bi bi-envelope"></i> Send a Message
                            </a>
                        </div>
                        <div class="mt-4 d-flex flex-wrap gap-4" style="font-size:.88rem; color:var(--text-muted);">
                            <span><i class="bi bi-telephone me-1"></i> <a href="tel:+19192442979" style="color:inherit;">(919) 244-2979</a></span>
                            <span><i class="bi bi-envelope me-1"></i> <a href="mailto:info@glasgowdb.com" style="color:inherit;">info@glasgowdb.com</a></span>
                            <span><i class="bi bi-clock me-1"></i> Mon–Fri, 9 AM–5 PM</span>
                        </div>
                    </div>
                    <div class="col-lg-5 text-center">
                        <img src="https://glasgowdb.com/wp-content/uploads/2024/02/GDB-color-logo.png"
                             alt="Glasgow Design Build" class="img-fluid" style="max-height:140px; filter: drop-shadow(0 8px 24px rgba(31,58,138,.4));">
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ── Footer ────────────────────────────────────────────────────── --}}
    <footer>
        <div class="container">
            <div class="row gy-4 mb-4">
                <div class="col-md-4">
                    <img src="https://glasgowdb.com/wp-content/uploads/2024/02/GDB-White-logo.png" alt="Glasgow Design Build" class="footer-logo mb-3">
                    <p class="mb-2">Raleigh's premier home renovation and remodeling experts. Serving Wake County and the Triangle area.</p>
                    <div class="d-flex gap-2 mt-3">
                        <a href="https://www.facebook.com/glasgowdesignbuild" target="_blank" class="social-link"><i class="bi bi-facebook"></i></a>
                        <a href="https://www.instagram.com/glasgowdesignbuild" target="_blank" class="social-link"><i class="bi bi-instagram"></i></a>
                        <a href="https://www.linkedin.com/in/jonathan-brothers-917278146/" target="_blank" class="social-link"><i class="bi bi-linkedin"></i></a>
                    </div>
                </div>
                <div class="col-md-4">
                    <h6 class="text-white fw-700 mb-3">Services</h6>
                    <ul class="list-unstyled" style="line-height:2;">
                        <li><a href="https://glasgowdb.com/nc-remodeling-expert/" target="_blank">Historic Restoration</a></li>
                        <li><a href="https://glasgowdb.com/nc-home-renovations/" target="_blank">Kitchen Remodels</a></li>
                        <li><a href="https://glasgowdb.com/nc-home-renovations/" target="_blank">Bathroom Remodels</a></li>
                        <li><a href="https://glasgowdb.com/nc-home-renovations/" target="_blank">Decks &amp; Outdoor Living</a></li>
                        <li><a href="https://glasgowdb.com/nc-home-renovations/" target="_blank">Whole Home Remodels</a></li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h6 class="text-white fw-700 mb-3">Contact</h6>
                    <ul class="list-unstyled" style="line-height:2;">
                        <li><a href="tel:+19192442979">(919) 244-2979</a></li>
                        <li><a href="mailto:info@glasgowdb.com">info@glasgowdb.com</a></li>
                        <li>Mon–Fri, 9 AM–5 PM</li>
                        <li>Raleigh, NC &bull; Wake County</li>
                        <li class="mt-2"><a href="https://calendly.com/glasgowdb" target="_blank" class="btn-gdb-primary py-2 px-3" style="font-size:.82rem;">Book via Calendly</a></li>
                    </ul>
                </div>
            </div>
            <div class="border-top pt-3 d-flex flex-column flex-md-row justify-content-between gap-2" style="border-color:var(--border) !important; font-size:.8rem;">
                <span>&copy; {{ date('Y') }} Glasgow Design Build, LLC. All rights reserved.</span>
                <span>Powered by <a href="/">{{ config('app.name') }}</a></span>
            </div>
        </div>
    </footer>

</body>
</html>
