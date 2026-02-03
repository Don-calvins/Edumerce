<?php
require_once '../includes/functions.php';

if ($_POST) {
    $result = registerUser(
        $_POST['name'], 
        $_POST['email'], 
        $_POST['password'], 
        $_POST['role'], 
        $_POST['country']
    );
    
    if ($result['success']) {
        $_SESSION['success'] = $result['message'];
        header('Location: login.php');
        exit;
    } else {
        $error = $result['message'];
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edumerce - Register</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="max-w-md mx-auto mt-20 p-8 bg-white rounded-lg shadow-lg">
        <h1 class="text-3xl font-bold text-center mb-8">Join Edumerce</h1>
        
        <?php if (isset($error)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Full Name</label>
                <input type="text" name="name" required class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Email</label>
                <input type="email" name="email" required class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Password</label>
                <input type="password" name="password" required class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            
           <div class="mb-4">
    <label class="block text-gray-700 text-sm font-bold mb-2">Role</label>
    <select name="role" required class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
        <option value="">Select Role</option>
        <option value="student" <?php echo ($_GET['role'] ?? '') === 'student' ? 'selected' : ''; ?>>Student (Post Jobs)</option>
        <option value="provider" <?php echo ($_GET['role'] ?? '') === 'provider' ? 'selected' : ''; ?>>Tutor/Expert (Do Jobs)</option>
    </select>
</div>

            
            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-bold mb-2">Country</label>
                <input type="text" name="country" required class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            
            <button type="submit" class="w-full bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-lg transition duration-200">
                Create Account
            </button>
        </form>
        
        <p class="text-center mt-6">
            Already have an account? <a href="login.php" class="text-blue-500 hover:underline">Login here</a>
        </p>
    </div>
</body>
</html>
