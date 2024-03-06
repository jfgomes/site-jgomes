// Function to set cookie consent and hide the bar
function acceptCookies() {
    // Set a cookie (you may want to use a more sophisticated method)
    document.cookie = "cookieConsent=accepted; expires=Fri, 31 Dec 9999 23:59:59 GMT; path=/";
    // Hide the cookie consent bar
    $("#cookie-consent-bar").hide();
}

window.onload = function () {
    // Check if the cookie consent was done
    if (document.cookie.indexOf("cookieConsent=accepted") === -1) {
        $("#cookie-consent-bar").show();
    } else{
        $("#cookie-consent-bar").hide();
    }
};
