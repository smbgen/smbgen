@php
    $footerSettings = \App\Models\CmsFooterSetting::getSettings();
    $companyName = config('business.company_name', config('app.name'));
    $companyWebsite = config('business.contact.website');
    $companyHost = $companyWebsite ? parse_url($companyWebsite, PHP_URL_HOST) : null;
    
    // Get the footer HTML - use default if use_default is true or footer_html is empty
    $footerHtml = ($footerSettings->use_default || empty($footerSettings->footer_html))
        ? \App\Models\CmsFooterSetting::getDefaultFooterHtml()
        : $footerSettings->footer_html;
@endphp

{!! Blade::render($footerHtml, [
    'companyName' => $companyName,
    'companyWebsite' => $companyWebsite,
    'companyHost' => $companyHost,
]) !!}
