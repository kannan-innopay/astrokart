<x-layouts.customer title="Terms of Service">
    <div class="mx-auto max-w-3xl px-4 py-12 sm:px-6 lg:px-8">
        <h1 class="font-display text-3xl font-bold text-gray-900">Terms of Service</h1>
        <p class="mt-2 text-sm text-gray-500">Last updated: 09 Jun 2026</p>

        <div class="prose prose-gray mt-8 max-w-none text-sm leading-relaxed text-gray-600 space-y-6">

            <section>
                <h2 class="font-display text-lg font-semibold text-gray-900">1. Acceptance of Terms</h2>
                <p>By accessing or using {{ config('app.name') }} ("the Platform"), operated by {{ $companyName }}, you agree to be bound by these Terms of Service. If you do not agree, please do not use the Platform.</p>
            </section>

            <section>
                <h2 class="font-display text-lg font-semibold text-gray-900">2. Description of Service</h2>
                <p>{{ config('app.name') }} is a Vedic astrology platform that provides:</p>
                <ul class="list-disc pl-5 space-y-1">
                    <li>Personalized birth chart (Kundali) generation based on your birth details.</li>
                    <li>Planetary transit tracking and Hora timing calculations.</li>
                    <li>Daily and monthly astrological predictions.</li>
                    <li>Muhurtham (auspicious timing) finder and compatibility matching.</li>
                    <li>Live consultations with independent astrologers (when available).</li>
                    <li>Premium subscription plans with additional features.</li>
                </ul>
            </section>

            <section>
                <h2 class="font-display text-lg font-semibold text-gray-900">3. Account Registration</h2>
                <p>To use the Platform, you must register using a valid mobile number and verify it via OTP. You are responsible for maintaining the confidentiality of your account and for all activities that occur under it. You must provide accurate birth details for chart generation — inaccurate information will result in inaccurate readings.</p>
            </section>

            <section>
                <h2 class="font-display text-lg font-semibold text-gray-900">4. Wallet and Payments</h2>
                <ul class="list-disc pl-5 space-y-1">
                    <li>The Platform uses a prepaid wallet system. You must recharge your wallet before starting paid consultations.</li>
                    <li>Consultation charges are deducted from your wallet on a per-minute basis at the astrologer's listed rate.</li>
                    <li>Wallet recharges are processed through our payment gateway partner and are non-refundable once credited, except as described in Section 7.</li>
                    <li>Subscription plans are charged from your wallet. Daily plans auto-renew each day if sufficient balance is available.</li>
                </ul>
            </section>

            <section>
                <h2 class="font-display text-lg font-semibold text-gray-900">5. Consultations</h2>
                <ul class="list-disc pl-5 space-y-1">
                    <li>Astrologers on the Platform are independent practitioners, not employees of {{ $companyName }}.</li>
                    <li>The Platform facilitates the connection but does not guarantee the accuracy or outcome of any astrological advice.</li>
                    <li>Consultations are billed per minute. A platform commission is deducted from the astrologer's earnings.</li>
                    <li>Consultations may be ended by either party at any time. Automatic timeout applies to prevent unintended charges.</li>
                    <li>Chat messages exchanged during consultations are stored for your reference and dispute resolution.</li>
                </ul>
            </section>

            <section>
                <h2 class="font-display text-lg font-semibold text-gray-900">6. Subscriptions</h2>
                <ul class="list-disc pl-5 space-y-1">
                    <li>Premium subscription plans provide access to additional features such as daily predictions, monthly forecasts, and detailed chart analysis.</li>
                    <li>Daily plans auto-renew from your wallet balance each day. If your wallet has insufficient balance, the subscription will enter a grace period before expiring.</li>
                    <li>You may cancel your subscription at any time. Cancellation takes effect at the end of the current billing period.</li>
                </ul>
            </section>

            <section>
                <h2 class="font-display text-lg font-semibold text-gray-900">7. Refund Policy</h2>
                <p>Wallet recharges are generally non-refundable. However, refunds may be considered in the following cases:</p>
                <ul class="list-disc pl-5 space-y-1">
                    <li>Technical failure that prevented a consultation from taking place after charges were deducted.</li>
                    <li>Duplicate or erroneous payment transactions.</li>
                    <li>Astrologer misconduct verified by our review team.</li>
                </ul>
                <p>To request a refund, contact us at <a href="mailto:{{ $supportEmail }}" class="text-cosmic-600 underline">{{ $supportEmail }}</a> within 7 days of the incident.</p>
            </section>

            <section>
                <h2 class="font-display text-lg font-semibold text-gray-900">8. Disclaimer</h2>
                <p>Astrological readings and predictions provided on the Platform are based on Vedic astrology traditions and astronomical calculations. They are intended for guidance and entertainment purposes only. {{ $companyName }} makes no guarantees regarding the accuracy, reliability, or applicability of any astrological advice. The Platform should not be used as a substitute for professional medical, legal, financial, or psychological advice.</p>
            </section>

            <section>
                <h2 class="font-display text-lg font-semibold text-gray-900">9. User Conduct</h2>
                <p>You agree not to:</p>
                <ul class="list-disc pl-5 space-y-1">
                    <li>Use the Platform for any unlawful purpose.</li>
                    <li>Harass, abuse, or send inappropriate content to astrologers or other users.</li>
                    <li>Attempt to manipulate the billing or wallet system.</li>
                    <li>Create multiple accounts to exploit promotional offers.</li>
                    <li>Reverse-engineer, scrape, or interfere with the Platform's infrastructure.</li>
                </ul>
                <p>Violation of these terms may result in account suspension or termination.</p>
            </section>

            <section>
                <h2 class="font-display text-lg font-semibold text-gray-900">10. Intellectual Property</h2>
                <p>All content, design, code, and branding on the Platform are the property of {{ $companyName }} and are protected by applicable intellectual property laws. You may not reproduce, distribute, or create derivative works without written permission.</p>
            </section>

            <section>
                <h2 class="font-display text-lg font-semibold text-gray-900">11. Limitation of Liability</h2>
                <p>To the fullest extent permitted by law, {{ $companyName }} shall not be liable for any indirect, incidental, or consequential damages arising from your use of the Platform, including but not limited to decisions made based on astrological advice received through the Platform.</p>
            </section>

            <section>
                <h2 class="font-display text-lg font-semibold text-gray-900">12. Changes to Terms</h2>
                <p>We reserve the right to update these terms at any time. Changes will be posted on this page with an updated revision date. Continued use of the Platform after changes constitutes acceptance of the revised terms.</p>
            </section>

            <section>
                <h2 class="font-display text-lg font-semibold text-gray-900">13. Governing Law</h2>
                <p>These terms are governed by the laws of India. Any disputes arising from or related to these terms shall be subject to the exclusive jurisdiction of the courts in the jurisdiction where {{ $companyName }} is registered.</p>
            </section>

            <section>
                <h2 class="font-display text-lg font-semibold text-gray-900">14. Contact Us</h2>
                <p>For questions about these terms, contact us at <a href="mailto:{{ $supportEmail }}" class="text-cosmic-600 underline">{{ $supportEmail }}</a>.</p>
            </section>

        </div>
    </div>
</x-layouts.customer>
