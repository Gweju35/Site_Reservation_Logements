function openPopup2() {
    var popup2 = document.getElementById('popup2');
    var overlay2 = document.getElementById('overlay2');

    popup2.style.display = 'block';
    overlay2.style.display = 'block';
}

function closePopup2() {
    var popup2 = document.getElementById('popup2');
    var overlay2 = document.getElementById('overlay2');

    popup2.style.display = 'none';
    overlay2.style.display = 'none';
  }

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