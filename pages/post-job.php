<?php
require_once '../includes/functions.php';

if (!isLoggedIn() || getUserRole() !== 'student') {
    header('Location: login.php');
    exit;
}

if ($_POST) {
    $result = postJob(
        $_SESSION['user_id'],
        $_POST['title'],
        $_POST['description'],
        $_POST['subject'],
        $_POST['level'],
        $_POST['budget'],
        $_POST['deadline']
    );
    
    if ($result['success']) {
        $_SESSION['success'] = $result['message'];
        header('Location: my-jobs.php');
        exit;
    } else {
        $error = $result['message'];
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Post New Job - Edumerce</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">
    <nav class="bg-white shadow-lg">
        <div class="max-w-6xl mx-auto px-4">
            <div class="flex justify-between items-center py-4">
                <a href="dashboard.php" class="text-2xl font-bold text-gray-800">Edumerce</a>
                <div>
                    <a href="my-jobs.php" class="text-blue-600 hover:underline mr-4">My Jobs</a>
                    <a href="dashboard.php" class="text-gray-600 hover:underline mr-4">Dashboard</a>
                    <a href="../logout.php" class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600">Logout</a>
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-4xl mx-auto py-12 px-4">
        <h1 class="text-4xl font-bold text-gray-800 mb-2">Post New Academic Job</h1>
        <p class="text-xl text-gray-600 mb-12">Experts will bid on your job after posting</p>
        
        <?php if (isset($error)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-6 py-4 rounded-xl mb-8">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" class="bg-white p-12 rounded-2xl shadow-2xl">
            <div class="grid md:grid-cols-2 gap-8 mb-8">
                <div>
                    <label class="block text-gray-700 font-bold mb-3 text-lg">Job Title *</label>
                    <input type="text" name="title" required 
                           class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-blue-200 focus:border-blue-500 text-lg"
                           placeholder="e.g. Python Programming Assignment - Data Analysis">
                </div>
                
                <div>
                    <label class="block text-gray-700 font-bold mb-3 text-lg">Budget (USD) *</label>
                    <input type="number" name="budget" step="0.01" min="5" required
                           class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-blue-200 focus:border-blue-500 text-lg"
                           placeholder="50.00">
                </div>
            </div>
            
            <div class="mb-8">
                <label class="block text-gray-700 font-bold mb-3 text-lg">Academic Subject *</label>
                <input type="text" name="subject" required
                       class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-blue-200 focus:border-blue-500 text-lg"
                       placeholder="e.g. Computer Science, Mathematics, Economics">
            </div>
            
            <div class="mb-8">
                <label class="block text-gray-700 font-bold mb-3 text-lg">Academic Level *</label>
                <select name="level" required class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-blue-200 focus:border-blue-500 text-lg">
                    <option value="">Select Level</option>
                    <option value="highschool">High School</option>
                    <option value="college">College/Undergraduate</option>
                    <option value="grad">Graduate/Masters/PhD</option>
                </select>
            </div>
            
            <div class="mb-8">
                <label class="block text-gray-700 font-bold mb-3 text-lg">Description / Requirements *</label>
                <textarea name="description" rows="8" required
                          class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-blue-200 focus:border-blue-500 text-lg"
                          placeholder="Detailed requirements:&#10;- What needs to be done?&#10;- Word count/pages&#10;- Specific instructions&#10;- Files/attachments&#10;- Deadline is strict!"></textarea>
            </div>
            
            <div class="mb-12">
                <label class="block text-gray-700 font-bold mb-3 text-lg">Deadline *</label>
                <input type="datetime-local" name="deadline" required
                       class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-blue-200 focus:border-blue-500 text-lg">
            </div>
            
            <div class="flex gap-4">
                <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-bold py-4 px-8 rounded-xl text-xl shadow-xl hover:shadow-2xl transition-all duration-300">
                    🚀 Post Job Now
                </button>
                <a href="my-jobs.php" class="flex-1 bg-gray-500 hover:bg-gray-600 text-white font-bold py-4 px-8 rounded-xl text-xl text-center shadow-xl hover:shadow-2xl transition-all duration-300">
                    ← Back to Jobs
                </a>
            </div>
        </form>
    </div>
</body>
</html>
