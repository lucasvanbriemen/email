export default {
  themeUrl: "https://components.lucasvanbriemen.nl/api/colors?theme=THEME_NAME",
  selectedTheme: "auto",

  custom_colors: [
    
  ],

  getTheme() {
    if (this.selectedTheme === "auto") {
      const darkModeMediaQuery = window.matchMedia("(prefers-color-scheme: dark)");
      return darkModeMediaQuery.matches ? "dark" : "light";
    }

    return this.selectedTheme;
  },

  setCssVariables() {
    document.documentElement.setAttribute("data-theme", this.getTheme());
    const url = this.themeUrl.replace("THEME_NAME", this.getTheme());
    fetch(url)
        .then(res => res.json())
        .then(data => {
            data.forEach(color => {
                document.documentElement.style.setProperty(`--${color.name}`, color.value);
            });
        })

    this.custom_colors.forEach(color => {
      const name = `--${color.name}`;
      const value = this.getTheme() === "dark" ? color.dark : color.light;
      document.documentElement.style.setProperty(name, value);
    });
  },
};