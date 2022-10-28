var switches = document.getElementsByClassName("switch");

var style = localStorage.getItem("style");

if (style == null) {
  setTheme("default");
} else {
  setTheme(style);
}

for (var i = 0; i < Object.keys(switches).length; i++) {
  var switchElement = switches[i];

  switchElement.addEventListener("click", function () {
    var theme = this.dataset.theme;
    setTheme(theme);
  });
}

function setTheme(theme) {
  switch (theme) {
    case theme == "default":
      document.getElementById("theme-switch").href = "./themes/default.css";
      break;
    case theme == "dark":
      document.getElementById("theme-switch").href = "./themes/dark.css";
      break;
    case theme == "deepblue":
      document.getElementById("theme-switch").href = "./themes/deepblue.css";
      break;
    case theme == "owlpurple":
      document.getElementById("theme-switch").href = "./themes/owlpurple.css";
      break;
    case theme == "mint":
      document.getElementById("theme-switch").href = "./themes/mint.css";
      break;
    default:
      document.getElementById("theme-switch").href = "./themes/default.css";
  }

  localStorage.setItem("style", theme);
}
