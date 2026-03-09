<?php
include("../config/db.php");

// Cargar categorías
$catQuery = "SELECT * FROM categorias ORDER BY id ASC";
$catResult = $conn->query($catQuery);

// Cargar productos con promedio de calificaciones
$prodQuery = "
SELECT p.*, 
       IFNULL(ROUND(AVG(c.puntuacion),1),0) AS promedio 
FROM productos p
LEFT JOIN calificaciones c ON p.id = c.producto_id
GROUP BY p.id
ORDER BY p.id ASC
";
$prodResult = $conn->query($prodQuery);
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $query = $conn->prepare("SELECT foto_perfil FROM usuarios WHERE id = ?");
    $query->bind_param("i", $user_id);
    $query->execute();
    $result = $query->get_result();
    $user = $result->fetch_assoc();
    $foto = $user ? $user['foto_perfil'] : 'default.png';
} else {
    // Si no hay usuario logueado, usar imagen por defecto
    $foto = 'default.png';
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <style>
.user-profile {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid #fff;
    cursor: pointer;
}

/* Dropdown */
.dropdown {
    position: relative;
    display: inline-block;
}

.dropdown-menu {
    display: none;
    position: absolute;
    top: 50px;
    right: 0;
    background-color: #fff;
    min-width: 120px;
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
    border-radius: 6px;
    overflow: hidden;
    z-index: 1000;
}

.dropdown-menu a {
    display: block;
    padding: 8px 12px;      /* menos padding que antes */
    text-decoration: none;
    color: #333;
    font-size: 11px;         /* tamaño de letra más pequeño */
    transition: background 0.2s;
}

.dropdown-menu a:hover {
    background-color: #f2f2f2;
}
</style>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ecomarket</title>

    <!-- FUENTES Y ICONOS -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@200;300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css">
    <link rel="stylesheet" href="https://maxst.icons8.com/vue-static/landings/line-awesome/line-awesome/1.3.0/css/line-awesome.min.css">
    <link rel="stylesheet" href="https://unpkg.com/aos@next/dist/aos.css">
    <link rel="stylesheet" href="../assets/css/cliente.css">

    <!-- CSS CARRITO Y CALIFICACIONES -->
    <style>
        .product-review { margin-top: 20px; font-family: 'Poppins', sans-serif; }
        .stars { display: flex; font-size: 1.5rem; color: #ddd; cursor: pointer; }
        .star.active, .star:hover, .star:hover ~ .star { color:#f5b301; }
        textarea.review-comment { width: 100%; min-height:60px; margin-top:10px; padding:8px; border-radius:5px; border:1px solid #ccc; }
        .btn-submit-review { margin-top:10px; padding:8px 15px; background-color:#874fff; color:#fff; border:none; border-radius:5px; cursor:pointer; }

        /* Promedio de estrellas */
        .avg-rating { margin-top:5px; font-size:0.9rem; color:#f5b301; }

        /* Botón flotante carrito */
        #cart-toggle { position: fixed; bottom: 30px; right: 30px; background-color: #874fff; color: #fff; border:none; padding:15px; border-radius:50%; font-size:20px; cursor:pointer; z-index:999; }
        #cart-toggle span { position:absolute; top:-8px; right:-8px; background:red; color:#fff; border-radius:50%; padding:2px 6px; font-size:12px; }

        /* Contenedor flotante */
        #cart-float { position: fixed; right:-400px; bottom:30px; width:350px; background:#fff; border:1px solid #ddd; border-radius:10px; box-shadow:0 5px 15px rgba(0,0,0,0.2); transition:right 0.3s ease; z-index:998; max-height:80%; display:flex; flex-direction:column; }
        #cart-float.active { right:30px; }
        #cart-float .cart-header { padding:15px; font-weight:bold; display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid #ddd; }
        #cart-float .cart-items { flex:1; overflow-y:auto; padding:10px; }
        #cart-float .cart-item { display:flex; justify-content:space-between; padding:5px 0; border-bottom:1px solid #eee; }
        #cart-float .cart-footer { padding:15px; border-top:1px solid #ddd; }
        #cart-float .cart-footer .cart-total { margin-bottom:10px; }

        /* MODAL DE RESEÑA */
        #review-modal { display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:1000; justify-content:center; align-items:center; }
        #review-modal .modal-content { background:#fff; padding:20px; border-radius:10px; width:400px; max-width:90%; position:relative; max-height:80%; overflow-y:auto; }
        #review-modal .modal-content h3 { margin-bottom:10px; }
        #review-modal .modal-content button.close-modal { position:absolute; top:10px; right:15px; font-size:20px; background:none; border:none; cursor:pointer; }
        #reviews-list div { border-bottom:1px solid #eee; margin-bottom:10px; padding-bottom:5px; }
    </style>
</head>
<body>

<div class="hm-wrapper">
    <header class="hm-header">
    <div class="container">
        <div class="header-menu">
            <div class="hm-logo">
                <a href="#"><img src="../assets/Ecomarket.png" alt="HM Store"></a>
            </div>
            <nav class="hm-menu">
                <ul>
                    <li><a href="#">Productos</a></li>
                    <li><a href="#">Campañas</a></li>
                    <li><a href="#">Nosotros</a></li>
                    <li><a href="#">Contacto</a></li>
                </ul>

                <!-- Foto de perfil con dropdown -->
                <div class="hm-icon-cart dropdown">
                    <img src="../uploads/perfiles/<?php echo $foto; ?>" alt="Perfil" class="user-profile" id="dropdownToggle" />
                    <div class="dropdown-menu" id="dropdownMenu">
                        <a href="perfil.php">Cuenta</a>
                        <a href="../auth/logout.php">Cerrar sesión</a>
                    </div>
                </div>

                <div class="icon-menu">
                    <button type="button"><i class="fas fa-bars"></i></button>
                </div>
            </nav>
        </div>
    </div>
</header>

    <!-- BANNER -->
    <div class="hm-banner">
        <div class="img-banner">
            <img src="../assets/unnamed.jpg" alt="Banner HM Store">
        </div>
    </div>

    <!-- CATEGORÍAS -->
    <section class="hm-page-block">
        <div class="container">
            <div class="header-title"><h1>Categorías</h1></div>
            <div class="hm-categorias-buttons">
                <?php while($cat = $catResult->fetch_assoc()): ?>
                    <button class="cat-btn" data-cat="<?php echo $cat['id']; ?>"><?php echo $cat['nombre']; ?></button>
                <?php endwhile; ?>
            </div>
        </div>
    </section>

    <!-- PRODUCTOS -->
    <section class="hm-page-block bg-fondo">
        <div class="container">
            <div class="header-title" data-aos="fade-up"><h1>Productos</h1></div>
            <div class="tabs-content tab-active" data-aos="fade-up">
                <div class="grid-product" id="productos-grid">
                    <?php while($prod = $prodResult->fetch_assoc()): ?>
                        <div class="product-item" 
                             data-id="<?php echo $prod['id']; ?>" 
                             data-cat="<?php echo $prod['categoria_id']; ?>" 
                             data-aos="fade-up">            
                            <div class="p-portada">
                                <?php
                                    $rutaProd = "../assets/productos/" . ($prod['imagen'] ?? 'default.png');
                                    if(!file_exists($rutaProd)) $rutaProd = "../assets/productos/default.png";
                                ?>
                                <img src="<?php echo $rutaProd; ?>" alt="<?php echo $prod['nombre']; ?>">
                            </div>
                            <div class="p-info">
                                <h3><?php echo $prod['nombre']; ?></h3>
                                <div class="precio"><span>Bs/ <?php echo number_format($prod['precio'],2); ?></span></div>

                                <!-- PROMEDIO ESTRELLAS -->
                                <div class="avg-rating">
                                    <?php
                                    $fullStars = floor($prod['promedio']);
                                    $halfStar = ($prod['promedio'] - $fullStars) >= 0.5;
                                    for($i=0;$i<$fullStars;$i++){ echo "★"; }
                                    if($halfStar){ echo "☆"; $i++; }
                                    for(; $i<5;$i++){ echo "☆"; }
                                    echo " ({$prod['promedio']})";
                                    ?>
                                </div>

                                <button class="hm-btn btn-primary add-cart">AGREGAR AL CARRITO</button>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- MODAL DE RESEÑA -->
<div id="review-modal">
    <div class="modal-content">
        <button class="close-modal" id="close-review-modal">&times;</button>
        <h3 id="review-product-name"></h3>
        <div class="stars" id="review-stars">
            <span class="star" data-value="1">&#9733;</span>
            <span class="star" data-value="2">&#9733;</span>
            <span class="star" data-value="3">&#9733;</span>
            <span class="star" data-value="4">&#9733;</span>
            <span class="star" data-value="5">&#9733;</span>
        </div>
        <textarea id="review-comment" placeholder="Escribe tu comentario..."></textarea>
        <button id="submit-review" class="hm-btn btn-primary">Enviar reseña</button>
        <div id="reviews-list"></div>
    </div>
</div>

<!-- CARRITO FLOTANTE -->
<div id="cart-float">
    <div class="cart-header">
        <h3>Carrito</h3>
        <button id="cart-close">&times;</button>
    </div>
    <div class="cart-items"></div>
    <div class="cart-footer">
        <div class="cart-total">Total: Bs/ <span id="cart-total">0.00</span></div>
        <button id="checkout-btn" class="hm-btn btn-primary">Pagar</button>
    </div>
</div>

<!-- BOTÓN FLOTANTE -->
<button id="cart-toggle"><i class="las la-shopping-cart"></i><span id="cart-count-float">0</span></button>

<script src="https://unpkg.com/aos@next/dist/aos.js"></script>
<script src="../assets/js/app.js"></script>
<script src="https://js.stripe.com/v3/"></script>
<script>
AOS.init({duration:1200});

// FILTRAR PRODUCTOS
const buttons = document.querySelectorAll('.cat-btn');
const productos = document.querySelectorAll('.product-item');
buttons.forEach(btn => {
    btn.addEventListener('click', () => {
        buttons.forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        const catId = btn.dataset.cat;
        productos.forEach(prod => {
            if(catId === 'all' || prod.dataset.cat === catId){
                prod.style.display = 'block';
            } else {
                prod.style.display = 'none';
            }
        });
    });
});

// CARRITO FLOTANTE
const cartToggle = document.getElementById('cart-toggle');
const cartFloat = document.getElementById('cart-float');
const cartClose = document.getElementById('cart-close');
const cartItemsContainer = document.querySelector('#cart-float .cart-items');
const cartTotalEl = document.getElementById('cart-total');
const cartCountFloat = document.getElementById('cart-count-float');

let cart = [];

cartToggle.addEventListener('click', () => cartFloat.classList.toggle('active'));
cartClose.addEventListener('click', () => cartFloat.classList.remove('active'));

document.querySelectorAll('.add-cart').forEach((btn) => {
    btn.addEventListener('click', async () => {
        const productItem = btn.closest('.product-item');
        const producto_id = productItem.dataset.id;
        const nombre = productItem.querySelector('h3').textContent;
        const precio = parseFloat(productItem.querySelector('.precio span').textContent.replace('Bs/','').trim());

        let prod = cart.find(p => p.producto_id === producto_id);
        if(prod){
            prod.cantidad += 1;
        } else {
            cart.push({producto_id, nombre, precio, cantidad: 1});
        }

        updateCart();

        // Guardar en DB
        const resp = await fetch('add_to_cart.php', {
            method: 'POST',
            headers: {'Content-Type':'application/json'},
            body: JSON.stringify({producto_id, cantidad:1})
        });
        const data = await resp.json();
        console.log(data);
    });
});

function updateCart(){
    cartItemsContainer.innerHTML = '';
    let total = 0;
    cart.forEach(p => {
        total += p.precio * p.cantidad;
        const itemEl = document.createElement('div');
        itemEl.classList.add('cart-item');
        itemEl.innerHTML = `<span>${p.nombre} x${p.cantidad}</span><span>Bs/ ${(p.precio * p.cantidad).toFixed(2)}</span>`;
        cartItemsContainer.appendChild(itemEl);
    });
    cartTotalEl.textContent = total.toFixed(2);
    cartCountFloat.textContent = cart.length;
}

// STRIPE CHECKOUT
const checkoutBtn = document.getElementById('checkout-btn');
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

// Abrir modal al hacer click en la imagen
document.querySelectorAll('.p-portada img').forEach(img => {
    img.addEventListener('click', () => {
        const productItem = img.closest('.product-item');
        selectedProductId = productItem.dataset.id;
        reviewProductName.textContent = productItem.querySelector('h3').textContent;
        selectedRating = 0;
        reviewComment.value = '';
        Array.from(reviewStars).forEach(s => s.style.color = '#ddd');
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
                        div.innerHTML = `<strong>${r.cliente}</strong><br>${'★'.repeat(r.puntuacion)+'☆'.repeat(5-r.puntuacion)}<br><span>${r.comentario}</span>`;
                        reviewsList.appendChild(div);
                    });
                }
            });
    });
});

closeReviewModal.addEventListener('click', () => reviewModal.style.display='none');

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
    if(selectedRating === 0){ alert('Selecciona una calificación'); return; }
    const comentario = reviewComment.value.trim();
    const formData = new FormData();
    formData.append('producto_id', selectedProductId);
    formData.append('puntuacion', selectedRating);
    formData.append('comentario', comentario);

    fetch('add_rating.php',{ method:'POST', body:formData })
        .then(res => res.json())
        .then(data => {
            if(data.success){
                alert('Reseña enviada correctamente');
                reviewComment.value=''; selectedRating=0; Array.from(reviewStars).forEach(s=>s.style.color='#ddd');
                // refrescar reseñas
                fetch(`get_reviews.php?producto_id=${selectedProductId}`)
                    .then(res => res.json())
                    .then(data => {
                        reviewsList.innerHTML = '';
                        if(data.length === 0) reviewsList.innerHTML = '<p>No hay reseñas todavía</p>';
                        else data.forEach(r=>{
                            const div = document.createElement('div');
                            div.innerHTML = `<strong>${r.cliente}</strong><br>${'★'.repeat(r.puntuacion)+'☆'.repeat(5-r.puntuacion)}<br><span>${r.comentario}</span>`;
                            reviewsList.appendChild(div);
                        });
                    });
            } else { alert('Error al enviar reseña'); console.log(data); }
        });
});
</script>
<script>
// Mostrar u ocultar dropdown al hacer clic
document.addEventListener("DOMContentLoaded", function() {
    const toggle = document.getElementById("dropdownToggle");
    const menu = document.getElementById("dropdownMenu");

    toggle.addEventListener("click", function(e) {
        e.stopPropagation();
        menu.style.display = menu.style.display === "block" ? "none" : "block";
    });

    // Cerrar menú si se hace clic fuera
    document.addEventListener("click", function() {
        menu.style.display = "none";
    });
});
</script>
</body>
</html>