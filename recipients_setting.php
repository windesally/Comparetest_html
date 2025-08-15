<?php
$recipientsFile = 'recipients.json';

// โหลดรายชื่อ
$recipients = json_decode(file_get_contents($recipientsFile), true);

// เพิ่มอีเมล
if (isset($_POST['add_email'])) {
    $newEmail = trim($_POST['email']);
    if (filter_var($newEmail, FILTER_VALIDATE_EMAIL) && !in_array($newEmail, $recipients)) {
        $recipients[] = $newEmail;
        file_put_contents($recipientsFile, json_encode($recipients, JSON_PRETTY_PRINT));
    }
}

// ลบอีเมล
if (isset($_GET['delete'])) {
    $emailToDelete = $_GET['delete'];
    $recipients = array_filter($recipients, fn($e) => $e !== $emailToDelete);
    file_put_contents($recipientsFile, json_encode(array_values($recipients), JSON_PRETTY_PRINT));
    header("Location: recipients_setting.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>ตั้งค่าผู้รับอีเมล</title>
    <link rel="stylesheet" href="recipients_setting.css">
</head>
<body>
    <header>
        <div style="text-align: center;">
            <img width="250" height="100" src="https://upload.wikimedia.org/wikipedia/commons/thumb/5/50/NXP_Semiconductors_logo_2023.svg/2560px-NXP_Semiconductors_logo_2023.svg.png" alt="NXP logo">
        </div>
    </header>

    <main>
        <section class="container">
            <h1 class="title_1">Gmail setting</h1>
            <form method="POST">
                <div class="gmailbox">
                    <input type="email" name="email" placeholder="Add gmail" required>
                </div>
                <div class="submit">
                    <button class="btn" type="submit" name="add_email">Add</button>
                </div> 
            </form>
<!--  -->
            <div class="list">
                <ul class="list-email">
                    <?php foreach ($recipients as $email): ?>
                        <li><?= htmlspecialchars($email) ?> 
                            <a href="?delete=<?= urlencode($email) ?>" onclick="return confirm('ลบผู้รับนี้?')">ลบ</a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
<!--  -->
            <div class="back">
                <a class="back-btn" href="admin.php">← Confirm</a>
            </div>

        </section>
    </main>
</body>
</html>
