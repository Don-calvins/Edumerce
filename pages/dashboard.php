<?php
require_once '../includes/functions.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$role = getUserRole();
$userName = $_SESSION['name'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edumerce Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">
    <nav class="bg-white shadow-lg">
        <div class="max-w-6xl mx-auto px-4">
            <div class="flex justify-between items-center py-4">
                <h1 class="text-2xl font-bold text-gray-800">Edumerce</h1>
                <div class="flex items-center space-x-4">
                    <span>Welcome, <?php echo htmlspecialchars($userName); ?>!</span>
                    <span class="text-sm text-gray-600">(<?php echo ucfirst($role); ?>)</span>
                    <a href="logout.php" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg transition duration-200">Logout</a>
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-6xl mx-auto py-8 px-4">
        <h2 class="text-3xl font-bold mb-8">Dashboard</h2>
        
        <?php if ($role === 'student'): ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
  <a href="post-job.php" class="bg-blue-500 hover:bg-blue-600 text-white p-8 rounded-xl text-center transition duration-200">
         <h3 class="text-2xl font-bold mb-2">📝 Post New Job</h3>
         <p>Create assignment, set budget & deadline</p>
       </a>
   <a href="my-jobs.php" class="bg-green-500 hover:bg-green-600 text-white p-8 rounded-xl text-center transition duration-200">
         <h3 class="text-2xl font-bold mb-2">💼 My Jobs</h3>
         <p>View active jobs & hired experts</p>
       </a>

   <a href="#" class="bg-purple-500 hover:bg-purple-600 text-white p-8 rounded-xl text-center transition duration-200">
                    <h3 class="text-2xl font-bold mb-2">⭐ Reviews</h3>
                    <p>Rate completed work</p>
                </a>
            </div>
            
        <?php elseif ($role === 'provider'): ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                
  <a href="jobs.php" class="bg-indigo-500 hover:bg-indigo-600 text-white p-8 rounded-xl text-center transition duration-200">
            <h3 class="text-2xl font-bold mb-2">🔍 Find Jobs</h3>
            <p>Browse student assignments</p>
   </a>
                </a>
                <a href="#" class="bg-yellow-500 hover:bg-yellow-600 text-white p-8 rounded-xl text-center transition duration-200">
                    <h3 class="text-2xl font-bold mb-2">💰 Earnings</h3>
                    <p>Track payments & balance</p>
                </a>
                <a href="#" class="bg-orange-500 hover:bg-orange-600 text-white p-8 rounded-xl text-center transition duration-200">
                    <h3 class="text-2xl font-bold mb-2">📋 Active Jobs</h3>
                    <p>Manage your accepted work</p>
                </a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
