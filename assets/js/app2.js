const menuHamburger = document.querySelector(".menu-btn");
const navLinks = document.querySelector(".nav-links");
const wordDiv = document.querySelector(".word");
const btnHeaderDiv = document.querySelector(".btnHeader");

menuHamburger.addEventListener('click', () => {
    navLinks.classList.toggle('mobile-menu');
    if (navLinks.classList.contains('mobile-menu')) {
        wordDiv.style.display = 'none';
        btnHeaderDiv.style.display = 'none';
    } else {
        setTimeout(() => {
        wordDiv.style.display = 'block';
        btnHeaderDiv.style.display = 'block';
        }, 250);
    }
});
