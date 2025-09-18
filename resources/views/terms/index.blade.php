@extends('layouts.guest')

@section('title', 'Terms and Conditions')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white shadow-lg rounded-lg p-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-6">Terms and Conditions</h1>
        <p class="text-sm text-gray-600 mb-8">Last updated: August 10, 2025</p>
        
        <div class="prose prose-lg max-w-none">
            <p class="text-gray-700 mb-6">
                These Terms of Service (the "Terms") govern access to and use of the MaintainXtra website at www.maintainxtra.com and related applications, platforms, and services (collectively, the "Service"). These Terms form a binding agreement between MaintainXtra ("we," "us," or "our") and the entity or person agreeing to these Terms ("Customer," "you," or "your"). If you use the Service on behalf of an organization, you represent that you have authority to bind that organization to these Terms.
            </p>
            
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-8">
                <p class="font-semibold text-blue-900 mb-2">Summary (not legally binding):</p>
                <p class="text-blue-800">MaintainXtra is a monthly subscription SaaS built for property managers and vacation rental managers. Your plan auto-renews each month until cancelled. You own your data; we license the software. Please use the Service lawfully and responsibly.</p>
            </div>

            <div class="space-y-8">
                <section>
                    <h2 class="text-2xl font-semibold text-gray-900 mb-4">1. The Service; Accounts</h2>
                    <div class="space-y-4">
                        <div>
                            <h3 class="text-lg font-medium text-gray-800 mb-2">1.1 Eligibility</h3>
                            <p class="text-gray-700">You must be at least 18 years old and able to enter into contracts to use the Service.</p>
                        </div>
                        <div>
                            <h3 class="text-lg font-medium text-gray-800 mb-2">1.2 Account Registration</h3>
                            <p class="text-gray-700">To use the Service, you must create an account and provide accurate, complete information. You are responsible for maintaining the confidentiality of your login credentials and for all activities under your account.</p>
                        </div>
                        <div>
                            <h3 class="text-lg font-medium text-gray-800 mb-2">1.3 Authorized Users</h3>
                            <p class="text-gray-700">You may allow your employees or contractors ("Authorized Users") to access the Service under your account, and you are responsible for their compliance with these Terms.</p>
                        </div>
                    </div>
                </section>

                <section>
                    <h2 class="text-2xl font-semibold text-gray-900 mb-4">2. Subscription Plans, Billing & Taxes</h2>
                    <div class="space-y-4">
                        <div>
                            <h3 class="text-lg font-medium text-gray-800 mb-2">2.1 Plans</h3>
                            <p class="text-gray-700">The Service is offered on a monthly subscription basis. Current plan features and pricing are presented during checkout or in your billing settings (the "Plan").</p>
                        </div>
                        <div>
                            <h3 class="text-lg font-medium text-gray-800 mb-2">2.2 Auto-Renewal</h3>
                            <p class="text-gray-700">Subscriptions automatically renew month-to-month unless cancelled prior to the end of the current billing period.</p>
                        </div>
                        <div>
                            <h3 class="text-lg font-medium text-gray-800 mb-2">2.3 Billing</h3>
                            <p class="text-gray-700">You authorize us and our payment processor to charge your payment method for all subscription fees, applicable taxes, and any add-ons or overage fees. Fees are payable in advance for each monthly period.</p>
                        </div>
                        <div>
                            <h3 class="text-lg font-medium text-gray-800 mb-2">2.4 Price Changes</h3>
                            <p class="text-gray-700">We may change pricing or features by providing advance notice via the Service or email. Changes take effect on your next renewal unless otherwise stated.</p>
                        </div>
                        <div>
                            <h3 class="text-lg font-medium text-gray-800 mb-2">2.5 Taxes</h3>
                            <p class="text-gray-700">Fees are exclusive of taxes, levies, duties, or similar governmental assessments (collectively, "Taxes"). You are responsible for all Taxes associated with your purchase, except for taxes based on our net income.</p>
                        </div>
                        <div>
                            <h3 class="text-lg font-medium text-gray-800 mb-2">2.6 Upgrades & Downgrades</h3>
                            <p class="text-gray-700">Upgrades take effect immediately and may be prorated for the remainder of the current period. Downgrades take effect at the next renewal and may impact features, limits, or storage.</p>
                        </div>
                    </div>
                </section>

                <section>
                    <h2 class="text-2xl font-semibold text-gray-900 mb-4">3. Free Trial, Grace Period, Cancellations & Refunds</h2>
                    <div class="space-y-4">
                        <div>
                            <h3 class="text-lg font-medium text-gray-800 mb-2">3.1 30-Day Free Trial (no credit card)</h3>
                            <p class="text-gray-700">When you sign up with your name, email, and password, your trial begins immediately (trial_started_at = the time of registration) and ends 30 days later (trial_expires_at = trial_started_at + 30 days). During Day 0–30, you have full access to the features included in your Plan.</p>
                        </div>
                        <div>
                            <h3 class="text-lg font-medium text-gray-800 mb-2">3.2 Post-Trial Grace Period (Days 31–37)</h3>
                            <p class="text-gray-700">From the day after your trial expires through Day 37, your account enters a 7-day grace period. On login you will be redirected to the payment screen with the notice: "Your free trial has ended. Subscribe now to keep your data."</p>
                        </div>
                        <div>
                            <h3 class="text-lg font-medium text-gray-800 mb-2">3.3 Account Lock (from Day 38)</h3>
                            <p class="text-gray-700">If payment is not completed by Day 38 after trial expiration, your account is locked. Locked accounts cannot access the Service and will see: "Your trial has expired. Reactivate anytime to continue."</p>
                        </div>
                        <div>
                            <h3 class="text-lg font-medium text-gray-800 mb-2">3.4 Cancellation (Paid Plans)</h3>
                            <p class="text-gray-700">You can cancel any paid subscription at any time via billing settings. Cancellation becomes effective at the end of the current billing period; you will retain access until then.</p>
                        </div>
                        <div>
                            <h3 class="text-lg font-medium text-gray-800 mb-2">3.5 Refunds</h3>
                            <p class="text-gray-700">Except where required by law or expressly stated otherwise, all fees are non-refundable and non-creditable, including for partial periods and unused features.</p>
                        </div>
                        <div>
                            <h3 class="text-lg font-medium text-gray-800 mb-2">3.6 Trials Are As-Is</h3>
                            <p class="text-gray-700">During the free trial, the Service is provided "as is" with no warranties or commitments, and features or limits may change.</p>
                        </div>
                    </div>
                    <p class="text-sm text-gray-600 mt-4">Timing note: Day counts are measured from the trial_expires_at timestamp maintained in our systems and may be calculated in UTC.</p>
                </section>

                <section>
                    <h2 class="text-2xl font-semibold text-gray-900 mb-4">4. Access Rights; Acceptable Use</h2>
                    <div class="space-y-4">
                        <div>
                            <h3 class="text-lg font-medium text-gray-800 mb-2">4.1 License</h3>
                            <p class="text-gray-700">Subject to these Terms and your payment of applicable fees, we grant you a limited, non-exclusive, non-transferable, revocable right to access and use the Service for your internal business purposes.</p>
                        </div>
                        <div>
                            <h3 class="text-lg font-medium text-gray-800 mb-2">4.2 Restrictions</h3>
                            <p class="text-gray-700">You will not (and will not permit anyone to): (a) copy, modify, or create derivative works of the Service; (b) reverse engineer, decompile, or attempt to extract source code; (c) resell, lease, or provide the Service to third parties as a service bureau; (d) access the Service for competitive benchmarking; or (e) use the Service in violation of law or these Terms.</p>
                        </div>
                        <div>
                            <h3 class="text-lg font-medium text-gray-800 mb-2">4.3 Acceptable Use</h3>
                            <p class="text-gray-700">You agree not to upload or transmit any content that is unlawful, infringing, defamatory, harassing, abusive, deceptive, malware, or that violates privacy or intellectual property rights; not to interfere with the security or operation of the Service; and not to attempt unauthorized access to accounts or systems.</p>
                        </div>
                    </div>
                </section>

                <section>
                    <h2 class="text-2xl font-semibold text-gray-900 mb-4">5. Customer Data, Privacy & Retention</h2>
                    <div class="space-y-4">
                        <div>
                            <h3 class="text-lg font-medium text-gray-800 mb-2">5.1 Customer Data Ownership</h3>
                            <p class="text-gray-700">You retain all right, title, and interest in and to data, content, files, and information submitted to the Service ("Customer Data").</p>
                        </div>
                        <div>
                            <h3 class="text-lg font-medium text-gray-800 mb-2">5.2 Our Use of Customer Data</h3>
                            <p class="text-gray-700">You grant us a worldwide, non-exclusive license to host, process, transmit, display, and otherwise use Customer Data to provide and maintain the Service; to prevent or address security, support, or technical issues; and as otherwise permitted by these Terms.</p>
                        </div>
                        <div>
                            <h3 class="text-lg font-medium text-gray-800 mb-2">5.3 Privacy</h3>
                            <p class="text-gray-700">Our collection and use of personal data is described in our Privacy Notice. You are responsible for providing notices and obtaining any required consents from your end users.</p>
                        </div>
                        <div>
                            <h3 class="text-lg font-medium text-gray-800 mb-2">5.4 Security</h3>
                            <p class="text-gray-700">We implement commercially reasonable safeguards designed to protect Customer Data. However, no method of transmission or storage is completely secure.</p>
                        </div>
                        <div>
                            <h3 class="text-lg font-medium text-gray-800 mb-2">5.5 Data Retention for Trials</h3>
                            <p class="text-gray-700">If your free trial ends without conversion to a paid subscription, we will retain your Customer Data for 90 days from the trial_expires_at timestamp (the "Trial Retention Period"). During this period you may reactivate by subscribing to regain access.</p>
                        </div>
                    </div>
                </section>

                <section>
                    <h2 class="text-2xl font-semibold text-gray-900 mb-4">6. Third-Party Services & Integrations</h2>
                    <p class="text-gray-700">The Service may interoperate with third-party products or services ("Third-Party Services"). Your use of Third-Party Services is subject to their terms and privacy policies. We are not responsible for Third-Party Services and disclaim all liability arising from them.</p>
                </section>

                <section>
                    <h2 class="text-2xl font-semibold text-gray-900 mb-4">7. Intellectual Property; Feedback</h2>
                    <div class="space-y-4">
                        <div>
                            <h3 class="text-lg font-medium text-gray-800 mb-2">7.1 Our IP</h3>
                            <p class="text-gray-700">We and our licensors own all right, title, and interest in and to the Service, including software, interfaces, designs, templates, know-how, and documentation. No rights are granted except as expressly stated in these Terms.</p>
                        </div>
                        <div>
                            <h3 class="text-lg font-medium text-gray-800 mb-2">7.2 Feedback</h3>
                            <p class="text-gray-700">If you provide feedback, suggestions, or ideas ("Feedback"), you grant us a perpetual, irrevocable, worldwide, royalty-free license to use the Feedback without restriction.</p>
                        </div>
                    </div>
                </section>

                <section>
                    <h2 class="text-2xl font-semibold text-gray-900 mb-4">8. Beta, Early Access & Free Features</h2>
                    <p class="text-gray-700">We may offer features identified as beta, preview, or early access ("Beta Features"). Beta Features may be unreliable or change at any time, are provided "as is," and are excluded from any warranties or service commitments.</p>
                </section>

                <section>
                    <h2 class="text-2xl font-semibold text-gray-900 mb-4">9. Availability, Support & Maintenance</h2>
                    <div class="space-y-4">
                        <div>
                            <h3 class="text-lg font-medium text-gray-800 mb-2">9.1 Availability</h3>
                            <p class="text-gray-700">We aim to keep the Service available 24/7, excluding planned maintenance and events beyond our reasonable control (see Section 15). We may update or modify the Service at any time.</p>
                        </div>
                        <div>
                            <h3 class="text-lg font-medium text-gray-800 mb-2">9.2 Support</h3>
                            <p class="text-gray-700">We provide reasonable email or in-app support during business hours, excluding public holidays. Support scope may vary by Plan.</p>
                        </div>
                        <div>
                            <h3 class="text-lg font-medium text-gray-800 mb-2">9.3 Maintenance Windows</h3>
                            <p class="text-gray-700">We may suspend access temporarily for maintenance or updates and will endeavor to schedule outside of peak hours and provide notice when feasible.</p>
                        </div>
                    </div>
                </section>

                <section>
                    <h2 class="text-2xl font-semibold text-gray-900 mb-4">10. Suspension & Termination</h2>
                    <div class="space-y-4">
                        <div>
                            <h3 class="text-lg font-medium text-gray-800 mb-2">10.1 Suspension</h3>
                            <p class="text-gray-700">We may suspend or limit the Service if: (a) you breach these Terms (including failure to pay); (b) your use poses a security risk; (c) your use could adversely impact the Service or others; (d) the trial or grace period has ended without payment; or (e) we are required by law.</p>
                        </div>
                        <div>
                            <h3 class="text-lg font-medium text-gray-800 mb-2">10.2 Grace Period & Lock</h3>
                            <p class="text-gray-700">Following trial expiration, your account will be placed in a 7-day grace period (Days 31–37). If no subscription is purchased by Day 38, the account will be locked until payment is made.</p>
                        </div>
                        <div>
                            <h3 class="text-lg font-medium text-gray-800 mb-2">10.3 Termination by You</h3>
                            <p class="text-gray-700">You may terminate at any time by cancelling your subscription. Termination is effective at the end of the current billing period.</p>
                        </div>
                        <div>
                            <h3 class="text-lg font-medium text-gray-800 mb-2">10.4 Termination by Us</h3>
                            <p class="text-gray-700">We may terminate these Terms for cause if you materially breach and fail to cure within 10 days of notice, or for convenience upon 30 days' notice (with a pro rata refund for prepaid, unused fees if we terminate for convenience).</p>
                        </div>
                        <div>
                            <h3 class="text-lg font-medium text-gray-800 mb-2">10.5 Effect of Termination</h3>
                            <p class="text-gray-700">Upon termination or expiration, your right to access the Service ends. We will make Customer Data export available for 30 days following termination of a paid subscription, and for the Trial Retention Period following a trial, after which we may delete or anonymize Customer Data, except as required by law.</p>
                        </div>
                    </div>
                </section>

                <section>
                    <h2 class="text-2xl font-semibold text-gray-900 mb-4">11. Warranties & Disclaimers</h2>
                    <div class="space-y-4">
                        <div>
                            <h3 class="text-lg font-medium text-gray-800 mb-2">11.1 Mutual Warranties</h3>
                            <p class="text-gray-700">Each party represents that it has the right and authority to enter into these Terms.</p>
                        </div>
                        <div>
                            <h3 class="text-lg font-medium text-gray-800 mb-2">11.2 Service Disclaimer</h3>
                            <p class="text-gray-700 font-semibold">THE SERVICE IS PROVIDED "AS IS" AND "AS AVAILABLE," WITHOUT WARRANTIES OF ANY KIND, WHETHER EXPRESS, IMPLIED, STATUTORY, OR OTHERWISE, INCLUDING WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE, AND NON-INFRINGEMENT. WE DO NOT WARRANT THAT THE SERVICE WILL BE UNINTERRUPTED, ERROR-FREE, OR SECURE.</p>
                        </div>
                    </div>
                </section>

                <section>
                    <h2 class="text-2xl font-semibold text-gray-900 mb-4">12. Limitation of Liability</h2>
                    <p class="text-gray-700 font-semibold">TO THE MAXIMUM EXTENT PERMITTED BY LAW: (a) NEITHER PARTY WILL BE LIABLE FOR ANY INDIRECT, INCIDENTAL, SPECIAL, CONSEQUENTIAL, COVER, OR PUNITIVE DAMAGES, OR LOSS OF PROFITS, REVENUE, DATA, OR GOODWILL, EVEN IF ADVISED OF THE POSSIBILITY; AND (b) EACH PARTY'S TOTAL LIABILITY ARISING OUT OF OR RELATED TO THESE TERMS WILL NOT EXCEED THE AMOUNTS PAID OR PAYABLE BY YOU TO US FOR THE SERVICE IN THE TWELVE (12) MONTHS PRECEDING THE EVENT GIVING RISE TO LIABILITY.</p>
                    <p class="text-sm text-gray-600 mt-2">Some jurisdictions do not allow certain limitations, so the above may not apply to you to the extent prohibited by law.</p>
                </section>

                <section>
                    <h2 class="text-2xl font-semibold text-gray-900 mb-4">13. Indemnification</h2>
                    <p class="text-gray-700">You will defend, indemnify, and hold us and our affiliates, officers, directors, employees, and agents harmless from and against any claims, damages, liabilities, costs, and expenses (including reasonable attorneys' fees) arising from: (a) your Customer Data; (b) your use of the Service in violation of these Terms or law; or (c) your infringement or misappropriation of any third-party rights.</p>
                </section>

                <section>
                    <h2 class="text-2xl font-semibold text-gray-900 mb-4">14. Confidentiality</h2>
                    <p class="text-gray-700">Each party will protect the other party's non-public information marked or reasonably understood to be confidential ("Confidential Information"). The receiving party will use Confidential Information solely to perform under these Terms and will protect it using reasonable care. Exceptions apply for information that is public, independently developed, or rightfully received without confidentiality obligations. If compelled by law to disclose, the receiving party will provide notice (if legally permitted) and cooperate to seek protective measures.</p>
                </section>

                <section>
                    <h2 class="text-2xl font-semibold text-gray-900 mb-4">15. Force Majeure</h2>
                    <p class="text-gray-700">Neither party is liable for delays or failures due to events beyond reasonable control, including acts of God, natural disasters, war, terrorism, labor disputes, government actions, internet or utility failures, or third-party service provider outages.</p>
                </section>

                <section>
                    <h2 class="text-2xl font-semibold text-gray-900 mb-4">16. Publicity; Marks</h2>
                    <p class="text-gray-700">With your consent (which may be given by email or in-app), we may identify you as a customer and use your name and logo on our website and marketing materials. You may revoke consent at any time by notifying us.</p>
                </section>

                <section>
                    <h2 class="text-2xl font-semibold text-gray-900 mb-4">17. Modifications to the Terms</h2>
                    <p class="text-gray-700">We may update these Terms from time to time. If we make material changes, we will provide notice through the Service or via email. Changes become effective upon posting or on the stated effective date. If you continue using the Service after the effective date, you accept the revised Terms.</p>
                </section>

                <section>
                    <h2 class="text-2xl font-semibold text-gray-900 mb-4">18. Governing Law; Dispute Resolution</h2>
                    <p class="text-gray-700">These Terms are governed by the laws of Italy, without regard to conflict of laws rules. The parties will submit to the exclusive jurisdiction and venue of the courts located in Italy. The United Nations Convention on Contracts for the International Sale of Goods does not apply.</p>
                    <p class="text-sm text-gray-600 mt-2">If required by law, you may have rights to bring claims in your local courts or under your local consumer protection laws.</p>
                </section>

                <section>
                    <h2 class="text-2xl font-semibold text-gray-900 mb-4">19. Notices</h2>
                    <p class="text-gray-700">Notices must be in writing and will be deemed given when: (a) delivered personally; (b) sent by certified or registered mail; (c) sent by a nationally recognized courier; or (d) sent by email to the addresses on file. Our contact for legal notices: admin@maintainxtra.com.</p>
                </section>

                <section>
                    <h2 class="text-2xl font-semibold text-gray-900 mb-4">20. General</h2>
                    <div class="space-y-4">
                        <div>
                            <h3 class="text-lg font-medium text-gray-800 mb-2">20.1 Assignment</h3>
                            <p class="text-gray-700">You may not assign these Terms without our prior written consent; we may assign to an affiliate or in connection with a merger, acquisition, or sale of assets.</p>
                        </div>
                        <div>
                            <h3 class="text-lg font-medium text-gray-800 mb-2">20.2 Entire Agreement</h3>
                            <p class="text-gray-700">These Terms, together with any order forms, DPA, and policies referenced herein, constitute the entire agreement and supersede prior agreements regarding the Service.</p>
                        </div>
                        <div>
                            <h3 class="text-lg font-medium text-gray-800 mb-2">20.3 Severability; Waiver</h3>
                            <p class="text-gray-700">If any provision is found unenforceable, it will be limited or eliminated to the minimum extent necessary. No waiver of any term is a waiver of any other term.</p>
                        </div>
                        <div>
                            <h3 class="text-lg font-medium text-gray-800 mb-2">20.4 No Third-Party Beneficiaries</h3>
                            <p class="text-gray-700">There are no third-party beneficiaries to these Terms.</p>
                        </div>
                    </div>
                </section>

                <section>
                    <h2 class="text-2xl font-semibold text-gray-900 mb-4">21. Contact</h2>
                    <p class="text-gray-700">Questions about these Terms? Contact us at <a href="mailto:admin@maintainxtra.com" class="text-blue-600 hover:text-blue-800 underline">admin@maintainxtra.com</a></p>
                </section>

                <section>
                    <h2 class="text-2xl font-semibold text-gray-900 mb-4">22. Account Communications & Marketing</h2>
                    <div class="space-y-4">
                        <div>
                            <h3 class="text-lg font-medium text-gray-800 mb-2">22.1 Transactional Reminders</h3>
                            <p class="text-gray-700">We may send account and service reminders related to your trial, grace period, and Trial Retention Period. As guidance, we may send up to three reminders around Day 37, Day 60, and Day 85 after trial expiration. Timing, content, and subject lines may vary.</p>
                        </div>
                        <div>
                            <h3 class="text-lg font-medium text-gray-800 mb-2">22.2 Marketing & CRM</h3>
                            <p class="text-gray-700">With your consent and as permitted by law, after deletion of your account records we may retain or move your email address to our customer relationship platform to send occasional offers, seasonal campaigns, or reactivation incentives. You can withdraw consent or unsubscribe at any time via the link in our emails or by contacting support.</p>
                        </div>
                        <div>
                            <h3 class="text-lg font-medium text-gray-800 mb-2">22.3 Opt-Out</h3>
                            <p class="text-gray-700">You may opt out of non-essential communications at any time. We will continue to send transactional communications necessary to administer your account (e.g., billing receipts, critical service notices).</p>
                        </div>
                    </div>
                </section>

                <section>
                    <h2 class="text-2xl font-semibold text-gray-900 mb-4">Service-Specific Addendum (Property & Vacation Rental Management)</h2>
                    <div class="space-y-4">
                        <div>
                            <h3 class="text-lg font-medium text-gray-800 mb-2">Tenant/Guest Data</h3>
                            <p class="text-gray-700">You are responsible for the accuracy and lawfulness of tenant and guest information entered into the Service and for complying with applicable housing, tenant privacy, and hospitality laws.</p>
                        </div>
                        <div>
                            <h3 class="text-lg font-medium text-gray-800 mb-2">Vendors/Service Providers</h3>
                            <p class="text-gray-700">You are responsible for vetting and managing third-party vendors. The Service facilitates communication and work orders but does not supervise or guarantee vendor performance.</p>
                        </div>
                        <div>
                            <h3 class="text-lg font-medium text-gray-800 mb-2">Compliance</h3>
                            <p class="text-gray-700">You are solely responsible for compliance with local regulations, including data protection, consumer, housing, safety, and record-keeping rules applicable to your operations.</p>
                        </div>
                    </div>
                </section>
            </div>

            <div class="bg-gray-50 border border-gray-200 rounded-lg p-6 mt-8">
                <p class="text-center font-semibold text-gray-900">By using the Service, you acknowledge that you have read and agree to these Terms.</p>
            </div>
        </div>
    </div>
</div>
@endsection

