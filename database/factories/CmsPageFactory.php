<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CmsPage>
 */
class CmsPageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'slug' => fake()->unique()->slug(),
            'title' => fake()->sentence(),
            'head_content' => '<meta name="description" content="'.fake()->sentence().'">',
            'body_content' => '<div class="container"><h1>'.fake()->sentence().'</h1><p>'.fake()->paragraph().'</p></div>',
            'cta_text' => 'Get Started',
            'cta_url' => '/book',
            'background_color' => 'bg-gray-900',
            'text_color' => 'text-white',
            'is_published' => true,
            'show_navbar' => true,
            'has_form' => false,
            'form_fields' => null,
            'form_submit_button_text' => 'Submit',
            'form_success_message' => 'Thank you for your submission! We will get back to you soon.',
            'form_redirect_url' => null,
        ];
    }

    /**
     * Create a CMS page with a lead form
     */
    public function withLeadForm(): static
    {
        return $this->state(fn (array $attributes) => [
            'has_form' => true,
            'form_fields' => [
                [
                    'type' => 'text',
                    'name' => 'name',
                    'label' => 'Full Name',
                    'placeholder' => 'John Doe',
                    'required' => true,
                    'options' => '',
                ],
                [
                    'type' => 'email',
                    'name' => 'email',
                    'label' => 'Email Address',
                    'placeholder' => 'john@example.com',
                    'required' => true,
                    'options' => '',
                ],
                [
                    'type' => 'tel',
                    'name' => 'phone',
                    'label' => 'Phone Number',
                    'placeholder' => '(555) 123-4567',
                    'required' => false,
                    'options' => '',
                ],
                [
                    'type' => 'text',
                    'name' => 'property_address',
                    'label' => 'Property Address',
                    'placeholder' => '123 Main Street, City, State',
                    'required' => false,
                    'options' => '',
                ],
                [
                    'type' => 'textarea',
                    'name' => 'message',
                    'label' => 'Message',
                    'placeholder' => 'Tell us about your project or inquiry...',
                    'required' => true,
                    'options' => '',
                ],
            ],
        ]);
    }

    /**
     * Create an unpublished page
     */
    public function unpublished(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_published' => false,
        ]);
    }

    /**
     * Create a page with a redirect URL
     */
    public function withRedirect(string $url = '/thank-you'): static
    {
        return $this->state(fn (array $attributes) => [
            'form_redirect_url' => $url,
        ]);
    }
}
