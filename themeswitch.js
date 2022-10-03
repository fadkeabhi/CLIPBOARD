let switches = document.getElementsByClassName('switch');

let style = localStorage.getItem('style');

if (style == null) {
  setTheme('default');
} else {
  setTheme(style);
}

for (let i of switches) {
  i.addEventListener('click', function () {
    let theme = this.dataset.theme;
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
