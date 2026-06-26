<?php
require 'config.php';

 $payload = file_get_contents('php://input');
 $signature = $_SERVER['HTTP_X_RAZORPAY_SIGNATURE'] ?? '';

 $expectedSignature = hash_hmac('sha256', $payload, RAZORPAY_KEY_SECRET);

if ($expectedSignature === $signature) {
    $event = json_decode($payload, true);
    
    if ($event['event'] === 'payment.captured') {
        $payment = $event['payload']['payment']['entity'];
        $ref_id = $payment['notes']['ref_id'] ?? '';
        
        if ($ref_id) {
            $url = SUPABASE_URL . '/rest/v1/customers?ref_id=eq.' . urlencode($ref_id);
            $ch = curl_init($url);
            curl_setopt_array($ch, [
                CURLOPT_CUSTOMREQUEST => 'PATCH',
                CURLOPT_POSTFIELDS => json_encode([
                    "payment_id" => $payment['id'],
                    "payment_status" => "paid",
                    "paid_at" => date('Y-m-d H:i:s')
                ]),
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HTTPHEADER => [
                    'Content-Type: application/json',
                    'apikey: ' . SUPABASE_ANON_KEY,
                    'Authorization: Bearer ' . SUPABASE_ANON_KEY
                ],
                CURLOPT_SSL_VERIFYPEER => false
            ]);
            curl_exec($ch);
            curl_close($ch);
        }
    }
    
    http_response_code(200);
    echo json_encode(['status' => 'ok']);
} else {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid signature']);
}