//  Set the Cookie
function setCookie(cname, cvalue, exdays) {
    var d = new Date();
    d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
    var expires = "expires=" + d.toUTCString();
    document.cookie = cname + "=" + cvalue + "; " + expires;
}

// Get the Cookie
function getCookie(cname) {
    var name = cname + "=";
    var ca   = document.cookie.split(';');
    for (var i = 0; i < ca.length; i++) {
        var  c = ca[i];
        while (c.charAt(0) == ' ') {
            c = c.substring(1);
        }
        if ( c.indexOf(name) == 0) {
            return c.substring(name.length, c.length);
        }
    }
    return "";
}

// Close the modal
$('#a2hs-toast-close').on('click', function(event) {
        event.preventDefault();
        $("#a2hs-toast").hide();
        setCookie("a2hs-msg-closed", "true", 200);
});

window.addEventListener('appinstalled', (evt) => {
    $("#a2hs-toast").hide()
    setCookie("a2hs-msg-closed", "true", 200);
});


let deferredPrompt;

window.addEventListener('beforeinstallprompt', (e) => {
    // Prevent Chrome 67 and earlier from automatically showing the prompt
    e.preventDefault();
    // Stash the event so it can be triggered later.
    deferredPrompt = e;
});

$('#a2hs-an-click').on('click', (e) => {
    // hide our user interface that shows our A2HS button
    //$('#a2hs-toast').removeClass("show")
    //$('#a2hs-toast').addClass("hide")
    // Show the prompt
    deferredPrompt.prompt();
    // Wait for the user to respond to the prompt
    deferredPrompt.userChoice.then((choiceResult) => {
        if (choiceResult.outcome === 'accepted') {
            console.log('User accepted the A2HS prompt');
        } else {
            console.log('User dismissed the A2HS prompt');
        }
        deferredPrompt = null;
    });
});


window.addEventListener('DOMContentLoaded', () => {
    let isMobile = false; //initiate as false
    let isIOS = false; //initiate as false

    // device detection
    if(/android|webos|iphone|ipad|ipod|blackberry|iemobile|opera mini/i.test(navigator.userAgent.toLowerCase())) {
        isMobile = true;
        if(/iphone|ipad|ipod/i.test(navigator.userAgent.toLowerCase()))
            isIOS = true;
    }

    function showMessage() {
        setTimeout(
            function() {
                $('#a2hs-toast').removeClass("hide")
                $('#a2hs-toast').addClass("show")
                setCookie("a2hs-msg-shown", "true", 200)
            }, 10000);
    }

    if(isMobile) {
        let standalone = false;
        if (navigator.standalone) standalone = true;
        if (window.matchMedia('(display-mode: standalone)').matches) standalone = true;
        if(standalone == false) {
            // Get the cookie called "a2hs-msg-closed"
            let closedMessage = getCookie("a2hs-msg-closed");
            let shownOnce = getCookie("a2hs-msg-shown");

            $("#a2hs-ios").hide();
            $("#a2hs-android").hide();
            if(closedMessage != "true") {
                if(isIOS) $("#a2hs-ios").show();
                else $("#a2hs-android").show();
                if(shownOnce != "true") showMessage()
                else {
                    $('#a2hs-toast').removeClass("hide")
                    $('#a2hs-toast').addClass("show")
                }
            }
        }
    }
});
