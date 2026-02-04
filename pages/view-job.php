<?php
require_once '../includes/functions.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$job_id = $_GET['id'] ?? 0;
$job = getJob($job_id);

if (!$job) {
    $_SESSION['error'] = 'Job not found';
    header('Location: jobs.php');
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title><?php echo htmlspecialchars($job['title']); ?> - Edumerce</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">
    <nav class="bg-white shadow-lg">
        <div class="max-w-6xl mx-auto px-4">
            <div class="flex justify-between items-center py-4">
                <a href="dashboard.php" class="text-2xl font-bold">Edumerce</a>
                <a href="jobs.php" class="bg-gray-500 text-white px-6 py-2 rounded-xl hover:bg-gray-600">← Back to Jobs</a>
            </div>
        </div>
    </nav>

    <div class="max-w-4xl mx-auto py-12 px-4">
        <div class="bg-white rounded-2xl shadow-2xl p-12 mb-8">
            <div class="flex items-start justify-between mb-8">
                <div>
                    <h1 class="text-4xl font-bold text-gray-800"><?php echo htmlspecialchars($job['title']); ?></h1>
                    <div class="flex items-center space-x-4 mt-4 text-gray-600">
                        <span class="text-2xl">💰 $<span class="text-3xl font-bold text-green-600"><?php echo number_format($job['budget'], 2); ?></span></span>
                        <span class="text-xl"><?php echo htmlspecialchars($job['subject']); ?></span>
                        <span class="px-4 py-2 bg-blue-100 text-blue-800 rounded-full"><?php echo ucfirst($job['level']); ?></span>
                    </div>
                </div>
                <div class="text-right">
                    <div class="text-2xl font-bold text-gray-500">Deadline</div>
                    <div class="text-3xl font-bold text-red-600"><?php echo date('M j, Y g:i A', strtotime($job['deadline'])); ?></div>
                </div>
            </div>

            <div class="prose max-w-none">
                <?php echo nl2br(htmlspecialchars($job['description'])); ?>
            </div>

            <div class="flex flex-wrap gap-4 mt-12 pt-8 border-t border-gray-200">
                <?php if (getUserRole() === 'provider'): ?>
                    <?php if ($job['status'] === 'posted'): ?>
                        <button onclick="startNegotiation(<?php echo $job['id']; ?>)" 
                                class="bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white px-12 py-4 rounded-2xl font-bold text-xl shadow-2xl hover:shadow-3xl transition-all duration-300">
                            💬 Start Negotiation
                        </button>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>

        <?php if (getUserRole() === 'student' && $job['student_id'] == $_SESSION['user_id']): ?>
            <div class="bg-yellow-50 border-2 border-yellow-200 p-8 rounded-2xl">
                <h3 class="text-2xl font-bold text-yellow-800 mb-4">👋 Your Job</h3>
                <p class="text-lg text-yellow-900">Posted <?php echo date('M j', strtotime($job['created_at'])); ?>. Waiting for expert bids.</p>
            </div>
        <?php endif; ?>
    </div>

    <script>
    function startNegotiation(jobId) {
        const price = prompt('Enter your proposed price (USD):');
        const deadline = prompt('Enter your proposed deadline (YYYY-MM-DDTHH:MM):');
        
        if (price && deadline) {
            fetch('job-chat.php?id=' + jobId + '&action=start&price=' + price + '&deadline=' + deadline)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('✅ Negotiation started! Go to your dashboard.');
                        window.location.href = 'dashboard.php';
                    } else {
                        alert('❌ ' + data.message);
                    }
                });
        }
    }
    </script>
</body>
</html>
