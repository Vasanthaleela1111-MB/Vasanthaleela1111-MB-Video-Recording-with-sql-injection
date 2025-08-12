<!DOCTYPE html>
<html>
<head>
    <title>OTP via SMS</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f2f2f2;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .otp-container {
            background: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            width: 300px;
            text-align: center;
        }

        h2 {
            margin-bottom: 20px;
            color: #333;
        }

        input[type="text"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 16px;
        }

        /* Hide the native file input */
        input[type="file"] {
            display: none;
        }

        /* Custom file upload button */
        .custom-file-upload {
            display: inline-block;
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 8px;
            background-color: #f8f8f8;
            cursor: pointer;
            font-size: 16px;
            color: #555;
            text-align: center;
            user-select: none;
            transition: background-color 0.3s ease;
        }

        .custom-file-upload:hover {
            background-color: #e2e2e2;
        }

        button {
            width: 100%;
            padding: 10px;
            background-color: #00ff5eff;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
        }

        /* Optional: button hover */
        button:hover {
            background-color: #00cc4f;
        }
    </style>
</head>
<body>

    <div class="otp-container">
        <h2>Enter Mobile Number</h2>
        <form method="post" action="<?= site_url('otp/send_otp') ?>" enctype="multipart/form-data">
            <input type="text" name="mobile" placeholder="Enter mobile number" required />

            <!-- Hidden file input -->
            <input type="file" id="file-upload" name="user_image" accept="image/*" required />

            <!-- Custom label that looks like a button -->
            <label for="file-upload" class="custom-file-upload">
                Choose Image
            </label>

            <button type="submit">Send OTP</button>
        </form>
    </div>

    <script>
        // Optional: show the selected file name on the label
        const fileUpload = document.getElementById('file-upload');
        const fileLabel = document.querySelector('.custom-file-upload');

        fileUpload.addEventListener('change', function() {
            if (this.files && this.files.length > 0) {
                fileLabel.textContent = this.files[0].name;
            } else {
                fileLabel.textContent = "Choose Image";
            }
        });
    </script>
</body>
</html>
