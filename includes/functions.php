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
?>
