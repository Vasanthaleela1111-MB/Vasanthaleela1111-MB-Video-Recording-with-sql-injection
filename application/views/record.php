<!DOCTYPE html>
<html>
<head>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <title>Record and Upload</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f3f4f6;
            padding: 0;
            margin: 0;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        header {
            background-color: #2563eb;
            color: white;
            padding: 15px;
            text-align: center;
            font-size: 20px;
            font-weight: bold;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        main {
            flex: 1;
            padding: 30px;
            text-align: center;
        }

        h2 {
            color: #333;
            margin-bottom: 20px;
        }

        button {
            background-color: #2563eb;
            color: white;
            border: none;
            padding: 10px 20px;
            margin: 10px 5px;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        button:hover {
            background-color: #1d4ed8;
        }

        button:disabled {
            background-color: #9ca3af;
            cursor: not-allowed;
        }

        video {
            margin-top: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            display: block;
    margin-left: auto;
    margin-right: auto;
        }

        #playback {
            margin-top: 30px;
        }

        footer {
            background-color: #111827;
            color: white;
            text-align: center;
            padding: 15px;
            font-size: 14px;
        }

        footer a {
            color: #93c5fd;
            text-decoration: none;
            margin: 0 10px;
        }

        footer a:hover {
            text-decoration: underline;
        }
@media screen and (max-width: 768px) and (orientation: portrait) {
    header {
        font-size: 16px;
        padding: 10px;
    }

    main {
        padding: 15px;
    }

    button {
        padding: 8px 15px;
        font-size: 14px;
    }

    video {
        width: 100%;
        height: auto;
    }
}
@media screen and (max-width: 768px) and (orientation: landscape) {
    header {
        font-size: 18px;
        padding: 8px;
    }

    main {
        padding: 10px;
    }

    button {
        padding: 8px 12px;
        font-size: 14px;
    }

    video {
        max-width: 80%;
        height: auto;
    }
}
@media screen and (max-width: 480px) {
    header {
        font-size: 14px;
    }

    button {
        padding: 6px 10px;
        font-size: 12px;
    }

    video {
        width: 100%;
    }
}

    </style>
</head>
<body>

<header>
  <div class="header-container" 
       style="display:flex; align-items:center; justify-content:space-between; padding: 10px 20px;  color:white;">
    
    <div class="logo" style="font-size:20px; font-weight:bold;">
      Video Verification Portal
    </div>

    <nav style="
    flex-grow:1; 
    display:flex; 
    justify-content:center; 
    gap:100px; 
">
    <a href="#" style="color:white; text-decoration:none; font-weight:bold;">Home</a>
    <a href="#" style="color:white; text-decoration:none; font-weight:bold;">Features</a>
    <a href="#" style="color:white; text-decoration:none; font-weight:bold;">Help</a>
</nav>
<div style="position: relative; display: inline-block;">
    <?php if (!empty($photo_url)) { ?>
        <img src="<?= htmlspecialchars($photo_url) ?>?v=<?= time() ?>"
             alt="Logo"
             id="profileImage"
             style="height:50px; width:auto; border-radius:5px;
                    box-shadow:0 2px 5px rgba(0,0,0,0.2);">

        <span id="editIcon"
              style="position:absolute; bottom:-5px; right:-5px; border-radius:50%; padding:3px;background:#000;
                     cursor:pointer;">
            <img src="https://img.icons8.com/lollipop/48/edit.png" alt="Edit" style="height:16px; width:16px;">
        </span>
    <?php } else { ?>
        <span style="color:#ccc;" id="profileImage">No Logo</span>
        <span id="editIcon"
              style="position:absolute; bottom:-5px; right:-5px;
                     background:#000; border-radius:50%; padding:3px;
                     cursor:pointer;">
            <img src="https://img.icons8.com/lollipop/24/edit.png" alt="Edit" style="height:16px; width:16px;">
        </span>
    <?php } ?>
    <input type="file" id="imageUpload" accept="image/*" style="display:none;">
</div>

<input type="file" id="imageUpload" accept="image/*" style="display:none;">
    </div>
  </div>
</header>

<main>
    <h2>🎥 Video Recorder</h2>

    <button id="startBtn">Start Recording</button>
    <button id="stopBtn" disabled>Stop Recording</button>
    <button id="playBtn" disabled>Verify Video</button>
  
    <br><br>
    <video id="preview" width="400" controls autoplay muted></video>
    <video id="playback" width="400" controls style="display:none;"></video>
    <div id="authSection" style="margin-top: 30px;"></div>
</main>

<footer>
    <a href="#">Privacy Policy</a> |
    <a href="#">Contact</a> |
    <a href="#">About Us</a>
</footer>

<div id="cropModal" style="
    display:none; 
    position:fixed; top:0; left:0; 
    width:100%; height:100%; 
    background:rgba(0,0,0,0.8); 
    align-items:center; justify-content:center;
    z-index:9999;
">
    <div style="background:white; padding:20px; border-radius:10px; max-width:90%; max-height:90%; overflow:auto;">
        <h3>Crop Image</h3>
        <img id="cropImage" style="max-width:100%;" />
        <br><br>
        <button id="cropConfirmBtn">Crop & Save</button>
        <button id="cropCancelBtn" style="background:red;">Cancel</button>
    </div>
</div>

<script>

    let currentRecordId = null;
const userMobile = '<?= $mobile ?>';

function saveUserVideo(videoFormData) {
    return fetch('https://18.189.144.42/dev1/sms/index.php/otp/save_user_video', {
        method: 'POST',
        body: videoFormData
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === 'wrong_code' && data.record_id) {
            currentRecordId = data.record_id;
            alert("Wrong code entered. Please upload your photo.");
        } else if (data.status === 'success') {
            alert("Code is valid, no photo upload needed.");
        } else {
            alert("Error: " + (data.message || "Unknown error"));
        }
        return data;
    });
}

const editIcon = document.getElementById('editIcon');
const imageUpload = document.getElementById('imageUpload');
const profileImage = document.getElementById('profileImage');

if (editIcon) {
    editIcon.addEventListener('click', () => {
        imageUpload.click();
    });
}
let cropper;

imageUpload.addEventListener('change', () => {
    const file = imageUpload.files[0];
    if (!file) return;

    const reader = new FileReader();
    reader.onload = function(e) {
        document.getElementById('cropImage').src = e.target.result;
        document.getElementById('cropModal').style.display = 'flex';

        setTimeout(() => {
            if (cropper) cropper.destroy();
            cropper = new Cropper(document.getElementById('cropImage'), {
                aspectRatio: 1,
                viewMode: 1
            });
        }, 100);
    };
    reader.readAsDataURL(file);
});
document.getElementById('cropCancelBtn').addEventListener('click', () => {
    document.getElementById('cropModal').style.display = 'none';
    if (cropper) cropper.destroy();
});

document.getElementById('cropConfirmBtn').addEventListener('click', () => {
    cropper.getCroppedCanvas({
        width: 200,
        height: 200
    }).toBlob((blob) => {
        document.getElementById('cropModal').style.display = 'none';
        if (cropper) cropper.destroy();

        const formData = new FormData();
        formData.append('photo', blob, 'cropped.png');
        formData.append('mobile', userMobile);
        if (currentRecordId) formData.append('record_id', currentRecordId);

        fetch('https://18.189.144.42/dev1/sms/index.php/otp/upload_photo', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success' && data.url) {
                currentRecordId = data.record_id; 
                // profileImage.src = data.url + '?v=' + Date.now();
                Swal.fire({
                    icon: 'success',
                    title: 'Profile Updated!',
                    text: 'Your profile photo has been successfully updated.',
                    timer: 2000,
                    showConfirmButton: true,
                    confirmButtonText: 'OK'
                }).then(() => {
        // Refresh image after popup closes
        profileImage.src = data.url + '?v=' + Date.now();
    });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Upload Failed',
                    text: data.message || "Unknown error",
                    confirmButtonText: 'OK'
                });
            }
        })
        .catch(err => {
            console.error("Error uploading image:", err);
            Swal.fire({
                icon: 'error',
                title: 'Upload Error',
                text: 'Something went wrong while uploading.',
                confirmButtonText: 'OK'
            });
        });
    }, 'image/png');
});


</script>

<script>
const userMobile = '<?= $mobile ?>';
</script>

<script>
let mediaRecorder;
let recordedChunks = [];
let videoBlobUrl = '';

const preview = document.getElementById('preview');
const playback = document.getElementById('playback');
const startBtn = document.getElementById('startBtn');
const stopBtn = document.getElementById('stopBtn');
const playBtn = document.getElementById('playBtn');

startBtn.onclick = async () => {
    try {
        const stream = await navigator.mediaDevices.getUserMedia({ video: true, audio: true });
        preview.srcObject = stream;
        preview.style.display = 'block';
        playback.style.display = 'none';
        
        mediaRecorder = new MediaRecorder(stream);
        recordedChunks = [];

        mediaRecorder.ondataavailable = function (e) {
            if (e.data.size > 0) recordedChunks.push(e.data);
        };

        mediaRecorder.onstop = function () {
            const blob = new Blob(recordedChunks, { type: 'video/webm' });
            videoBlobUrl = URL.createObjectURL(blob);
            playback.src = videoBlobUrl;

            const formData = new FormData();
            formData.append('video', blob, 'recording.webm');

            fetch('https://18.189.144.42/dev1/sms/index.php/otp/upload_video', {
            // fetch('http://localhost/sms/index.php/otp/upload_video', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json()) 
            .then(data => {
                if (data.status === 'success') {
                    console.log("Upload success. S3 URL:", data.url);
                    videoBlobUrl = data.url;
                } else {
                    console.error("Upload failed:", data.message || data);
                }
            })
            .catch(err => {
                console.error("Upload error:", err);
            });
        };

        mediaRecorder.start();
        startBtn.disabled = true;
        stopBtn.disabled = false;
        playBtn.disabled = true;
    } catch (err) {
        alert("Camera/mic permission denied or not available.");
        console.error(err);
    }
};

stopBtn.onclick = () => {
    mediaRecorder.stop();
    preview.srcObject.getTracks().forEach(track => track.stop());
    preview.srcObject = null;
    preview.style.display = 'none';

    startBtn.disabled = false;
    stopBtn.disabled = true;
    playBtn.disabled = false;
};

playBtn.onclick = () => {
    document.getElementById('authSection').innerHTML = `
        <button id="verifyBtn">Use Authenticator App</button>
    `;

    document.getElementById('verifyBtn').onclick = () => {
        fetch('https://18.189.144.42/dev1/sms/index.php/otp/generate_qr', {
        // fetch('http://localhost/sms/index.php/otp/generate_qr', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: new URLSearchParams()
        })
        .then(res => res.json())
        .then(qrData => {
            if (qrData.status === 'success') {
                const secret = qrData.secret;
                const qrCode = qrData.qr_code;

                document.getElementById('authSection').innerHTML = `
                    <p>Scan the QR below using Google Authenticator:</p>
                    <img src="${qrCode}" width="200"><br><br>
                    <input type="text" id="totp_code" placeholder="Enter 6-digit code"><br><br>
                    <button id="submitOtp">Submit & Play Video</button>
                `;
                document.getElementById('submitOtp').onclick = () => {
        const code = document.getElementById('totp_code').value;
        if (!code) return alert("Please enter the code!");

        // fetch('http://localhost/sms/index.php/otp/save_user_video', {
        fetch('https://18.189.144.42/dev1/sms/index.php/otp/save_user_video', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({
                mobile: userMobile,
                code: code,
                secret: secret,
                video_url: videoBlobUrl
            })
        })
        .then(res => res.text())
        .then(text => {
            try {
                const data = JSON.parse(text);
                if (data.status === 'success') {
                    Swal.fire({
            icon: 'success',
            title: 'Success!',
            text: 'OTP Verified. The video will play now.',
            showConfirmButton: true,
            confirmButtonText: 'OK'
        }).then(() => {
        playback.style.display = 'block';
        playback.play();
        document.getElementById('authSection').innerHTML = '';
        });
    } else if (data.status === 'wrong_code') {
        currentRecordId = data.record_id;  // <--- Save record_id here
        // alert("Invalid code — but video will still play.");
        Swal.fire({
            icon: 'error',
            title: 'Invalid Code',
            // text: 'But the video will still play.',
            text: 'Please try again.',
            // timer: 0000,
            showConfirmButton: true,
            confirmButtonText: 'OK'
        });
        playback.style.display = 'none';
    } else if (data.status === 'sql_injection_detected') {
    Swal.fire({
        icon: 'error',
        title: 'Security Alert',
        text: 'Suspicious input detected. The video will still play.',
        showConfirmButton: true,
        confirmButtonText: 'OK'
    }).then(() => {
        document.getElementById('authSection').innerHTML = '';
        playback.style.display = 'block';
        playback.muted = false;
        playback.play().catch(err => console.log('Playback error:', err));
    });
}

            } catch (err) {
                console.error("Invalid JSON from server:", text);
                alert("Server error occurred.");
            }
        })
        .catch(err => {
            console.error("Network error:", err);
            alert("Network error. Try again.");
        });
    };

                } else {
                    alert("QR Generation failed.");
                }
            });
        };
    };
</script>

</body>
</html>
