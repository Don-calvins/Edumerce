<?php
require_once '../includes/functions.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$jobs = getOpenJobs();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Browse Jobs - Edumerce</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">
    <nav class="bg-white shadow-lg">
        <div class="max-w-6xl mx-auto px-4">
            <div class="flex justify-between items-center py-4">
                <a href="dashboard.php" class="text-2xl font-bold text-gray-800">Edumerce</a>
                <div>
                    <?php if (getUserRole() === 'student'): ?>
                        <a href="post-job.php" class="text-blue-600 hover:underline mr-4">Post Job</a>
                        <a href="my-jobs.php" class="text-gray-600 hover:underline mr-4">My Jobs</a>
                    <?php else: ?>
                        <a href="jobs.php" class="text-blue-600 font-bold mr-4">Browse Jobs</a>
                    <?php endif; ?>
                    <a href="dashboard.php" class="text-gray-600 hover:underline mr-4">Dashboard</a>
                    <a href="../logout.php" class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600">Logout</a>
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-6xl mx-auto py-12 px-4">
        <div class="flex justify-between items-center mb-12">
            <h1 class="text-4xl font-bold text-gray-800">📚 Available Jobs (<?php echo count($jobs); ?>)</h1>
            <?php if (getUserRole() === 'student'): ?>
                <a href="post-job.php" class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-4 rounded-xl font-bold text-xl shadow-xl">
                    Post New Job
                </a>
            <?php endif; ?>
        </div>

        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php foreach ($jobs as $job): ?>
                <div class="bg-white p-8 rounded-2xl shadow-xl hover:shadow-2xl transition-all duration-300 border-2 border-gray-100 hover:border-blue-200">
                    <div class="flex items-start justify-between mb-4">
                        <h3 class="text-2xl font-bold text-gray-800"><?php echo htmlspecialchars($job['title']); ?></h3>
                        <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm font-bold">
                            $<?php echo number_format($job['budget'], 2); ?>
                        </span>
                    </div>
                    
                    <div class="space-y-2 mb-6">
                        <span class="inline-block bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm">
                            <?php echo htmlspecialchars($job['subject']); ?>
                        </span>
                        <span class="inline-block bg-purple-100 text-purple-800 px-3 py-1 rounded-full text-sm">
                            <?php echo ucfirst($job['level']); ?>
                        </span>
                    </div>
                    
                    <p class="text-gray-600 mb-6 line-clamp-3"><?php echo htmlspecialchars(substr($job['description'], 0, 150)); ?>...</p>
                    
                    <div class="flex items-center justify-between text-sm text-gray-500 mb-4">
                        <span>Posted by <?php echo htmlspecialchars($job['student_name']); ?></span>
                        <span class="font-semibold"><?php echo date('M j', strtotime($job['created_at'])); ?></span>
                    </div>
                    
                    <div class="flex items-center space-x-2 mb-4">
                        <span class="text-sm font-bold text-gray-700">Deadline:</span>
                        <span class="text-lg font-bold text-red-600"><?php echo date('M j, Y g:i A', strtotime($job['deadline'])); ?></span>
                    </div>
                    
                    <a href="view-job.php?id=<?php echo $job['id']; ?>" 
              class="w-full block bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white font-bold py-3 px-6 rounded-xl text-center shadow-xl hover:shadow-2xl transition-all duration-300 text-lg">
             💬 Message Student & Negotiate
                 </a>

                </div>
            <?php endforeach; ?>
        </div>
        
        <?php if (empty($jobs)): ?>
            <div class="text-center py-24">
                <div class="w-32 h-32 bg-gray-200 rounded-3xl flex items-center justify-center mx-auto mb-8">
                    <span class="text-4xl">📭</span>
                </div>
                <h2 class="text-3xl font-bold text-gray-600 mb-4">No jobs posted yet</h2>
                <?php if (getUserRole() === 'student'): ?>
                    <p class="text-xl text-gray-500 mb-8">Be the first! Post your academic assignment.</p>
                    <a href="post-job.php" class="bg-blue-600 hover:bg-blue-700 text-white px-12 py-4 rounded-xl font-bold text-xl shadow-xl">
                        🚀 Post First Job
                    </a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
