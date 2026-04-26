<?php

use App\Mail\ClientPortalAccessMail;

it('renders client portal access email using business name instead of app name fallback', function () {
    config()->set('business.name', 'Construction Co Demo');
    config()->set('business.company_name', null);
    config()->set('app.company_name', null);
    config()->set('app.name', 'Example');

    $mail = new ClientPortalAccessMail(
        clientName: 'Test Client',
        emailAddress: 'client@example.com',
        resetUrl: 'https://example.test/reset-token'
    );

    $rendered = $mail->render();

    expect($rendered)
        ->toContain('Construction Co Demo')
        ->not->toContain('Welcome to Example!');
});
