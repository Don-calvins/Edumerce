<?php
require_once '../includes/functions.php';

if (!isLoggedIn() || getUserRole() !== 'student') {
    header('Location: login.php');
    exit;
}

$agreement_id = $_GET['agreement'] ?? 0;
$agreement = getAgreement($agreement_id);

if (!$agreement) {
    $_SESSION['error'] = 'Payment agreement not found';
    header('Location: my-jobs.php');
    exit;
}

// Process payment
if ($_POST) {
    $result = processUpfrontPayment($agreement_id, $_SESSION['email']);
    
    if ($result['success']) {
        $_SESSION['payment_intent'] = $result['payment_intent'];
        $_SESSION['success'] = 'Payment processing...';
    } else {
        $error = $result['message'];
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Secure Payment - Edumerce</title>
    <script src="https://js.stripe.com/v3/"></script>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen">
    <nav class="bg-white shadow-lg">
        <div class="max-w-4xl mx-auto px-4">
            <div class="flex justify-between items-center py-4">
                <a href="dashboard.php" class="text-2xl font-bold">Edumerce</a>
                <a href="my-jobs.php" class="bg-gray-500 text-white px-6 py-2 rounded-xl hover:bg-gray-600">← Back to Jobs</a>
            </div>
        </div>
    </nav>

    <div class="max-w-2xl mx-auto py-12 px-4">
        <div class="bg-white rounded-3xl shadow-2xl p-12 text-center">
            <div class="w-24 h-24 bg-green-100 rounded-3xl flex items-center justify-center mx-auto mb-8">
                <span class="text-4xl">💳</span>
            </div>
            
            <h1 class="text-4xl font-bold text-gray-800 mb-4">Secure Payment Required</h1>
            <p class="text-xl text-gray-600 mb-2">50% upfront to start work on:</p>
            <h2 class="text-2xl font-bold text-blue-600 mb-8"><?php echo htmlspecialchars($agreement['title']); ?></h2>
            
            <div class="bg-gradient-to-r from-green-50 to-blue-50 border-4 border-green-100 p-8 rounded-2xl mb-12">
                <div class="text-4xl font-bold text-green-600 mb-4">$<?php echo number_format($agreement['agreed_price'] * 0.5, 2); ?></div>
                <p class="text-lg text-green-800">Upfront Payment (50%)</p>
                <p class="text-sm text-green-700 mt-2">✅ Remaining 50% due after review & download</p>
            </div>

            <?php if (isset($error)): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-6 py-4 rounded-2xl mb-8 text-left">
                    ❌ <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <form id="payment-form" method="POST">
                <div id="card-element" class="bg-gray-50 border-2 border-dashed border-gray-300 rounded-2xl p-6 mb-8 h-48 flex items-center justify-center text-gray-500">
                    Enter card details...
                </div>
                
                <button id="submit" type="submit" class="w-full bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white font-bold py-6 px-12 rounded-2xl text-xl shadow-2xl hover:shadow-3xl transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed">
                    <span id="button-text">🔒 Pay $<?php echo number_format($agreement['agreed_price'] * 0.5, 2); ?> Now</span>
                    <div id="spinner" class="hidden">Processing...</div>
                </button>
                <p class="text-xs text-gray-500 mt-4">Secure 256-bit SSL • Powered by Stripe</p>
            </form>
        </div>
    </div>

    <script>
    // Initialize Stripe.js
    const stripe = Stripe('<?php echo STRIPE_PUBLISHABLE_KEY; ?>');
    const elements = stripe.elements();
    
    // Card element
    const cardElement = elements.create('card', {
        style: {
            base: {
                fontSize: '18px',
                color: '#424770',
                '::placeholder': { color: '#aab7c4' }
            }
        }
    });
    cardElement.mount('#card-element');

    const form = document.getElementById('payment-form');
    form.addEventListener('submit', async (event) => {
        event.preventDefault();
        
        const submitBtn = document.getElementById('submit');
        const buttonText = document.getElementById('button-text');
        const spinner = document.getElementById('spinner');
        
        submitBtn.disabled = true;
        buttonText.style.display = 'none';
        spinner.classList.remove('hidden');

        // Confirm card payment
        const { error, paymentIntent } = await stripe.confirmCardPayment('<?php echo $result['client_secret'] ?? ''; ?>', {
            payment_method: {
                card: cardElement,
                billing_details: {
                    email: '<?php echo $_SESSION['email']; ?>'
                }
            }
        });

        if (error) {
            // Display error
            document.getElementById('card-element').textContent = error.message;
            submitBtn.disabled = false;
            buttonText.style.display = 'block';
            spinner.classList.add('hidden');
        } else {
            // Success - redirect
            window.location.href = 'payment-success.php?session_id=' + paymentIntent.id;
        }
    });
    </script>
</body>
</html>
