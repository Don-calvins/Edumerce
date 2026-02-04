<?php
require_once '../includes/functions.php';

if (!isLoggedIn() || getUserRole() !== 'admin') {
    header('Location: login.php');
    exit;
}

// Stats
$stats = [
    'total_jobs' => $pdo->query("SELECT COUNT(*) FROM jobs")->fetchColumn(),
    'total_users' => $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn(),
    'total_revenue' => $pdo->query("SELECT SUM(amount) FROM payments WHERE status = 'released'")->fetchColumn() ?? 0,
    'pending_disputes' => $pdo->query("SELECT COUNT(*) FROM jobs WHERE status = 'disputed'")->fetchColumn()
];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard - Edumerce</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">
    <nav class="bg-white shadow-lg">
        <div class="max-w-6xl mx-auto px-4">
            <div class="flex justify-between items-center py-4">
                <h1 class="text-3xl font-bold text-gray-800">Edumerce Admin</h1>
                <a href="../logout.php" class="bg-red-500 text-white px-6 py-2 rounded-xl hover:bg-red-600">Logout</a>
            </div>
        </div>
    </nav>

    <div class="max-w-6xl mx-auto py-12 px-4">
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-12">
            <div class="bg-white p-8 rounded-2xl shadow-xl border-4 border-blue-100">
                <div class="text-4xl font-bold text-blue-600"><?php echo $stats['total_jobs']; ?></div>
                <div class="text-gray-600 mt-2">Total Jobs</div>
            </div>
            <div class="bg-white p-8 rounded-2xl shadow-xl border-4 border-green-100">
                <div class="text-4xl font-bold text-green-600">$<?php echo number_format($stats['total_revenue'], 2); ?></div>
                <div class="text-gray-600 mt-2">Platform Revenue</div>
            </div>
            <div class="bg-white p-8 rounded-2xl shadow-xl border-4 border-purple-100">
                <div class="text-4xl font-bold text-purple-600"><?php echo $stats['total_users']; ?></div>
                <div class="text-gray-600 mt-2">Total Users</div>
            </div>
            <div class="bg-white p-8 rounded-2xl shadow-xl border-4 border-orange-100">
                <div class="text-4xl font-bold text-orange-600"><?php echo $stats['pending_disputes']; ?></div>
                <div class="text-gray-600 mt-2">Pending Disputes</div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="grid md:grid-cols-3 gap-6 mb-12">
            <a href="#users" class="bg-gradient-to-r from-blue-500 to-blue-600 text-white p-8 rounded-2xl shadow-2xl hover:shadow-3xl">
                <div class="text-3xl mb-4">👥</div>
                <div class="font-bold text-xl">Manage Users</div>
            </a>
            <a href="#revenue" class="bg-gradient-to-r from-green-500 to-green-600 text-white p-8 rounded-2xl shadow-2xl hover:shadow-3xl">
                <div class="text-3xl mb-4">💰</div>
                <div class="font-bold text-xl">View Revenue</div>
            </a>
            <a href="#disputes" class="bg-gradient-to-r from-orange-500 to-orange-600 text-white p-8 rounded-2xl shadow-2xl hover:shadow-3xl">
                <div class="text-3xl mb-4">⚠️</div>
                <div class="font-bold text-xl">Resolve Disputes</div>
            </a>
        </div>

        <!-- Recent Activity -->
        <div class="bg-white rounded-2xl shadow-xl p-8">
            <h2 class="text-2xl font-bold mb-6">Recent Jobs</h2>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="p-4 text-left font-bold">Job Title</th>
                            <th class="p-4 text-left font-bold">Student</th>
                            <th class="p-4 text-left font-bold">Provider</th>
                            <th class="p-4 text-left font-bold">Status</th>
                            <th class="p-4 text-left font-bold">Amount</th>
                            <th class="p-4 text-left font-bold">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $recent_jobs = $pdo->query("
                            SELECT j.*, u1.name as student_name, u2.name as provider_name 
                            FROM jobs j 
                            LEFT JOIN users u1 ON j.student_id = u1.id 
                            LEFT JOIN agreements a ON j.id = a.job_id 
                            LEFT JOIN users u2 ON a.provider_id = u2.id 
                            ORDER BY j.created_at DESC LIMIT 10
                        ")->fetchAll();
                        
                        foreach ($recent_jobs as $job): 
                        ?>
                        <tr class="border-b hover:bg-gray-50">
                            <td class="p-4 font-semibold"><?php echo htmlspecialchars(substr($job['title'], 0, 40)); ?>...</td>
                            <td class="p-4"><?php echo htmlspecialchars($job['student_name']); ?></td>
                            <td class="p-4"><?php echo $job['provider_name'] ?? 'None'; ?></td>
                            <td class="p-4">
                                <span class="px-3 py-1 rounded-full text-xs font-bold <?php 
                                    echo match($job['status']) {
                                        'posted' => 'bg-yellow-100 text-yellow-800',
                                        'accepted' => 'bg-blue-100 text-blue-800',
                                        'delivered' => 'bg-purple-100 text-purple-800',
                                        'completed' => 'bg-green-100 text-green-800',
                                        default => 'bg-gray-100 text-gray-800'
                                    };
                                ?>">
                                    <?php echo ucfirst(str_replace('_', ' ', $job['status'])); ?>
                                </span>
                            </td>
                            <td class="p-4 font-bold text-green-600">$<?php echo number_format($job['budget'], 2); ?></td>
                            <td class="p-4">
                                <a href="#" class="text-blue-600 hover:underline text-sm">View</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
