<x-layouts.customer title="Privacy Policy">
    <div class="mx-auto max-w-3xl px-4 py-12 sm:px-6 lg:px-8">
        <h1 class="font-display text-3xl font-bold text-gray-900">Privacy Policy</h1>
        <p class="mt-2 text-sm text-gray-500">Last updated: 09 Jun 2026</p>

        <div class="prose prose-gray mt-8 max-w-none text-sm leading-relaxed text-gray-600 space-y-6">

            <section>
                <h2 class="font-display text-lg font-semibold text-gray-900">1. Information We Collect</h2>
                <p>When you use {{ config('app.name') }}, we collect the following types of information:</p>
                <ul class="list-disc pl-5 space-y-1">
                    <li><strong>Account Information:</strong> Mobile number, name, email address (optional), and profile photo.</li>
                    <li><strong>Birth Details:</strong> Date of birth, time of birth, and place of birth — used solely to generate your personalized Vedic birth chart and predictions.</li>
                    <li><strong>Usage Data:</strong> Pages visited, features used, consultation history, and interaction timestamps.</li>
                    <li><strong>Payment Information:</strong> Wallet recharge transactions and payment gateway references. We do not store your card or bank details — all payments are processed securely by our payment gateway partner.</li>
                    <li><strong>Chat Data:</strong> Messages exchanged during consultations with astrologers, including any birth chart data shared.</li>
                    <li><strong>Device Information:</strong> Device type, operating system, browser type, and IP address for security and analytics purposes.</li>
                </ul>
            </section>

            <section>
                <h2 class="font-display text-lg font-semibold text-gray-900">2. How We Use Your Information</h2>
                <p>We use the information we collect to:</p>
                <ul class="list-disc pl-5 space-y-1">
                    <li>Generate your Vedic birth chart (Kundali), planetary transits, Hora timings, and daily predictions.</li>
                    <li>Facilitate consultations between you and astrologers on our platform.</li>
                    <li>Process wallet recharges and consultation payments.</li>
                    <li>Send you OTP codes for authentication and account security.</li>
                    <li>Send notifications about predictions, Dasha period changes, and subscription status.</li>
                    <li>Improve our services, fix issues, and develop new features.</li>
                </ul>
            </section>

            <section>
                <h2 class="font-display text-lg font-semibold text-gray-900">3. Data Sharing</h2>
                <p>We do not sell your personal information. We share data only in the following cases:</p>
                <ul class="list-disc pl-5 space-y-1">
                    <li><strong>With Astrologers:</strong> When you start a consultation, the astrologer may see your name and any birth chart details you choose to share during the session.</li>
                    <li><strong>Payment Processors:</strong> Transaction details are shared with our payment gateway partner to process wallet recharges.</li>
                    <li><strong>SMS/OTP Providers:</strong> Your mobile number is shared with our OTP provider solely for authentication.</li>
                    <li><strong>Legal Requirements:</strong> We may disclose information if required by law, regulation, or legal process.</li>
                </ul>
            </section>

            <section>
                <h2 class="font-display text-lg font-semibold text-gray-900">4. Data Security</h2>
                <p>We implement industry-standard security measures to protect your data, including encrypted connections (HTTPS), secure password hashing, and access controls. Birth chart data and consultation messages are stored securely and accessible only to you and the astrologer involved in the consultation.</p>
            </section>

            <section>
                <h2 class="font-display text-lg font-semibold text-gray-900">5. Data Retention</h2>
                <p>We retain your account and birth chart data for as long as your account is active. Consultation chat history is retained for reference and dispute resolution. If you wish to delete your account and all associated data, please contact us at <a href="mailto:{{ $supportEmail }}" class="text-cosmic-600 underline">{{ $supportEmail }}</a>.</p>
            </section>

            <section>
                <h2 class="font-display text-lg font-semibold text-gray-900">6. Your Rights</h2>
                <p>You have the right to:</p>
                <ul class="list-disc pl-5 space-y-1">
                    <li>Access and update your personal information through your profile settings.</li>
                    <li>Request a copy of the data we hold about you.</li>
                    <li>Request deletion of your account and associated data.</li>
                    <li>Opt out of non-essential notifications.</li>
                </ul>
            </section>

            <section>
                <h2 class="font-display text-lg font-semibold text-gray-900">7. Cookies and Analytics</h2>
                <p>We use session cookies for authentication and may use analytics tools to understand how users interact with our platform. No third-party advertising cookies are used.</p>
            </section>

            <section>
                <h2 class="font-display text-lg font-semibold text-gray-900">8. Changes to This Policy</h2>
                <p>We may update this privacy policy from time to time. Changes will be posted on this page with an updated revision date. Continued use of the platform after changes constitutes acceptance of the revised policy.</p>
            </section>

            <section>
                <h2 class="font-display text-lg font-semibold text-gray-900">9. Contact Us</h2>
                <p>If you have any questions about this privacy policy or your data, contact us at <a href="mailto:{{ $supportEmail }}" class="text-cosmic-600 underline">{{ $supportEmail }}</a>.</p>
            </section>

        </div>
    </div>
</x-layouts.customer>
