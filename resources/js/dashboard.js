export default {
    init() {
        this.summarize();
    },

    summarize() {
        fetch('/ai_summary')
            .then(response => response.json())
            .then(data => {
                document.querySelector('.ai-summary').innerHTML = data.summary;
            });
    },
}