import api from "./api.js";

export default {
  themeUrl: "https://components.lucasvanbriemen.nl/api/colors",

  custom_colors: {
    "starred": {
      "dark": "rgb(238, 222, 108)",
      "light": "rgb(248, 255, 38)"
    },
  },

  getTheme() {
    const darkModeMediaQuery = window.matchMedia("(prefers-color-scheme: dark)");
    return darkModeMediaQuery.matches ? "dark" : "light";
  },

  async applyTheme() {
    document.documentElement.setAttribute("data-theme", this.getTheme());
    const colors = await api.get(this.themeUrl);

    const mergedColors = { ...colors, ...this.custom_colors };

    Object.entries(mergedColors).forEach(([name, color]) => {
      const value = this.getTheme() === "dark" ? color.dark : color.light;
      document.documentElement.style.setProperty(`--${name}`, value);
    });
  },
};