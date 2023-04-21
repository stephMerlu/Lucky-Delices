/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import './styles/app.scss';

// start the Stimulus application
import './bootstrap';

// Sélectionnez votre élément de navigation
const nav = document.querySelector('.navbar');

// Ajoutez un écouteur d'événements pour le scroll
window.addEventListener('scroll', () => {
  // Vérifiez la position de la page
  if (window.pageYOffset > 100) {
    // Ajoutez une classe pour ajouter un background color à votre barNav
    nav.classList.add('scrolled');
  } else {
    // Supprimez la classe pour retirer le background color à votre barNav
    nav.classList.remove('scrolled');
  }
});