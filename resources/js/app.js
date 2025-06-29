export default {
    setUlr: function(url) {
        // Set the URL without reloading the page
        window.history.pushState({}, '', url);
    },
};