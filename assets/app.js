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

//navbar

const nav = document.querySelector('.navbar');

window.addEventListener('scroll', () => {
  if (window.pageYOffset > 100) {
    nav.classList.add('scrolled');
  } else {
    nav.classList.remove('scrolled');
  }
});

// likes

document.addEventListener('DOMContentLoaded', function() {
  const bookmarks = document.querySelectorAll('.bookmark');
  bookmarks.forEach(bookmark => {
    const recipeId = bookmark.dataset.recipeId;
    const isLiked = localStorage.getItem(recipeId) === 'liked';

    if (isLiked) {
      bookmark.classList.add('liked');
      const icon = bookmark.querySelector('i');
      icon.classList.add('fas');
    }

    bookmark.addEventListener('click', function(event) {
      event.preventDefault();

      const isCurrentlyLiked = this.classList.contains('liked');

      fetch(`/like-recipe/${recipeId}`, {
        method: isCurrentlyLiked ? 'DELETE' : 'POST'
      })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            this.classList.toggle('liked');
            const icon = this.querySelector('i');
            icon.classList.toggle('fas');

            const newState = this.classList.contains('liked') ? 'liked' : 'unliked';
            localStorage.setItem(recipeId, newState);
          }
        })
        .catch(error => {
          console.error('Une erreur s\'est produite lors de la requête :', error);
        });
    });
  });
});
