<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AISEOController extends Controller
{
    public function viewTool()
    {
        return view('ai-seo-tool-v1');
    }

    public function handleQuery(Request $request)
    {
        // Validate the request
        $request->validate([
            'query' => 'required|string|max:1000',
        ]);

        $query = $request->input('query');
        $apiKey = config('services.openai.api_key');

        // Check if API key is configured
        if (! $apiKey) {
            \Log::error('OpenAI API key not configured');

            return response()->json(['error' => 'OpenAI API key not configured.'], 500);
        }

        try {
            $response = Http::withToken($apiKey)
                ->timeout(30)
                ->post('https://api.openai.com/v1/chat/completions', [
                    'model' => 'gpt-3.5-turbo',
                    'messages' => [
                        ['role' => 'system', 'content' => 'You are smbgen AI SEO Assistant. Answer queries with expert-level SEO guidance.'],
                        ['role' => 'user', 'content' => $query],
                    ],
                    'max_tokens' => 1000,
                    'temperature' => 0.7,
                ]);

            if ($response->failed()) {
                \Log::error('OpenAI API failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                // Check if it's a quota error and provide helpful response
                if ($response->status() === 429) {
                    return response()->json([
                        'response' => "I'm currently experiencing high demand. Here's some general SEO advice for your query: '".$query."'\n\n".$this->getFallbackSEOAdvice($query),
                    ]);
                }

                return response()->json(['error' => 'OpenAI API request failed: '.$response->status()], 500);
            }

            $data = $response->json();

            if (! isset($data['choices'][0]['message']['content'])) {
                \Log::error('Unexpected OpenAI response structure', ['response' => $data]);

                return response()->json(['error' => 'Unexpected API response structure.'], 500);
            }

            return response()->json([
                'response' => $data['choices'][0]['message']['content'],
            ]);

        } catch (\Exception $e) {
            \Log::error('OpenAI API exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json(['error' => 'An error occurred while processing your request.'], 500);
        }
    }

    private function getFallbackSEOAdvice($query)
    {
        $query = strtolower($query);

        if (str_contains($query, 'keyword') || str_contains($query, 'research')) {
            return 'For keyword research, use tools like Google Keyword Planner, Ahrefs, or SEMrush. Focus on long-tail keywords with lower competition and higher intent.';
        }

        if (str_contains($query, 'content') || str_contains($query, 'writing')) {
            return 'Create high-quality, relevant content that answers user questions. Use proper heading structure (H1, H2, H3), include relevant keywords naturally, and aim for comprehensive coverage of topics.';
        }

        if (str_contains($query, 'technical') || str_contains($query, 'speed') || str_contains($query, 'performance')) {
            return 'Optimize page speed by compressing images, using a CDN, minimizing CSS/JS, and leveraging browser caching. Use Google PageSpeed Insights to identify issues.';
        }

        if (str_contains($query, 'backlink') || str_contains($query, 'link')) {
            return 'Build quality backlinks through guest posting, creating shareable content, and building relationships with industry influencers. Focus on relevance over quantity.';
        }

        if (str_contains($query, 'local') || str_contains($query, 'google my business')) {
            return 'For local SEO, optimize your Google My Business profile, get customer reviews, ensure NAP consistency across directories, and create location-specific content.';
        }

        return 'Focus on creating valuable content, optimizing for user experience, building quality backlinks, and ensuring your site is technically sound. SEO is a long-term strategy that requires consistent effort.';
    }
}
