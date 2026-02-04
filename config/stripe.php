<?php
// Test keys - replace with your live keys later
define('STRIPE_PUBLISHABLE_KEY', 'pk_test_YOUR_PUBLISHABLE_KEY_HERE');
define('STRIPE_SECRET_KEY', 'sk_test_YOUR_SECRET_KEY_HERE');

require_once __DIR__ . '/../vendor/autoload.php'; // Install via Composer below

\Stripe\Stripe::setApiKey(STRIPE_SECRET_KEY);
?>
