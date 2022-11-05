var switches = document.getElementsByClassName("switch");

var style = localStorage.getItem("style");

if (style == null) {
  setTheme("default");
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
  switch (theme) {
    case "default":
      document.getElementById("theme-switch").href = "./themes/default.css";
      break;
    case "dark":
      document.getElementById("theme-switch").href = "./themes/dark.css";
      break;
    case "deepblue":
      document.getElementById("theme-switch").href = "./themes/deepblue.css";
      break;
    case "owlpurple":
      document.getElementById("theme-switch").href = "./themes/owlpurple.css";
      break;
    case "mint":
      document.getElementById("theme-switch").href = "./themes/mint.css";
      break;
    case "lemon":
      document.getElementById("theme-switch").href = "./themes/lemon.css";
      break;
    default:
      document.getElementById("theme-switch").href = "./themes/default.css";
  }

  localStorage.setItem("style", theme);
}
