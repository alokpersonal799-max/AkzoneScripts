<script>
function pageEditor() {
    return {
        contentType: '{{ old("content_type", $page->content_type ?? "text") }}',
        layout: '{{ old("layout", $page->layout ?? "card") }}',
        selectedTemplate: '',

        applyTemplate() {
            if (!this.selectedTemplate) return;
            const templates = this.getTemplates();
            const tpl = templates[this.selectedTemplate];
            if (tpl) {
                this.$refs.contentArea.value = tpl.content;
                this.$refs.titleInput.value = tpl.title;
                this.contentType = 'html';
            }
        },

        openPreview() {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route("admin.pages.preview") }}';
            form.target = '_blank';

            const token = document.createElement('input');
            token.type = 'hidden';
            token.name = '_token';
            token.value = '{{ csrf_token() }}';
            form.appendChild(token);

            const title = document.createElement('input');
            title.type = 'hidden';
            title.name = 'title';
            title.value = this.$refs.titleInput.value;
            form.appendChild(title);

            const content = document.createElement('input');
            content.type = 'hidden';
            content.name = 'content';
            content.value = this.$refs.contentArea.value;
            form.appendChild(content);

            const ctype = document.createElement('input');
            ctype.type = 'hidden';
            ctype.name = 'content_type';
            ctype.value = this.contentType;
            form.appendChild(ctype);

            const lay = document.createElement('input');
            lay.type = 'hidden';
            lay.name = 'layout';
            lay.value = this.layout;
            form.appendChild(lay);

            document.body.appendChild(form);
            form.submit();
            document.body.removeChild(form);
        },

        getTemplates() {
            return {
                privacy: {
                    title: 'Privacy Policy',
                    content: this.privacyTemplate()
                },
                refund: {
                    title: 'Refund Policy',
                    content: this.refundTemplate()
                },
                terms: {
                    title: 'Terms of Service',
                    content: this.termsTemplate()
                },
                about: {
                    title: 'About Us',
                    content: this.aboutTemplate()
                },
                faq: {
                    title: 'FAQ',
                    content: this.faqTemplate()
                },
                contact: {
                    title: 'Contact Info',
                    content: this.contactTemplate()
                }
            };
        },

        privacyTemplate() {
            return `<div class="space-y-8">
    <section>
        <h2 class="text-2xl font-bold text-gray-900 mb-4">Privacy Policy</h2>
        <p class="text-gray-600 mb-4">Last updated: <strong>${new Date().toLocaleDateString('en-US', {year:'numeric',month:'long',day:'numeric'})}</strong></p>
        <p class="text-gray-600">Your privacy is important to us. This Privacy Policy explains how we collect, use, disclose, and safeguard your information when you visit our website and use our services.</p>
    </section>

    <section>
        <h3 class="text-xl font-semibold text-gray-800 mb-3">Information We Collect</h3>
        <div class="space-y-3 text-gray-600">
            <p><strong>Personal Data:</strong> We may collect personally identifiable information such as your name, email address, and payment information when you register or make a purchase.</p>
            <p><strong>Usage Data:</strong> We automatically collect information about how you interact with our site, including your IP address, browser type, pages visited, and time spent.</p>
            <p><strong>Cookies:</strong> We use cookies and similar tracking technologies to track activity on our service and store certain information.</p>
        </div>
    </section>

    <section>
        <h3 class="text-xl font-semibold text-gray-800 mb-3">How We Use Your Information</h3>
        <ul class="list-disc list-inside space-y-2 text-gray-600">
            <li>To provide and maintain our service</li>
            <li>To process your transactions and send related information</li>
            <li>To send you updates, marketing communications, and promotional offers</li>
            <li>To detect, prevent, and address technical issues</li>
            <li>To improve our website and customer experience</li>
        </ul>
    </section>

    <section>
        <h3 class="text-xl font-semibold text-gray-800 mb-3">Data Security</h3>
        <p class="text-gray-600">We implement appropriate security measures to protect your personal information. However, no method of transmission over the Internet is 100% secure, and we cannot guarantee absolute security.</p>
    </section>

    <section>
        <h3 class="text-xl font-semibold text-gray-800 mb-3">Your Rights</h3>
        <p class="text-gray-600 mb-2">You have the right to:</p>
        <ul class="list-disc list-inside space-y-2 text-gray-600">
            <li>Access and receive a copy of your personal data</li>
            <li>Rectify or update your personal information</li>
            <li>Request deletion of your personal data</li>
            <li>Object to processing of your personal data</li>
            <li>Withdraw consent at any time</li>
        </ul>
    </section>

    <section>
        <h3 class="text-xl font-semibold text-gray-800 mb-3">Contact Us</h3>
        <p class="text-gray-600">If you have any questions about this Privacy Policy, please contact us at <a href="mailto:support@yourstore.com" class="text-blue-600 hover:underline">support@yourstore.com</a>.</p>
    </section>
</div>`;
        },

        refundTemplate() {
            return `<div class="space-y-8">
    <section>
        <h2 class="text-2xl font-bold text-gray-900 mb-4">Refund Policy</h2>
        <p class="text-gray-600 mb-4">We want you to be completely satisfied with your purchase. Please read our refund policy carefully before making a purchase.</p>
    </section>

    <section>
        <h3 class="text-xl font-semibold text-gray-800 mb-3">Digital Products</h3>
        <p class="text-gray-600 mb-3">Due to the nature of digital products, all sales are generally considered final once the product has been downloaded or accessed. However, we offer refunds in the following cases:</p>
        <ul class="list-disc list-inside space-y-2 text-gray-600">
            <li><strong>Defective Product:</strong> If the product is significantly different from its description or is non-functional</li>
            <li><strong>Duplicate Purchase:</strong> If you accidentally purchased the same product twice</li>
            <li><strong>Technical Issues:</strong> If you are unable to download or access the product after our support team has attempted to resolve the issue</li>
        </ul>
    </section>

    <section>
        <h3 class="text-xl font-semibold text-gray-800 mb-3">Refund Request Timeline</h3>
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
            <p class="text-gray-700"><strong>You must request a refund within 7 days</strong> of the original purchase date. Requests made after this period will not be eligible for a refund.</p>
        </div>
    </section>

    <section>
        <h3 class="text-xl font-semibold text-gray-800 mb-3">How to Request a Refund</h3>
        <ol class="list-decimal list-inside space-y-2 text-gray-600">
            <li>Contact our support team with your order number</li>
            <li>Provide a detailed explanation of why you are requesting a refund</li>
            <li>Allow up to 3-5 business days for our team to review your request</li>
            <li>If approved, the refund will be processed to your original payment method</li>
        </ol>
    </section>

    <section>
        <h3 class="text-xl font-semibold text-gray-800 mb-3">Non-Refundable Situations</h3>
        <ul class="list-disc list-inside space-y-2 text-gray-600">
            <li>Change of mind after downloading the product</li>
            <li>Failure to read the product description before purchasing</li>
            <li>Incompatibility with software not listed in requirements</li>
            <li>Requests made after the 7-day refund window</li>
        </ul>
    </section>

    <section>
        <h3 class="text-xl font-semibold text-gray-800 mb-3">Contact Us</h3>
        <p class="text-gray-600">For refund requests or questions, please reach out to <a href="mailto:support@yourstore.com" class="text-blue-600 hover:underline">support@yourstore.com</a>.</p>
    </section>
</div>`;
        },

        termsTemplate() {
            return `<div class="space-y-8">
    <section>
        <h2 class="text-2xl font-bold text-gray-900 mb-4">Terms of Service</h2>
        <p class="text-gray-600 mb-4">Last updated: <strong>${new Date().toLocaleDateString('en-US', {year:'numeric',month:'long',day:'numeric'})}</strong></p>
        <p class="text-gray-600">By accessing and using this website, you accept and agree to be bound by the terms and provision of this agreement.</p>
    </section>

    <section>
        <h3 class="text-xl font-semibold text-gray-800 mb-3">1. Acceptance of Terms</h3>
        <p class="text-gray-600">By creating an account or making a purchase, you acknowledge that you have read, understood, and agree to be bound by these Terms of Service and our Privacy Policy.</p>
    </section>

    <section>
        <h3 class="text-xl font-semibold text-gray-800 mb-3">2. User Accounts</h3>
        <ul class="list-disc list-inside space-y-2 text-gray-600">
            <li>You must provide accurate and complete registration information</li>
            <li>You are responsible for maintaining the confidentiality of your account</li>
            <li>You must be at least 18 years old to create an account</li>
            <li>One person may not maintain more than one account</li>
        </ul>
    </section>

    <section>
        <h3 class="text-xl font-semibold text-gray-800 mb-3">3. Purchases and Payments</h3>
        <p class="text-gray-600 mb-2">When making a purchase:</p>
        <ul class="list-disc list-inside space-y-2 text-gray-600">
            <li>All prices are displayed in the currency shown and include applicable taxes</li>
            <li>Payment must be completed before product access is granted</li>
            <li>You agree to provide current, complete, and accurate billing information</li>
        </ul>
    </section>

    <section>
        <h3 class="text-xl font-semibold text-gray-800 mb-3">4. Product License</h3>
        <p class="text-gray-600">Upon purchase, you are granted a non-exclusive, non-transferable license to use the digital product for personal or commercial use as specified in the product description. You may not redistribute, resell, or share purchased products.</p>
    </section>

    <section>
        <h3 class="text-xl font-semibold text-gray-800 mb-3">5. Intellectual Property</h3>
        <p class="text-gray-600">All content on this website, including text, graphics, logos, and digital products, is the property of our company or its content suppliers and is protected by intellectual property laws.</p>
    </section>

    <section>
        <h3 class="text-xl font-semibold text-gray-800 mb-3">6. Limitation of Liability</h3>
        <p class="text-gray-600">We shall not be liable for any indirect, incidental, special, consequential, or punitive damages resulting from your use of our services or products.</p>
    </section>

    <section>
        <h3 class="text-xl font-semibold text-gray-800 mb-3">7. Changes to Terms</h3>
        <p class="text-gray-600">We reserve the right to modify these terms at any time. Changes will be effective immediately upon posting. Your continued use of the service constitutes acceptance of the modified terms.</p>
    </section>
</div>`;
        },

        aboutTemplate() {
            return `<div class="space-y-8">
    <section class="text-center pb-8 border-b border-gray-100">
        <h2 class="text-3xl font-bold text-gray-900 mb-4">About Us</h2>
        <p class="text-lg text-gray-600 max-w-2xl mx-auto">We are passionate about delivering high-quality digital products that help you achieve your goals. Our mission is to make premium digital resources accessible to everyone.</p>
    </section>

    <section class="grid md:grid-cols-3 gap-6 py-8">
        <div class="text-center p-6 rounded-xl bg-blue-50">
            <div class="text-3xl font-bold text-blue-600 mb-2">500+</div>
            <p class="text-gray-600 font-medium">Products Available</p>
        </div>
        <div class="text-center p-6 rounded-xl bg-green-50">
            <div class="text-3xl font-bold text-green-600 mb-2">10,000+</div>
            <p class="text-gray-600 font-medium">Happy Customers</p>
        </div>
        <div class="text-center p-6 rounded-xl bg-purple-50">
            <div class="text-3xl font-bold text-purple-600 mb-2">4.9/5</div>
            <p class="text-gray-600 font-medium">Average Rating</p>
        </div>
    </section>

    <section>
        <h3 class="text-xl font-semibold text-gray-800 mb-4">Our Story</h3>
        <div class="space-y-4 text-gray-600">
            <p>Founded with a simple idea: premium digital products should not cost a fortune. We started as a small team of creators who believed in quality over quantity.</p>
            <p>Today, we continue to curate and create the best digital resources for professionals, businesses, and creatives worldwide. Every product in our store is carefully reviewed to ensure it meets our high standards.</p>
        </div>
    </section>

    <section>
        <h3 class="text-xl font-semibold text-gray-800 mb-4">Why Choose Us?</h3>
        <div class="grid md:grid-cols-2 gap-4">
            <div class="flex items-start gap-3 p-4 rounded-lg border border-gray-100">
                <span class="text-green-500 text-xl">&#10003;</span>
                <div>
                    <h4 class="font-medium text-gray-800">Quality Guaranteed</h4>
                    <p class="text-sm text-gray-500">Every product is tested and verified before listing</p>
                </div>
            </div>
            <div class="flex items-start gap-3 p-4 rounded-lg border border-gray-100">
                <span class="text-green-500 text-xl">&#10003;</span>
                <div>
                    <h4 class="font-medium text-gray-800">Instant Delivery</h4>
                    <p class="text-sm text-gray-500">Download immediately after purchase</p>
                </div>
            </div>
            <div class="flex items-start gap-3 p-4 rounded-lg border border-gray-100">
                <span class="text-green-500 text-xl">&#10003;</span>
                <div>
                    <h4 class="font-medium text-gray-800">Dedicated Support</h4>
                    <p class="text-sm text-gray-500">Friendly support team ready to help</p>
                </div>
            </div>
            <div class="flex items-start gap-3 p-4 rounded-lg border border-gray-100">
                <span class="text-green-500 text-xl">&#10003;</span>
                <div>
                    <h4 class="font-medium text-gray-800">Regular Updates</h4>
                    <p class="text-sm text-gray-500">Products are updated to stay current</p>
                </div>
            </div>
        </div>
    </section>
</div>`;
        },

        faqTemplate() {
            return `<div class="space-y-8">
    <section>
        <h2 class="text-2xl font-bold text-gray-900 mb-4">Frequently Asked Questions</h2>
        <p class="text-gray-600">Find answers to the most common questions about our products and services.</p>
    </section>

    <section class="space-y-4">
        <div class="border border-gray-200 rounded-lg p-5">
            <h3 class="text-lg font-semibold text-gray-800 mb-2">How do I download my purchased products?</h3>
            <p class="text-gray-600">After completing your purchase, you can download your products from the Dashboard > My Purchases section. Download links are available immediately after payment confirmation.</p>
        </div>

        <div class="border border-gray-200 rounded-lg p-5">
            <h3 class="text-lg font-semibold text-gray-800 mb-2">What payment methods do you accept?</h3>
            <p class="text-gray-600">We accept various payment methods including credit/debit cards, PayPal, and bank transfers. Available methods are shown during checkout based on your location.</p>
        </div>

        <div class="border border-gray-200 rounded-lg p-5">
            <h3 class="text-lg font-semibold text-gray-800 mb-2">Can I get a refund?</h3>
            <p class="text-gray-600">Yes, we offer refunds within 7 days of purchase for eligible cases. Please review our Refund Policy for full details on eligible situations and the refund process.</p>
        </div>

        <div class="border border-gray-200 rounded-lg p-5">
            <h3 class="text-lg font-semibold text-gray-800 mb-2">Do I get free updates?</h3>
            <p class="text-gray-600">Yes! When you purchase a product, you receive all future updates for free. You will be notified by email when updates are available.</p>
        </div>

        <div class="border border-gray-200 rounded-lg p-5">
            <h3 class="text-lg font-semibold text-gray-800 mb-2">Can I use products for commercial projects?</h3>
            <p class="text-gray-600">This depends on the specific product license. Each product listing includes license details. Most products include a commercial use license, but please check the product description before purchasing.</p>
        </div>

        <div class="border border-gray-200 rounded-lg p-5">
            <h3 class="text-lg font-semibold text-gray-800 mb-2">How do I contact support?</h3>
            <p class="text-gray-600">You can reach our support team through the Contact page, or by opening a support ticket from your dashboard. We typically respond within 24 hours.</p>
        </div>
    </section>
</div>`;
        },

        contactTemplate() {
            return `<div class="space-y-8">
    <section class="text-center pb-6">
        <h2 class="text-2xl font-bold text-gray-900 mb-4">Contact Us</h2>
        <p class="text-gray-600 max-w-xl mx-auto">We would love to hear from you. Reach out through any of the channels below and we will get back to you as soon as possible.</p>
    </section>

    <section class="grid md:grid-cols-3 gap-6">
        <div class="text-center p-6 rounded-xl bg-gray-50 border border-gray-100">
            <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-6 h-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
            </div>
            <h3 class="font-semibold text-gray-800 mb-1">Email</h3>
            <a href="mailto:support@yourstore.com" class="text-blue-600 hover:underline">support@yourstore.com</a>
        </div>

        <div class="text-center p-6 rounded-xl bg-gray-50 border border-gray-100">
            <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-6 h-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <h3 class="font-semibold text-gray-800 mb-1">Business Hours</h3>
            <p class="text-gray-600 text-sm">Mon - Fri: 9:00 AM - 6:00 PM</p>
        </div>

        <div class="text-center p-6 rounded-xl bg-gray-50 border border-gray-100">
            <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-6 h-6 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            </div>
            <h3 class="font-semibold text-gray-800 mb-1">Location</h3>
            <p class="text-gray-600 text-sm">Your City, Country</p>
        </div>
    </section>

    <section class="bg-blue-50 rounded-xl p-6 text-center">
        <h3 class="text-lg font-semibold text-gray-800 mb-2">Need Quick Help?</h3>
        <p class="text-gray-600 mb-4">Check our FAQ section for instant answers to common questions, or open a support ticket for personalized assistance.</p>
        <div class="flex justify-center gap-3">
            <a href="/p/faq" class="inline-flex items-center px-4 py-2 bg-white border border-gray-200 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition">View FAQ</a>
            <a href="/contact" class="inline-flex items-center px-4 py-2 bg-blue-600 rounded-lg text-sm font-medium text-white hover:bg-blue-700 transition">Contact Form</a>
        </div>
    </section>
</div>`;
        }
    };
}
</script>
