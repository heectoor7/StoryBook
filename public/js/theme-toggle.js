(function () {
  var root = document.documentElement;
  var button = document.getElementById('themeToggle');
  var stored = localStorage.getItem('theme');

  if (stored === 'light') {
    root.setAttribute('data-theme', 'light');
  }

  if (!button) {
    return;
  }

  button.addEventListener('click', function () {
    var current = root.getAttribute('data-theme');
    var next = current === 'light' ? 'dark' : 'light';

    if (next === 'light') {
      root.setAttribute('data-theme', 'light');
      localStorage.setItem('theme', 'light');
    } else {
      root.removeAttribute('data-theme');
      localStorage.setItem('theme', 'dark');
    }
  });
})();
