<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>End-User License Agreement - CLIENTBRIDGE</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <div class="max-w-4xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                <h1 class="text-3xl font-bold text-gray-900">End-User License Agreement</h1>
                <p class="mt-1 text-sm text-gray-500">Last updated: {{ date('F j, Y') }}</p>
            </div>

            <div class="px-4 py-5 sm:p-6 space-y-6 text-gray-700">
                <section>
                    <h2 class="text-xl font-semibold text-gray-900 mb-3">1. Agreement to Terms</h2>
                    <p>By accessing and using CLIENTBRIDGE ("the Service"), you agree to be bound by this End-User License Agreement. If you do not agree to these terms, do not use the Service.</p>
                </section>

                <section>
                    <h2 class="text-xl font-semibold text-gray-900 mb-3">2. License Grant</h2>
                    <p>Subject to your compliance with this Agreement, we grant you a limited, non-exclusive, non-transferable, revocable license to access and use the Service for your business operations.</p>
                </section>

                <section>
                    <h2 class="text-xl font-semibold text-gray-900 mb-3">3. Restrictions</h2>
                    <p>You agree not to:</p>
                    <ul class="list-disc ml-6 mt-2 space-y-1">
                        <li>Modify, reverse engineer, or create derivative works of the Service</li>
                        <li>Use the Service for any illegal or unauthorized purpose</li>
                        <li>Interfere with or disrupt the Service or servers</li>
                        <li>Attempt to gain unauthorized access to any portion of the Service</li>
                        <li>Remove or alter any proprietary notices on the Service</li>
                    </ul>
                </section>

                <section>
                    <h2 class="text-xl font-semibold text-gray-900 mb-3">4. User Accounts</h2>
                    <p>You are responsible for maintaining the confidentiality of your account credentials and for all activities that occur under your account. You must notify us immediately of any unauthorized use of your account.</p>
                </section>

                <section>
                    <h2 class="text-xl font-semibold text-gray-900 mb-3">5. Data and Privacy</h2>
                    <p>Your use of the Service is also governed by our Privacy Policy. We collect, use, and protect your data as described in that policy.</p>
                </section>

                <section>
                    <h2 class="text-xl font-semibold text-gray-900 mb-3">6. Third-Party Integrations</h2>
                    <p>The Service may integrate with third-party services such as Google Services. Your use of these integrations is subject to the respective third-party terms and conditions.</p>
                </section>

                <section>
                    <h2 class="text-xl font-semibold text-gray-900 mb-3">7. Termination</h2>
                    <p>We reserve the right to suspend or terminate your access to the Service at any time, with or without notice, for conduct that we believe violates this Agreement or is harmful to other users, us, or third parties, or for any other reason.</p>
                </section>

                <section>
                    <h2 class="text-xl font-semibold text-gray-900 mb-3">8. Disclaimer of Warranties</h2>
                    <p>THE SERVICE IS PROVIDED "AS IS" AND "AS AVAILABLE" WITHOUT WARRANTIES OF ANY KIND, EITHER EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO IMPLIED WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE, AND NON-INFRINGEMENT.</p>
                </section>

                <section>
                    <h2 class="text-xl font-semibold text-gray-900 mb-3">9. Limitation of Liability</h2>
                    <p>IN NO EVENT SHALL CLIENTBRIDGE BE LIABLE FOR ANY INDIRECT, INCIDENTAL, SPECIAL, CONSEQUENTIAL, OR PUNITIVE DAMAGES, OR ANY LOSS OF PROFITS OR REVENUES, WHETHER INCURRED DIRECTLY OR INDIRECTLY.</p>
                </section>

                <section>
                    <h2 class="text-xl font-semibold text-gray-900 mb-3">10. Changes to Agreement</h2>
                    <p>We reserve the right to modify this Agreement at any time. We will notify users of any material changes. Your continued use of the Service after such modifications constitutes acceptance of the updated terms.</p>
                </section>

                <section>
                    <h2 class="text-xl font-semibold text-gray-900 mb-3">11. Contact Information</h2>
                    <p>If you have questions about this Agreement, please contact us at:</p>
                    <p class="mt-2">
                        Email: <a href="mailto:alex@oldlinecyber.com" class="text-blue-600 hover:text-blue-800">alex@oldlinecyber.com</a><br>
                        Website: <a href="{{ config('app.url') }}" class="text-blue-600 hover:text-blue-800">{{ config('app.url') }}</a>
                    </p>
                </section>
            </div>

            <div class="px-4 py-4 sm:px-6 bg-gray-50 border-t border-gray-200">
                <a href="{{ url('/') }}" class="text-blue-600 hover:text-blue-800 font-medium">
                    ← Back to Home
                </a>
            </div>
        </div>
    </div>
</body>
</html>
