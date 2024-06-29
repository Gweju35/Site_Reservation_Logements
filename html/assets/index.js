document.addEventListener('DOMContentLoaded', function () {
  const navButtons = document.querySelectorAll('.nav_button');

  // Récupérer l'élément actif stocké dans le localStorage
  const activeElement = localStorage.getItem('activeElement');

  // Si un élément actif est enregistré, activer cet élément
  if (activeElement) {
      const element = document.querySelector(`[data-element-id="${activeElement}"]`);
      if (element) {
          element.classList.add('active');
      }
  }

  // Ajouter un gestionnaire de clic pour les éléments
  navButtons.forEach(button => {
      button.addEventListener('click', function () {
          // Retirer la classe active de l'ancien élément
          const activeElement = document.querySelector('.nav_button.active');
          if (activeElement) {
              activeElement.classList.remove('active');
          }

          // Ajouter la classe active au nouvel élément
          this.classList.add('active');

          // Stocker l'ID de l'élément actif dans le localStorage
          localStorage.setItem('activeElement', this.getAttribute('data-element-id'));
      });
  });
});


window.addEventListener('DOMContentLoaded', function () {
  var inputDestination = document.getElementById('destination');
  var inputPersonnes = document.getElementById('personnes');

  function updatePlaceholder() {
      var screenWidth = window.innerWidth;

      if (screenWidth<=1595) {
          inputDestination.placeholder = 'Rechercher';
          inputPersonnes.placeholder = 'Rechercher';
      } else {
          inputDestination.placeholder = 'Rechercher une destination';
          inputPersonnes.placeholder = 'Rechercher des personnes';
      }
  }

  updatePlaceholder();
  window.addEventListener('resize', updatePlaceholder);
});


window.addEventListener('DOMContentLoaded', function () {
  var search_bar1 = document.querySelector('.container');
  var search_bar2 = document.querySelector('.container-760');

  function updateSearchBar() {
      var screenWidth = window.innerWidth;

      if (screenWidth<=1020) {
          search_bar2.style.display = 'flex';
          search_bar1.style.display = 'none';
      } else {
          search_bar1.style.display = 'flex';
          search_bar2.style.display = 'none';
      }

  }

  updateSearchBar();
  window.addEventListener('resize', updateSearchBar);
});

window.addEventListener('DOMContentLoaded', function () {
  var separator = document.querySelector('#separator-responsive');

  function updateSeparator() {
      var screenWidth = window.innerWidth;

      if (screenWidth<=580) {
          separator.style.display = 'block';
      } else {
          separator.style.display = 'none';
      }
  }
  updateSeparator();
  window.addEventListener('resize', updateSeparator);
})


function openPopup() {
  if (window.innerWidth <= 1020) {
    var popup = document.getElementById('popup');
    var overlay = document.getElementById('overlay');

    popup.style.display = 'block';
    overlay.style.display = 'block';

  } 
}

function closePopup() {
  var popup = document.getElementById('popup');
  var overlay = document.getElementById('overlay');

  popup.style.display = 'none';
  overlay.style.display = 'none';
}

function closePopup2() {
  var popup2 = document.getElementById('popup2');
  var overlay2 = document.getElementById('overlay2');

  popup2.style.display = 'none';
  overlay2.style.display = 'none';
}

function openPopup2() {
    var popup2 = document.getElementById('popup2');
    var overlay2 = document.getElementById('overlay2');

    popup2.style.display = 'block';
    overlay2.style.display = 'block';
}

document.addEventListener('DOMContentLoaded', function () {
  var overlay = document.getElementById('overlay');
  var searchBars = document.getElementsByClassName('search-bar-responsive');

  overlay.addEventListener('click', function (event) {
    if (event.target === overlay) {
      closePopup();
    }
  });

  for (var i = 0; i < searchBars.length; i++) {
    searchBars[i].addEventListener('click', openPopup);
  }
});

document.addEventListener('DOMContentLoaded', function () {
  var overlay2 = document.getElementById('overlay2');
  var filter = document.getElementsByClassName('filter-button');

  overlay2.addEventListener('click', function (event) {
    if (event.target === overlay2) {
      closePopup2();
    }
  });

  for (var i = 0; i < filter.length; i++) {
    filter[i].addEventListener('click', openPopup2);
  }
});

/* Ouvrir et fermer le menu burger en format mobile */
var sidenav = document.getElementById("mySidenav");
var openBtn = document.getElementById("openBtn");
var closeBtn = document.getElementById("closeBtn");
var linksContainer = document.querySelector("nav");

/* Désactiver le scroll quand le menu est ouvert */
function disableScroll() {
document.body.style.overflow = 'hidden';
}

/* Réactiver le scroll quand le menu est fermé */
function enableScroll() {
document.body.style.overflow = '';
}

/* Fermer le menu quand on clique sur la croix */
function closeNavOnLinkClick() {
sidenav.classList.remove("active");
enableScroll();
linksContainer.removeEventListener("click", closeNavOnLinkClick);
}

openBtn.addEventListener("click", function() {
sidenav.classList.add("active");
disableScroll();
linksContainer.addEventListener("click", closeNavOnLinkClick);
});

closeBtn.addEventListener("click", function() {
sidenav.classList.remove("active");
enableScroll();
linksContainer.removeEventListener("click", closeNavOnLinkClick);
});

/* Fermer le menu quand on clique à l'exterieur de celui-ci 
document.addEventListener("click", function(event) {
const isClickedInsideHeader = event.target.closest('header');
const isSideNavActive = sidenav.classList.contains('active');

if (!isClickedInsideHeader && isSideNavActive) {
  event.preventDefault();
}
}); */

/* Fermer le menu quand on clique à l'exterieur de celui-ci */
document.addEventListener("click", function(event) {
const isClickedInsideHeader = event.target.closest('header');
const isSideNavActive = sidenav.classList.contains('active');

if (!isClickedInsideHeader && isSideNavActive) {
  sidenav.classList.remove("active");
  enableScroll();
  linksContainer.removeEventListener("click", closeNavOnLinkClick);
}
});

/* Menu lateral des Filtres/Tris et de la Carte */
/* ------ Filtres ------ */
var sidenavFiltres = document.getElementById("mySidenavFiltres"); // le menu latéral
var openBtnFiltres = document.getElementById("openBtnFiltres"); // le bouton pour ouvrir/fermer le menu
var iconBtnFiltres = document.getElementById("iconBtnFiltres"); // le bouton d'icône

iconBtnFiltres.addEventListener("click", function (event) {
  event.preventDefault();
  sidenavFiltres.style.display = "block";
  setTimeout(function () {
  openBtnFiltres.style.display = "block";
  // Vérifiez la largeur de l'écran
  if (window.matchMedia("(max-width: 630px)").matches) {
      // Code pour une largeur maximale de 630px
      if ((sidenavCarte.classList.contains("active")) && (sidenavFiltres.classList.contains("active"))) {
          sidenavFiltres.classList.remove("active");
      } else if (sidenavCarte.classList.contains("active")) {
          sidenavFiltres.classList.add("active");
      } else {
          sidenavFiltres.classList.add("active");
      }
  } else {
      // Code pour une largeur minimale de 630px
      if ((sidenavCarte.classList.contains("active")) && (sidenavFiltres.classList.contains("active"))) {
          sidenavFiltres.classList.remove("active");
          contenuPage.classList.remove("affichage3");
          contenuPage.classList.add("affichage2");
      } else if (sidenavFiltres.classList.contains("active")){
          contenuPage.classList.remove("affichage4");
          contenuPage.classList.add("affichage1");
      } else if (sidenavCarte.classList.contains("active")) {
          sidenavFiltres.classList.add("active");
          contenuPage.classList.remove("affichage2");
          contenuPage.classList.add("affichage3");
      } else {
          sidenavFiltres.classList.add("active");
          contenuPage.classList.remove("affichage1");
          contenuPage.classList.add("affichage4");
      }
  }
}, 1);
});

openBtnFiltres.addEventListener("click", function (event) {
    event.preventDefault();
    openBtnFiltres.style.display = "none";
    if ((sidenavCarte.classList.contains("active")) && (sidenavFiltres.classList.contains("active"))) {
      sidenavFiltres.classList.remove("active");
      contenuPage.classList.remove("affichage3");
      contenuPage.classList.add("affichage2");
  } else if (sidenavFiltres.classList.contains("active")){
      sidenavFiltres.classList.remove("active");
      contenuPage.classList.remove("affichage4");
      contenuPage.classList.add("affichage1");
  } else if (sidenavCarte.classList.contains("active")) {
      sidenavFiltres.classList.add("active");
      contenuPage.classList.remove("affichage2");
      contenuPage.classList.add("affichage3");
  } else {
    sidenavFiltres.classList.add("active");
    contenuPage.classList.remove("affichage1");
    contenuPage.classList.add("affichage4");
  }
  setTimeout(function () {
    sidenavFiltres.style.display = "none";
  }, 500);
});

/* ------ Carte ------ */
var sidenavCarte = document.getElementById("mySidenavCarte"); // le menu latéral
var openBtnCarte = document.getElementById("openBtnCarte"); // le bouton pour ouvrir/fermer le menu
var iconBtnCarte = document.getElementById("iconBtnCarte"); // le bouton d'icône
var contenuPage = document.getElementById("contenuPage");


iconBtnCarte.addEventListener("click", function (event) {
  event.preventDefault();
  sidenavCarte.style.display = "block";
  setTimeout(function () {
  openBtnCarte.style.display = "block";
  if (window.matchMedia("(max-width: 630px)").matches) {
    if ((sidenavCarte.classList.contains("active")) && (sidenavFiltres.classList.contains("active"))) {
      sidenavCarte.classList.remove("active");
    } else if (sidenavFiltres.classList.contains("active")) {
      sidenavCarte.classList.add("active");
    } else {
      sidenavCarte.classList.add("active");
    }
  }

  else {
    if ((sidenavCarte.classList.contains("active")) && (sidenavFiltres.classList.contains("active"))) {
      sidenavCarte.classList.remove("active");
      contenuPage.classList.remove("affichage3");
      contenuPage.classList.add("affichage4");
    } else if (sidenavCarte.classList.contains("active")) {
      contenuPage.classList.remove("affichage2");
      contenuPage.classList.add("affichage1");
    } else if (sidenavFiltres.classList.contains("active")) {
      sidenavCarte.classList.add("active");
      contenuPage.classList.remove("affichage4");
      contenuPage.classList.add("affichage3");
    } else {
      sidenavCarte.classList.add("active");
      contenuPage.classList.remove("affichage1");
      contenuPage.classList.add("affichage2");
    }
  }
}, 1);
});

openBtnCarte.addEventListener("click", function (event) {
    event.preventDefault();
    openBtnCarte.style.display = "none";
    if ((sidenavCarte.classList.contains("active")) && (sidenavFiltres.classList.contains("active"))) {
      sidenavCarte.classList.remove("active");
      contenuPage.classList.remove("affichage3");
      contenuPage.classList.add("affichage4");
  } else if (sidenavCarte.classList.contains("active")){
      sidenavCarte.classList.remove("active");
      contenuPage.classList.remove("affichage2");
      contenuPage.classList.add("affichage1");
  } else if (sidenavFiltres.classList.contains("active")){
      sidenavCarte.classList.add("active");
      contenuPage.classList.remove("affichage4");
      contenuPage.classList.add("affichage3");
  } else {
      sidenavCarte.classList.add("active");
      contenuPage.classList.remove("affichage1");
      contenuPage.classList.add("affichage2");
  }
  setTimeout(function () {
    sidenavCarte.style.display = "none";
  }, 500);
});


