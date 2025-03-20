document.addEventListener('DOMContentLoaded', function() {
    const imageData = document.getElementById('imageInput').value;
    if (imageData) {
        const img = document.createElement('img');
        img.src = 'data:image/jpeg;base64,' + imageData;
        img.id = 'image-result';
        img.style.width = '320px';
        img.style.height = '320px';
        img.style.borderRadius = '100%';

        videoWrapper.style.display = 'none';
        videoWrapper.parentNode.insertBefore(img, videoWrapper.nextSibling);

        // Show reset button and hide capture button
        document.getElementById('capture-button').style.display = 'none';
        document.getElementById('reset-button').style.display = 'inline-block';
    }
});

    let capturedImage;
    const video = document.getElementById('video');
    const canvas = document.getElementById('canvas');
    const imageInput = document.getElementById('imageInput');
    const videoWrapper = document.getElementsByClassName('video-wrapper')[0];
    const constraints = {
        video: true
    };

// Access the webcam and stream the video to the video element
navigator.mediaDevices.getUserMedia(constraints)
    .then((stream) => {
        video.srcObject = stream;
        video.onloadedmetadata = () => {
            // Set the canvas size to match the video aspect ratio
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
        };
    })
    .catch((err) => {
        alert('Can\'t access camera');
        console.error('Error accessing the camera: ', err);
    });

    function takeSnapshot() {
        if (capturedImage) {
            capturedImage.remove();
            capturedImage = null;
        }
        const context = canvas.getContext('2d');
        context.drawImage(video, 0, 0, canvas.width, canvas.height);
        const dataURL = canvas.toDataURL('image/jpeg');
        const base64String = dataURL.split(',')[1];
    
        const img = document.createElement('img');
        img.src = dataURL;
        img.id = 'image-result';
        img.style.width = '320px';
        img.style.height = '320px';
        img.style.borderRadius = '100%';
        capturedImage = img;
        document.getElementById('reset-button').style.display = 'inline-block';

    
        video.style.display = 'none';
        canvas.style.display = 'none';
        document.getElementById('capture-button').style.display = 'none';

        video.parentNode.insertBefore(img, video);
    
        // Set the base64 string to the hidden input
        imageInput.value = base64String;
    }

    function removeSnapshot() {
        if (capturedImage) {
            capturedImage.remove();
            capturedImage = null;
        }
    
        // Show video for retaking the snapshot
        video.style.display = 'block';
        canvas.style.display = 'none';
        imageInput.value = '';
        document.getElementById('capture-button').style.display = 'inline-block';
        document.getElementById('reset-button').style.display = 'none';

    
        // Show the capture button again
        videoWrapper.style.display = 'block';
        const imgResult = document.getElementById('image-result');
        if(imgResult){
            imgResult.remove();
            imgResult = null;
        }
    }

    function prepareImage(event) {
        if (!imageInput.value) {
            event.preventDefault();
            alert('Please take a snapshot to set the user image.');
        }
        // Include additional validation or processing before form submission if needed
    }

