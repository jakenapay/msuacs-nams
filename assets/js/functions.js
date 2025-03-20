iziToast.settings({
    timeout: 2000,
    icon: "fa fa-info",
    animateInside: true,
    titleColor: "#f5f5f5",
    messageColor: "#f5f5f5",
    iconColor: "#f5f5f5",
    transitionIn: "fadeInLeft",
    transitionOut: "fadeOutLeft",
    position: "topLeft",
    displayMode: "replace",
    layout: 1,
    close: false
});

function refresh(duration) {
    return setTimeout(function() {
        window.location.replace(site_url + 'admin/dashboard');
    }, duration);
}

function primary(message, icon) {
    return iziToast.info({
        class: "nams-toast",
        backgroundColor: "linear-gradient(30deg, rgb(16, 107, 181), rgb(79, 193, 238))",
        message: message,
        icon: icon
    });
}

function success(message, icon, callback = null) {
    return iziToast.success({
        class: "nams-toast",
        backgroundColor: "linear-gradient(30deg, rgb(74, 117, 16), rgb(51, 199, 55))",
        message: message,
        icon: icon
    });
}

function warning(message, icon) {
    return iziToast.warning({
        class: "nams-toast",
        backgroundColor: "linear-gradient(30deg, rgb(179, 103, 24), rgb(243, 168, 32))",
        message: message,
        icon: icon
    });
}

function danger(message, icon) {
    return iziToast.error({
        title: "Error",
        class: "nams-toast",
        backgroundColor: "linear-gradient(30deg, rgb(197, 23, 23), rgb(255, 125, 33))",
        message: message,
        icon: icon
    });
}
