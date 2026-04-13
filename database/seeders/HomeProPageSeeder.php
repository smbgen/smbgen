<?php

namespace Database\Seeders;

use App\Models\CmsPage;
use Illuminate\Database\Seeder;

class HomeProPageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $headContent = <<<'HTML'
<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }
    
    body {
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
        line-height: 1.6;
        color: #333;
        background: #f8f9fa;
    }
    
    /* Action Bar Styles */
    .action-bar {
        background: #667eea;
        padding: 8px 0;
        position: sticky;
        top: 0;
        z-index: 1001;
    }
    
    .action-bar-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 20px;
        display: flex;
        gap: 15px;
        align-items: center;
        justify-content: flex-end;
    }
    
    .action-bar a {
        text-decoration: none;
        color: white;
        font-weight: 600;
        padding: 6px 16px;
        border-radius: 4px;
        transition: all 0.3s;
        white-space: nowrap;
        font-size: 0.9rem;
    }
    
    .action-bar a.btn-primary {
        background: rgba(255, 255, 255, 0.2);
        border: 2px solid white;
    }
    
    .action-bar a.btn-primary:hover {
        background: white;
        color: #667eea;
    }
    
    .action-bar a:not(.btn-primary):hover {
        background: rgba(255, 255, 255, 0.15);
    }

    /* Navigation Styles */
    .top-nav {
        background: #fff;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        position: sticky;
        top: 52px;
        z-index: 1000;
    }
    
    .nav-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        min-height: 70px;
    }
    
    .logo {
        font-size: 1.5rem;
        font-weight: 700;
        color: #667eea;
        text-decoration: none;
    }
    
    .nav-menu {
        display: flex;
        list-style: none;
        gap: 30px;
        align-items: center;
    }
    
    .nav-menu a {
        text-decoration: none;
        color: #333;
        font-weight: 500;
        transition: color 0.3s;
        white-space: nowrap;
    }
    
    .nav-menu a:hover {
        color: #667eea;
    }
    
    .nav-toggle {
        display: none;
        background: none;
        border: none;
        font-size: 1.5rem;
        cursor: pointer;
        color: #333;
    }
    
    @media (max-width: 992px) {
        .action-bar-container {
            flex-wrap: wrap;
            justify-content: center;
        }
        
        .action-bar a {
            font-size: 0.85rem;
            padding: 6px 12px;
        }
        
        .top-nav {
            top: auto;
        }
        
        .nav-toggle {
            display: block;
        }
        
        .nav-menu {
            position: fixed;
            left: -100%;
            top: 110px;
            flex-direction: column;
            background: white;
            width: 100%;
            text-align: center;
            transition: left 0.3s;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            padding: 20px 0;
            gap: 20px;
        }
        
        .nav-menu.active {
            left: 0;
        }
    }
    
    .container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 20px;
    }
    
    .hero-section {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 80px 20px;
        text-align: center;
        margin-bottom: 60px;
    }
    
    .hero-section h1 {
        font-size: 3rem;
        margin-bottom: 20px;
        font-weight: 700;
    }
    
    .hero-section h2 {
        font-size: 1.8rem;
        margin-bottom: 20px;
        font-weight: 600;
    }
    
    .hero-section .lead {
        font-size: 1.25rem;
        max-width: 800px;
        margin: 0 auto;
        opacity: 0.95;
    }
    
    .services-section {
        padding: 60px 20px;
        background: white;
        margin-bottom: 40px;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    
    .services-section h3 {
        font-size: 2rem;
        margin-bottom: 40px;
        text-align: center;
        color: #667eea;
    }
    
    .service-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 30px;
    }
    
    .service {
        padding: 30px;
        background: #f8f9fa;
        border-radius: 8px;
        transition: transform 0.3s, box-shadow 0.3s;
    }
    
    .service:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 15px rgba(102, 126, 234, 0.2);
    }
    
    .service h4 {
        font-size: 1.5rem;
        margin-bottom: 15px;
        color: #667eea;
    }
    
    .service p {
        color: #666;
        line-height: 1.8;
    }
    
    .why-choose-us {
        padding: 60px 20px;
        background: white;
        margin-bottom: 40px;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    
    .why-choose-us h3 {
        font-size: 2rem;
        margin-bottom: 30px;
        text-align: center;
        color: #667eea;
    }
    
    .why-choose-us ul {
        list-style: none;
        max-width: 600px;
        margin: 0 auto;
    }
    
    .why-choose-us li {
        padding: 15px 0;
        font-size: 1.1rem;
        border-bottom: 1px solid #e9ecef;
    }
    
    .why-choose-us li:last-child {
        border-bottom: none;
    }
    
    .cta-section {
        padding: 60px 20px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        text-align: center;
        border-radius: 12px;
        margin-bottom: 40px;
    }
    
    .cta-section h3 {
        font-size: 2rem;
        margin-bottom: 20px;
    }
    
    .cta-section p {
        font-size: 1.2rem;
        margin-bottom: 15px;
    }
    
    .cta-section strong {
        font-size: 1.3rem;
        display: block;
        margin-top: 20px;
    }
    
    @media (max-width: 768px) {
        .hero-section h1 {
            font-size: 2rem;
        }
        
        .hero-section h2 {
            font-size: 1.5rem;
        }
        
        .service-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const navToggle = document.querySelector('.nav-toggle');
    const navMenu = document.querySelector('.nav-menu');
    
    if (navToggle) {
        navToggle.addEventListener('click', function() {
            navMenu.classList.toggle('active');
        });
        
        // Close menu when clicking a link
        document.querySelectorAll('.nav-menu a').forEach(link => {
            link.addEventListener('click', function() {
                navMenu.classList.remove('active');
            });
        });
    }
});
</script>
HTML;

        $homeBodyContent = <<<'HTML'
<!-- Hero Section -->
<div class="hero-section">
    <div class="container">
        <h1>Home Pro Inc</h1>
        <h2>Professional Home Services You Can Trust</h2>
        <p class="lead">From routine maintenance to major renovations, Home Pro Inc delivers quality workmanship and reliable service for homeowners throughout the region.</p>
    </div>
</div>

<!-- Main Content -->
<div class="container">
    <div class="services-section">
        <h3>Our Services</h3>
        <div class="service-grid">
            <div class="service">
                <h4>🔬 Mold Inspection</h4>
                <p>Professional mold testing and inspection services with detailed reports and lab analysis.</p>
            </div>
            <div class="service">
                <h4>🛡️ Mold Remediation</h4>
                <p>Complete mold removal and prevention with industry-leading techniques and materials.</p>
            </div>
            <div class="service">
                <h4>💧 Water Damage Restoration</h4>
                <p>24/7 emergency water damage response, extraction, and complete restoration services.</p>
            </div>
            <div class="service">
                <h4>🌬️ Air Quality Testing</h4>
                <p>Comprehensive indoor air quality analysis to ensure your home is safe and healthy.</p>
            </div>
            <div class="service">
                <h4>🚨 Emergency Services</h4>
                <p>Fast response times for urgent water damage, flooding, and environmental emergencies.</p>
            </div>
            <div class="service">
                <h4>📋 Prevention Solutions</h4>
                <p>Proactive measures to prevent mold growth, water damage, and air quality issues.</p>
            </div>
        </div>
    </div>

    <div class="why-choose-us">
        <h3>Why Choose Home Pro Inc?</h3>
        <ul>
            <li>✓ Licensed and insured professionals</li>
            <li>✓ 24/7 emergency service available</li>
            <li>✓ Upfront pricing with no hidden fees</li>
            <li>✓ Quality workmanship guaranteed</li>
            <li>✓ Same-day service for urgent repairs</li>
            <li>✓ Industry-certified technicians</li>
        </ul>
    </div>

    <div class="cta-section">
        <h3>Ready to Get Started?</h3>
        <p>Schedule your free consultation today and let our experts help with your home improvement needs.</p>
        <p><strong>Call us at (555) 123-4567 or book online below.</strong></p>
    </div>
</div>
HTML;

        $formFields = [
            [
                'name' => 'name',
                'type' => 'text',
                'label' => 'Full Name',
                'placeholder' => 'John Smith',
                'required' => true,
            ],
            [
                'name' => 'email',
                'type' => 'email',
                'label' => 'Email Address',
                'placeholder' => 'john@example.com',
                'required' => true,
            ],
            [
                'name' => 'phone',
                'type' => 'tel',
                'label' => 'Phone Number',
                'placeholder' => '(555) 123-4567',
                'required' => true,
            ],
            [
                'name' => 'service_needed',
                'type' => 'select',
                'label' => 'Service Needed',
                'options' => 'Mold Inspection,Mold Remediation,Water Damage,Air Quality Testing,Emergency Service,Prevention,Other',
                'required' => true,
            ],
            [
                'name' => 'message',
                'type' => 'textarea',
                'label' => 'Tell Us About Your Needs',
                'placeholder' => 'Describe your situation or questions...',
                'required' => false,
            ],
        ];

        // Shared action bar
        $actionBar = <<<'HTML'
<!-- Action Bar -->
<div class="action-bar">
    <div class="action-bar-container">
        <a target="blank" href="/book" class="btn-primary">📞 Book A Call</a>
        <a target="blank" href="/contact">📋 Request Callback</a>
        <a target="blank" href="/login">🔐 Client Portal</a>
    </div>
</div>

HTML;

        // Shared navigation
        $pageNavigation = <<<'HTML'
<!-- Navigation -->
<nav class="top-nav">
    <div class="nav-container">
        <a href="/home" class="logo">Home Pro Inc</a>
        <button class="nav-toggle">☰</button>
        <ul class="nav-menu">
            <li><a href="/home">Home</a></li>
            <li><a href="/mold-inspection">Mold Inspection</a></li>
            <li><a href="/mold-remediation">Mold Remediation</a></li>
            <li><a href="/water-damage-restoration">Water Damage</a></li>
            <li><a href="/air-quality-testing">Air Quality</a></li>
            <li><a href="/emergency-services">Emergency</a></li>
            <li><a href="/prevention-tips">Prevention</a></li>
        </ul>
    </div>
</nav>

HTML;

        // Combined navigation for all pages
        $navigation = $actionBar.$pageNavigation;

        // Create Home page
        CmsPage::updateOrCreate(
            ['slug' => 'home'],
            [
                'title' => 'Home Pro Inc - Professional Home Services',
                'head_content' => $headContent,
                'body_content' => $navigation.$homeBodyContent,
                'is_published' => true,
                'has_form' => true,
                'form_fields' => $formFields,
                'form_submit_button_text' => 'Request Free Consultation',
                'form_success_message' => "Thank you! We'll contact you within 24 hours to discuss your needs.",
                'notification_email' => 'info@homepro.com',
                'send_admin_notification' => true,
                'send_client_notification' => true,
            ]
        );

        // Create all other pages with the same head content and navigation
        $pages = [
            [
                'slug' => 'mold-inspection',
                'title' => 'Professional Mold Inspection Services - Home Pro Inc',
                'hero_title' => 'Professional Mold Inspection Services',
                'hero_subtitle' => 'Protect Your Home & Health with Expert Mold Testing',
                'content' => '<div class="container"><div class="services-section"><h3>Comprehensive Mold Inspection</h3><p style="text-align: center; margin-bottom: 2rem;">Our certified inspectors use advanced equipment to detect hidden mold and provide detailed reports with lab analysis.</p><div class="service-grid"><div class="service"><h4>Visual Inspection</h4><p>Thorough examination of all areas including HVAC systems, crawl spaces, basements, and walls.</p></div><div class="service"><h4>Air Quality Testing</h4><p>Professional air sampling to detect mold spores and identify contamination levels.</p></div><div class="service"><h4>Surface Testing</h4><p>Sample collection from suspected areas with laboratory analysis and species identification.</p></div></div></div><div class="cta-section"><h3>Schedule Your Inspection Today</h3><p>Don\'t wait - early detection can save thousands in repairs.</p><p><strong>Call (555) 123-4567 for same-day service</strong></p></div></div>',
            ],
            [
                'slug' => 'mold-remediation',
                'title' => 'Mold Remediation & Removal - Home Pro Inc',
                'hero_title' => 'Complete Mold Remediation',
                'hero_subtitle' => 'Safe, Effective Mold Removal by Certified Professionals',
                'content' => '<div class="container"><div class="services-section"><h3>Professional Mold Removal</h3><p style="text-align: center; margin-bottom: 2rem;">We don\'t just cover it up - we remove mold completely and prevent future growth.</p><div class="service-grid"><div class="service"><h4>Complete Containment</h4><p>Proper containment procedures to prevent mold spores from spreading during removal.</p></div><div class="service"><h4>HEPA Filtration</h4><p>Industrial air scrubbers with HEPA filters to capture airborne mold spores.</p></div><div class="service"><h4>Antimicrobial Treatment</h4><p>Application of EPA-approved antimicrobial solutions and mold-resistant sealants.</p></div></div></div><div class="why-choose-us"><h3>Our Process</h3><ul><li>✓ Free inspection and detailed estimate</li><li>✓ Complete containment setup</li><li>✓ Safe mold removal and disposal</li><li>✓ HEPA air filtration</li><li>✓ Antimicrobial treatment</li><li>✓ Final clearance testing</li></ul></div><div class="cta-section"><h3>Get Started Today</h3><p>Fast response for emergencies. Professional service guaranteed.</p><p><strong>Call (555) 123-4567 now</strong></p></div></div>',
            ],
            [
                'slug' => 'water-damage-restoration',
                'title' => 'Water Damage Restoration - Home Pro Inc',
                'hero_title' => '24/7 Water Damage Restoration',
                'hero_subtitle' => 'Fast Response to Minimize Damage & Prevent Mold',
                'content' => '<div class="container"><div class="services-section"><h3>Emergency Water Damage Services</h3><p style="text-align: center; margin-bottom: 2rem;">Water damage requires immediate action. Our team responds quickly to extract water, dry structures, and prevent mold growth.</p><div class="service-grid"><div class="service"><h4>🚨 Emergency Response</h4><p>24/7 availability for water emergencies. We arrive fast to minimize damage.</p></div><div class="service"><h4>💧 Water Extraction</h4><p>Powerful extraction equipment removes standing water quickly and efficiently.</p></div><div class="service"><h4>🌬️ Structural Drying</h4><p>Industrial dehumidifiers and air movers to dry walls, floors, and contents.</p></div><div class="service"><h4>🔬 Mold Prevention</h4><p>Antimicrobial treatment to prevent mold growth after water damage.</p></div></div></div><div class="why-choose-us"><h3>Common Water Damage Causes</h3><ul><li>✓ Burst pipes and plumbing leaks</li><li>✓ Roof leaks and storm damage</li><li>✓ Basement flooding</li><li>✓ Appliance failures</li><li>✓ Sewage backups</li><li>✓ HVAC condensation</li></ul></div><div class="cta-section"><h3>Water Emergency? Call Now!</h3><p>Every minute counts. Don\'t let water damage turn into a mold problem.</p><p><strong>📞 (555) 123-4567 - 24/7 Emergency Line</strong></p></div></div>',
            ],
            [
                'slug' => 'air-quality-testing',
                'title' => 'Air Quality Testing - Home Pro Inc',
                'hero_title' => 'Indoor Air Quality Testing',
                'hero_subtitle' => 'Ensure Your Home Has Safe, Healthy Air',
                'content' => '<div class="container"><div class="services-section"><h3>Comprehensive Air Quality Analysis</h3><p style="text-align: center; margin-bottom: 2rem;">Poor indoor air quality can cause allergies, respiratory issues, and other health problems. Our testing identifies pollutants and provides solutions.</p><div class="service-grid"><div class="service"><h4>Mold Spore Testing</h4><p>Air sampling to detect mold spores and identify species present in your home.</p></div><div class="service"><h4>VOC Testing</h4><p>Detection of volatile organic compounds from chemicals, paints, and building materials.</p></div><div class="service"><h4>Allergen Testing</h4><p>Identification of common allergens including dust mites, pollen, and pet dander.</p></div></div></div><div class="why-choose-us"><h3>Signs You Need Air Quality Testing</h3><ul><li>✓ Persistent allergies or respiratory issues</li><li>✓ Musty or chemical odors</li><li>✓ Recent water damage or mold</li><li>✓ After renovation or remodeling</li><li>✓ Poor ventilation or humidity issues</li><li>✓ Before purchasing a home</li></ul></div><div class="cta-section"><h3>Breathe Easy</h3><p>Schedule your air quality test and get peace of mind about your indoor environment.</p><p><strong>Call (555) 123-4567 today</strong></p></div></div>',
            ],
            [
                'slug' => 'emergency-services',
                'title' => '24/7 Emergency Services - Home Pro Inc',
                'hero_title' => '24/7 Emergency Services',
                'hero_subtitle' => 'Fast Response When You Need It Most',
                'content' => '<div class="container"><div style="background: #dc3545; color: white; padding: 2rem; border-radius: 12px; text-align: center; margin-bottom: 3rem;"><h2 style="font-size: 2rem; margin-bottom: 1rem;">🚨 Emergency? Call Now!</h2><p style="font-size: 1.5rem; font-weight: bold;">📞 (555) 123-4567</p><p style="font-size: 1.1rem;">Available 24 hours a day, 7 days a week</p></div><div class="services-section"><h3>Emergency Services Available</h3><div class="service-grid"><div class="service"><h4>💧 Water Damage</h4><p>Burst pipes, flooding, sewage backups - we respond immediately to minimize damage.</p></div><div class="service"><h4>🔥 Fire Damage</h4><p>Smoke and fire damage restoration with complete cleanup and odor removal.</p></div><div class="service"><h4>🌪️ Storm Damage</h4><p>Emergency response for storm-related damage including roof leaks and flooding.</p></div><div class="service"><h4>☠️ Mold Emergency</h4><p>Rapid response for severe mold contamination and health hazards.</p></div></div></div><div class="why-choose-us"><h3>Why Call Us for Emergencies</h3><ul><li>✓ 24/7 availability - call anytime</li><li>✓ Rapid response within hours</li><li>✓ Fully equipped emergency vehicles</li><li>✓ Certified emergency technicians</li><li>✓ Direct insurance billing</li><li>✓ Complete documentation for claims</li></ul></div><div class="cta-section"><h3>Don\'t Wait - Call Now</h3><p>Emergency situations require immediate action to prevent further damage.</p><p><strong>📞 (555) 123-4567 - 24/7 Emergency Hotline</strong></p></div></div>',
            ],
            [
                'slug' => 'prevention-tips',
                'title' => 'Mold & Water Damage Prevention Tips - Home Pro Inc',
                'hero_title' => 'Prevention Tips',
                'hero_subtitle' => 'Simple Steps to Protect Your Home',
                'content' => '<div class="container"><div class="services-section"><h3>Prevent Mold & Water Damage</h3><p style="text-align: center; margin-bottom: 2rem;">An ounce of prevention is worth a pound of cure. Follow these tips to keep your home safe and dry.</p><div style="display: grid; gap: 2rem;"><div style="background: white; padding: 2rem; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);"><h4 style="color: #667eea; margin-bottom: 1rem;">💧 Control Moisture</h4><ul style="line-height: 2;"><li>Keep humidity below 60% (ideally 30-50%)</li><li>Use dehumidifiers in basements and crawl spaces</li><li>Run bathroom fans during and after showers</li><li>Vent clothes dryers outside</li><li>Fix leaky faucets and pipes immediately</li></ul></div><div style="background: white; padding: 2rem; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);"><h4 style="color: #667eea; margin-bottom: 1rem;">🏠 Maintain Your Home</h4><ul style="line-height: 2;"><li>Inspect roof and gutters regularly</li><li>Clean gutters and downspouts</li><li>Grade soil away from foundation</li><li>Seal cracks in foundation and walls</li><li>Service HVAC systems annually</li></ul></div><div style="background: white; padding: 2rem; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);"><h4 style="color: #667eea; margin-bottom: 1rem;">🔍 Regular Inspections</h4><ul style="line-height: 2;"><li>Check under sinks for leaks</li><li>Inspect basement and crawl space</li><li>Look for water stains on ceilings</li><li>Check window and door seals</li><li>Monitor HVAC condensation</li></ul></div><div style="background: white; padding: 2rem; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);"><h4 style="color: #667eea; margin-bottom: 1rem;">⚡ Quick Action</h4><ul style="line-height: 2;"><li>Address water damage within 24-48 hours</li><li>Clean spills and leaks immediately</li><li>Replace water-damaged materials</li><li>Don\'t ignore musty odors</li><li>Call professionals for suspected mold</li></ul></div></div></div><div class="cta-section"><h3>Need Professional Help?</h3><p>Prevention is great, but if you already have issues, we can help.</p><p><strong>Call (555) 123-4567 for inspection</strong></p></div></div>',
            ],
        ];

        foreach ($pages as $pageData) {
            $heroSection = <<<HTML
<div class="hero-section">
    <div class="container">
        <h1>{$pageData['hero_title']}</h1>
        <h2>{$pageData['hero_subtitle']}</h2>
    </div>
</div>

HTML;

            CmsPage::updateOrCreate(
                ['slug' => $pageData['slug']],
                [
                    'title' => $pageData['title'],
                    'head_content' => $headContent,
                    'body_content' => $navigation.$heroSection.$pageData['content'],
                    'is_published' => true,
                    'has_form' => true,
                    'form_fields' => $formFields,
                    'form_submit_button_text' => 'Request Free Consultation',
                    'form_success_message' => "Thank you! We'll contact you within 24 hours to discuss your needs.",
                    'notification_email' => 'info@homepro.com',
                    'send_admin_notification' => true,
                    'send_client_notification' => true,
                ]
            );
        }

        $this->command->info('✅ Home Pro Inc CMS pages created successfully!');
        $this->command->info('   - /home (Published)');
        $this->command->info('   - /mold-inspection (Published)');
        $this->command->info('   - /mold-remediation (Published)');
        $this->command->info('   - /water-damage-restoration (Published)');
        $this->command->info('   - /air-quality-testing (Published)');
        $this->command->info('   - /emergency-services (Published)');
        $this->command->info('   - /prevention-tips (Published)');
        $this->command->info('');
        $this->command->info('🎉 All pages include navigation bar and contact forms!');
    }
}
