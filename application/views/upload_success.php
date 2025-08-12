<?php $escaped_video_path = htmlspecialchars($video_path ?? '', ENT_QUOTES); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Video</title>
  <link rel="stylesheet" href="https://cdn.plyr.io/3.7.8/plyr.css" />
  <script src="https://cdn.plyr.io/3.7.8/plyr.polyfilled.js"></script>
  <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/fix-webm-duration@2.0.0/dist/browser.js"></script>
  <style>
    body {
         font-family: sans-serif;
          background: #f0f0f0;
           text-align: center;
            padding: 30px; }
    .video- {
      position: relative;
      width: 100%;
      padding-top: 56.25%; 
      background-color: #000;
    }

    .video-container {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      object-fit: cover; 
      border-radius: 10px;
    }

    @media (orientation: portrait) {
      .video-wrapper {
        padding-top: 75%; 
      }
    }

    @media (orientation: landscape) {
      .video-wrapper {
        padding-top: 56.25%; 
      }
    }
    .video-container {
         position: relative;
         width: 100%;
          max-width: 800px;
           margin: auto; }
    video {
  max-height: 80vh;
  width: 100%;
  height: auto;
  display: block;
  margin: auto;
  background: black;
  object-fit: contain;
}
@media (max-width: 768px) {
  video {
    max-height: 70vh;
    height: auto;
    width: 100%;
    object-fit: contain;  /* a bit less height on small screens */
  }
}

@media (max-width: 480px) {
  video {
   max-height: 90vh; /* Increased to better use vertical space */
    width: 100vw;
    height: auto;
    object-fit: contain;/* even less on very small mobiles */
  }
}
@media (max-width: 360px) {
  video {
    max-height: 95vh;
    width: 100vw;
    height: auto;
    object-fit: contain;
  }
}
    .plyr__tooltip { 
        display: none !important; }
    .custom-tooltip {
      position: absolute;
       background: white; 
       color: #333; 
       padding: 4px 8px;
      font-size: 13px;
       border-radius: 4px; 
       box-shadow: 0 2px 6px rgba(0,0,0,0.2);
      pointer-events: none;
       transform: translate(-50%, -150%);
        white-space: nowrap;
      display: none; z-index: 10;
    }
    .video-controls-overlay {
      position: absolute;
       top: 50%;
        left: 0; 
        right: 0;
      display: flex; 
      justify-content: space-between;
      transform: translateY(-50%);
       pointer-events: none;
        padding: 0 20px;
    }
    .video-controls-overlay button {
      pointer-events: all; 
    }
.my-custom-controls {
  display: flex;
  justify-content: center;
  gap: 10px;
  margin-top: 5px;
}
.custom-plyr-button {
  /* padding: 6px 12px; */
  /* font-size: 14px; */
  /* background: #007bff; */
  /* color: white; */
  border: none;
  /* border-radius: 4px; */
  cursor: pointer;
}

.my-custom-controls svg {
  vertical-align: middle;
  margin-right: 4px;
}
.video-controls-overlay {
  position: absolute;
  top: 50%;
  left: 1000;
  right: 0;
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0 30px;
  transform: translateY(-50%);
  z-index: 10;
  opacity: 0; /* hide by default */
  /* transition: opacity 0.3s ease; */
  pointer-events: none; /* block interaction while hidden */
}

.video-controls-overlay img {
  width: 48px;
  height: 48px;
  cursor: pointer;
  pointer-events: all; /* allow clicking */
  opacity: 0.8;
  transition: transform 0.2s ease, opacity 0.2s ease;
}

.video-controls-overlay img:hover {
  transform: scale(1.2);
  opacity: 1;
}

.video-container:hover .video-controls-overlay {
  opacity: 1;
  pointer-events: all;
}
.video-wrapper {
  position: relative;
  width: 100%;
  max-width: 800px;
  margin: auto;
  aspect-ratio: 16 / 9; /* fallback aspect */
  background: #000;
  border-radius: 10px;
  overflow: hidden;
}

.video-wrapper video {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  object-fit: contain;
}
.blinking-stop {
  width: 32px;
  height: 32px;
  animation: blink 1s infinite ease-in-out;
  will-change: opacity;
  backface-visibility: hidden;
}

@keyframes blink {
  0%, 100% { opacity: 1; }
  50% { opacity: 0; }
}

</style>
</head>
<body>

  <h2> Video Uploaded Successfully</h2>
  <p>Now playing your uploaded video:</p>

<!-- <div class="video-container">
  <video id="player" playsinline controls>
    <source src="<?= $escaped_video_path ?>" type="video/mp4">
  </video>
</div> -->
<div class="video-wrapper">
<div class="video-container">
  <video id="player" playsinline controls>
    <source src="<?= $escaped_video_path ?>" type="video/mp4">
  </video>
  
<span id="recording-timer"
      style="
        position: absolute;
        bottom: 50px;
        right: 20px;
        background: red;
        color: white;
        font-size: 12px;
        border-radius: 50%;
        min-width: 40px;
        height: 38px;
        line-height: 38px;
        text-align: center;
        display: none;
        z-index: 999;
      ">
  00:00
</span>
  <div class="video-controls-overlay">
    <img id="customBackward" 
         src="https://img.icons8.com/ios-filled/24/ffffff/reply-arrow.png" 
         alt="Back 10s"
         title="Back 10 seconds" />

    <img id="customForward" 
         src="https://img.icons8.com/ios-filled/24/ffffff/forward-arrow.png" 
         alt="Forward 10s"
         title="Forward 10 seconds" />
  </div>
</div>

    
</div>


  </div>

  <script>
  const player = new Plyr('#player', {
    controls: [ 'play','progress','current-time', 'duration', 'mute', 'volume', 'fullscreen'],
    tooltips: { controls: true } 
  });
  function formatTime(seconds) {
    var minutes = Math.floor(seconds / 60);
    var secs = Math.floor(seconds % 60);

    if (minutes < 10) {
      minutes = '0' + minutes;
    }
    if (secs < 10) {
      secs = '0' + secs;
    }

    return '-' + minutes + ':' + secs;
  }
  player.on('ready', function () {
    var progressBar = player.elements.container.querySelector('.plyr__progress');
    var tooltip = document.createElement('div');
    tooltip.className = 'custom-tooltip';
    progressBar.style.position = 'relative';
    progressBar.appendChild(tooltip);
    progressBar.addEventListener('mousemove', function (event) {
      if (!player.duration) return;
      var barRect = progressBar.getBoundingClientRect();
      var mouseX = event.clientX;
      var percent = (mouseX - barRect.left) / barRect.width;
      if (percent < 0) percent = 0;
      if (percent > 1) percent = 1;

      var timeHovered = percent * player.duration;
      var timeLeft = player.duration - timeHovered;

      tooltip.textContent = formatTime(timeLeft);
      tooltip.style.left = (percent * 100) + '%';
      tooltip.style.display = 'block';
    });

    progressBar.addEventListener('mouseleave', function () {
      tooltip.style.display = 'none';
    });
  const controlsContainer = player.elements.controls;
  const customControls = document.createElement("div");
  customControls.className = "my-custom-controls";
customControls.innerHTML = `
  <img id="customStop"
     src="https://img.icons8.com/ios-filled/48/fa314a/stop.png"
     alt="Stop"
     title="Stop"
     class="blinking-stop"
     style="cursor: pointer; margin: 0 10px;" />
       <a href="<?= $video_path ?>" download title="Download Video">
    <img 
      src="https://img.icons8.com/ios-filled/24/ffffff/download--v1.png" 
      alt="Download"
      style="cursor: pointer; margin: 0 10px;" />
  </a>
  <img id="customRecord"
     src="https://img.icons8.com/flat-round/24/record.png"
     alt="Record"
     title="Record"
     style="cursor: pointer; margin: 0 10px;" />

`;


progressBar.parentNode.insertBefore(customControls, progressBar.nextSibling);
const downloadBtn = document.createElement("a");
downloadBtn.href = "<?= $video_path ?>";
downloadBtn.download = "";
downloadBtn.className = "download-button";

customControls.parentNode.insertBefore(downloadBtn, customControls.nextSibling);


//   const startBtn = customControls.querySelector("#customStart");
// startBtn.addEventListener("click", function () {
//   player.play(); 
// });
const stopBtn = customControls.querySelector("#customStop");
stopBtn.addEventListener("click", function () {
  player.pause();
});
const forwardBtn = document.getElementById("customForward");
const backwardBtn = document.getElementById("customBackward");

forwardBtn.addEventListener("click", function () {
  player.currentTime += 10;
});

backwardBtn.addEventListener("click", function () {
  player.currentTime -= 10;
});



  });
</script>
<script>
  // function adjustAspectRatio() {
  //   const container = document.querySelector('.video-container');
  //   if (window.innerHeight > window.innerWidth) {
  //     container.style.aspectRatio = '9 / 16'; 
  //   } else {
  //     container.style.aspectRatio = '16 / 9';
  //   }
  // }
//   function adjustOrientation() {
//   const container = document.querySelector('.video-container');
//   if (!container) return;
//   if (window.screen.orientation) {
//     const angle = window.screen.orientation.angle;

//     if (angle === 90 || angle === 270) {
//       // Landscape
//       container.style.aspectRatio = '16 / 9';
//     } else {
//       // Portrait or Upside-down
//       container.style.aspectRatio = '9 / 16';
//     }
//   } else {
//     // Fallback using screen dimensions
//     if (window.innerHeight > window.innerWidth) {
//       container.style.aspectRatio = '9 / 16';
//     } else {
//       container.style.aspectRatio = '16 / 9';
//     }
//   }
// }

//   window.addEventListener('resize', adjustOrientation);
//   window.addEventListener('orientationchange', adjustOrientation);
//   document.addEventListener('DOMContentLoaded', adjustOrientation);
// </script>
<script>
let recorder, recordedChunks = [], canvasStream;
let recording = false, recordStartTime = 0, recordInterval;

const recordButton = document.getElementById("customRecord");
const videoElement = document.getElementById("player");
const recordingTimer = document.getElementById("recording-timer");

recordButton.addEventListener("click", () => recording ? stopRecording() : startRecording());

function startRecording() {
  const canvas = Object.assign(document.createElement("canvas"), {
    width: videoElement.videoWidth,
    height: videoElement.videoHeight
  });
  const ctx = canvas.getContext("2d");

  let frameDrawn = false;

  function draw() {
    if (recording && !videoElement.paused && !videoElement.ended) {
      ctx.drawImage(videoElement, 0, 0, canvas.width, canvas.height);
      if (!frameDrawn) {
        frameDrawn = true;
        const mp4Mime = 'video/mp4; codecs="avc1.42E01E"';
        const webmMime = 'video/webm; codecs=vp8,opus';
        const preferredMimeType = MediaRecorder.isTypeSupported(mp4Mime) ? mp4Mime : webmMime;
        // Now that at least one frame is drawn, start the recorder
        recorder = new MediaRecorder(canvas.captureStream(30), {
          mimeType: preferredMimeType
        });
        recordedChunks = [];

        recorder.ondataavailable = e => {
          if (e.data.size > 0) recordedChunks.push(e.data);
        };
        recorder.onstop = () => {
          const blob = new Blob(recordedChunks, { type: recorder.mimeType });
          const url = URL.createObjectURL(blob);
          const extension = recorder.mimeType.includes("mp4") ? "mp4" : "webm";
          const a = Object.assign(document.createElement("a"), {
            href: url,
            download: `recorded-video.${extension}`
          });
          a.click();
          URL.revokeObjectURL(url);
        };

        recorder.start();
        recordStartTime = Date.now();
        updateTimer();
        recordInterval = setInterval(updateTimer, 1000);
        recordingTimer.style.display = "block";
        recordButton.title = "Stop Recording";
      }
      requestAnimationFrame(draw);
    }
  }

  if (videoElement.paused) videoElement.play();
  recording = true;
  draw();
}


function stopRecording() {
  if (recorder?.state === "recording") recorder.stop();
  recording = false;
  recordButton.title = "Record";
  clearInterval(recordInterval);
  recordingTimer.style.display = "none";
  recordingTimer.textContent = "00:00";
}

function updateTimer() {
  const t = Math.floor((Date.now() - recordStartTime) / 1000);
  recordingTimer.textContent = `${t/60|0}`.padStart(2,"0")+":"+(t%60).toString().padStart(2,"0");
}
</script>
<script>
  // Target the stop icon
  const stopIcon = document.getElementById("customStop");

  // On hover: stop blinking
  stopIcon.addEventListener("mouseenter", () => {
    stopIcon.style.animation = "none";
  });

  // On mouse leave: resume blinking
  stopIcon.addEventListener("mouseleave", () => {
    stopIcon.style.animation = "blink 1s infinite ease-in-out";
  });
</script>

</body>
</html>
