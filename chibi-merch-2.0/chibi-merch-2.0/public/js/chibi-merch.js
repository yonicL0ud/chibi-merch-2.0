// Chibi Merch 2.0 - JavaScript
// Solo se usa para mostrar y ocultar el menu en pantallas pequenas.

const botonMenu = document.getElementById('nav__toggle');
const menu = document.getElementById('nav');

if (botonMenu && menu) {
    botonMenu.addEventListener('click', function () {
        menu.classList.toggle('nav--abierto');
    });
}