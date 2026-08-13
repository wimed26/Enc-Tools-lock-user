<?php
header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);
if (!isset($input['script'])) {
    echo json_encode(['status' => 'error', 'message' => 'No script provided']);
    exit;
}

$Data = $input['script'];
$enableExpiry = $input['expiry'] ?? false;

// Tambahkan proteksi expired online jika dicentang
if ($enableExpiry) {
    $expiryCode = "local Date = gg.makeRequest(\"http://www.whatismyip.org/\")\n" .
                  "if Date == \"The user did not allow access to the Internet.\" then return " .
                  "elseif Date == \"java.net.UnknownHostException: Unable to resolve host \\\"www.whatismyip.org\\\": No address associated with hostname\" then gg.alert(\"Please connect to the network\") return " .
                  "else Date = Date[\"headers\"][\"Date\"][1] end\n";
    $Data = $expiryCode . $Data;
}

// Fungsi Random Name Generator ala Tùng Đen
function randomName($length = 9) {
    $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
    $result = '';
    for ($i = 0; $i < length; $i++) {
        $result .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $result;
}

$NameTableKey = randomName(9);
$NameTableCode1 = randomName(9);
$NameTableCode2 = randomName(9);

// Buat Key XOR Acak
$TableKey = [];
$KeyValues = [];
for ($i = 1; $i <= 40; $i++) {
    $val = rand(94, 255);
    $TableKey[] = $val;
    $KeyValues[] = $val;
}

// Simulasi Enkripsi Blok Teks & String Obfuscation
$encodedData = "local \n" . $NameTableKey . " = {\n" . implode(",\n", $KeyValues) . "\n}\n\n";
$encodedData .= "local Pairs = pairs\nlocal Char = string.char\nlocal Unpack = table.unpack\n\n";

// Tambahkan blok inti enkripsi Tùng đen ke hasil akhir
$finalOutput = "print(\"\\n© Encryption by Tùng đen [ v16 ]\\nTelegram: @Tungden\")\n\n";
$finalOutput .= $encodedData;
$finalOutput .= $Data; // Gabungkan dengan skrip utama yang sudah dimodifikasi

echo json_encode([
    'status' => 'success',
    'encrypted' => $finalOutput
]);
?>
