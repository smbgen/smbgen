<?php

namespace Database\Seeders;

use App\Models\CmsPage;
use Illuminate\Database\Seeder;

class RtsEnviroPageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $headContent = <<<'HTML'
<style>
  :root {
    --rts-primary: #2c5f2d;
    --rts-secondary: #4a7c4e;
    --rts-accent: #6b9b6e;
    --rts-dark: #1a3a1b;
    --rts-light: #f4f9f4;
    --rts-warning: #d97706;
  }
  
  .hero-rts {
    background: linear-gradient(135deg, var(--rts-dark) 0%, var(--rts-primary) 50%, var(--rts-secondary) 100%);
    background-size: 400% 400%;
    animation: gradientShift 20s ease infinite;
    color: #fff;
    padding: 6rem 2rem 4rem;
    text-align: center;
    margin: -2rem -2rem 3rem -2rem;
    position: relative;
    overflow: hidden;
  }
  
  .hero-rts::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><circle cx="50" cy="50" r="40" fill="rgba(255,255,255,0.03)"/></svg>');
    opacity: 0.1;
  }
  
  @keyframes gradientShift {
    0%, 100% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
  }
  
  .badge-rts {
    display: inline-block;
    background: rgba(255, 255, 255, 0.2);
    padding: 0.5rem 1rem;
    border-radius: 25px;
    margin: 0.25rem;
    font-size: 0.9rem;
    backdrop-filter: blur(10px);
  }
  
  .service-card {
    background: white;
    border-left: 4px solid var(--rts-primary);
    border-radius: 8px;
    padding: 2rem;
    margin: 1.5rem 0;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
  }
  
  .service-card:hover {
    transform: translateX(8px);
    box-shadow: 0 4px 16px rgba(44, 95, 45, 0.2);
  }
  
  .service-icon {
    font-size: 2.5rem;
    margin-bottom: 1rem;
    display: block;
  }
  
  .trust-badge {
    background: var(--rts-light);
    border: 2px solid var(--rts-accent);
    border-radius: 12px;
    padding: 1.5rem;
    text-align: center;
    margin: 1rem;
  }
  
  .trust-badge h4 {
    color: var(--rts-primary);
    margin-bottom: 0.5rem;
    font-weight: bold;
  }
  
  .cta-button {
    background: var(--rts-primary);
    color: white;
    padding: 1rem 2.5rem;
    border-radius: 50px;
    text-decoration: none;
    display: inline-block;
    font-weight: bold;
    font-size: 1.1rem;
    transition: all 0.3s ease;
    border: 3px solid var(--rts-primary);
  }
  
  .cta-button:hover {
    background: var(--rts-secondary);
    border-color: var(--rts-secondary);
    transform: translateY(-3px);
    box-shadow: 0 8px 20px rgba(44, 95, 45, 0.3);
    color: white;
  }
  
  .cta-button-outline {
    background: transparent;
    color: white;
    border: 3px solid white;
  }
  
  .cta-button-outline:hover {
    background: white;
    color: var(--rts-primary);
  }
  
  .emergency-banner {
    background: var(--rts-warning);
    color: white;
    padding: 1rem;
    text-align: center;
    font-weight: bold;
    border-radius: 8px;
    margin: 2rem 0;
    animation: pulse 2s infinite;
  }
  
  @keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.8; }
  }
  
  .testimonial-box {
    background: var(--rts-light);
    padding: 2rem;
    border-radius: 12px;
    border-left: 4px solid var(--rts-accent);
    margin: 2rem 0;
    font-style: italic;
  }
  
  .problem-card {
    background: linear-gradient(135deg, #fff 0%, var(--rts-light) 100%);
    padding: 2rem;
    border-radius: 12px;
    margin: 1rem 0;
    border: 2px solid var(--rts-accent);
  }
</style>
HTML;

        $bodyContent = <<<'HTML'
<div class="hero-rts">
  <div style="position: relative; z-index: 2;">
    <h1 style="font-size: 3.5rem; font-weight: bold; margin-bottom: 1rem; text-shadow: 2px 2px 4px rgba(0,0,0,0.3);">
      🏡 RTS Environmental Services
    </h1>
    <p style="font-size: 1.4rem; margin-bottom: 1.5rem; font-weight: 500;">
      Licensed, Insured, and Accredited · Residential and Commercial
    </p>
    <div style="margin: 2rem 0;">
      <span class="badge-rts">🏆 30 Years Experience</span>
      <span class="badge-rts">👨‍👩‍👧‍👦 Family Owned & Operated</span>
      <span class="badge-rts">📋 MD MHIC #93174</span>
    </div>
    <div style="margin-top: 2rem; display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap;">
      <a href="tel:8007225589" class="cta-button">📞 (800) 722-5589</a>
      <a href="#contact" class="cta-button cta-button-outline">Get Free Consultation</a>
    </div>
  </div>
</div>

<div style="max-width: 1200px; margin: 0 auto; padding: 2rem;">

  <div class="emergency-banner">
    🚨 WATER DAMAGE? MOLD EMERGENCY? We respond FAST! Same-day service available. Call now: (800) 722-5589
  </div>

  <section style="text-align: center; margin: 4rem 0;">
    <h2 style="font-size: 2.8rem; color: var(--rts-dark); margin-bottom: 1rem;">
      Protect Your Home & Health
    </h2>
    <p style="font-size: 1.2rem; color: #555; max-width: 800px; margin: 0 auto 3rem;">
      Since 1990, we've been Metro DC and Maryland's trusted experts in mold inspection, remediation, 
      asbestos testing, and environmental services. Don't risk your family's health or your home's value.
    </p>
  </section>

  <section style="margin: 4rem 0;">
    <h2 style="font-size: 2.5rem; color: var(--rts-dark); text-align: center; margin-bottom: 3rem;">
      Our Expert Services
    </h2>
    
    <div class="service-card">
      <span class="service-icon">🔬</span>
      <h3 style="color: var(--rts-primary); font-size: 1.8rem; margin-bottom: 1rem;">
        Mold Inspection & Testing
      </h3>
      <p style="color: #555; line-height: 1.8; margin-bottom: 1rem;">
        Professional mold inspections with 3rd party accredited lab testing. We identify hidden mold in HVAC systems, 
        crawl spaces, basements, and behind walls. Fast turnaround with detailed reports and photos.
      </p>
      <ul style="color: #555; line-height: 2;">
        <li>✓ Pre-purchase home inspections</li>
        <li>✓ Surface and air quality analysis</li>
        <li>✓ Hidden mold detection (HVAC, walls, crawl spaces)</li>
        <li>✓ Same-day service available</li>
      </ul>
    </div>

    <div class="service-card">
      <span class="service-icon">🛡️</span>
      <h3 style="color: var(--rts-primary); font-size: 1.8rem; margin-bottom: 1rem;">
        Mold Remediation & Removal
      </h3>
      <p style="color: #555; line-height: 1.8; margin-bottom: 1rem;">
        Complete mold removal and prevention services. We don't just cover it up—we remove it properly and 
        apply mold-resistant sealants to prevent future growth.
      </p>
      <ul style="color: #555; line-height: 2;">
        <li>✓ Complete mold removal (walls, HVAC, crawl spaces)</li>
        <li>✓ Mold-resistant sealant application</li>
        <li>✓ Water damage response & restoration</li>
        <li>✓ Long-term prevention solutions</li>
      </ul>
    </div>

    <div class="service-card">
      <span class="service-icon">⚠️</span>
      <h3 style="color: var(--rts-primary); font-size: 1.8rem; margin-bottom: 1rem;">
        Asbestos Testing & Lead Paint Services
      </h3>
      <p style="color: #555; line-height: 1.8; margin-bottom: 1rem;">
        Certified asbestos materials identification and lead paint renovation services. Protect your family 
        during renovations with proper testing and documentation.
      </p>
      <ul style="color: #555; line-height: 2;">
        <li>✓ PLM and TEM asbestos testing</li>
        <li>✓ Materials identification</li>
        <li>✓ Lead paint renovation & repair</li>
        <li>✓ Complete documentation services</li>
      </ul>
    </div>

    <div class="service-card">
      <span class="service-icon">🏗️</span>
      <h3 style="color: var(--rts-primary); font-size: 1.8rem; margin-bottom: 1rem;">
        Crawl Space & Basement Services
      </h3>
      <p style="color: #555; line-height: 1.8; margin-bottom: 1rem;">
        Complete crawl space encapsulation, moisture control, and basement waterproofing. Stop water intrusion 
        and create a healthy, dry environment.
      </p>
      <ul style="color: #555; line-height: 2;">
        <li>✓ Crawl space encapsulation</li>
        <li>✓ Dehumidifier installation (auto-drain systems)</li>
        <li>✓ Foundation waterproofing & Drylok application</li>
        <li>✓ Moisture barrier installation</li>
      </ul>
    </div>
  </section>

  <section style="background: linear-gradient(135deg, var(--rts-primary), var(--rts-secondary)); color: white; padding: 4rem 2rem; border-radius: 16px; text-align: center; margin: 4rem 0;">
    <h2 style="font-size: 2.5rem; margin-bottom: 1rem;">Why Choose RTS Environmental?</h2>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 2rem; margin-top: 3rem;">
      <div>
        <div style="font-size: 3rem; margin-bottom: 1rem;">🏆</div>
        <h4 style="font-size: 1.3rem; margin-bottom: 0.5rem;">30+ Years Experience</h4>
        <p style="opacity: 0.9;">Serving Metro DC & Maryland since 1990</p>
      </div>
      <div>
        <div style="font-size: 3rem; margin-bottom: 1rem;">⚡</div>
        <h4 style="font-size: 1.3rem; margin-bottom: 0.5rem;">Fast Response</h4>
        <p style="opacity: 0.9;">Same-day and next-day service available</p>
      </div>
      <div>
        <div style="font-size: 3rem; margin-bottom: 1rem;">📋</div>
        <h4 style="font-size: 1.3rem; margin-bottom: 0.5rem;">Licensed & Insured</h4>
        <p style="opacity: 0.9;">MD MHIC #93174 · Fully accredited</p>
      </div>
      <div>
        <div style="font-size: 3rem; margin-bottom: 1rem;">👨‍👩‍👧‍👦</div>
        <h4 style="font-size: 1.3rem; margin-bottom: 0.5rem;">Family Owned</h4>
        <p style="opacity: 0.9;">We treat your home like our own</p>
      </div>
    </div>
  </section>

  <section style="margin: 4rem 0;">
    <h2 style="font-size: 2.5rem; color: var(--rts-dark); text-align: center; margin-bottom: 1rem;">
      Common Problems We Solve
    </h2>
    <p style="text-align: center; color: #555; margin-bottom: 3rem; font-size: 1.1rem;">
      Don't ignore these warning signs! Early detection saves thousands in repairs.
    </p>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2rem;">
      <div class="problem-card">
        <h4 style="color: var(--rts-primary); font-size: 1.3rem; margin-bottom: 1rem;">🌫️ Musty Basement Smell</h4>
        <p style="color: #555;">That smell is mold! Hidden mold behind drywall or in HVAC systems can cause serious health issues.</p>
      </div>

      <div class="problem-card">
        <h4 style="color: var(--rts-primary); font-size: 1.3rem; margin-bottom: 1rem;">💧 Water Damage</h4>
        <p style="color: #555;">Leaky downspouts, foundation cracks, or burst pipes? We respond fast to prevent mold growth.</p>
      </div>

      <div class="problem-card">
        <h4 style="color: var(--rts-primary); font-size: 1.3rem; margin-bottom: 1rem;">🏚️ Pre-Purchase Inspections</h4>
        <p style="color: #555;">Don't skip the mold inspection! Your home inspector isn't trained for hidden mold detection.</p>
      </div>

      <div class="problem-card">
        <h4 style="color: var(--rts-primary); font-size: 1.3rem; margin-bottom: 1rem;">❄️ HVAC Mold</h4>
        <p style="color: #555;">Mold in flex ducting and air handlers spreads spores throughout your home. We inspect and clean.</p>
      </div>

      <div class="problem-card">
        <h4 style="color: var(--rts-primary); font-size: 1.3rem; margin-bottom: 1rem;">🕷️ Crawl Space Issues</h4>
        <p style="color: #555;">Moisture, rodents, and mold in your crawl space? Professional encapsulation is the solution.</p>
      </div>

      <div class="problem-card">
        <h4 style="color: var(--rts-primary); font-size: 1.3rem; margin-bottom: 1rem;">🏗️ Renovation Testing</h4>
        <p style="color: #555;">Renovating an older home? Test for asbestos and lead paint before you start work.</p>
      </div>
    </div>
  </section>

  <section style="margin: 4rem 0;">
    <h2 style="font-size: 2.5rem; color: var(--rts-dark); text-align: center; margin-bottom: 3rem;">
      Service Areas
    </h2>
    <div style="text-align: center; background: var(--rts-light); padding: 2rem; border-radius: 12px;">
      <p style="font-size: 1.2rem; color: var(--rts-dark); font-weight: 500; margin-bottom: 1rem;">
        📍 Metro DC & Central/Eastern Maryland
      </p>
      <p style="color: #555; line-height: 2;">
        Washington DC · Chevy Chase · Silver Spring · Takoma Park · Bethesda · Rockville · 
        Springfield · Montgomery County · Prince George's County · and surrounding areas
      </p>
    </div>
  </section>

  <div class="testimonial-box">
    <p style="font-size: 1.1rem; color: #333; line-height: 1.8; margin-bottom: 1rem;">
      "We have the HVAC guy come every year and they never said anything! RTS found hidden mold in our 
      attic HVAC system that could have caused serious health problems. They removed it professionally 
      and sealed everything to prevent future growth. Highly recommend!"
    </p>
    <p style="color: var(--rts-primary); font-weight: bold;">— Silver Spring Homeowner</p>
  </div>

  <section id="contact" style="margin: 4rem 0; padding: 3rem; background: white; border-radius: 16px; box-shadow: 0 4px 20px rgba(0,0,0,0.1); border: 3px solid var(--rts-accent);">
    <h2 style="text-align: center; font-size: 2.5rem; color: var(--rts-dark); margin-bottom: 1rem;">
      🏡 Protect Your Home Today
    </h2>
    <p style="text-align: center; color: #555; font-size: 1.1rem; margin-bottom: 1rem;">
      Free virtual consultations available! Fill out the form below or call us directly.
    </p>
    <p style="text-align: center; margin-bottom: 3rem;">
      <a href="tel:8007225589" style="color: var(--rts-primary); font-size: 1.8rem; font-weight: bold; text-decoration: none;">
        📞 (800) 722-5589
      </a>
      <br>
      <a href="mailto:info@rtsenviro.com" style="color: var(--rts-secondary); font-size: 1.1rem; text-decoration: none;">
        ✉️ info@rtsenviro.com
      </a>
    </p>
  </section>

  <section style="text-align: center; margin: 4rem 0; padding: 2rem; background: var(--rts-light); border-radius: 12px;">
    <h3 style="color: var(--rts-dark); margin-bottom: 1rem;">Don't Wait Until It's Too Late!</h3>
    <p style="color: #555; font-size: 1.1rem; margin-bottom: 2rem;">
      Mold grows fast. Water damage gets worse every day. Protect your investment and your family's health.
    </p>
    <a href="#contact" class="cta-button">Schedule Your Inspection Now</a>
  </section>

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
                'placeholder' => '(301) 555-1234',
                'required' => true,
            ],
            [
                'name' => 'address',
                'type' => 'text',
                'label' => 'Property Address',
                'placeholder' => 'Silver Spring, MD',
                'required' => false,
            ],
            [
                'name' => 'service_needed',
                'type' => 'select',
                'label' => 'Service Needed',
                'options' => 'Mold Inspection,Mold Remediation,Water Damage,Asbestos Testing,Lead Paint,Crawl Space Encapsulation,HVAC Mold,Other',
                'required' => true,
            ],
            [
                'name' => 'urgency',
                'type' => 'select',
                'label' => 'Urgency',
                'options' => 'Emergency (Same Day),Urgent (Within 2 Days),Routine,Just Exploring',
                'required' => false,
            ],
            [
                'name' => 'message',
                'type' => 'textarea',
                'label' => 'Describe Your Situation',
                'placeholder' => 'Tell us about your mold, water damage, or environmental concern...',
                'required' => false,
            ],
        ];

        CmsPage::updateOrCreate(
            ['slug' => 'rtsenviro'],
            [
                'title' => 'RTS Environmental Services - Mold Inspection & Remediation',
                'head_content' => $headContent,
                'body_content' => $bodyContent,
                'is_published' => false, // Draft by default
                'has_form' => true,
                'form_fields' => $formFields,
                'form_submit_button_text' => 'Request Free Consultation',
                'form_success_message' => "Thank you! We'll contact you within 2 hours during business hours. For emergencies, please call (800) 722-5589 now!",
                'notification_email' => 'info@rtsenviro.com',
                'send_admin_notification' => true,
                'send_client_notification' => true,
            ]
        );

        $this->command->info('✅ RTS Enviro CMS page created at slug: rtsenviro (unpublished draft)');
    }
}
