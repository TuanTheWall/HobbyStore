const menuHamburger: HTMLElement | null = document.querySelector(".menu-btn");
const navLinks: HTMLElement | null = document.querySelector(".nav-links");

if (menuHamburger && navLinks) {
    menuHamburger.addEventListener("click", () => {
        navLinks.classList.toggle('mobile-menu');
    });
}