# Domain Connection Guide for ClientBridge

This guide explains how to connect your custom domain to your ClientBridge instance.

## Overview

By default, your ClientBridge instance is accessible via a subdomain:
- **Default Subdomain**: `your-business.clientbridge.app`

You can also connect your own custom domain (e.g., `www.yourbusiness.com`) to your ClientBridge instance.

## Prerequisites

- Access to your domain registrar (GoDaddy, Namecheap, Cloudflare, etc.)
- Admin access to your ClientBridge instance
- Your custom domain name

## Step 1: Add Domain in ClientBridge

1. Log in to your ClientBridge admin panel
2. Navigate to **Settings → Domains**
3. Click **Add Custom Domain**
4. Enter your domain name (e.g., `www.yourbusiness.com` or `yourbusiness.com`)
5. Click **Add Domain**
6. You'll see DNS configuration instructions - **keep this page open**

## Step 2: Configure DNS Settings

You have two options for connecting your domain:

### Option A: Using a Subdomain (Recommended)

If you're using a subdomain like `www.yourbusiness.com`:

**Add a CNAME Record:**
```
Type: CNAME
Name: www (or your subdomain)
Value: your-business.clientbridge.app
TTL: 3600 (or Auto)
```

### Option B: Using Root Domain

If you're using the root domain `yourbusiness.com`:

**Add an A Record:**
```
Type: A
Name: @ (or leave blank for root)
Value: [IP Address provided by ClientBridge]
TTL: 3600 (or Auto)
```

**Note**: Some registrars support CNAME flattening or ALIAS records for root domains. Check with your provider.

## Step 3: DNS Configuration by Provider

### GoDaddy

1. Log in to your GoDaddy account
2. Go to **My Products** → **DNS**
3. Click **Add** in the Records section
4. Select record type (CNAME or A)
5. Fill in the Name and Value fields
6. Click **Save**

### Namecheap

1. Log in to Namecheap
2. Go to **Domain List** → Select your domain
3. Click **Advanced DNS**
4. Click **Add New Record**
5. Select record type and fill in details
6. Click the checkmark to save

### Cloudflare

1. Log in to Cloudflare
2. Select your domain
3. Go to **DNS** tab
4. Click **Add record**
5. Fill in the details
6. **Important**: Set **Proxy status** to **DNS only** (grey cloud)
7. Click **Save**

### Other Providers

Most DNS providers have similar interfaces:
1. Find the DNS Management or DNS Records section
2. Add a new record
3. Enter the Type, Name/Host, and Value
4. Save changes

## Step 4: Verify Domain Connection

1. Return to your ClientBridge domain settings
2. Click **Verify Domain** next to your domain
3. Wait for DNS propagation (can take 5 minutes to 48 hours, usually ~15 minutes)
4. Once verified, you'll see a green checkmark

## Step 5: Set as Primary Domain (Optional)

1. In ClientBridge domain settings
2. Click **Set as Primary** next to your custom domain
3. This will make your custom domain the default access point

## SSL Certificate

- SSL certificates are automatically generated and renewed
- Once your domain is verified, HTTPS will be enabled within 24 hours
- Your site will be accessible via both HTTP and HTTPS during transition
- After SSL activation, HTTP traffic will automatically redirect to HTTPS

## Troubleshooting

### Domain Not Verifying

**Check DNS propagation:**
```bash
dig your-domain.com
nslookup your-domain.com
```

Or use online tools:
- https://dnschecker.org
- https://www.whatsmydns.net

**Common issues:**
- DNS changes haven't propagated (wait longer)
- Incorrect DNS record type (CNAME vs A)
- Typo in the DNS value
- Cloudflare proxy enabled (must be DNS only)
- Multiple DNS records for the same name (remove duplicates)

### "This site can't be reached" Error

- DNS hasn't propagated yet (wait 15-30 minutes)
- DNS record is incorrect
- Your domain registrar hasn't applied changes

### SSL Certificate Issues

- SSL generation takes up to 24 hours after domain verification
- Ensure your domain is fully verified first
- Contact ClientBridge support if SSL doesn't activate after 24 hours

## DNS Record Examples

### Example 1: Subdomain with CNAME
```
Type: CNAME
Name: www
Value: franks-lawn-care.clientbridge.app
TTL: 3600
```
Result: `www.frankslawncare.com` → ClientBridge instance

### Example 2: Root Domain with A Record
```
Type: A
Name: @
Value: 203.0.113.10
TTL: 3600
```
Result: `frankslawncare.com` → ClientBridge instance

### Example 3: Both Root and WWW
```
# Root domain
Type: A
Name: @
Value: 203.0.113.10

# WWW subdomain
Type: CNAME
Name: www
Value: franks-lawn-care.clientbridge.app
```
Result: Both `frankslawncare.com` and `www.frankslawncare.com` work

## Email Considerations

**Important**: If you're using email with your domain (e.g., `you@yourbusiness.com`), be careful with DNS changes.

- **Subdomains (www, app, etc.)** → Safe to point to ClientBridge
- **Root domain** → May affect email delivery
- **Solution**: Only point www to ClientBridge, keep root for email

### Safe Configuration for Email Users
```
# Point www to ClientBridge
Type: CNAME
Name: www
Value: your-business.clientbridge.app

# Keep email working
Type: MX
Name: @
Value: (your email provider's MX records)
```

## Support

Need help?
- **Documentation**: Check your ClientBridge admin panel → Documentation
- **Email Support**: Contact your ClientBridge administrator
- **DNS Propagation**: Wait at least 30 minutes before troubleshooting

## FAQ

**Q: Can I use multiple domains?**
A: Yes! Add as many custom domains as needed. Each can point to the same ClientBridge instance.

**Q: Will my old domain still work?**
A: Yes, your default `*.clientbridge.app` subdomain will always work.

**Q: How long does DNS propagation take?**
A: Typically 15-30 minutes, but can take up to 48 hours in rare cases.

**Q: Do I need to pay extra for custom domains?**
A: Custom domain support is included in Professional and Enterprise plans. Check your plan details.

**Q: Can I use an apex domain without www?**
A: Yes, use an A record pointing to the IP address provided by ClientBridge.

**Q: What about wildcard domains?**
A: Wildcard domains (*.yourdomain.com) are supported on Enterprise plans only.

**Q: Can I remove a domain after adding it?**
A: Yes, go to Settings → Domains and click Remove next to the domain.
