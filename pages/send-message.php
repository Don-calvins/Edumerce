<?php
require_once '../includes/functions.php';

if ($_POST && isset($_POST['agreement_id']) && isset($_POST['message'])) {
    $result = sendMessage($_POST['agreement_id'], $_SESSION['user_id'], $_POST['message']);
    header('Content-Type: application/json');
    echo json_encode($result);
} else {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}
?>
