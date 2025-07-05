export default {
    init: function () {
        this.setIframeHeight(document.querySelector('iframe'));

        // On window resize, set the iframe height again
        window.addEventListener('resize', function () {
            // Do it after a delay to prevent the iframe from jumping
            setTimeout(function () {
                email.setIframeHeight(document.querySelector('iframe'));
            }, 100);
        });
    },
 
    setIframeHeight: function (iframe) {
        try {
            const doc = iframe.contentDocument || iframe.contentWindow.document;
            iframe.style.height = doc.documentElement.scrollHeight + 'px';
        } catch (e) {
            console.error('Iframe resize failed:', e);
        }
    },
}