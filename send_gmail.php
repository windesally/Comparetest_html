<?php
// แสดง error ทุกชนิด (เพื่อ debug)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// ถ้ารันนานเกินไปให้หยุด
set_time_limit(15);

echo "เริ่มทำงาน...<br>";

// โหลด PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/PHPMailer/PHPMailer-master/src/Exception.php';
require __DIR__ . '/PHPMailer/PHPMailer-master/src/PHPMailer.php';
require __DIR__ . '/PHPMailer/PHPMailer-master/src/SMTP.php';

// อ่านผลลัพธ์จากการ compare (ส่งมาจากฟอร์ม)
$resultText = $_POST['result'] ?? '(ไม่มีข้อมูล compare)';
echo "ข้อมูลที่จะส่ง: <pre>" . htmlspecialchars($resultText) . "</pre><br>";

// โหลดรายชื่อผู้รับจาก JSON
$recipientsFile = 'recipients.json';
if (!file_exists($recipientsFile)) {
    die("❌ ไม่พบไฟล์ $recipientsFile");
}
$recipients = json_decode(file_get_contents($recipientsFile), true);
if (!is_array($recipients)) {
    die("❌ ไฟล์ $recipientsFile มีรูปแบบไม่ถูกต้อง");
}

echo "พบผู้รับ: " . implode(", ", $recipients) . "<br>";

// ตั้งค่า PHPMailer
$mail = new PHPMailer(true);

try {
    // Debug mode
    $mail->SMTPDebug = 2; // 2 = แสดง log ละเอียด
    $mail->Debugoutput = 'html';

    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'testcompareproject@gmail.com'; // Gmail ของคุณ
    $mail->Password   = 'ohcd cddg fjrh xvvh';           // App Password ของ Gmail
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;
    $mail->CharSet    = 'UTF-8';

    $mail->setFrom('testcompareproject@gmail.com', 'Result System');

    // เพิ่มผู้รับ
    foreach ($recipients as $email) {
        $mail->addAddress($email);
    }

    // เนื้อหาอีเมล
    $mail->isHTML(false);
    $mail->Subject = 'Compare Result';
    $mail->Body    = $resultText;

    echo "กำลังส่งอีเมล...<br>";
    if ($mail->send()) {
        echo "✅ ส่งอีเมลสำเร็จ";
    } else {
        echo "❌ การส่งอีเมลล้มเหลว: " . $mail->ErrorInfo;
    }

} catch (Exception $e) {
    echo "❌ เกิดข้อผิดพลาด: {$mail->ErrorInfo}";
}

echo "<br>จบการทำงาน";
