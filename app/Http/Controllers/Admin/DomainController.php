<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stancl\Tenancy\Database\Models\Domain;

class DomainController extends Controller
{
    /**
     * Display domain management page
     */
    public function index()
    {
        $tenant = tenant();
        $domains = Domain::where('tenant_id', $tenant->id)->get();
        
        // Get primary domain
        $primaryDomain = $domains->where('domain', $tenant->primary_domain)->first();

        return view('admin.domains.index', compact('domains', 'primaryDomain', 'tenant'));
    }

    /**
     * Store a new domain
     */
    public function store(Request $request)
    {
        $request->validate([
            'domain' => [
                'required',
                'string',
                'max:255',
                'regex:/^(?:[a-zA-Z0-9](?:[a-zA-Z0-9\-]{0,61}[a-zA-Z0-9])?\.)+[a-zA-Z]{2,}$/',
                'unique:domains,domain',
            ],
        ], [
            'domain.regex' => 'Please enter a valid domain name (e.g., www.example.com or example.com)',
            'domain.unique' => 'This domain is already in use.',
        ]);

        $tenant = tenant();

        try {
            // Create new domain
            $domain = Domain::create([
                'domain' => strtolower($request->domain),
                'tenant_id' => $tenant->id,
            ]);

            Log::info('Domain added to tenant', [
                'tenant_id' => $tenant->id,
                'domain' => $domain->domain,
                'user_id' => auth()->id(),
            ]);

            return back()->with('success', 'Domain added successfully. Please configure DNS settings to verify.');

        } catch (\Exception $e) {
            Log::error('Failed to add domain', [
                'tenant_id' => $tenant->id,
                'domain' => $request->domain,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Failed to add domain: ' . $e->getMessage());
        }
    }

    /**
     * Remove a domain
     */
    public function destroy(Domain $domain)
    {
        $tenant = tenant();

        // Verify domain belongs to current tenant
        if ($domain->tenant_id !== $tenant->id) {
            abort(403, 'Unauthorized to delete this domain.');
        }

        // Prevent deleting the primary domain
        if ($domain->domain === $tenant->primary_domain) {
            return back()->with('error', 'Cannot delete the primary domain. Set another domain as primary first.');
        }

        try {
            $domainName = $domain->domain;
            $domain->delete();

            Log::info('Domain removed from tenant', [
                'tenant_id' => $tenant->id,
                'domain' => $domainName,
                'user_id' => auth()->id(),
            ]);

            return back()->with('success', 'Domain removed successfully.');

        } catch (\Exception $e) {
            Log::error('Failed to remove domain', [
                'tenant_id' => $tenant->id,
                'domain_id' => $domain->id,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Failed to remove domain: ' . $e->getMessage());
        }
    }

    /**
     * Set domain as primary
     */
    public function setPrimary(Domain $domain)
    {
        $tenant = tenant();

        // Verify domain belongs to current tenant
        if ($domain->tenant_id !== $tenant->id) {
            abort(403, 'Unauthorized to modify this domain.');
        }

        try {
            $tenant->primary_domain = $domain->domain;
            $tenant->save();

            Log::info('Primary domain updated', [
                'tenant_id' => $tenant->id,
                'domain' => $domain->domain,
                'user_id' => auth()->id(),
            ]);

            return back()->with('success', 'Primary domain updated successfully.');

        } catch (\Exception $e) {
            Log::error('Failed to set primary domain', [
                'tenant_id' => $tenant->id,
                'domain_id' => $domain->id,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Failed to set primary domain: ' . $e->getMessage());
        }
    }

    /**
     * Verify domain DNS configuration
     */
    public function verify(Domain $domain)
    {
        $tenant = tenant();

        // Verify domain belongs to current tenant
        if ($domain->tenant_id !== $tenant->id) {
            abort(403, 'Unauthorized to verify this domain.');
        }

        // Get the expected IP or CNAME target
        $expectedIp = config('app.server_ip', '127.0.0.1');
        $expectedCname = $tenant->domains->first()->domain ?? null;

        try {
            // Check DNS records
            $records = dns_get_record($domain->domain, DNS_A + DNS_CNAME);

            $verified = false;

            foreach ($records as $record) {
                if ($record['type'] === 'A' && $record['ip'] === $expectedIp) {
                    $verified = true;
                    break;
                }
                if ($record['type'] === 'CNAME' && isset($record['target'])) {
                    $verified = true;
                    break;
                }
            }

            if ($verified) {
                // Update domain verification status (if you have such a field)
                Log::info('Domain verified', [
                    'tenant_id' => $tenant->id,
                    'domain' => $domain->domain,
                ]);

                return back()->with('success', 'Domain verified successfully! SSL certificate will be generated within 24 hours.');
            } else {
                return back()->with('error', 'Domain verification failed. Please check your DNS settings and try again in a few minutes.');
            }

        } catch (\Exception $e) {
            Log::error('Domain verification error', [
                'tenant_id' => $tenant->id,
                'domain' => $domain->domain,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Failed to verify domain: ' . $e->getMessage());
        }
    }

    /**
     * Show DNS setup guide
     */
    public function setupGuide()
    {
        $tenant = tenant();
        $defaultDomain = $tenant->domains->first();
        $serverIp = config('app.server_ip', '203.0.113.10');

        return view('admin.domains.setup-guide', compact('tenant', 'defaultDomain', 'serverIp'));
    }
}
