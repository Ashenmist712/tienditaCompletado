document.addEventListener('DOMContentLoaded', function() {
    // Interceptar clics en botones de agregar al carrito para evitar navegación si están dentro de un link (<a>)
    const addToCartBtns = document.querySelectorAll('.add-to-cart-btn, .btn-icon');
    
    addToCartBtns.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault(); // Evitar navegación o comportamiento por defecto
            
            const targetFormId = this.getAttribute('data-form-target');
            if (targetFormId) {
                const form = document.getElementById(targetFormId);
                if(form) {
                    form.submit();
                }
            }
        });
    });

    // --- Lógica del Carrito Lateral (Offcanvas) ---
    const openCartBtn = document.getElementById('openCartBtn');
    const closeCartBtn = document.getElementById('closeCartBtn');
    const continueShoppingBtn = document.getElementById('continueShoppingBtn');
    const cartOverlay = document.getElementById('cartOverlay');
    const cartSidebar = document.getElementById('cartSidebar');

    window.openCartSidebar = function() {
        if(cartOverlay && cartSidebar) {
            cartOverlay.classList.add('active');
            cartSidebar.classList.add('active');
            document.body.style.overflow = 'hidden'; // Evitar scroll de fondo
        }
    };

    window.closeCartSidebar = function() {
        if(cartOverlay && cartSidebar) {
            cartOverlay.classList.remove('active');
            cartSidebar.classList.remove('active');
            document.body.style.overflow = ''; // Restaurar scroll
        }
    };

    if (openCartBtn) openCartBtn.addEventListener('click', function(e) { e.preventDefault(); openCartSidebar(); });
    if (closeCartBtn) closeCartBtn.addEventListener('click', closeCartSidebar);
    if (continueShoppingBtn) continueShoppingBtn.addEventListener('click', closeCartSidebar);
    if (cartOverlay) cartOverlay.addEventListener('click', closeCartSidebar);
});
