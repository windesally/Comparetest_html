<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// --- ตั้งค่า path ---
$basePaths = [
    "PKASM011" => "C:\\Users\\nxg13764\\Desktop\\Project compare\\Python\\TEST\\PKASM011\\mc\\bind",
    "PKASM012" => "\\\\192.168.76.58\\data\\05_ EE\\Pathomphat S\\TEST compare project\\PKASM\\PKASM012\\mc\\bind",
    "PKASM013" => "\\\\192.168.76.58\\data\\05_ EE\\Pathomphat S\\TEST compare project\\PKASM\\PKASM013\\mc\\bind",
    "PKASM014" => "\\\\192.168.76.58\\data\\05_ EE\\Pathomphat S\\TEST compare project\\PKASM\\PKASM014\\mc\\bind"
];

// --- ฟังก์ชันสร้าง cat จากไฟล์ไม่มีนามสกุล ---
function createCatFromFile($sourcePath, $destPath) {
    $fileContent = file_get_contents($sourcePath);
    $isText = (strlen($fileContent) === mb_strlen($fileContent, '8bit'));
    $fp = fopen($destPath, "w");
    fwrite($fp, "=== " . basename($sourcePath) . " (.cat generated) ===\n");

    if ($isText) {
        fwrite($fp, $fileContent);
    } else {
        $offset = 0;
        $bytes = unpack('C*', $fileContent);
        $line = [];
        foreach ($bytes as $i => $byte) {
            $line[] = sprintf("%02X", $byte);
            if (count($line) === 16) {
                fwrite($fp, sprintf(
                    "%08X  %-48s  %s\n",
                    $offset,
                    implode(' ', $line),
                    preg_replace('/[^ -~]/', '.', pack('C*', ...array_slice($bytes, $i - 15, 16)))
                ));
                $line = [];
                $offset += 16;
            }
        }
        if (!empty($line)) {
            fwrite($fp, sprintf(
                "%08X  %-48s  %s\n",
                $offset,
                implode(' ', $line),
                preg_replace('/[^ -~]/', '.', pack('C*', ...array_slice($bytes, $i - count($line) + 1, count($line))))
            ));
        }
    }
    fclose($fp);
}

// --- ฟังก์ชันลบโฟลเดอร์ ---
function deleteDirectory($dir) {
    if (!is_dir($dir)) return;
    $items = array_diff(scandir($dir), ['.', '..']);
    foreach ($items as $item) {
        $path = $dir . DIRECTORY_SEPARATOR . $item;
        if (is_dir($path)) {
            deleteDirectory($path);
        } else {
            @unlink($path);
        }
    }
    @rmdir($dir);
}

// --- เริ่มทำงาน ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['recipe']) && !empty($_POST['machine']) && isset($_FILES['file'])) {
    $machine = $_POST['machine'];
    $recipeName = $_POST['recipe'];

    if (!isset($basePaths[$machine])) {
        exit("❌ Machine not found in basePaths.");
    }

    $recipePath = $basePaths[$machine] . DIRECTORY_SEPARATOR . $recipeName . ".cat";
    if (!file_exists($recipePath)) {
        exit("❌ Recipe file not found: " . $recipePath);
    }

    // อัพโหลดไฟล์
    $uploadDir = __DIR__ . "/uploads";
    @mkdir($uploadDir, 0777, true);
    $uploadPath = $uploadDir . "/" . basename($_FILES['file']['name']);
    move_uploaded_file($_FILES['file']['tmp_name'], $uploadPath);

    // แตกไฟล์
    $outputDir = $uploadDir . "/extracted";
    @mkdir($outputDir, 0777, true);
    exec("7z e " . escapeshellarg($uploadPath) . " -o" . escapeshellarg($outputDir) . " -y");

    // แปลงไฟล์ไม่มีนามสกุลเป็น .cat
    $allFiles = array_merge(glob($uploadDir . "/*"), glob($outputDir . "/*"));
    foreach ($allFiles as $file) {
        if (is_file($file) && pathinfo($file, PATHINFO_EXTENSION) === '') {
            $newName = $file . '.cat';
            if (!file_exists($newName)) {
                createCatFromFile($file, $newName);
            }
        }
    }

    // หาไฟล์ .cat ใน extracted
    $catFiles = array_merge(glob($outputDir . "/*.cat"), glob($uploadDir . "/*.cat"));
    if (empty($catFiles)) {
        deleteDirectory($uploadDir);
        exit("❌ No .cat file found after extraction.");
    }

    $uploadedCatFile = $catFiles[0];

    // อ่านและเปรียบเทียบ
    $recipeLines = file($recipePath, FILE_IGNORE_NEW_LINES);
    $uploadLines = file($uploadedCatFile, FILE_IGNORE_NEW_LINES);
    $diffs = [];
    $maxLines = max(count($recipeLines), count($uploadLines));
    for ($i = 0; $i < $maxLines; $i++) {
        $rLine = $recipeLines[$i] ?? "";
        $uLine = $uploadLines[$i] ?? "";
        if ($rLine !== $uLine) {
            $diffs[] = "Line " . ($i + 1) . ":\nRecipe: " . $rLine . "\nUpload: " . $uLine;
        }
    }

    echo empty($diffs) ? "✅ No differences found." : implode("\n\n", $diffs);

    // ลบโฟลเดอร์ uploads หลังเสร็จงาน
    deleteDirectory($uploadDir);
}
?>
