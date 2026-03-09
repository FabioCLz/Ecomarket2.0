// abrir/cerrar menu
const btnMenu = document.querySelector(".btn-menu");
const sidebar = document.querySelector(".ds-left-menu");

btnMenu.addEventListener("click", () => {
    sidebar.classList.toggle("menu-active");
});

// cargar paginas sin recargar
$(document).ready(function(){

    function initCarrito() {
        // Aquí va todo tu código del carrito y Stripe
        const cartToggle = document.getElementById('cart-toggle');
        const cartFloat = document.getElementById('cart-float');
        const cartClose = document.getElementById('cart-close');
        const cartItemsContainer = document.querySelector('#cart-float .cart-items');
        const cartTotalEl = document.getElementById('cart-total');
        const cartCountFloat = document.getElementById('cart-count-float');
        const checkoutBtn = document.getElementById('checkout-btn');

        let cart = [];

        if(cartToggle) cartToggle.addEventListener('click', () => cartFloat.classList.toggle('active'));
        if(cartClose) cartClose.addEventListener('click', () => cartFloat.classList.remove('active'));

        document.querySelectorAll('.add-cart').forEach((btn) => {
            btn.addEventListener('click', () => {
                const productItem = btn.closest('.product-item');
                const nombre = productItem.querySelector('h3').textContent;
                const precio = parseFloat(productItem.querySelector('.precio span').textContent.replace('S/','').trim());

                let prod = cart.find(p => p.nombre === nombre);
                if(prod){
                    prod.cantidad += 1;
                } else {
                    cart.push({nombre, precio, cantidad: 1});
                }

                updateCart();
            });
        });

        function updateCart(){
            cartItemsContainer.innerHTML = '';
            let total = 0;
            cart.forEach(p => {
                total += p.precio * p.cantidad;
                const itemEl = document.createElement('div');
                itemEl.classList.add('cart-item');
                itemEl.innerHTML = `<span>${p.nombre} x${p.cantidad}</span><span>S/ ${(p.precio*p.cantidad).toFixed(2)}</span>`;
                cartItemsContainer.appendChild(itemEl);
            });
            cartTotalEl.textContent = total.toFixed(2);
            cartCountFloat.textContent = cart.length;
        }

        if(checkoutBtn){
            checkoutBtn.addEventListener('click', async () => {
                if(cart.length === 0){
                    alert("El carrito está vacío");
                    return;
                }

                const response = await fetch('checkout.php', {
                    method: 'POST',
                    headers: {'Content-Type':'application/json'},
                    body: JSON.stringify(cart)
                });
                const data = await response.json();

                if(data.url){
                    window.location.href = data.url;
                } else {
                    alert("Error al iniciar el pago");
                    console.log(data);
                }
            });
        }
    }

    $(".ds-menu a").click(function(e){
        e.preventDefault();
        let pagina = $(this).attr("href");
        $("#contenido").fadeOut(150,function(){
            $("#contenido").load(pagina,function(){
                $("#contenido").fadeIn(150);
                initCarrito(); // inicializar carrito después de cargar la página
            });
        });
    });

    // cargar home por defecto
    $("#contenido").load("sections/home.php", function(){
        initCarrito(); // inicializar carrito en home
    });

}); 
function initRatings() {
    document.querySelectorAll('.product-rating').forEach(container => {
        const stars = container.querySelectorAll('.star');
        const commentEl = container.querySelector('.rating-comment');
        const submitBtn = container.querySelector('.submit-rating');
        const reviewsList = container.querySelector('.reviews-list');
        const productId = container.dataset.productId;
        let rating = 0;

        // Seleccionar estrellas
        stars.forEach(star => {
            star.addEventListener('mouseover', () => {
                const val = parseInt(star.dataset.value);
                highlightStars(stars, val);
            });
            star.addEventListener('mouseout', () => {
                highlightStars(stars, rating);
            });
            star.addEventListener('click', () => {
                rating = parseInt(star.dataset.value);
                highlightStars(stars, rating);
            });
        });

        function highlightStars(stars, val){
            stars.forEach(s => {
                s.style.color = parseInt(s.dataset.value) <= val ? 'gold' : '#ccc';
            });
        }

        // Enviar calificación
        submitBtn.addEventListener('click', async () => {
            if(rating === 0){
                alert('Selecciona una puntuación');
                return;
            }
            const comment = commentEl.value.trim();
            const resp = await fetch('add_rating.php', {
                method: 'POST',
                headers: {'Content-Type':'application/json'},
                body: JSON.stringify({producto_id: productId, puntuacion: rating, comentario: comment})
            });
            const data = await resp.json();
            if(data.success){
                commentEl.value = '';
                rating = 0;
                highlightStars(stars, 0);
                loadReviews(productId, reviewsList);
            } else {
                alert('Error al guardar calificación');
            }
        });

        // Cargar comentarios iniciales
        loadReviews(productId, reviewsList);
    });
}

async function loadReviews(productId, container){
    const resp = await fetch('get_reviews.php?producto_id='+productId);
    const data = await resp.json();
    container.innerHTML = '';
    data.forEach(r => {
        const div = document.createElement('div');
        div.innerHTML = `<strong>${'⭐'.repeat(r.puntuacion)}</strong> - ${r.comentario} <em>(${r.fecha})</em>`;
        container.appendChild(div);
    });
}

document.addEventListener('DOMContentLoaded', initRatings);



// ===== RESEÑAS =====
const reviewModal = document.getElementById('review-modal');
const closeReviewModal = document.getElementById('close-review-modal');
const reviewProductName = document.getElementById('review-product-name');
const reviewStars = document.getElementById('review-stars').children;
const reviewComment = document.getElementById('review-comment');
const submitReview = document.getElementById('submit-review');
const reviewsList = document.getElementById('reviews-list');

let selectedProductId = null;
let selectedRating = 0;

// Abrir modal al hacer clic en la imagen
document.querySelectorAll('.p-portada img').forEach(img => {
    img.addEventListener('click', () => {
        const productItem = img.closest('.product-item');
        selectedProductId = productItem.dataset.id;
        reviewProductName.textContent = productItem.querySelector('h3').textContent;
        selectedRating = 0;
        reviewComment.value = '';

        // Reset stars
        Array.from(reviewStars).forEach(star => star.style.color = '#ddd');

        reviewModal.style.display = 'flex';

        // Cargar reseñas existentes
        fetch(`get_reviews.php?producto_id=${selectedProductId}`)
            .then(res => res.json())
            .then(data => {
                reviewsList.innerHTML = '';
                if(data.length === 0){
                    reviewsList.innerHTML = '<p>No hay reseñas todavía</p>';
                } else {
                    data.forEach(r => {
                        const div = document.createElement('div');
                        div.style.borderBottom = '1px solid #eee';
                        div.style.marginBottom = '10px';
                        div.style.paddingBottom = '5px';
                        div.innerHTML = `
                            <strong>${r.cliente}</strong><br>
                            ${'★'.repeat(r.puntuacion) + '☆'.repeat(5 - r.puntuacion)}<br>
                            <span>${r.comentario}</span>
                        `;
                        reviewsList.appendChild(div);
                    });
                }
            });
    });
});

// Cerrar modal
closeReviewModal.addEventListener('click', () => reviewModal.style.display = 'none');

// Selección de estrellas
Array.from(reviewStars).forEach(star => {
    star.addEventListener('mouseenter', () => {
        const val = parseInt(star.dataset.value);
        Array.from(reviewStars).forEach(s => s.style.color = '#ddd');
        for(let i=0;i<val;i++) reviewStars[i].style.color='#f5b301';
    });
    star.addEventListener('mouseleave', () => {
        Array.from(reviewStars).forEach((s,i) => s.style.color = (i<selectedRating ? '#f5b301':'#ddd'));
    });
    star.addEventListener('click', () => {
        selectedRating = parseInt(star.dataset.value);
    });
});

// Enviar reseña
submitReview.addEventListener('click', () => {
    if(selectedRating === 0){
        alert('Selecciona una calificación');
        return;
    }

    const comentario = reviewComment.value.trim();

    const formData = new FormData();
    formData.append('producto_id', selectedProductId);
    formData.append('puntuacion', selectedRating);
    formData.append('comentario', comentario);

    fetch('add_rating.php', {
        method:'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if(data.success){
            alert('Reseña enviada correctamente');
            reviewComment.value = '';
            selectedRating = 0;
            Array.from(reviewStars).forEach(s => s.style.color = '#ddd');

            // Refrescar lista de reseñas
            fetch(`get_reviews.php?producto_id=${selectedProductId}`)
                .then(res => res.json())
                .then(data => {
                    reviewsList.innerHTML = '';
                    if(data.length === 0){
                        reviewsList.innerHTML = '<p>No hay reseñas todavía</p>';
                    } else {
                        data.forEach(r => {
                            const div = document.createElement('div');
                            div.style.borderBottom = '1px solid #eee';
                            div.style.marginBottom = '10px';
                            div.style.paddingBottom = '5px';
                            div.innerHTML = `
                                <strong>${r.cliente}</strong><br>
                                ${'★'.repeat(r.puntuacion) + '☆'.repeat(5 - r.puntuacion)}<br>
                                <span>${r.comentario}</span>
                            `;
                            reviewsList.appendChild(div);
                        });
                    }
                });
        } else {
            alert('Error al enviar reseña');
            console.log(data);
        }
    });
});