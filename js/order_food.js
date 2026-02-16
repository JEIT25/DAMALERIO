/**
 * Order Food page: load restaurants, load menu, cart (persisted in localStorage), checkout.
 * Uses BASE_URL and relative paths for API calls. Cart syncs to navbar badge via localStorage.
 */
(function () {
    const base = window.BASE_URL || '';
    const api = base + '/php/database';
    const CART_STORAGE_KEY = 'foodgrab_cart';

    let restaurants = [];
    let menu = [];
    let currentRestaurantId = null;
    let currentRestaurantName = '';
    let cart = []; // { menu_item_id, name, unit_price, quantity, restaurant_id }
    let paymentMethods = [];

    const $ = (id) => document.getElementById(id);
    const restaurantsView = $('restaurantsView');
    const menuView = $('menuView');
    const checkoutView = $('checkoutView');
    const checkoutTotal = $('checkoutTotal');
    const paymentMethodsList = $('paymentMethodsList');

    function saveCartToStorage() {
        try {
            localStorage.setItem(CART_STORAGE_KEY, JSON.stringify(cart));
            if (typeof window.updateNavCartBadge === 'function') window.updateNavCartBadge();
        } catch (e) {}
    }

    function loadCartFromStorage() {
        try {
            const raw = localStorage.getItem(CART_STORAGE_KEY);
            if (raw) {
                const parsed = JSON.parse(raw);
                if (Array.isArray(parsed)) cart = parsed;
            }
        } catch (e) {}
    }

    function showView(name) {
        restaurantsView.style.display = name === 'restaurants' ? 'block' : 'none';
        menuView.style.display = name === 'menu' ? 'block' : 'none';
        checkoutView.style.display = name === 'checkout' ? 'block' : 'none';
    }

    function updateCartUI() {
        let total = 0;
        let count = 0;
        cart.forEach(i => { count += i.quantity; total += i.quantity * i.unit_price; });
        if (checkoutTotal) checkoutTotal.textContent = total.toFixed(2);
        saveCartToStorage();
    }

    function addToCart(item, qty) {
        const num = parseInt(qty, 10) || 1;
        const existing = cart.find(c => c.menu_item_id === item.id && c.restaurant_id === currentRestaurantId);
        if (existing) existing.quantity += num;
        else cart.push({
            menu_item_id: item.id,
            name: item.name,
            unit_price: parseFloat(item.price),
            quantity: num,
            restaurant_id: currentRestaurantId
        });
        updateCartUI();
    }

    async function loadRestaurants() {
        const res = await fetch(api + '/restaurants_list.php');
        const data = await res.json();
        if (data.success && data.restaurants) {
            restaurants = data.restaurants;
            const list = $('restaurantsList');
            list.innerHTML = data.restaurants.map(r =>
                `<div class="restaurant-card" data-id="${r.id}" data-name="${escapeHtml(r.name)}">
                    <h3>${escapeHtml(r.name)}</h3>
                    <p>${escapeHtml(r.description || r.address || '')}</p>
                </div>`
            ).join('');
            list.querySelectorAll('.restaurant-card').forEach(el => {
                el.addEventListener('click', () => {
                    currentRestaurantId = parseInt(el.dataset.id, 10);
                    currentRestaurantName = el.dataset.name;
                    loadMenu(currentRestaurantId);
                    showView('menu');
                });
            });
        }
    }

    async function loadMenu(restaurantId) {
        const res = await fetch(api + '/menu_list.php?restaurant_id=' + restaurantId);
        const data = await res.json();
        if (data.success && data.menu) {
            menu = data.menu;
            $('menuRestaurantName').textContent = currentRestaurantName;
            const list = $('menuList');
            list.innerHTML = data.menu.map(m => {
                const avail = m.is_available == 1;
                        return `<div class="menu-item ${!avail ? 'unavailable' : ''}" data-id="${m.id}">
                    <h4>${escapeHtml(m.name)}</h4>
                    ${m.description ? `<p class="muted small">${escapeHtml(m.description)}</p>` : ''}
                    <span class="price">₱${parseFloat(m.price).toFixed(2)}</span>
                    <div class="menu-item-actions">
                        <input type="number" min="1" value="1">
                        <button type="button" class="btn-add" data-menu-id="${m.id}">Add</button>
                        <button type="button" class="btn-fav" data-menu-id="${m.id}" title="Favorite">♥</button>
                    </div>
                </div>`;
            }).join('');
            list.querySelectorAll('.btn-add').forEach(btn => {
                btn.addEventListener('click', () => {
                    const mid = parseInt(btn.dataset.menuId, 10);
                    const item = menu.find(m => m.id == mid);
                    if (!item) return;
                    const input = btn.closest('.menu-item').querySelector('input[type="number"]');
                    addToCart({ id: item.id, name: item.name, price: item.price }, input.value);
                });
            });
            list.querySelectorAll('.btn-fav').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    e.preventDefault();
                    toggleFavorite(btn.dataset.menuId, btn);
                });
            });
            (async function setFavoritedHearts() {
                try {
                    const r = await fetch(api + '/favorites_list.php');
                    const d = await r.json();
                    if (d.favorites) {
                        const ids = d.favorites.map(f => String(f.menu_item_id));
                        list.querySelectorAll('.btn-fav').forEach(b => {
                            b.classList.toggle('favorited', ids.includes(b.dataset.menuId));
                        });
                    }
                } catch (_) {}
            })();
        }
    }

    async function toggleFavorite(menuItemId, btn) {
        const fd = new FormData();
        fd.append('menu_item_id', menuItemId);
        fd.append('action', 'toggle');
        const res = await fetch(api + '/favorite_toggle.php', { method: 'POST', body: fd });
        const data = await res.json();
        if (data.success) btn.classList.toggle('favorited', data.favorited);
    }

    async function loadPaymentMethods() {
        const res = await fetch(api + '/payment_methods_list.php');
        const data = await res.json();
        paymentMethods = (data.success && data.payment_methods) ? data.payment_methods : [];
        if (!paymentMethodsList) return;
        paymentMethodsList.innerHTML = '';
        paymentMethods.forEach(pm => {
            const label = document.createElement('label');
            label.innerHTML = `<input type="radio" name="payment_method_id" value="${pm.id}"> ${escapeHtml(pm.label)}`;
            paymentMethodsList.appendChild(label);
        });
        const cash = document.createElement('label');
        cash.innerHTML = '<input type="radio" name="payment_method_id" value="" checked> Cash on Delivery';
        paymentMethodsList.appendChild(cash);
    }

    $('backToRestaurants').addEventListener('click', () => { showView('restaurants'); });
    $('backToMenu').addEventListener('click', () => { showView('menu'); });
    $('checkoutForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        const delivery_address = $('delivery_address').value.trim();
        const notes = $('notes').value.trim();
        const payment_method_id = document.querySelector('input[name="payment_method_id"]:checked');
        const pmId = payment_method_id && payment_method_id.value ? payment_method_id.value : null;
        const items = cart.map(c => ({
            menu_item_id: c.menu_item_id,
            quantity: c.quantity,
            unit_price: c.unit_price
        }));
        const rid = cart[0] && cart[0].restaurant_id ? cart[0].restaurant_id : currentRestaurantId;
        const payload = {
            restaurant_id: rid,
            delivery_address,
            notes,
            payment_method_id: pmId ? parseInt(pmId, 10) : null,
            items
        };
        const submitBtn = $('checkoutForm').querySelector('.submitBtn');
        submitBtn.disabled = true;
        submitBtn.textContent = 'Placing order...';
        try {
            const res = await fetch(api + '/place_order.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            });
            const data = await res.json();
            if (data.success) {
                cart = [];
                updateCartUI();
                showView('restaurants');
                alert('Order #' + data.order_id + ' placed successfully. View Order History to track.');
                submitBtn.textContent = 'Place Order';
                submitBtn.disabled = false;
                return;
            }
            alert(data.error || 'Failed to place order');
        } catch (err) {
            alert('Network error. Try again.');
        }
        submitBtn.textContent = 'Place Order';
        submitBtn.disabled = false;
    });

    function escapeHtml(s) {
        if (!s) return '';
        const div = document.createElement('div');
        div.textContent = s;
        return div.innerHTML;
    }

    loadCartFromStorage();
    if (cart.length > 0 && cart[0].restaurant_id) {
        currentRestaurantId = cart[0].restaurant_id;
        currentRestaurantName = ''; // will stay empty until user opens menu again
    }
    loadRestaurants();
    loadPaymentMethods();
    showView('restaurants');
    updateCartUI();
})();
