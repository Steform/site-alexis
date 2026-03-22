import { Controller } from '@hotwired/stimulus';

/**
 * Reorders slider photo cards via native drag-and-drop (grip handle only) and persists order via JSON POST.
 */
export default class extends Controller {
    static values = {
        url: String,
        csrfToken: String,
        successMessage: String,
    };

    connect() {
        this.dragging = null;
        this.element.querySelectorAll('[data-photo-id]').forEach((item) => {
            item.setAttribute('draggable', 'true');
            item.addEventListener('dragstart', (e) => {
                if (!e.target.closest('[data-sortable-handle]')) {
                    e.preventDefault();
                    return;
                }
                this.dragging = item;
                e.dataTransfer.effectAllowed = 'move';
                e.dataTransfer.setData('text/plain', item.getAttribute('data-photo-id'));
                item.classList.add('opacity-50');
            });
            item.addEventListener('dragend', () => {
                item.classList.remove('opacity-50');
                this.dragging = null;
            });
            item.addEventListener('dragover', (e) => {
                e.preventDefault();
                e.dataTransfer.dropEffect = 'move';
            });
            item.addEventListener('drop', (e) => {
                e.preventDefault();
                if (!this.dragging || this.dragging === item) {
                    return;
                }
                const parent = this.element;
                const children = Array.from(parent.children);
                const from = children.indexOf(this.dragging);
                const to = children.indexOf(item);
                if (from < 0 || to < 0) {
                    return;
                }
                if (from < to) {
                    parent.insertBefore(this.dragging, item.nextSibling);
                } else {
                    parent.insertBefore(this.dragging, item);
                }
                this.persistOrder();
            });
        });
    }

    async persistOrder() {
        const ids = Array.from(this.element.querySelectorAll('[data-photo-id]')).map((el) =>
            parseInt(el.getAttribute('data-photo-id'), 10)
        );
        try {
            const response = await fetch(this.urlValue, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    Accept: 'application/json',
                },
                body: JSON.stringify({
                    _token: this.csrfTokenValue,
                    ids,
                }),
            });
            const data = await response.json().catch(() => ({}));
            if (!response.ok || !data.ok) {
                window.location.reload();
                return;
            }
            this.showToast(this.successMessageValue || '');
        } catch {
            window.location.reload();
        }
    }

    showToast(message) {
        if (!message) {
            return;
        }
        const el = document.createElement('div');
        el.className =
            'alert alert-success alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3';
        el.style.zIndex = '2000';
        el.setAttribute('role', 'alert');
        el.innerHTML =
            message +
            '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
        document.body.appendChild(el);
        window.setTimeout(() => {
            el.remove();
        }, 3500);
    }
}
