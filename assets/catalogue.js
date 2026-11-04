const STORAGE_KEY = 'shopflow.cart.v1';

function initCatalogue() {
    const dialog = document.querySelector('#cart-dialog');
    if (!dialog || dialog.dataset.ready) return;
    dialog.dataset.ready = 'true';
    const products = new Map([...document.querySelectorAll('[data-product-id]')].map(card => [card.dataset.productId, {
        id: card.dataset.productId, name: card.dataset.productName,
        price: Number(card.dataset.productPrice), stock: Number(card.dataset.productStock), theme: card.dataset.productTheme,
    }]));
    let cart = {};
    try {
        const saved = JSON.parse(localStorage.getItem(STORAGE_KEY) || '{}');
        for (const [id, quantity] of Object.entries(saved || {})) {
            if (products.has(id) && Number.isInteger(quantity) && quantity > 0) cart[id] = Math.min(quantity, products.get(id).stock);
        }
    } catch { /* A blocked or obsolete storage must not prevent shopping. */ }
    const money = cents => new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'EUR' }).format(cents / 100);
    let toastTimer;
    function announce(message) {
        const toast = document.querySelector('[data-cart-toast]');
        toast.textContent = message;
        toast.classList.add('is-visible');
        clearTimeout(toastTimer);
        toastTimer = setTimeout(() => toast.classList.remove('is-visible'), 2800);
    }
    function render() {
        let count = 0, total = 0;
        const items = dialog.querySelector('[data-cart-items]');
        items.replaceChildren();
        for (const [id, quantity] of Object.entries(cart)) {
            const product = products.get(id);
            if (!quantity || !product) continue;
            count += quantity;
            total += product.price * quantity;
            const row = document.createElement('article');
            row.className = `cart-item theme-${product.theme}`;
            // Product labels are assigned as text, never interpolated into HTML.
            row.innerHTML = '<div class="cart-item-art" aria-hidden="true">[°‿°]<span>⚡</span></div><div class="cart-item-info"><h3></h3><p class="cart-unit-price"></p><div class="quantity-control"><button type="button" data-step="-1">−</button><span></span><button type="button" data-step="1">+</button></div></div><div class="cart-item-end"><strong></strong><button type="button" class="remove-item">Retirer ×</button></div>';
            row.querySelector('h3').textContent = product.name;
            row.querySelector('.cart-unit-price').textContent = `${money(product.price)} / unité`;
            row.querySelector('.quantity-control span').textContent = quantity;
            row.querySelector('.cart-item-end strong').textContent = money(product.price * quantity);
            row.querySelectorAll('[data-step]').forEach(button => {
                const step = Number(button.dataset.step);
                button.setAttribute('aria-label', `${step > 0 ? 'Ajouter' : 'Retirer'} une unité de ${product.name}`);
                button.disabled = step > 0 && quantity >= product.stock;
                button.addEventListener('click', () => {
                    cart[id] += step;
                    if (cart[id] <= 0) delete cart[id];
                    render();
                    const focusRow = [...items.children].find(item => item.dataset.id === id);
                    (focusRow?.querySelector(`[data-step="${step}"]`) || dialog.querySelector('[data-cart-close]')).focus();
                });
            });
            row.dataset.id = id;
            row.querySelector('.remove-item').setAttribute('aria-label', `Retirer ${product.name} du panier`);
            row.querySelector('.remove-item').addEventListener('click', () => {
                delete cart[id]; render(); dialog.querySelector('[data-cart-close]').focus();
            });
            items.append(row);
        }
        document.querySelectorAll('[data-cart-count]').forEach(node => node.textContent = count);
        dialog.querySelector('[data-cart-total]').textContent = money(total);
        dialog.querySelector('[data-cart-empty]').hidden = count > 0;
        dialog.querySelector('[data-cart-summary]').hidden = count === 0;
        document.querySelectorAll('[data-product-id]').forEach(card => {
            const product = products.get(card.dataset.productId);
            const button = card.querySelector('.add-button');
            button.disabled = (cart[product.id] || 0) >= product.stock;
            button.querySelector('span').textContent = product.stock === 0 ? 'Indisponible' : button.disabled ? 'Tout le stock est dans ton panier' : 'Ajouter au panier';
        });
        try { localStorage.setItem(STORAGE_KEY, JSON.stringify(cart)); } catch { /* Session-only cart remains usable. */ }
    }
    function closeCart() { dialog.close(); }
    dialog.addEventListener('close', () => { document.body.classList.remove('cart-is-open'); });
    document.querySelectorAll('[data-cart-open]').forEach(button => button.addEventListener('click', () => {
        dialog.showModal(); document.body.classList.add('cart-is-open');
    }));
    dialog.querySelectorAll('[data-cart-close]').forEach(button => button.addEventListener('click', closeCart));
    dialog.addEventListener('click', event => { if (event.target === dialog) closeCart(); });
    dialog.querySelector('[data-cart-clear]').addEventListener('click', () => { cart = {}; render(); dialog.querySelector('[data-cart-close]').focus(); });
    document.querySelectorAll('[data-product-id]').forEach(card => card.querySelector('.add-button').addEventListener('click', event => {
        const product = products.get(card.dataset.productId);
        if ((cart[product.id] || 0) >= product.stock) return;
        cart[product.id] = (cart[product.id] || 0) + 1;
        render(); announce(`${product.name} ajouté au butin !`);
        if (matchMedia('(prefers-reduced-motion: reduce)').matches) return;
        const bounds = event.currentTarget.getBoundingClientRect();
        for (let i = 0; i < 12; i++) {
            const flame = document.createElement('span');
            flame.className = 'flame-particle'; flame.textContent = i % 3 ? '✦' : '⚡';
            flame.style.left = `${bounds.left + bounds.width / 2}px`; flame.style.top = `${bounds.top}px`;
            flame.style.setProperty('--dx', `${(Math.random() - .5) * 260}px`);
            flame.style.setProperty('--dy', `${-60 - Math.random() * 180}px`);
            flame.style.setProperty('--spin', `${(Math.random() - .5) * 100}deg`);
            document.getElementById('flames').append(flame); setTimeout(() => flame.remove(), 1000);
        }
    }));
    render();
}

document.addEventListener('turbo:load', initCatalogue);
initCatalogue();
