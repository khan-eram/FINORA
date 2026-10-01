<?php

session_start();

include("config/database.php");

// Check if user is logged in
if (!isset($_SESSION["user_id"])) {
    http_response_code(401);
    echo "Not logged in";
    exit();
}

$user_id = $_SESSION["user_id"];

// Check if voice file exists
if (!isset($_FILES["voice"])) {
    http_response_code(400);
    echo "Voice file was not received";
    exit();
}

$audio = $_FILES["voice"];

// Check upload error
if ($audio["error"] !== UPLOAD_ERR_OK) {

    http_response_code(400);

    echo "Upload error code: " . $audio["error"];

    exit();
}


// ======================================
// CHECK FILE SIZE
// ======================================

if ($audio["size"] <= 0) {

    http_response_code(400);

    echo "Empty audio file";

    exit();
}


// Maximum 10 MB
if ($audio["size"] > 10 * 1024 * 1024) {

    http_response_code(400);

    echo "Voice message is larger than 10 MB";

    exit();
}


// ======================================
// DETERMINE AUDIO TYPE
// ======================================

$mime_type = "";

// Use finfo if available
if (function_exists("finfo_open")) {

    $finfo = finfo_open(FILEINFO_MIME_TYPE);

    if ($finfo) {

        $mime_type = finfo_file(
            $finfo,
            $audio["tmp_name"]
        );

        finfo_close($finfo);
    }
}


// ======================================
// ALLOWED AUDIO FORMATS
// ======================================

$allowed_types = [

    "audio/webm" => "webm",

    "video/webm" => "webm",

    "audio/ogg" => "ogg",

    "application/ogg" => "ogg",

    "audio/mp4" => "mp4",

    "video/mp4" => "mp4",

    "audio/mpeg" => "mp3"

];


// If browser/server gives an unexpected MIME type,
// check the original browser MIME type as fallback.

$browser_type = $_POST["mime_type"] ?? "";


// Determine extension
if (isset($allowed_types[$mime_type])) {

    $extension = $allowed_types[$mime_type];

} elseif (isset($allowed_types[$browser_type])) {

    $extension = $allowed_types[$browser_type];

} else {

    // Most desktop browsers using MediaRecorder
    // will produce WebM recordings.
    $extension = "webm";
}





// ======================================
// CREATE UNIQUE FILE NAME
// ======================================

$file_name =
    "voice_" .
    $user_id .
    "_" .
    bin2hex(random_bytes(10)) .
    "." .
    $extension;

// ======================================
// VOICE UPLOAD DIRECTORY
// ======================================

$upload_dir = __DIR__ . "/uploads/chat_voice/";


// Check that the folder exists
if (!is_dir($upload_dir)) {

    http_response_code(500);

    echo "Voice upload folder does not exist: " . $upload_dir;

    exit();
}


// Check that the folder is writable
if (!is_writable($upload_dir)) {

    http_response_code(500);

    echo "Voice upload folder is not writable";

    exit();
}

// ======================================
// FILE PATH
// ======================================

$file_path = $upload_dir . $file_name;

// ======================================
// SAVE AUDIO FILE
// ======================================

if (!move_uploaded_file(
    $audio["tmp_name"],
    $file_path
)) {

    http_response_code(500);

    echo "Could not save audio file";

    exit();
}


// ======================================
// DATABASE PATH
// ======================================

$audio_path =
    "uploads/chat_voice/" .
    $file_name;


// ======================================
// SAVE MESSAGE IN DATABASE
// ======================================

$stmt = mysqli_prepare(
    $conn,
    "INSERT INTO chat_messages
    (user_id, sender, message, message_type, audio_path)
    VALUES (?, 'user', '', 'voice', ?)"
);


if (!$stmt) {

    if (file_exists($file_path)) {
        unlink($file_path);
    }

    http_response_code(500);

    echo "Database prepare error";

    exit();
}


mysqli_stmt_bind_param(
    $stmt,
    "is",
    $user_id,
    $audio_path
);


if (mysqli_stmt_execute($stmt)) {

    mysqli_stmt_close($stmt);

    echo "success";

} else {

    if (file_exists($file_path)) {
        unlink($file_path);
    }

    mysqli_stmt_close($stmt);

    http_response_code(500);

    echo "Database error: " . mysqli_error($conn);

    exit();
}

?>