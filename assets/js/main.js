$(document).ready(function () {
    // Fade effect to flash messages
    $('.alert').fadeIn('slow', function () {
        // After fade-in, add a delay and then start fading out
        $(this).delay(3000).fadeOut('slow');
    });
});

//Loading on button when submitting
function displayLoader(){
    document.getElementById('loader').style.display = "inline-block";
    let hideLoader = () => {
        document.getElementById('loader').style.display = "none";
    }
    setTimeout(hideLoader, 1000);
}

function displayLoader2(){
    document.getElementById('loader2').style.display = "inline-block";
    let hideLoader = () => {
        document.getElementById('loader2').style.display = "none";
    }
    setTimeout(hideLoader, 1000);
}

function redirectToForm(){
    window.location.href = site_url + "form";
}

function redirectToExisting(){
    window.location.href = site_url + "form/status";
}

function redirectToHome(){
    window.location.href = site_url;
}

//Date format for export file names
function getFormattedDate() {
    const now = new Date();
    const year = now.getFullYear();
    const month = (now.getMonth() + 1).toString().padStart(2, '0');
    const day = now.getDate().toString().padStart(2, '0');
    const hours = now.getHours().toString().padStart(2, '0');
    const minutes = now.getMinutes().toString().padStart(2, '0');

    return `${year}-${month}-${day}-${hours}${minutes}`;
}