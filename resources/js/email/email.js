export default {
    setIframeHeight: function (iframe) {
        try {
            const doc = iframe.contentDocument || iframe.contentWindow.document;
            iframe.style.height = doc.documentElement.scrollHeight + 'px';
        } catch (e) {
            console.error('Iframe resize failed:', e);
        }
    },
}