<?php
require_once '../includes/functions.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$agreement_id = $_GET['agreement'] ?? 0;
$agreement = getAgreement($agreement_id);

if (!$agreement) {
    $_SESSION['error'] = 'Agreement not found';
    header('Location: dashboard.php');
    exit;
}

// Check if review already exists
$stmt = $pdo->prepare("SELECT id FROM reviews WHERE agreement_id = ?");
$stmt->execute([$agreement_id]);
if ($stmt->fetch()) {
    $_SESSION['error'] = 'Review already submitted';
    header('Location: dashboard.php');
    exit;
}

if ($_POST) {
    global $pdo;
    $stmt = $pdo->prepare("INSERT INTO reviews (agreement_id, rating, comment) VALUES (?, ?, ?)");
    if ($stmt->execute([$agreement_id, $_POST['rating'], $_POST['comment']])) {
        // Mark job as completed
        $pdo->prepare("UPDATE jobs j JOIN agreements a ON j.id = a.job_id SET j.status = 'completed' WHERE a.id = ?")->execute([$agreement_id]);
        $_SESSION['success'] = 'Thank you for your review!';
        header('Location: dashboard.php');
        exit;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Leave Review - Edumerce</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-purple-50 to-pink-50 min-h-screen">
    <nav class="bg-white shadow-lg">
        <div class="max-w-4xl mx-auto px-4">
            <div class="flex justify-between items-center py-4">
                <a href="dashboard.php" class="text-2xl font-bold">Edumerce</a>
                <a href="dashboard.php" class="bg-gray-500 text-white px-6 py-2 rounded-xl">← Dashboard</a>
            </div>
        </nav>

    <div class="max-w-2xl mx-auto py-20 px-4">
        <div class="bg-white rounded-3xl shadow-2xl p-12 text-center">
            <div class="w-24 h-24 bg-yellow-100 rounded-3xl flex items-center justify-center mx-auto mb-8">
                <span class="text-4xl">⭐</span>
            </div>
            
            <h1 class="text-4xl font-bold text-gray-800 mb-4"><?php echo getUserRole() === 'student' ? 'Review Provider' : 'Review Student'; ?></h1>
            <p class="text-xl text-gray-600 mb-8">How would you rate this job completion?</p>
            
            <h3 class="text-2xl font-bold mb-12"><?php echo htmlspecialchars($agreement['title']); ?></h3>

            <?php if (isset($_SESSION['success'])): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-8 py-6 rounded-2xl mb-8 text-lg">
                    <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                </div>
            <?php endif; ?>

            <form method="POST" class="space-y-8">
                <!-- Star Rating -->
                <div class="flex items-center justify-center gap-2 mb-12">
                    <?php for ($i = 5; $i >= 1; $i--): ?>
                        <label class="cursor-pointer">
                            <input type="radio" name="rating" value="<?php echo $i; ?>" required class="sr-only">
                            <span class="text-4xl <?php echo $_POST['rating'] ?? 0 >= $i ? 'text-yellow-400' : 'text-gray-300'; ?> hover:text-yellow-400 transition-all">⭐</span>
                        </label>
                    <?php endfor; ?>
                </div>

                <!-- Review Text -->
                <div>
                    <label class="block text-gray-700 font-bold mb-4 text-xl text-left">What did you think?</label>
                    <textarea name="comment" rows="6" 
                              class="w-full p-6 border-2 border-gray-200 rounded-2xl focus:ring-4 focus:ring-purple-200 focus:border-purple-500 text-lg"
                              placeholder="Share your experience... (optional)"><?php echo $_POST['comment'] ?? ''; ?></textarea>
                </div>

                <button type="submit" 
                        class="w-full bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 text-white font-bold py-6 px-12 rounded-2xl text-xl shadow-2xl hover:shadow-3xl transition-all duration-300">
                    ✅ Submit Review & Complete Job
                </button>
            </form>
        </div>
    </div>
</body>
</html>
