<?php

namespace Database\Seeders;

use App\Models\CmsPage;
use Illuminate\Database\Seeder;

class CmsDemoPageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $headContent = <<<'HTML'
<style>
  :root {
    --color-deep: #2c3e50;
    --color-ground: #543f54;
    --color-warm: #a57d63;
    --color-surface: #dbdbd5;
  }
  
  .hero {
    background: linear-gradient(-45deg, #001F3F, #2c3e50, #001F3F, #a57d63);
    background-size: 400% 400%;
    animation: gradientMove 15s ease infinite;
    color: #fff;
    padding: 5rem 2rem;
    text-align: center;
    margin: -2rem -2rem 3rem -2rem;
  }
  
  @keyframes gradientMove {
    0% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
    100% { background-position: 0% 50%; }
  }
  
  .feature-box {
    background: white;
    border-radius: 12px;
    padding: 2rem;
    margin: 1rem 0;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    transition: transform 0.3s ease;
  }
  
  .feature-box:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 12px rgba(0,0,0,0.15);
  }
  
  .feature-icon {
    font-size: 3rem;
    margin-bottom: 1rem;
  }
  
  .btn-main {
    background: linear-gradient(135deg, var(--color-ground), var(--color-deep));
    color: #fff;
    border: none;
    padding: 1rem 2rem;
    border-radius: 8px;
    font-weight: bold;
    text-decoration: none;
    display: inline-block;
    transition: all 0.3s ease;
  }
  
  .btn-main:hover {
    background: linear-gradient(135deg, var(--color-warm), var(--color-ground));
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(0,0,0,0.2);
    color: #fff;
  }
</style>
HTML;

        $bodyContent = <<<'HTML'
<div class="hero">
  <h1 style="font-size: 3.5rem; font-weight: bold; margin-bottom: 1.5rem;">Welcome to CLIENTBRIDGE</h1>
  <p style="font-size: 1.25rem; margin-bottom: 2rem;">Simple. Secure. Smart Infrastructure for Small Business.</p>
  <a href="#contact" class="btn-main">Get Started Today</a>
</div>

<div style="max-width: 1200px; margin: 0 auto; padding: 2rem;">
  
  <section style="text-align: center; margin: 4rem 0;">
    <h2 style="font-size: 2.5rem; color: var(--color-deep); margin-bottom: 1rem;">Why Choose CLIENTBRIDGE?</h2>
    <p style="font-size: 1.1rem; color: #666; max-width: 800px; margin: 0 auto 3rem;">
      We provide everything your small business needs to thrive online—secure infrastructure, 
      professional branding, and tools that just work.
    </p>
    
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2rem; margin-top: 3rem;">
      
      <div class="feature-box">
        <div class="feature-icon">🔒</div>
        <h3 style="color: var(--color-deep);">Cyber Secure</h3>
        <p style="color: #666;">
          Enterprise-grade security without the complexity. We protect your data, 
          your customers, and your reputation.
        </p>
      </div>
      
      <div class="feature-box">
        <div class="feature-icon">⚡</div>
        <h3 style="color: var(--color-deep);">Lightning Fast</h3>
        <p style="color: #666;">
          Built for speed and performance. Your website loads quickly, 
          your tools respond instantly.
        </p>
      </div>
      
      <div class="feature-box">
        <div class="feature-icon">🎨</div>
        <h3 style="color: var(--color-deep);">Professional Brand</h3>
        <p style="color: #666;">
          Stand out from competitors with a polished, modern brand identity 
          that builds trust with customers.
        </p>
      </div>
      
    </div>
  </section>
  
  <section style="background: linear-gradient(135deg, var(--color-ground), var(--color-deep)); color: white; padding: 4rem 2rem; border-radius: 16px; text-align: center; margin: 4rem 0;">
    <h2 style="font-size: 2.5rem; margin-bottom: 1rem;">Ready to Grow Your Business?</h2>
    <p style="font-size: 1.2rem; margin-bottom: 2rem; opacity: 0.9;">
      Join hundreds of small businesses that trust CLIENTBRIDGE for their infrastructure.
    </p>
    <a href="#contact" class="btn-main" style="background: white; color: var(--color-deep);">
      Start Your Free Consultation
    </a>
  </section>
  
  <section style="margin: 4rem 0;">
    <h2 style="text-align: center; font-size: 2.5rem; color: var(--color-deep); margin-bottom: 3rem;">What You Get</h2>
    
    <div style="display: grid; gap: 2rem;">
      <div style="display: flex; gap: 2rem; align-items: start; padding: 2rem; background: #f8f9fa; border-radius: 12px;">
        <div style="font-size: 3rem; flex-shrink: 0;">🌐</div>
        <div>
          <h3 style="color: var(--color-deep); margin-bottom: 0.5rem;">Professional Website</h3>
          <p style="color: #666; margin: 0;">
            Custom-designed website that reflects your brand and converts visitors into customers. 
            Mobile-responsive and optimized for search engines.
          </p>
        </div>
      </div>
      
      <div style="display: flex; gap: 2rem; align-items: start; padding: 2rem; background: #f8f9fa; border-radius: 12px;">
        <div style="font-size: 3rem; flex-shrink: 0;">📧</div>
        <div>
          <h3 style="color: var(--color-deep); margin-bottom: 0.5rem;">Business Email</h3>
          <p style="color: #666; margin: 0;">
            Professional email addresses with your domain name. Secure, reliable, 
            and includes spam protection.
          </p>
        </div>
      </div>
      
      <div style="display: flex; gap: 2rem; align-items: start; padding: 2rem; background: #f8f9fa; border-radius: 12px;">
        <div style="font-size: 3rem; flex-shrink: 0;">📊</div>
        <div>
          <h3 style="color: var(--color-deep); margin-bottom: 0.5rem;">Client Management</h3>
          <p style="color: #666; margin: 0;">
            Manage appointments, track leads, and communicate with customers—all in one place. 
            Simple tools that save you hours every week.
          </p>
        </div>
      </div>
      
      <div style="display: flex; gap: 2rem; align-items: start; padding: 2rem; background: #f8f9fa; border-radius: 12px;">
        <div style="font-size: 3rem; flex-shrink: 0;">🛡️</div>
        <div>
          <h3 style="color: var(--color-deep); margin-bottom: 0.5rem;">Security & Backups</h3>
          <p style="color: #666; margin: 0;">
            Automatic backups, SSL certificates, malware protection, and monitoring. 
            Sleep well knowing your business is protected 24/7.
          </p>
        </div>
      </div>
    </div>
  </section>
  
  <section id="contact" style="margin: 4rem 0; padding: 3rem; background: white; border-radius: 16px; box-shadow: 0 4px 20px rgba(0,0,0,0.1);">
    <h2 style="text-align: center; font-size: 2.5rem; color: var(--color-deep); margin-bottom: 1rem;">
      Let's Talk About Your Business
    </h2>
    <p style="text-align: center; color: #666; font-size: 1.1rem; margin-bottom: 3rem;">
      Fill out the form below and we'll reach out to discuss how CLIENTBRIDGE can help you grow.
    </p>
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
                'placeholder' => '(555) 123-4567',
                'required' => false,
            ],
            [
                'name' => 'business_type',
                'type' => 'text',
                'label' => 'Business Type',
                'placeholder' => 'e.g., Restaurant, Salon, Consulting',
                'required' => false,
            ],
            [
                'name' => 'message',
                'type' => 'textarea',
                'label' => 'Tell Us About Your Goals',
                'placeholder' => 'Tell us about your business goals...',
                'required' => false,
            ],
        ];

        CmsPage::updateOrCreate(
            ['slug' => 'demo'],
            [
                'title' => 'CLIENTBRIDGE Demo Page',
                'head_content' => $headContent,
                'body_content' => $bodyContent,
                'is_published' => false, // Draft by default
                'has_form' => true,
                'form_fields' => $formFields,
                'form_submit_button_text' => 'Send My Information',
                'form_success_message' => "Thank you! We'll be in touch within 24 hours to discuss your business needs.",
                'notification_email' => config('business.contact.email'),
                'send_admin_notification' => true,
                'send_client_notification' => true,
            ]
        );

        $this->command->info('✅ CMS Demo page created at slug: demo (unpublished draft)');
    }
}
