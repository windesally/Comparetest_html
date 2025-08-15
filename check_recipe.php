<?php
header("Content-Type: application/json; charset=utf-8");

$machine = $_GET['machine'] ?? '';
$recipe = $_GET['recipe'] ?? '';

$basePaths = [
    "PKASM011" => "\\\\192.168.76.58\\data\\05_ EE\\Pathomphat S\\TEST compare project\\PKASM\\PKASM011\\mc\\bind",
    "PKASM012" => "\\\\192.168.76.58\\data\\05_ EE\\Pathomphat S\\TEST compare project\\PKASM\\PKASM012\\mc\\bind",
    "PKASM013" => "\\\\192.168.76.58\\data\\05_ EE\\Pathomphat S\\TEST compare project\\PKASM\\PKASM013\\mc\\bind",
    "PKASM014" => "\\\\192.168.76.58\\data\\05_ EE\\Pathomphat S\\TEST compare project\\PKASM\\PKASM014\\mc\\bind"
];

if (empty($machine) || empty($recipe)) {
    echo json_encode(["status" => "error", "message" => "Missing machine or recipe."]);
    exit;
}

if (!isset($basePaths[$machine])) {
    echo json_encode(["status" => "error", "message" => "Invalid machine selected."]);
    exit;
}

$recipePath = $basePaths[$machine] . DIRECTORY_SEPARATOR . $recipe;

if (!str_ends_with(strtolower($recipePath), '.cat')) {
    $recipePath .= ".cat";
}

if (!file_exists($recipePath)) {
    echo json_encode(["status" => "error", "message" => "Recipe file not found: $recipePath"]);
    exit;
}

$content = @file($recipePath, FILE_IGNORE_NEW_LINES);

if ($content === false) {
    echo json_encode(["status" => "error", "message" => "Cannot read recipe file."]);
    exit;
}

echo json_encode([
    "status" => "ok",
    "path" => $recipePath,
    "preview" => array_slice($content, 0, 5) // แสดงแค่ 5 บรรทัดแรก
]);
exit;
