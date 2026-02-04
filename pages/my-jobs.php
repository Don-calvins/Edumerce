<?php
require_once '../includes/functions.php';

if (!isLoggedIn() || getUserRole() !== 'student') {
    header('Location: login.php');
    exit;
}

$jobs = getUserJobs($_SESSION['user_id']);
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Jobs - Edumerce</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">
    <nav class="bg-white shadow-lg">
        <div class="max-w-6xl mx-auto px-4">
            <div class="flex justify-between items-center py-4">
                <a href="dashboard.php" class="text-2xl font-bold text-gray-800">Edumerce</a>
                <div>
                    <a href="post-job.php" class="text-blue-600 hover:underline mr-4">Post Job</a>
                    <a href="jobs.php" class="text-gray-600 hover:underline mr-4">Browse Jobs</a>
                    <a href="dashboard.php" class="text-gray-600 hover:underline mr-4">Dashboard</a>
                    <a href="../logout.php" class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600">Logout</a>
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-6xl mx-auto py-12 px-4">
        <div class="flex justify-between items-center mb-12">
            <div>
                <h1 class="text-4xl font-bold text-gray-800">💼 My Jobs (<?php echo count($jobs); ?>)</h1>
                <p class="text-xl text-gray-600 mt-2">Manage your posted assignments and hired experts</p>
            </div>
            <a href="post-job.php" class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-4 rounded-xl font-bold text-xl shadow-xl">
                📝 Post New Job
            </a>
        </div>

        <?php if (empty($jobs)): ?>
            <div class="text-center py-24">
                <div class="w-32 h-32 bg-gray-200 rounded-3xl flex items-center justify-center mx-auto mb-8">
                    <span class="text-4xl">📭</span>
                </div>
                <h2 class="text-3xl font-bold text-gray-600 mb-4">No jobs posted yet</h2>
                <p class="text-xl text-gray-500 mb-8">Post your first academic assignment and get expert help!</p>
                <a href="post-job.php" class="bg-blue-600 hover:bg-blue-700 text-white px-12 py-4 rounded-xl font-bold text-xl shadow-xl">
                    🚀 Post First Job
                </a>
            </div>
        <?php else: ?>
            <div class="space-y-6">
                <?php foreach ($jobs as $job): 
                    $statusClass = match($job['status']) {
                        'posted' => 'bg-yellow-100 text-yellow-800',
                        'negotiating' => 'bg-orange-100 text-orange-800',
                        'accepted' => 'bg-blue-100 text-blue-800',
                        'delivered' => 'bg-purple-100 text-purple-800',
                        'completed' => 'bg-green-100 text-green-800',
                        'disputed' => 'bg-red-100 text-red-800',
                        default => 'bg-gray-100 text-gray-800'
                    };
                    $statusIcon = match($job['status']) {
                        'posted' => '⏳',
                        'negotiating' => '💬',
                        'accepted' => '✅',
                        'delivered' => '📤',
                        'completed' => '⭐',
                        'disputed' => '⚠️',
                        default => '📋'
                    };
                ?>
                    <div class="bg-white rounded-2xl shadow-xl p-8 border-2 <?php echo $statusClass; ?>">
                        <div class="flex flex-wrap items-start justify-between gap-4 mb-6">
                            <div>
                                <h3 class="text-2xl font-bold text-gray-800 mb-1"><?php echo htmlspecialchars($job['title']); ?></h3>
                                <div class="flex items-center space-x-4 text-sm text-gray-600">
                                    <span class="inline-block <?php echo $statusClass; ?> px-3 py-1 rounded-full font-semibold text-xs">
                                        <?php echo $statusIcon; ?> <?php echo ucfirst(str_replace('_', ' ', $job['status'])); ?>
                                    </span>
                                    <span>Budget: $<span class="font-bold text-lg text-green-600"><?php echo number_format($job['budget'], 2); ?></span></span>
                                    <?php if ($job['provider_id']): ?>
                                        <span>Hired: Provider #<?php echo $job['provider_id']; ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="text-3xl font-bold text-gray-800">$<?php echo number_format($job['budget'], 2); ?></div>
                                <div class="text-sm text-gray-500"><?php echo date('M j, Y', strtotime($job['created_at'])); ?></div>
                            </div>
                        </div>

                        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                            <div class="bg-gray-50 p-6 rounded-xl">
                                <div class="text-2xl mb-2"><?php echo $job['subject']; ?></div>
                                <div class="text-sm text-gray-600"><?php echo ucfirst($job['level']); ?> Level</div>
                            </div>
                            <div class="bg-blue-50 p-6 rounded-xl">
                                <div class="font-bold text-lg mb-1"><?php echo date('M j, Y g:i A', strtotime($job['deadline'])); ?></div>
                                <div class="text-sm text-blue-600 font-semibold">Deadline</div>
                            </div>
                            <div class="bg-green-50 p-6 rounded-xl">
                                <div class="text-lg mb-1"><?php echo substr($job['description'], 0, 80); ?>...</div>
                                <div class="text-sm text-gray-600">Description Preview</div>
                            </div>
                        </div>

                        <div class="flex flex-wrap gap-3 pt-6 border-t border-gray-200">
                            <?php if ($job['status'] === 'posted'): ?>
                                <a href="post-job.php" class="bg-yellow-500 hover:bg-yellow-600 text-white px-6 py-3 rounded-xl font-semibold">
                                    ✏️ Edit Job
                                </a>
                                <button class="bg-red-500 hover:bg-red-600 text-white px-6 py-3 rounded-xl font-semibold">
                                    🗑️ Delete Job
                                </button>
                            <?php elseif ($job['status'] === 'negotiating'): ?>
                                <span class="bg-orange-100 text-orange-800 px-4 py-2 rounded-xl font-semibold">
                                    💬 In Negotiation
                                </span>
                                <a href="#" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-xl font-semibold">
                                    View Chat
                                </a>
                            <?php elseif ($job['status'] === 'accepted'): ?>
                                <span class="bg-blue-100 text-blue-800 px-4 py-2 rounded-xl font-semibold">
                                    ✅ Provider Hired
                                </span>
                                <a href="#" class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-3 rounded-xl font-semibold">
                                    💰 Make Payment
                                </a>
                            <?php elseif ($job['status'] === 'delivered'): ?>
                                <span class="bg-purple-100 text-purple-800 px-4 py-2 rounded-xl font-semibold">
                                    📤 Work Delivered
                                </span>
                                <a href="#" class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-xl font-semibold">
                                    ✅ Review & Pay
                                </a>
                            <?php elseif ($job['status'] === 'completed'): ?>
                                <span class="bg-green-100 text-green-800 px-4 py-2 rounded-xl font-semibold">
                                    ⭐ Job Completed
                                </span>
                                <button class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-xl font-semibold">
                                    📋 View Details
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
