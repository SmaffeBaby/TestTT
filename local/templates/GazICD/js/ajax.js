document.addEventListener("DOMContentLoaded", function () {
    let navLinks = document.querySelectorAll(".navigation-text a");
    let currentUrl = window.location.pathname;

    navLinks.forEach(function (link) {
        if (link.getAttribute("href") === currentUrl) {
            link.classList.add("active");
        }
    });
});
