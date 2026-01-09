<?php
if (!defined('NEXORA_CONFIG')) {
    define('NEXORA_CONFIG', true);
}

define('COMPANY_NAME', 'Nexora');
define('COMPANY_TAGLINE', 'Empowering Your Business with Smart Software Solutions');
define('COMPANY_DESCRIPTION', 'We specialize in delivering cutting-edge software solutions designed to streamline your business operations.');

define('CONTACT_EMAIL', 'nexorait@outlook.com');
define('CONTACT_PHONE_1', '+94 77 635 0902');
define('CONTACT_PHONE_2', '+94 70 671 7131');
define('CONTACT_WHATSAPP', '+94706717131');
define('CONTACT_ADDRESS', '218 Doalakanda, Dehiaththakandiya, Sri Lanka');

define('SOCIAL_FACEBOOK', '#');
define('SOCIAL_TWITTER', '#');
define('SOCIAL_LINKEDIN', '#');
define('SOCIAL_INSTAGRAM', '#');

define('MAIL_FROM_EMAIL', 'noreply@nexora.com');
define('MAIL_FROM_NAME', 'Nexora Website');
define('MAIL_RECIPIENT', 'nexorait@outlook.com');

define('SITE_URL', 'https://www.nexora.com'); 
define('TIMEZONE', 'Asia/Colombo');

define('BUSINESS_HOURS', '9:00 AM - 6:00 PM');
define('BUSINESS_DAYS', 'Monday - Saturday');

date_default_timezone_set(TIMEZONE);
$stats = [
    'clients' => 500,
    'projects' => 1000,
    'team_members' => 50,
    'satisfaction' => 99
];

$services = [
    [
        'icon' => '⚙️',
        'title' => 'Business Automation',
        'description' => 'Transform your workflows with intelligent automation that reduces manual tasks, minimizes errors, and frees your team to focus on what matters most.'
    ],
    [
        'icon' => '📊',
        'title' => 'Analytics & Insights',
        'description' => 'Harness the power of data with advanced analytics dashboards that provide real-time insights and help you make confident, data-driven decisions.'
    ],
    [
        'icon' => '☁️',
        'title' => 'Cloud Solutions',
        'description' => 'Access your business tools from anywhere with secure, scalable cloud infrastructure that grows with your business needs.'
    ],
    [
        'icon' => '🔒',
        'title' => 'Security & Compliance',
        'description' => 'Protect your business with enterprise-grade security measures and ensure compliance with industry standards and regulations.'
    ],
    [
        'icon' => '🎯',
        'title' => 'Custom Development',
        'description' => 'Get tailored software solutions designed specifically for your unique business requirements and challenges.'
    ],
    [
        'icon' => '💬',
        'title' => '24/7 Support',
        'description' => 'Count on our dedicated support team to be there whenever you need assistance, ensuring your business runs smoothly around the clock.'
    ]
];


$testimonials = [
    [
        'content' => 'Nexora transformed our business operations completely. The automation tools saved us countless hours, and the POS system is incredibly intuitive. Our revenue has increased by 40% since implementation.',
        'author' => 'Sarah Mitchell',
        'role' => 'CEO, Retail Solutions Inc.'
    ],
    [
        'content' => 'The team at Nexora delivered exactly what we needed. Their POS system is fast, reliable, and the customer support is outstanding. Highly recommended!',
        'author' => 'John Anderson',
        'role' => 'Owner, Tech Store'
    ],
    [
        'content' => 'Working with Nexora has been a game-changer for our business. The cloud solutions they provided have given us the flexibility we needed to grow.',
        'author' => 'Emily Chen',
        'role' => 'Director, E-commerce Co.'
    ]
];

$meta = [
    'description' => 'Nexora provides innovative software solutions for businesses. From POS systems to cloud solutions, we empower your business with smart technology.',
    'keywords' => 'business software, POS systems, cloud solutions, business automation, Sri Lanka, Dehiaththakandiya',
    'author' => 'Nexora',
    'og_image' => SITE_URL . '/images/og-image.jpg', 
    'og_type' => 'website'
];

return [
    'company' => [
        'name' => COMPANY_NAME,
        'tagline' => COMPANY_TAGLINE,
        'description' => COMPANY_DESCRIPTION
    ],
    'contact' => [
        'email' => CONTACT_EMAIL,
        'phone1' => CONTACT_PHONE_1,
        'phone2' => CONTACT_PHONE_2,
        'whatsapp' => CONTACT_WHATSAPP,
        'address' => CONTACT_ADDRESS
    ],
    'social' => [
        'facebook' => SOCIAL_FACEBOOK,
        'twitter' => SOCIAL_TWITTER,
        'linkedin' => SOCIAL_LINKEDIN,
        'instagram' => SOCIAL_INSTAGRAM
    ],
    'stats' => $stats,
    'services' => $services,
    'testimonials' => $testimonials,
    'meta' => $meta
];
?>