<?php
session_start();
require_once '../config/database.php';

function hashPassword($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function getUserRole() {
    return $_SESSION['role'] ?? null;
}

function registerUser($name, $email, $password, $role, $country) {
    global $pdo;
    
    if (emailExists($email)) {
        return ['success' => false, 'message' => 'Email already registered'];
    }
    
    $hashed = hashPassword($password);
    $stmt = $pdo->prepare("INSERT INTO users (role, email, password_hash, name, country) VALUES (?, ?, ?, ?, ?)");
    
    if ($stmt->execute([$role, $email, $hashed, $name, $country])) {
        return ['success' => true, 'message' => 'Account created successfully'];
    }
    return ['success' => false, 'message' => 'Registration failed'];
}

function loginUser($email, $password) {
    global $pdo;
    
    $stmt = $pdo->prepare("SELECT id, email, password_hash, role, name FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    
    if ($user && verifyPassword($password, $user['password_hash'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['name'] = $user['name'];
        return ['success' => true, 'message' => 'Login successful'];
    }
    return ['success' => false, 'message' => 'Invalid credentials'];
}

function emailExists($email) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    return $stmt->rowCount() > 0;
}

// Post a new job (for students only)
function postJob($student_id, $title, $description, $subject, $level, $budget, $deadline) {
    global $pdo;
    
    $stmt = $pdo->prepare("INSERT INTO jobs (student_id, title, description, subject, level, budget, deadline) VALUES (?, ?, ?, ?, ?, ?, ?)");
    
    if ($stmt->execute([$student_id, $title, $description, $subject, $level, $budget, $deadline])) {
        return ['success' => true, 'message' => 'Job posted successfully!', 'job_id' => $pdo->lastInsertId()];
    }
    return ['success' => false, 'message' => 'Failed to post job'];
}

// Get all open jobs (for providers to browse)
function getOpenJobs() {
    global $pdo;
    $stmt = $pdo->query("SELECT j.*, u.name as student_name FROM jobs j JOIN users u ON j.student_id = u.id WHERE j.status = 'posted' ORDER BY j.created_at DESC");
    return $stmt->fetchAll();
}

// Get user's jobs (for students)
function getUserJobs($user_id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT j.*, a.provider_id FROM jobs j LEFT JOIN agreements a ON j.id = a.job_id WHERE j.student_id = ? ORDER BY j.created_at DESC");
    $stmt->execute([$user_id]);
    return $stmt->fetchAll();
}

// Get single job details
function getJob($job_id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT j.*, u.name as student_name FROM jobs j JOIN users u ON j.student_id = u.id WHERE j.id = ?");
    $stmt->execute([$job_id]);
    return $stmt->fetch();
}

// Start negotiation for a job (create agreement)
function startNegotiation($job_id, $provider_id, $price, $deadline) {
    global $pdo;
    
    // Check if job exists and is available
    $job = getJob($job_id);
    if (!$job || $job['status'] !== 'posted') {
        return ['success' => false, 'message' => 'Job not available'];
    }
    
    $stmt = $pdo->prepare("UPDATE jobs SET status = 'negotiating' WHERE id = ?");
    $stmt->execute([$job_id]);
    
    $stmt = $pdo->prepare("INSERT INTO agreements (job_id, provider_id, agreed_price, agreed_deadline) VALUES (?, ?, ?, ?)");
    if ($stmt->execute([$job_id, $provider_id, $price, $deadline])) {
        return ['success' => true, 'message' => 'Negotiation started!', 'agreement_id' => $pdo->lastInsertId()];
    }
    return ['success' => false, 'message' => 'Failed to start negotiation'];
}

// Send chat message
function sendMessage($agreement_id, $sender_id, $message) {
    global $pdo;
    $stmt = $pdo->prepare("INSERT INTO messages (agreement_id, sender_id, message) VALUES (?, ?, ?)");
    if ($stmt->execute([$agreement_id, $sender_id, $message])) {
        return ['success' => true, 'message_id' => $pdo->lastInsertId()];
    }
    return ['success' => false];
}

// Get messages for agreement
function getMessages($agreement_id) {
    global $pdo;
    $stmt = $pdo->prepare("
        SELECT m.*, u.name, u.role 
        FROM messages m 
        JOIN users u ON m.sender_id = u.id 
        WHERE m.agreement_id = ? 
        ORDER BY m.created_at ASC
    ");
    $stmt->execute([$agreement_id]);
    return $stmt->fetchAll();
}

// Get agreement details
function getAgreement($agreement_id) {
    global $pdo;
    $stmt = $pdo->prepare("
        SELECT a.*, j.title, j.student_id, j.budget, u.name as provider_name 
        FROM agreements a 
        JOIN jobs j ON a.job_id = j.id 
        JOIN users u ON a.provider_id = u.id 
        WHERE a.id = ?
    ");
    $stmt->execute([$agreement_id]);
    return $stmt->fetch();
}

// Process 50% upfront payment
function processUpfrontPayment($agreement_id, $customer_email) {
    require_once '../config/stripe.php';
    
    try {
        $agreement = getAgreement($agreement_id);
        if (!$agreement) return ['success' => false, 'message' => 'Agreement not found'];
        
        $upfront_amount = $agreement['agreed_price'] * 0.5;
        
        // Create PaymentIntent for 50%
        $payment_intent = \Stripe\PaymentIntent::create([
            'amount' => intval($upfront_amount * 100), // cents
            'currency' => 'usd',
            'customer' => $customer_email,
            'metadata' => [
                'agreement_id' => $agreement_id,
                'type' => 'upfront_50'
            ]
        ]);
        
        // Record payment intent
        global $pdo;
        $stmt = $pdo->prepare("INSERT INTO payments (agreement_id, amount, type, stripe_id, status) VALUES (?, ?, 'upfront', ?, 'pending')");
        $stmt->execute([$agreement_id, $upfront_amount, $payment_intent->id]);
        
        return ['success' => true, 'client_secret' => $payment_intent->client_secret, 'payment_intent' => $payment_intent->id];
        
    } catch (Exception $e) {
        return ['success' => false, 'message' => $e->getMessage()];
    }
}

// Confirm payment success (webhook or frontend)
function confirmUpfrontPayment($payment_intent_id) {
    require_once '../config/stripe.php';
    
    try {
        global $pdo;
        $payment_intent = \Stripe\PaymentIntent::retrieve($payment_intent_id);
        
        if ($payment_intent->status === 'succeeded') {
            $stmt = $pdo->prepare("UPDATE payments SET status = 'held' WHERE stripe_id = ?");
            $stmt->execute([$payment_intent_id]);
            
            // Update job status
            $stmt = $pdo->prepare("UPDATE jobs j JOIN agreements a ON j.id = a.job_id SET j.status = 'accepted' WHERE a.id = (SELECT agreement_id FROM payments WHERE stripe_id = ?)");
            $stmt->execute([$payment_intent_id]);
            
            return ['success' => true];
        }
        return ['success' => false, 'message' => 'Payment not confirmed'];
    } catch (Exception $e) {
        return ['success' => false, 'message' => $e->getMessage()];
    }
}
?>



