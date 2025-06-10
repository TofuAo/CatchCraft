<?php
require_once 'Database Connection.php';
header('Content-Type: application/json');

$database = new Database();
$conn = $database->getConnection();

try {
    $query = "SELECT * FROM site_settings WHERE setting_key IN ('support_whatsapp', 'support_email') ORDER BY setting_key";
    $stmt = $conn->query($query);
    $settings = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $contact_info = [
        'whatsapp' => '',
        'email' => ''
    ];
    
    foreach ($settings as $setting) {
        if ($setting['setting_key'] === 'support_whatsapp') {
            $contact_info['whatsapp'] = $setting['setting_value'];
        } else if ($setting['setting_key'] === 'support_email') {
            $contact_info['email'] = $setting['setting_value'];
        }
    }
    
    echo json_encode([
        'success' => true,
        'whatsapp' => $contact_info['whatsapp'],
        'email' => $contact_info['email']
    ]);
} catch(PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error fetching contact information'
    ]);
}
?> 