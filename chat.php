<?php

session_start();

include("config/database.php");

// Check if user is logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION["user_id"];

// ======================================
// DELETE MESSAGE
// ======================================

if (isset($_POST["delete_message"])) {

    $message_id = intval($_POST["delete_message"]);

    // Get audio path first
    $audio_path = null;

    $check_stmt = mysqli_prepare(
        $conn,
        "SELECT audio_path, message_type
         FROM chat_messages
         WHERE id = ? AND user_id = ? AND sender = 'user'"
    );

    mysqli_stmt_bind_param(
        $check_stmt,
        "ii",
        $message_id,
        $user_id
    );

    mysqli_stmt_execute($check_stmt);

    $check_result = mysqli_stmt_get_result($check_stmt);

    if ($message = mysqli_fetch_assoc($check_result)) {
        $audio_path = $message["audio_path"];
    }

    mysqli_stmt_close($check_stmt);


    // Delete database message
    $stmt = mysqli_prepare(
        $conn,
        "DELETE FROM chat_messages
         WHERE id = ? AND user_id = ? AND sender = 'user'"
    );

    mysqli_stmt_bind_param(
        $stmt,
        "ii",
        $message_id,
        $user_id
    );

    mysqli_stmt_execute($stmt);

    mysqli_stmt_close($stmt);


    // Delete audio file if it was a voice message
    if (!empty($audio_path)) {

        $full_audio_path = __DIR__ . "/" . $audio_path;

        if (file_exists($full_audio_path)) {
            unlink($full_audio_path);
        }
    }


    header("Location: chat.php");
    exit();
}


// ======================================
// SEND TEXT MESSAGE
// ======================================

elseif ($_SERVER["REQUEST_METHOD"] === "POST") {

    $message = trim($_POST["message"] ?? "");

    if ($message !== "") {

        // Save user message
        $stmt = mysqli_prepare(
            $conn,
            "INSERT INTO chat_messages
             (user_id, sender, message, message_type)
             VALUES (?, 'user', ?, 'text')"
        );

        mysqli_stmt_bind_param(
            $stmt,
            "is",
            $user_id,
            $message
        );

        mysqli_stmt_execute($stmt);

        mysqli_stmt_close($stmt);


        // Temporary assistant response
        $assistant_message = "I received your message. 😊";

        $stmt = mysqli_prepare(
            $conn,
            "INSERT INTO chat_messages
             (user_id, sender, message, message_type)
             VALUES (?, 'assistant', ?, 'text')"
        );

        mysqli_stmt_bind_param(
            $stmt,
            "is",
            $user_id,
            $assistant_message
        );

        mysqli_stmt_execute($stmt);

        mysqli_stmt_close($stmt);
    }


    // Prevent duplicate submission when refreshing
    header("Location: chat.php");
    exit();
}


// ======================================
// GET CHAT MESSAGES
// ======================================

$message_query = mysqli_prepare(
    $conn,
    "SELECT id, sender, message, created_at, message_type, audio_path
     FROM chat_messages
     WHERE user_id = ?
     ORDER BY created_at ASC, id ASC"
);

mysqli_stmt_bind_param(
    $message_query,
    "i",
    $user_id
);

mysqli_stmt_execute($message_query);

$message_result = mysqli_stmt_get_result($message_query);

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>FINORA Chat</title>

    <link rel="stylesheet" href="css/dashboard.css">

    <link rel="stylesheet" href="css/chat.css">

</head>


<body>


<?php include("includes/dashboard_navbar.php"); ?>


<div class="chat-container">


    <!-- CHAT HEADER -->

    <div class="chat-header">

        <div class="chat-avatar">
            💬
        </div>

        <div>

            <h2>FINORA Assistant</h2>

            <p>
                Your personal finance assistant
            </p>

        </div>

    </div>



    <!-- CHAT MESSAGES -->

    <div class="chat-messages" id="chatMessages">

        <?php

        $previous_date = "";

        if (mysqli_num_rows($message_result) > 0) {

            while ($chat = mysqli_fetch_assoc($message_result)) {

                $message_date = date(
                    "Y-m-d",
                    strtotime($chat["created_at"])
                );


                // Date separator
                if ($message_date !== $previous_date) {

                    echo '<div class="chat-date">';

                    echo date(
                        "d F Y",
                        strtotime($chat["created_at"])
                    );

                    echo '</div>';

                    $previous_date = $message_date;
                }


                // ==================================
                // USER MESSAGE
                // ==================================

                if ($chat["sender"] === "user") {

        ?>

                    <div class="message user-message">

                        <div class="message-content">


                            <?php if ($chat["message_type"] === "voice") { ?>

                                <!-- VOICE MESSAGE -->

                                <div class="voice-message">

                                    <span class="voice-message-icon">
                                        🎙️
                                    </span>

                                    <audio
                                        controls
                                        preload="metadata"
                                        src="<?php echo htmlspecialchars($chat["audio_path"]); ?>"
                                    ></audio>

                                </div>

                            <?php } else { ?>

                                <!-- TEXT MESSAGE -->

                                <p>
                                    <?php
                                    echo htmlspecialchars(
                                        $chat["message"]
                                    );
                                    ?>
                                </p>

                            <?php } ?>


                            <span class="message-time">

                                <?php
                                echo date(
                                    "g:i A",
                                    strtotime($chat["created_at"])
                                );
                                ?>

                            </span>


                            <!-- DELETE MESSAGE -->

                            <form
                                method="POST"
                                class="delete-message-form"
                            >

                                <button
                                    type="submit"
                                    name="delete_message"
                                    value="<?php echo $chat["id"]; ?>"
                                    class="delete-message-btn"
                                    title="Delete message"
                                    onclick="return confirm('Delete this message?');"
                                >
                                    🗑️
                                </button>

                            </form>


                        </div>

                    </div>


        <?php

                }


                // ==================================
                // ASSISTANT MESSAGE
                // ==================================

                else {

        ?>

                    <div class="message assistant-message">

                        <div class="message-icon">
                            🤖
                        </div>

                        <div class="message-content">

                            <?php if ($chat["message_type"] === "voice") { ?>

                                <div class="voice-message">

                                    <span class="voice-message-icon">
                                        🎙️
                                    </span>

                                    <audio
                                        controls
                                        preload="metadata"
                                        src="<?php echo htmlspecialchars($chat["audio_path"]); ?>"
                                    ></audio>

                                </div>

                            <?php } else { ?>

                                <p>
                                    <?php
                                    echo htmlspecialchars(
                                        $chat["message"]
                                    );
                                    ?>
                                </p>

                            <?php } ?>


                            <span class="message-time">

                                <?php
                                echo date(
                                    "g:i A",
                                    strtotime($chat["created_at"])
                                );
                                ?>

                            </span>

                        </div>

                    </div>

        <?php

                }

            }

        } else {

        ?>

            <!-- FIRST CHAT MESSAGE -->

            <div class="message assistant-message">

                <div class="message-icon">
                    🤖
                </div>

                <div class="message-content">

                    <p>
                        Hi! 👋 I'm your FINORA Assistant.
                    </p>

                    <p>
                        You can tell me about a transaction,
                        or use the microphone 🎤 when you're busy.
                    </p>

                    <span class="message-time">

                        <?php echo date("g:i A"); ?>

                    </span>

                </div>

            </div>

        <?php } ?>

    </div>



    <!-- ==================================
         CHAT INPUT
    ================================== -->

    <form
        method="POST"
        class="chat-input-area"
        id="chatForm"
    >

        <button
            type="button"
            class="voice-btn"
            id="voiceBtn"
            title="Voice message"
        >
            🎤
        </button>


        <input
            type="text"
            name="message"
            class="chat-input"
            id="chatInput"
            placeholder="Type a message..."
            autocomplete="off"
        >


        <button
            type="submit"
            class="send-btn"
        >
            ➤
        </button>

    </form>


</div>


<!-- ==================================
     VOICE RECORDING JAVASCRIPT
================================== -->

<script>

let mediaRecorder = null;
let audioChunks = [];
let audioStream = null;

let recordingTimer = null;
let recordingSeconds = 0;

const voiceBtn = document.getElementById("voiceBtn");
const chatInput = document.getElementById("chatInput");
const chatForm = document.getElementById("chatForm");


// ======================================
// FIND SUPPORTED AUDIO FORMAT
// ======================================

function getSupportedMimeType() {

    const formats = [
        "audio/webm;codecs=opus",
        "audio/webm",
        "audio/mp4",
        "audio/ogg;codecs=opus"
    ];

    for (const format of formats) {

        if (
            typeof MediaRecorder !== "undefined" &&
            MediaRecorder.isTypeSupported(format)
        ) {
            return format;
        }
    }

    return "";
}


// ======================================
// START RECORDING
// ======================================

async function startRecording() {

    try {

        if (
            !navigator.mediaDevices ||
            !navigator.mediaDevices.getUserMedia
        ) {

            alert(
                "Microphone recording is not supported by this browser."
            );

            return;
        }


        // Ask for microphone permission
        audioStream =
            await navigator.mediaDevices.getUserMedia({
                audio: true
            });


        const mimeType = getSupportedMimeType();


        if (mimeType) {

            mediaRecorder =
                new MediaRecorder(
                    audioStream,
                    {
                        mimeType: mimeType
                    }
                );

        } else {

            mediaRecorder =
                new MediaRecorder(audioStream);
        }


        audioChunks = [];


        // Collect audio data
        mediaRecorder.ondataavailable = function(event) {

            if (event.data && event.data.size > 0) {

                audioChunks.push(event.data);
            }
        };


        // Recording stopped
        mediaRecorder.onstop = function() {

            const actualType =
                mediaRecorder.mimeType ||
                mimeType ||
                "audio/webm";

            const audioBlob =
                new Blob(
                    audioChunks,
                    {
                        type: actualType
                    }
                );


            uploadVoiceMessage(audioBlob);


            // Stop microphone
            if (audioStream) {

                audioStream
                    .getTracks()
                    .forEach(track => track.stop());

                audioStream = null;
            }
        };


        mediaRecorder.onerror = function() {

            stopMicrophone();

            resetRecordingButton();

            alert(
                "Something went wrong while recording."
            );
        };


        // Start
        mediaRecorder.start();


        // Change button
        voiceBtn.innerHTML = "⏹️";

        voiceBtn.classList.add("recording");

        voiceBtn.title = "Stop recording";


        // Start timer
        recordingSeconds = 0;

        recordingTimer =
            setInterval(function() {

                recordingSeconds++;

                voiceBtn.innerHTML =
                    "⏹️ " +
                    formatTime(recordingSeconds);

            }, 1000);


    } catch (error) {

        console.error(error);

        stopMicrophone();

        resetRecordingButton();


        if (error.name === "NotAllowedError") {

            alert(
                "Microphone permission was denied. Please allow microphone access for FINORA."
            );

        } else {

            alert(
                "Unable to access your microphone."
            );
        }
    }
}


// ======================================
// STOP RECORDING
// ======================================

function stopRecording() {

    if (
        mediaRecorder &&
        mediaRecorder.state === "recording"
    ) {

        mediaRecorder.stop();
    }

    stopMicrophone();

    clearInterval(recordingTimer);

    recordingTimer = null;

    resetRecordingButton();
}


// ======================================
// STOP MICROPHONE
// ======================================

function stopMicrophone() {

    if (audioStream) {

        audioStream
            .getTracks()
            .forEach(track => track.stop());

        audioStream = null;
    }
}


// ======================================
// RESET BUTTON
// ======================================

function resetRecordingButton() {

    voiceBtn.innerHTML = "🎤";

    voiceBtn.classList.remove("recording");

    voiceBtn.title = "Voice message";
}


// ======================================
// TIMER FORMAT
// ======================================

function formatTime(seconds) {

    const minutes =
        Math.floor(seconds / 60);

    const secs =
        seconds % 60;

    return (
        String(minutes).padStart(2, "0") +
        ":" +
        String(secs).padStart(2, "0")
    );
}


// ======================================
// UPLOAD VOICE MESSAGE
// ======================================

async function uploadVoiceMessage(audioBlob) {

    if (!audioBlob || audioBlob.size === 0) {

        alert("No audio was recorded.");

        return;
    }

    const formData = new FormData();

    formData.append(
        "voice",
        audioBlob,
        "voice_message.webm"
    );

    formData.append(
        "mime_type",
        audioBlob.type
    );

    voiceBtn.disabled = true;

    try {

        const response = await fetch(
            "voice_upload.php",
            {
                method: "POST",
                body: formData
            }
        );

        const result = await response.text();

        console.log("HTTP Status:", response.status);
        console.log("Server Response:", result);

        if (
            response.ok &&
            result.trim() === "success"
        ) {

            alert("Voice message saved successfully! 🎙️");

            window.location.href = "chat.php";

        } else {

            alert(
                "VOICE UPLOAD ERROR\n\n" +
                "HTTP Status: " +
                response.status +
                "\n\n" +
                "Server Response:\n" +
                result
            );
        }

    } catch (error) {

        console.error(error);

        alert(
            "NETWORK ERROR\n\n" +
            error.message
        );

    } finally {

        voiceBtn.disabled = false;
    }
}

// ======================================
// VOICE BUTTON
// ======================================

voiceBtn.addEventListener(
    "click",
    function() {

        if (
            mediaRecorder &&
            mediaRecorder.state === "recording"
        ) {

            stopRecording();

        } else {

            startRecording();
        }

    }
);


// ======================================
// TEXT MESSAGE
// ======================================

chatForm.addEventListener(
    "submit",
    function(event) {

        // If currently recording,
        // stop recording instead of sending text.
        if (
            mediaRecorder &&
            mediaRecorder.state === "recording"
        ) {

            event.preventDefault();

            stopRecording();

            return;
        }


        // Allow normal PHP form submission
        // for text messages.
    }
);


// ======================================
// KEEP CHAT SCROLLED TO BOTTOM
// ======================================

const chatMessages =
    document.getElementById("chatMessages");

if (chatMessages) {

    chatMessages.scrollTop =
        chatMessages.scrollHeight;
}

</script>


</body>

</html>