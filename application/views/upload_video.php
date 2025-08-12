<!DOCTYPE html>
<html>
<head>
  <title>Upload Video</title>
  <style>
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background-color: #f2f2f2;
      margin: 0;
      padding: 40px;
      display: flex;
      flex-direction: column;
      align-items: center;
    }

    h2 {
      color: #333;
      margin-bottom: 30px;
    }

    form {
      background-color: #fff;
      padding: 30px;
      border-radius: 10px;
      box-shadow: 0 0 15px rgba(0,0,0,0.1);
      width: 100%;
      max-width: 400px;
    }

    input[type="file"] {
      width: 100%;
      padding: 10px;
      margin-bottom: 20px;
      border: 1px solid #ccc;
      border-radius: 5px;
    }

    button {
      background-color: #28a745;
      color: white;
      padding: 12px 20px;
      border: none;
      border-radius: 5px;
      font-size: 16px;
      cursor: pointer;
      width: 100%;
    }

    button:hover {
      background-color: #218838;
    }

    #uploadProgress {
      width: 100%;
      display: none;
      margin-top: 10px;
      margin-bottom: 15px;
    }

    #progressText {
      text-align: center;
      display: none;
      margin-bottom: 10px;
      font-size: 14px;
      color: #555;
    }

    @media screen and (max-width: 500px) {
      body {
        padding: 20px;
      }

      form {
        padding: 20px;
      }
    }
  </style>
</head>
<body>

  <h2>Upload Your Video</h2>

  <form id="uploadForm" method="post" action="<?= site_url('otp/upload') ?>" enctype="multipart/form-data">

    <?php if (isset($error)) : ?>
      <div style="color:red; margin-bottom:10px;">
        <?= $error ?>
      </div>
    <?php endif; ?>

    <input type="file" name="video" accept="video/*" required />

    <!-- Progress bar (initially hidden) -->
    <progress id="uploadProgress" value="0" max="100"></progress>
    <div id="progressText"></div>

    <button type="submit">Upload</button>
  </form>

  <script>
    const form = document.getElementById('uploadForm');
    const progressBar = document.getElementById('uploadProgress');
    const progressText = document.getElementById('progressText');

    form.addEventListener('submit', function(event) {
      event.preventDefault(); 

      const fileInput = form.querySelector('input[type="file"]');
      const file = fileInput.files[0];
      if (!file) return;

      const xhr = new XMLHttpRequest();
      const formData = new FormData(form);

      xhr.open('POST', form.action, true);

      xhr.upload.onloadstart = function () {
        progressBar.style.display = 'block';
        progressText.style.display = 'block';
        progressBar.value = 0;
        progressText.textContent = 'Uploading... 0%';
      };

      xhr.upload.onprogress = function (e) {
        if (e.lengthComputable) {
          const percent = Math.round((e.loaded / e.total) * 100);
          progressBar.value = percent;
          progressText.textContent = `Uploading... ${percent}%`;
        }
      };
  xhr.send(formData);
    });
  </script>

</body>
</html>
