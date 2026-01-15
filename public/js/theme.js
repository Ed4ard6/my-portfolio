(function () {
  const root = document.documentElement;
  const btn = document.getElementById("themeToggle");
  const icon = btn ? btn.querySelector(".icon") : null;
  const logo = document.getElementById("siteLogo");

  if (!btn || !icon || !logo) return;

  const STORAGE_KEY = "theme";

  function systemPrefersLight() {
    return window.matchMedia &&
      window.matchMedia("(prefers-color-scheme: light)").matches;
  }

  function setTheme(theme) {
    if (theme === "light") {
      root.setAttribute("data-theme", "light");
      icon.textContent = "☀️";
      logo.src = "/img/logo-light.png";
    } else {
      root.removeAttribute("data-theme"); // oscuro por defecto
      icon.textContent = "🌙";
      logo.src = "/img/logo-dark.png";
    }
  }

  function getInitialTheme() {
    const saved = localStorage.getItem(STORAGE_KEY);
    if (saved === "light" || saved === "dark") return saved;
    return systemPrefersLight() ? "light" : "dark";
  }

  let current = getInitialTheme();
  setTheme(current);

  btn.addEventListener("click", () => {
    current = current === "dark" ? "light" : "dark";
    localStorage.setItem(STORAGE_KEY, current);
    setTheme(current);
  });
})();
