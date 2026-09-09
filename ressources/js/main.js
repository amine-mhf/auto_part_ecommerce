// ============================================================
// MAIN.JS - JAVASCRIPT CLIENT (APIS RAPIDO)
// ============================================================

// 1. FILTRE DES SOUS-CATÉGORIES (PAGE PRODUITS)
function initCategoryFilter() {
    const categorieSelect = document.getElementById('categorieSelect');
    const sousCategorieSelect = document.getElementById('sousCategorieSelect');

    if (!categorieSelect || !sousCategorieSelect) return;

    const allOptions = Array.from(sousCategorieSelect.options);

    function updateSousCategories() {
        const selectedCat = categorieSelect.value;
        sousCategorieSelect.innerHTML = '<option value="">Toutes</option>';

        allOptions.forEach(opt => {
            if (opt.value === "") return;
            const catId = opt.getAttribute('data-cat');
            if (selectedCat === "" || catId == selectedCat) {
                sousCategorieSelect.appendChild(opt.cloneNode(true));
            }
        });
    }

    categorieSelect.addEventListener('change', updateSousCategories);
    updateSousCategories();
}

// 2. COMPTEUR DYNAMIQUE DU PANIER (badge dans le header)

function updateCartBadge(count) {
    const badge = document.getElementById('cartCountBadge');
    if (!badge) return;
    if (count > 0) {
        badge.textContent = count;
        badge.style.display = 'inline-flex';
    } else {
        badge.textContent = '';
        badge.style.display = 'none';
    }
}

// 3. AJOUT AU PANIER (AJAX AVEC FEEDBACK VISUEL + MISE À JOUR DU COMPTEUR)

function initAddToCart() {
    document.querySelectorAll('.add-to-cart').forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.preventDefault();

            const productId = this.getAttribute('data-id');
            const originalText = this.innerHTML;
            const originalBg = this.style.backgroundColor;

            // Feedback temporaire "Ajouté"
            this.innerHTML = 'Ajouté !';
            this.style.backgroundColor = '#10b981';

            fetch('/pieces_auto/ajax_panier.php?ajouter=' + productId)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        updateCartBadge(data.count);
                        setTimeout(() => {
                            this.innerHTML = originalText;
                            this.style.backgroundColor = originalBg;
                        }, 1500);
                    } else {
                        this.innerHTML = 'Stock insuffisant';
                        this.style.backgroundColor = '#dc2626';
                        setTimeout(() => {
                            this.innerHTML = originalText;
                            this.style.backgroundColor = originalBg;
                        }, 1500);
                    }
                })
                .catch(() => {
                    this.innerHTML = 'Erreur';
                    this.style.backgroundColor = '#dc2626';
                    setTimeout(() => {
                        this.innerHTML = originalText;
                        this.style.backgroundColor = originalBg;
                    }, 1500);
                });
        });
    });
}

// 4. GESTION DES QUANTITÉS DANS LE PANIER (AJAX) - augmente/diminue le compteur en direct

function recalcCartTotal() {
    let totalGeneral = 0;
    document.querySelectorAll('.total-ligne').forEach(td => {
        const val = parseFloat(td.textContent.replace(/[^\d,]/g, '').replace(',', '.'));
        if (!isNaN(val)) totalGeneral += val;
    });
    const totalGeneralElem = document.getElementById('total-general');
    if (totalGeneralElem) {
        totalGeneralElem.textContent = totalGeneral.toFixed(2).replace('.', ',') + ' DA';
    }
}

// Verrou anti double-clic par produit (évite les requêtes concurrentes)
const cartUpdateLocks = {};

function sendQuantityUpdate(productId, newQty, row, input) {
    if (cartUpdateLocks[productId]) return;
    cartUpdateLocks[productId] = true;

    const priceUnit = parseFloat(
        row.querySelector('.prix-unitaire').textContent.replace(/[^\d,]/g, '').replace(',', '.')
    );

    fetch('/pieces_auto/panier.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'update_quantity=1&produit_id=' + productId + '&quantite=' + newQty
    })
        .then(response => response.json())
        .then(data => {
            cartUpdateLocks[productId] = false;

            if (data.success) {
                if (data.removed) {
                    // Quantité ramenée à 0 : on retire la carte du panier immédiatement
                    row.style.opacity = '0';
                    setTimeout(() => {
                        row.remove();
                        const container = document.getElementById('cart-table');
                        if (container && container.children.length === 0) {
                            location.reload();
                        } else {
                            recalcCartTotal();
                        }
                    }, 150);
                } else {
                    const totalLigne = priceUnit * newQty;
                    row.querySelector('.total-ligne').textContent = totalLigne.toFixed(2).replace('.', ',') + ' DA';
                    recalcCartTotal();
                }
                // Compteur du header mis à jour en temps réel, sans recharger la page
                updateCartBadge(data.cart_count);
            } else {
                alert('Stock insuffisant');
                location.reload();
            }
        })
        .catch(() => {
            cartUpdateLocks[productId] = false;
            alert('Erreur lors de la mise à jour du panier');
        });
}

function initCartQuantityHandlers() {
    const steppers = document.querySelectorAll('.qty-stepper');
    if (steppers.length === 0) return;

    steppers.forEach(stepper => {
        const productId = stepper.getAttribute('data-id');
        const stock = parseInt(stepper.getAttribute('data-stock'), 10);
        const input = stepper.querySelector('.qty-input');
        const btnMinus = stepper.querySelector('.qty-minus');
        const btnPlus = stepper.querySelector('.qty-plus');
        const row = stepper.closest('.cart-item-card');

        // Boutons +/- : mise à jour immédiate, en temps réel, sans rechargement
        btnPlus.addEventListener('click', function () {
            let qty = parseInt(input.value, 10) || 0;
            if (qty >= stock) return;
            qty += 1;
            input.value = qty;
            sendQuantityUpdate(productId, qty, row, input);
        });

        btnMinus.addEventListener('click', function () {
            let qty = parseInt(input.value, 10) || 0;
            qty -= 1;
            if (qty < 0) qty = 0;
            input.value = qty;
            sendQuantityUpdate(productId, qty, row, input);
        });

        // Saisie manuelle dans le champ
        input.addEventListener('change', function () {
            let newQty = parseInt(this.value, 10);
            if (isNaN(newQty) || newQty < 0) newQty = 0;
            if (newQty > stock) newQty = stock;
            this.value = newQty;
            sendQuantityUpdate(productId, newQty, row, input);
        });
    });
}

// 5. GESTION DES MODES DE PAIEMENT (COMMANDE.PHP)

function initPaymentHandlers() {
    const radioCarte = document.querySelector('input[name="payment_method_radio"][value="carte"]');
    const radioEspeces = document.querySelector('input[name="payment_method_radio"][value="especes"]');
    const cardFields = document.getElementById('cardFields');
    const especesMessage = document.getElementById('especesMessage');
    const paymentMethodInput = document.getElementById('payment_method_input');
    const submitBtn = document.getElementById('submitBtn');

    if (!radioCarte || !radioEspeces) return;

    // Formatage automatique du numéro de carte (XXXX XXXX XXXX XXXX)
    const cardNumberInput = document.getElementById('card_number');
    if (cardNumberInput) {
        cardNumberInput.addEventListener('input', function () {
            let value = this.value.replace(/\s/g, '');
            if (value.length > 16) value = value.slice(0, 16);
            let formatted = '';
            for (let i = 0; i < value.length; i++) {
                if (i > 0 && i % 4 === 0) formatted += ' ';
                formatted += value[i];
            }
            this.value = formatted;
        });
    }

    function updatePaymentMethod() {
        const isEspeces = radioEspeces.checked;

        paymentMethodInput.value = isEspeces ? 'especes' : 'carte';

        if (isEspeces) {
            cardFields.style.display = 'none';
            especesMessage.style.display = 'flex';
            submitBtn.innerHTML = 'Confirmer la commande';
            document.querySelectorAll('#cardFields input, #cardFields select').forEach(field => {
                field.disabled = true;
                field.removeAttribute('required');
            });
        } else {
            cardFields.style.display = 'block';
            especesMessage.style.display = 'none';
            submitBtn.innerHTML = 'Payer maintenant';
            document.querySelectorAll('#cardFields input, #cardFields select').forEach(field => {
                field.disabled = false;
            });
        }
    }

    radioCarte.addEventListener('change', updatePaymentMethod);
    radioEspeces.addEventListener('change', updatePaymentMethod);
    updatePaymentMethod();
}

// 6. MENU MOBILE (BURGER)

function initMobileNav() {
    const toggle = document.getElementById('navToggle');
    const nav = document.getElementById('mainNav');
    if (!toggle || !nav) return;

    toggle.addEventListener('click', function () {
        nav.classList.toggle('nav-open');
        toggle.classList.toggle('nav-toggle-active');
    });

    // Sur mobile/tactile, les sous-menus (Catégories, puis chaque famille de
    // pièces) ne peuvent pas s'ouvrir au survol : on les ouvre au clic.
    nav.querySelectorAll('.dropdown > a, .dropdown-sub > a').forEach(link => {
        link.addEventListener('click', function (e) {
            if (window.innerWidth > 900) return; // desktop : le survol suffit
            e.preventDefault();
            this.parentElement.classList.toggle('nav-open-sub');
        });
    });
}

// 7. POPUP DE CONFIRMATION DE PAIEMENT (COMMANDE.PHP)

function initPaymentSuccessOverlay() {
    const overlay = document.querySelector('.payment-success-overlay');
    if (!overlay) return;
    document.body.style.overflow = 'hidden';
}

// 8. INITIALISATION GÉNÉRALE AU CHARGEMENT DE LA PAGE

document.addEventListener('DOMContentLoaded', function () {
    initCategoryFilter();          // Filtres produits
    initAddToCart();               // Boutons "Ajouter au panier"
    initCartQuantityHandlers();    // Gestion quantités (panier) en temps réel
    initPaymentHandlers();         // Mode de paiement (commande)
    initMobileNav();               // Menu mobile
    initPaymentSuccessOverlay();   // Popup de confirmation de commande
});
