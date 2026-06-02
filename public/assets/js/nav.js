const navToggle = document.getElementById("nav-toggle");
const siteNav = document.getElementById("site-nav");

if (navToggle && siteNav) {
    navToggle.addEventListener("click", function () {
        siteNav.classList.toggle("active");
    });
}

const profileButton = document.getElementById("profile-menu-button");
const profileDropdown = document.getElementById("profile-dropdown");

if (profileButton && profileDropdown) {
    profileButton.addEventListener("click", function (event) {
        event.stopPropagation();
        profileDropdown.classList.toggle("open");
    });

    document.addEventListener("click", function () {
        profileDropdown.classList.remove("open");
    });
}