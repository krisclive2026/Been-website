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
    $name   = $data['name'] ?? '';
    $email  = $data['email'] ?? '';
    $amount = (float) ($data['amount'] ?? 0);

    $amount_in_paise = (int) round($amount * 100);

    // Create the order via Razorpay's REST API directly (no SDK/composer needed)
    $ch = curl_init('https://api.razorpay.com/v1/orders');
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_USERPWD        => RAZORPAY_KEY_ID . ':' . RAZORPAY_KEY_SECRET,
        CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
        CURLOPT_POSTFIELDS     => json_encode([
            'amount'          => $amount_in_paise,
            'currency'        => 'INR',
            'payment_capture' => 1,
        ]),
    ]);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlErr  = curl_error($ch);
    curl_close($ch);

    if ($response === false || $httpCode >= 400) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => $curlErr ?: 'Order creation failed']);
        exit;
    }

    $order = json_decode($response, true);

    execute(
        "INSERT INTO payments (name, email, amount, razorpay_order_id, status)
         VALUES (?, ?, ?, ?, 'Pending')",
        [$name, $email, $amount, $order['id']]
    );

    echo json_encode([
        'success'      => true,
        'order_id'     => $order['id'],
        'amount'       => $amount_in_paise,
        'razorpay_key' => RAZORPAY_KEY_ID,
    ]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
