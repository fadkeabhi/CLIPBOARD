var switches = document.getElementsByClassName('switch');

var style = localStorage.getItem('style');

if (style == null) {
  setTheme('default');
} else {
  setTheme(style);
}

for (var i = 0; i <= Object.keys(switches).length; i++) {
  var switchElement = switches[i];

  switchElement.addEventListener('click', function () {
    var theme = this.dataset.theme;
    setTheme(theme);
  });
}

function setTheme(theme) {
  if (theme == 'default') {
    document.getElementById('theme-switch').href = './themes/default.css';
  } else if (theme == 'dark') {
    document.getElementById('theme-switch').href = './themes/dark.css';
  }

  localStorage.setItem('style', theme);
}
