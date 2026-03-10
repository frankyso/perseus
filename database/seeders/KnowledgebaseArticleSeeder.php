<?php

namespace Database\Seeders;

use App\Enums\KnowledgebaseArticleStatus;
use App\Models\KnowledgebaseArticle;
use App\Models\KnowledgebaseCategory;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class KnowledgebaseArticleSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::query()->first();

        if (! $admin) {
            return;
        }

        /** @var array<string, array<int, array{title: string, excerpt: string, body: string}>> $articles */
        $articles = [
            'Getting Started' => [
                [
                    'title' => 'How to Create Your Account',
                    'excerpt' => 'Learn how to sign up and get started with Perseus in just a few minutes.',
                    'body' => '<h2>Creating Your Account</h2><p>Getting started with Perseus is easy. Follow these simple steps to create your account and start using our platform.</p><h3>Step 1: Visit the Registration Page</h3><p>Navigate to our website and click the <strong>"Sign Up"</strong> button in the top right corner.</p><h3>Step 2: Fill in Your Details</h3><p>Enter your name, email address, and create a secure password. Make sure your password is at least 8 characters long.</p><h3>Step 3: Verify Your Email</h3><p>Check your inbox for a verification email and click the confirmation link to activate your account.</p><h3>Step 4: Complete Your Profile</h3><p>Once verified, log in and complete your profile by adding your company information and preferences.</p><p>That\'s it! You\'re all set to start using Perseus.</p>',
                ],
                [
                    'title' => 'Quick Start Guide',
                    'excerpt' => 'A comprehensive guide to help you navigate and use the main features of Perseus.',
                    'body' => '<h2>Welcome to Perseus</h2><p>This quick start guide will walk you through the main features and help you get productive right away.</p><h3>Dashboard Overview</h3><p>Your dashboard is the central hub where you can see all your activities, recent tickets, and important notifications.</p><h3>Navigation</h3><p>Use the sidebar menu to access different sections:</p><ul><li><strong>Dashboard</strong> — Overview of your account activity</li><li><strong>Tickets</strong> — Create and manage support tickets</li><li><strong>Knowledgebase</strong> — Browse help articles and guides</li><li><strong>Settings</strong> — Manage your profile and preferences</li></ul><h3>Getting Help</h3><p>If you can\'t find what you need in our knowledgebase, you can always submit a support ticket and our team will assist you.</p>',
                ],
                [
                    'title' => 'System Requirements',
                    'excerpt' => 'Check the minimum system requirements to ensure compatibility with Perseus.',
                    'body' => '<h2>System Requirements</h2><p>Perseus is a web-based application that runs in your browser. Here are the requirements for the best experience.</p><h3>Supported Browsers</h3><ul><li>Google Chrome (latest 2 versions)</li><li>Mozilla Firefox (latest 2 versions)</li><li>Safari (latest 2 versions)</li><li>Microsoft Edge (latest 2 versions)</li></ul><h3>Internet Connection</h3><p>A stable internet connection with a minimum speed of 1 Mbps is recommended for optimal performance.</p><h3>Screen Resolution</h3><p>We recommend a minimum screen resolution of 1280x720 pixels. The application is fully responsive and works on mobile devices as well.</p>',
                ],
            ],
            'Account & Billing' => [
                [
                    'title' => 'How to Update Your Profile',
                    'excerpt' => 'Step-by-step instructions for updating your personal information and profile settings.',
                    'body' => '<h2>Updating Your Profile</h2><p>Keep your profile information up to date to ensure you receive important communications and have the best experience.</p><h3>Changing Your Name or Email</h3><ol><li>Go to <strong>Settings → Profile</strong></li><li>Update your name or email address</li><li>Click <strong>"Save"</strong> to apply the changes</li></ol><p>If you change your email address, you will need to verify the new email before it takes effect.</p><h3>Changing Your Password</h3><ol><li>Go to <strong>Settings → Password</strong></li><li>Enter your current password</li><li>Enter and confirm your new password</li><li>Click <strong>"Update Password"</strong></li></ol><h3>Two-Factor Authentication</h3><p>We strongly recommend enabling two-factor authentication for added security. Go to <strong>Settings → Two-Factor Authentication</strong> to set it up.</p>',
                ],
                [
                    'title' => 'Understanding Your Billing',
                    'excerpt' => 'Everything you need to know about billing cycles, invoices, and payment methods.',
                    'body' => '<h2>Billing Overview</h2><p>This article covers everything related to your billing and subscription management.</p><h3>Billing Cycle</h3><p>Your billing cycle starts on the date you first subscribed. You will be billed on the same date each month (or year, depending on your plan).</p><h3>Viewing Invoices</h3><p>You can view and download all your past invoices from the billing section in your account settings.</p><h3>Payment Methods</h3><p>We accept the following payment methods:</p><ul><li>Visa, Mastercard, American Express</li><li>Bank transfer (for annual plans)</li><li>PayPal</li></ul><h3>Updating Payment Information</h3><p>To update your payment method, go to your account settings and click on "Payment Methods". You can add a new card or remove an existing one.</p>',
                ],
                [
                    'title' => 'How to Cancel Your Subscription',
                    'excerpt' => 'Learn about the cancellation process and what happens to your data.',
                    'body' => '<h2>Cancelling Your Subscription</h2><p>We\'re sorry to see you go. Here\'s how to cancel your subscription.</p><h3>Steps to Cancel</h3><ol><li>Go to <strong>Settings → Billing</strong></li><li>Click <strong>"Cancel Subscription"</strong></li><li>Select a reason for cancellation (optional)</li><li>Confirm the cancellation</li></ol><h3>What Happens After Cancellation?</h3><ul><li>Your account remains active until the end of your current billing period</li><li>You will not be charged again</li><li>Your data is retained for 30 days after the subscription ends</li><li>You can reactivate your subscription at any time within those 30 days</li></ul><h3>Need Help?</h3><p>If you\'re cancelling due to an issue we can help with, please contact our support team first. We\'d love the chance to make things right.</p>',
                ],
            ],
            'Troubleshooting' => [
                [
                    'title' => 'I Can\'t Log In to My Account',
                    'excerpt' => 'Common login issues and how to resolve them quickly.',
                    'body' => '<h2>Login Troubleshooting</h2><p>Having trouble logging in? Here are the most common issues and their solutions.</p><h3>Forgot Your Password?</h3><p>Click the <strong>"Forgot your password?"</strong> link on the login page. Enter your email address and we\'ll send you a password reset link.</p><h3>Account Locked</h3><p>After 5 failed login attempts, your account is temporarily locked for 15 minutes. Wait and try again, or reset your password.</p><h3>Two-Factor Authentication Issues</h3><p>If you\'ve lost access to your authenticator app:</p><ol><li>Use one of your recovery codes to log in</li><li>Go to Settings → Two-Factor Authentication</li><li>Disable and re-enable 2FA with your new device</li></ol><h3>Browser Issues</h3><ul><li>Clear your browser cookies and cache</li><li>Try using an incognito/private window</li><li>Make sure JavaScript is enabled</li><li>Try a different browser</li></ul><p>If none of these solutions work, please submit a support ticket and we\'ll help you regain access.</p>',
                ],
                [
                    'title' => 'Email Notifications Not Working',
                    'excerpt' => 'Troubleshoot issues with missing or delayed email notifications.',
                    'body' => '<h2>Email Notification Issues</h2><p>If you\'re not receiving email notifications, try these steps.</p><h3>Check Your Spam Folder</h3><p>Our emails sometimes end up in spam or junk folders. Check there first and mark our emails as "Not Spam".</p><h3>Whitelist Our Email</h3><p>Add <strong>notifications@perseus.test</strong> to your email contacts or whitelist to prevent filtering.</p><h3>Verify Your Email Address</h3><p>Make sure your email address is correct and verified in your profile settings.</p><h3>Check Notification Settings</h3><p>Go to <strong>Settings → Notifications</strong> and make sure the relevant notification types are enabled.</p><h3>Still Not Working?</h3><p>If you\'ve tried all the above and still aren\'t receiving emails, please submit a support ticket with the following information:</p><ul><li>Your email address</li><li>Which notifications you\'re missing</li><li>When you last received an email from us</li></ul>',
                ],
                [
                    'title' => 'Page Loading Slowly',
                    'excerpt' => 'Tips to improve page loading speed and resolve performance issues.',
                    'body' => '<h2>Improving Page Load Speed</h2><p>If pages are loading slowly, here are some steps you can take to improve performance.</p><h3>Check Your Internet Connection</h3><p>Run a speed test at <strong>speedtest.net</strong> to verify your connection speed. We recommend at least 1 Mbps for optimal performance.</p><h3>Clear Browser Cache</h3><p>Cached data can sometimes cause issues. Clear your browser cache and reload the page.</p><h3>Disable Browser Extensions</h3><p>Some browser extensions can interfere with page loading. Try disabling them temporarily to see if performance improves.</p><h3>Try a Different Browser</h3><p>If the issue persists, try accessing Perseus from a different browser to rule out browser-specific problems.</p><h3>Check System Status</h3><p>Occasionally, performance issues may be on our end. Check our status page for any ongoing incidents.</p>',
                ],
            ],
            'FAQs' => [
                [
                    'title' => 'What is Perseus?',
                    'excerpt' => 'An overview of Perseus and what it can do for you and your team.',
                    'body' => '<h2>About Perseus</h2><p>Perseus is a modern support ticket and knowledgebase platform designed to help businesses provide exceptional customer support.</p><h3>Key Features</h3><ul><li><strong>Support Tickets</strong> — Create, track, and manage support requests with ease</li><li><strong>Knowledgebase</strong> — Build a comprehensive self-service help center for your customers</li><li><strong>Department Routing</strong> — Automatically route tickets to the right team</li><li><strong>File Attachments</strong> — Share screenshots and documents with your support requests</li><li><strong>Role-Based Access</strong> — Manage your team with different permission levels</li></ul><h3>Who is Perseus For?</h3><p>Perseus is designed for businesses of all sizes that want to streamline their customer support operations and empower customers to find answers on their own.</p>',
                ],
                [
                    'title' => 'How Do I Submit a Support Ticket?',
                    'excerpt' => 'Learn how to create a support ticket and get help from our team.',
                    'body' => '<h2>Submitting a Support Ticket</h2><p>When you can\'t find the answer in our knowledgebase, you can submit a support ticket to get help from our team.</p><h3>Steps to Submit a Ticket</h3><ol><li>Log in to your account</li><li>Click <strong>"Tickets"</strong> in the sidebar navigation</li><li>Click <strong>"Create Ticket"</strong></li><li>Fill in the required information:<ul><li><strong>Subject</strong> — A brief description of your issue</li><li><strong>Description</strong> — Detailed explanation of the problem</li><li><strong>Department</strong> — Select the relevant department</li><li><strong>Priority</strong> — Choose the urgency level</li></ul></li><li>Attach any relevant files (screenshots, documents)</li><li>Click <strong>"Submit"</strong></li></ol><h3>What Happens Next?</h3><p>Our team will review your ticket and respond as soon as possible. You\'ll receive an email notification when there\'s an update on your ticket.</p><h3>Response Times</h3><ul><li><strong>Urgent</strong> — Within 1 hour</li><li><strong>High</strong> — Within 4 hours</li><li><strong>Medium</strong> — Within 24 hours</li><li><strong>Low</strong> — Within 48 hours</li></ul>',
                ],
                [
                    'title' => 'Is My Data Secure?',
                    'excerpt' => 'Learn about our security measures and how we protect your data.',
                    'body' => '<h2>Data Security</h2><p>We take the security of your data very seriously. Here\'s how we protect your information.</p><h3>Encryption</h3><p>All data transmitted between your browser and our servers is encrypted using TLS 1.3. Your passwords are hashed using bcrypt with a cost factor of 12.</p><h3>Two-Factor Authentication</h3><p>We offer TOTP-based two-factor authentication to add an extra layer of security to your account.</p><h3>Access Controls</h3><p>We implement role-based access controls to ensure that team members only have access to the data they need.</p><h3>Data Backups</h3><p>Your data is backed up daily and stored securely. We retain backups for 30 days.</p><h3>Compliance</h3><p>We are committed to maintaining the highest standards of data protection and regularly review our security practices.</p><h3>Reporting Security Issues</h3><p>If you discover a security vulnerability, please report it to our security team immediately by submitting a support ticket with "Security" priority.</p>',
                ],
            ],
        ];

        foreach ($articles as $categoryName => $categoryArticles) {
            $category = KnowledgebaseCategory::query()->where('name', $categoryName)->first();

            if (! $category) {
                continue;
            }

            foreach ($categoryArticles as $index => $articleData) {
                KnowledgebaseArticle::query()->updateOrCreate(
                    ['slug' => Str::slug($articleData['title'])],
                    [
                        'title' => $articleData['title'],
                        'slug' => Str::slug($articleData['title']),
                        'excerpt' => $articleData['excerpt'],
                        'body' => $articleData['body'],
                        'knowledgebase_category_id' => $category->id,
                        'author_id' => $admin->id,
                        'status' => KnowledgebaseArticleStatus::Published,
                        'sort_order' => $index,
                        'published_at' => now()->subDays(rand(1, 30)),
                    ]
                );
            }
        }
    }
}
