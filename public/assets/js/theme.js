document.addEventListener("DOMContentLoaded", function () {
    const themeToggle = document.getElementById("theme-toggle");

    const savedTheme = localStorage.getItem("theme") || "light";

    document.body.setAttribute("data-theme", savedTheme);

    if (!themeToggle) {
        return;
    }

    themeToggle.textContent =
        savedTheme === "dark" ? "☀ Light Mode" : "🌙 Dark Mode";

    themeToggle.addEventListener("click", function () {
        const currentTheme = document.body.getAttribute("data-theme");
        const newTheme = currentTheme === "dark" ? "light" : "dark";

        document.body.setAttribute("data-theme", newTheme);
        localStorage.setItem("theme", newTheme);

        themeToggle.textContent =
            newTheme === "dark" ? "☀ Light Mode" : "🌙 Dark Mode";
    });
});