<?php
require_once __DIR__ . '/../db.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

try {
    $data = json_decode(file_get_contents('php://input'), true) ?? [];
    $razorpay_order_id   = $data['razorpay_order_id'] ?? '';
    $razorpay_payment_id = $data['razorpay_payment_id'] ?? '';
    $razorpay_signature  = $data['razorpay_signature'] ?? '';

    $generated_signature = hash_hmac(
        'sha256',
        $razorpay_order_id . '|' . $razorpay_payment_id,
        RAZORPAY_KEY_SECRET
    );

    if (!hash_equals($generated_signature, $razorpay_signature)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Signature mismatch']);
        exit;
    }

    execute(
        "UPDATE payments SET razorpay_payment_id = ?, status = 'Paid' WHERE razorpay_order_id = ?",
        [$razorpay_payment_id, $razorpay_order_id]
    );

    echo json_encode(['success' => true]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
