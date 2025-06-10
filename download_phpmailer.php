<?php
$files = [
    'Exception.php' => 'https://raw.githubusercontent.com/PHPMailer/PHPMailer/master/src/Exception.php',
    'PHPMailer.php' => 'https://raw.githubusercontent.com/PHPMailer/PHPMailer/master/src/PHPMailer.php',
    'SMTP.php' => 'https://raw.githubusercontent.com/PHPMailer/PHPMailer/master/src/SMTP.php'
];

$baseDir = 'vendor/phpmailer/phpmailer/src/';

// Create directory if it doesn't exist
if (!file_exists($baseDir)) {
    mkdir($baseDir, 0777, true);
}

foreach ($files as $filename => $url) {
    $content = file_get_contents($url);
    if ($content === false) {
        echo "Failed to download $filename\n";
        continue;
    }
    
    if (file_put_contents($baseDir . $filename, $content) === false) {
        echo "Failed to save $filename\n";
        continue;
    }
    
    echo "Successfully downloaded and saved $filename\n";
}

echo "\nPHPMailer files have been downloaded successfully!";
?> 