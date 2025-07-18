export default {
    init() {
        this.summarize();
    },

    summarize() {
        fetch('/ai_summary')
            .then(response => response.json())
            .then(data => {
                console.log(data);
                alert(data.response);
            });
    },
}