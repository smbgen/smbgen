# Cyber AI Audit Domain Setup

## Overview
This document explains how to set up the cyberaiaudit.com domain to redirect to your Laravel application's cyber audit demo.

## Files Created

### 1. Redirect File (`public_html_redirect/index.html`)
This file should be uploaded to the `public_html` folder of your cyberaiaudit.com hosting.

**Purpose**: Provides a professional redirect page that automatically sends visitors to your demo.

**Features**:
- Professional loading animation
- Automatic redirect after 0 seconds
- Manual fallback link
- Responsive design
- Branded with Cyber AI Audit styling

### 2. Demo Landing Page (`resources/views/cyber-audit-demo.blade.php`)
This is the main demo page that visitors will see after the redirect.

**Features**:
- Modern, dark-themed design
- Interactive AI chat demo
- Responsive layout
- SEO optimized
- Call-to-action buttons linking to lead form

## Setup Instructions

### Step 1: Upload Redirect File
1. Upload the `public_html_redirect/index.html` file to your cyberaiaudit.com hosting
2. Place it in the `public_html` folder (or equivalent root directory)
3. Ensure it's named `index.html` so it loads by default

### Step 2: Update URLs (When Going Live)
When you deploy to production, update these URLs in the redirect file:
- Change `http://smbgen.test/cyber-audit-demo` to your production URL
- Example: `https://yourdomain.com/cyber-audit-demo`

### Step 3: Test the Setup
1. Visit `cyberaiaudit.com` - should redirect to your demo
2. Test the interactive chat demo
3. Verify all links work correctly

## Demo Features

### Interactive AI Chat
The demo includes a simulated AI chat that responds to common cybersecurity questions:
- Ransomware protection
- Current threats
- Compliance frameworks
- Network security
- Data protection

### Responsive Design
- Works on desktop, tablet, and mobile
- Modern dark theme
- Smooth animations
- Professional typography

### Lead Generation
- Multiple call-to-action buttons
- Links to lead form
- Clear value proposition

## Customization

### Update Demo Responses
Edit the `demoResponses` object in the JavaScript section to add more AI responses.

### Change Styling
Modify the CSS variables in the `:root` section to change colors and branding.

### Add Features
- Integrate with real AI API
- Add more interactive elements
- Include testimonials or case studies

## Technical Notes

### Route Configuration
The demo page is accessible at `/cyber-audit-demo` and uses a simple view route.

### SEO Optimization
- Meta description included
- Semantic HTML structure
- Fast loading (no heavy dependencies)

### Performance
- Uses CDN for Bootstrap and icons
- Minimal custom CSS
- Optimized for fast loading

## Troubleshooting

### Redirect Not Working
1. Check file permissions on hosting
2. Verify file is in correct directory
3. Test with manual link

### Demo Page Not Loading
1. Check Laravel route configuration
2. Verify view file exists
3. Check for any syntax errors

### Styling Issues
1. Ensure CDN links are accessible
2. Check browser console for errors
3. Verify CSS is loading properly
