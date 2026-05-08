import api from "./api.js";

export default {
  themeUrl: "https://components.lucasvanbriemen.nl/api/colors",

  custom_colors: [
    
  ],

  getTheme() {
    const darkModeMediaQuery = window.matchMedia("(prefers-color-scheme: dark)");
    return darkModeMediaQuery.matches ? "dark" : "light";
  },

  async applyTheme() {
    document.documentElement.setAttribute("data-theme", this.getTheme());
    const colors = await api.get(this.themeUrl);

    colors.forEach(color => {
      const name = `--${color.name}`;
      const value = this.getTheme() === "dark" ? color.dark : color.light;
      document.documentElement.style.setProperty(name, value);
    });
    

    this.custom_colors.forEach(color => {
      const name = `--${color.name}`;
      const value = this.getTheme() === "dark" ? color.dark : color.light;
      document.documentElement.style.setProperty(name, value);
    });
  },
};