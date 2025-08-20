export default {
    _count: 0,
    _el: null,

    _ensureEl() {
        if (!this._el) {
            this._el = document.querySelector('.app-loader');
        }
        return this._el;
    },

    show() {
        const el = this._ensureEl();
        if (!el) return;
        this._count++;
        el.classList.remove('hidden');
        el.setAttribute('aria-busy', 'true');
    },

    hide(force = false) {
        const el = this._ensureEl();
        if (!el) return;
        if (force) {
            this._count = 0;
        } else {
            this._count = Math.max(0, this._count - 1);
        }
        if (this._count === 0) {
            el.classList.add('hidden');
            el.setAttribute('aria-busy', 'false');
        }
    },
}

