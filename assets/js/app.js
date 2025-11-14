var menuHamburger = document.querySelector(".menu-btn");
var navLinks = document.querySelector(".nav-links");
if (menuHamburger && navLinks) {
    menuHamburger.addEventListener("click", function () {
        navLinks.classList.toggle('mobile-menu');
    });
}
