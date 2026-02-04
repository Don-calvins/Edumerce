<?php
require_once '../includes/functions.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$agreement_id = $_GET['id'] ?? 0;
$agreement = getAgreement($agreement_id);

if (!$agreement) {
    $_SESSION['error'] = 'Chat not found';
    header('Location: dashboard.php');
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Chat - <?php echo htmlspecialchars($agreement['title']); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex flex-col">
    <nav class="bg-white shadow-lg">
        <div class="max-w-4xl mx-auto px-4">
            <div class="flex justify-between items-center py-4">
                <a href="dashboard.php" class="text-2xl font-bold">Edumerce</a>
                <a href="javascript:history.back()" class="bg-gray-500 text-white px-6 py-2 rounded-xl hover:bg-gray-600">← Back</a>
            </div>
        </div>
    </nav>

    <div class="flex-1 max-w-4xl mx-auto px-4 py-8 w-full">
        <!-- Job Header -->
        <div class="bg-white rounded-2xl shadow-xl p-6 mb-8 border-b-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold"><?php echo htmlspecialchars($agreement['title']); ?></h1>
                    <div class="flex items-center space-x-4 text-sm text-gray-600 mt-2">
                        <span>💰 $<span class="font-bold text-green-600"><?php echo number_format($agreement['agreed_price'], 2); ?></span></span>
                        <span>Deadline: <?php echo date('M j, Y g:i A', strtotime($agreement['agreed_deadline'])); ?></span>
                    </div>
                </div>
                <div class="text-right">
                    <span class="px-4 py-2 bg-blue-100 text-blue-800 rounded-full font-bold">Negotiating</span>
                </div>
            </div>
        </div>

        <!-- Messages -->
        <div id="messages-container" class="bg-white rounded-2xl shadow-xl p-8 mb-8 h-96 md:h-[500px] overflow-y-auto border-2 border-gray-100">
            <?php 
            $messages = getMessages($agreement_id);
            foreach ($messages as $msg): 
                $isMe = $msg['sender_id'] == $_SESSION['user_id'];
                $roleClass = $msg['role'] === 'student' ? 'bg-blue-500' : 'bg-green-500';
            ?>
                <div class="flex <?php echo $isMe ? 'justify-end mb-4' : 'mb-4'; ?>">
                    <div class="max-w-xs md:max-w-md lg:max-w-lg">
                        <div class="flex items-end space-x-2 <?php echo $isMe ? 'flex-row-reverse space-x-reverse' : ''; ?>">
                            <div class="w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center text-xs font-bold text-white">
                                <?php echo strtoupper(substr($msg['name'], 0, 1)); ?>
                            </div>
                            <div class="p-4 rounded-2xl <?php echo $isMe ? $roleClass.' text-white' : 'bg-gray-200 text-gray-900'; ?> max-w-full">
                                <div class="text-sm"><?php echo htmlspecialchars($msg['message']); ?></div>
                                <div class="text-xs opacity-75 mt-1"><?php echo date('M j g:i A', strtotime($msg['created_at'])); ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Action Buttons + Message Input -->
        <div class="bg-white rounded-2xl shadow-xl p-6 border-t-4 border-indigo-500">
            <div class="flex flex-col md:flex-row gap-4 items-end">
                <?php if (getUserRole() === 'student'): ?>
                    <button onclick="acceptAgreement(<?php echo $agreement_id; ?>)" 
                            class="flex-1 md:flex-none bg-green-600 hover:bg-green-700 text-white font-bold py-4 px-8 rounded-xl shadow-xl hover:shadow-2xl transition-all duration-300">
                        ✅ Accept Terms & Pay 50%
                    </button>
                <?php else: ?>
                    <button onclick="finalizeTerms(<?php echo $agreement_id; ?>)" 
                            class="flex-1 md:flex-none bg-blue-600 hover:bg-blue-700 text-white font-bold py-4 px-8 rounded-xl shadow-xl hover:shadow-2xl transition-all duration-300">
                        📝 Finalize Terms
                    </button>
                <?php endif; ?>

                <div class="flex-1 relative">
                    <input type="text" id="message-input" placeholder="Type your message..." 
                           class="w-full px-6 py-4 border-2 border-gray-200 rounded-2xl focus:outline-none focus:ring-4 focus:ring-blue-200 focus:border-blue-500 text-lg">
                    <button onclick="sendMessage(<?php echo $agreement_id; ?>)" 
                            class="absolute right-3 top-1/2 -translate-y-1/2 bg-blue-600 hover:bg-blue-700 text-white p-3 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300">
                        ➤
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
    // Auto-scroll to bottom
    window.onload = function() {
        scrollToBottom();
    };

    function scrollToBottom() {
        const container = document.getElementById('messages-container');
        container.scrollTop = container.scrollHeight;
    }

    async function sendMessage(agreementId) {
        const input = document.getElementById('message-input');
        const message = input.value.trim();
        
        if (!message) return;

        try {
            const response = await fetch('send-message.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `agreement_id=${agreementId}&message=${encodeURIComponent(message)}`
            });
            
            const data = await response.json();
            
            if (data.success) {
                input.value = '';
                loadMessages(agreementId);
            }
        } catch (error) {
            alert('Failed to send message');
        }
    }

    async function loadMessages(agreementId) {
        // This would fetch fresh messages via AJAX - simplified for MVP
        location.reload();
    }

    // Enter key to send
    document.getElementById('message-input').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            const agreementId = <?php echo $agreement_id; ?>;
            sendMessage(agreementId);
        }
    });

    function acceptAgreement(agreementId) {
        if (confirm('Pay 50% upfront to start work?')) {
            alert('🛠️ Stripe integration coming next!');
        }
    }

    function finalizeTerms(agreementId) {
        alert('📝 Student will review your proposal');
    }
    </script>
</body>
</html>
