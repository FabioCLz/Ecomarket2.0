<?php
session_start();
include("../config/db.php");

if(!isset($_SESSION["usuario_id"])){
    header("Location: ../auth/login.php");
    exit();
}

$vendedor = $_SESSION["usuario_id"];

/* PRODUCTOS */
$productos = mysqli_query($conn,"SELECT * FROM productos WHERE vendedor_id='$vendedor' ORDER BY id DESC");

/* PERFIL */
$queryPerfil = mysqli_query($conn,"SELECT * FROM usuarios WHERE id='$vendedor'");
$perfil = mysqli_fetch_assoc($queryPerfil);

/* METRICAS */
$totalProductos = mysqli_fetch_assoc(
    mysqli_query($conn,"SELECT COUNT(*) total FROM productos WHERE vendedor_id='$vendedor'")
)["total"];

$stockBajo = mysqli_fetch_assoc(
    mysqli_query($conn,"SELECT COUNT(*) total FROM productos WHERE vendedor_id='$vendedor' AND stock <=5")
)["total"];

$totalVentas = mysqli_fetch_assoc(
    mysqli_query($conn,"SELECT SUM(total) total FROM pedidos")
)["total"] ?? 0;

/* TRAER CATEGORÍAS */
$categorias = mysqli_query($conn, "SELECT * FROM categorias ORDER BY nombre ASC");
?>
<!DOCTYPE html>
<html>
<head>
<title>EcoMarket Dashboard</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
body{margin:0;font-family:Arial;background:#f4f6f5;}
.header{background:#2e7d32;color:white;padding:15px 30px;display:flex;justify-content:space-between;align-items:center;}
.logo{font-size:22px;font-weight:bold;}
.perfil{position:relative;cursor:pointer;}
.perfil img{width:40px;height:40px;border-radius:50%;object-fit:cover;}
.menu-perfil{display:none;position:absolute;right:0;top:50px;background:white;box-shadow:0 5px 15px rgba(0,0,0,0.1);border-radius:8px;overflow:hidden;}
.menu-perfil a{display:block;padding:10px 15px;text-decoration:none;color:#333;}
.menu-perfil a:hover{background:#f3f3f3;}
.metricas{display:grid;grid-template-columns:repeat(auto-fit,minmax(150px,1fr));gap:15px;padding:20px;}
.card-metrica{background:white;padding:15px;border-radius:10px;box-shadow:0 3px 10px rgba(0,0,0,0.08);text-align:center;font-size:14px;}
.card-metrica i{font-size:24px;color:#2e7d32;margin-bottom:5px;}
.graficos-container{display:grid;grid-template-columns:repeat(auto-fit,minmax(250px,1fr));gap:20px;padding:20px;}
.btn-agregar{margin:20px;background:#2e7d32;color:white;border:none;padding:12px 18px;border-radius:8px;cursor:pointer;}
.productos{padding:20px;display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:20px;}
.card{background:white;border-radius:10px;padding:15px;box-shadow:0 3px 10px rgba(0,0,0,0.08);cursor:pointer;transition:.2s;}
.card:hover{transform:scale(1.03);}
.card img{width:100%;height:150px;object-fit:cover;border-radius:8px;}
.stock-bajo{color:#c62828;font-size:13px;margin-top:5px;}
.modal{display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.5);justify-content:center;align-items:center;z-index:1000;}
.modal-contenido{background:white;padding:30px;border-radius:12px;width:400px;max-height:90vh;overflow-y:auto;}
.modal input,.modal textarea,.modal select{width:100%;padding:10px;margin-top:10px;border:1px solid #ccc;border-radius:6px;}
.modal button{margin-top:15px;width:100%;padding:12px;background:#2e7d32;color:white;border:none;border-radius:6px;cursor:pointer;}
.cerrar{float:right;cursor:pointer;font-size:18px;}
.filtros{padding:0 20px;margin-top:10px;}
.filtros label{font-weight:bold;margin-right:10px;}
.filtros select{padding:5px;border-radius:5px;border:1px solid #ccc;}
</style>
</head>
<body>

<div class="header">
    <div class="logo"><i class="fa-solid fa-leaf"></i> EcoMarket</div>
    <div class="perfil" onclick="toggleMenu()">
        <img src="../uploads/perfiles/<?php echo $perfil['foto_perfil'] ?? 'default.png' ?>">
        <div class="menu-perfil" id="menuPerfil">
            <a href="javascript:void(0)" onclick="abrirPerfil()"><i class="fa-solid fa-user"></i> Cuenta</a>
            <a href="../auth/logout.php"><i class="fa-solid fa-right-from-bracket"></i> Cerrar sesión</a>
        </div>
    </div>
</div>

<!-- METRICAS -->
<div class="metricas">
    <div class="card-metrica">
        <i class="fa-solid fa-box"></i>
        <h3><?php echo $totalProductos ?></h3>
        <p>Productos</p>
    </div>
    <div class="card-metrica">
        <i class="fa-solid fa-triangle-exclamation"></i>
        <h3><?php echo $stockBajo ?></h3>
        <p>Stock bajo</p>
    </div>
    <div class="card-metrica">
        <i class="fa-solid fa-dollar-sign"></i>
        <h3>$<?php echo $totalVentas ?></h3>
        <p>Ventas</p>
    </div>
</div>

<!-- BOTÓN AGREGAR PRODUCTO -->
<button class="btn-agregar" onclick="abrirAgregar()">
    <i class="fa-solid fa-plus"></i> Agregar producto
</button>

<!-- PRODUCTOS -->
<div class="productos">
<?php while($p=mysqli_fetch_assoc($productos)){ ?>
    <div class="card" onclick="abrirEditar(<?php echo $p['id']?>)">
        <img src="../assets/productos/<?php echo $p['imagen']?>">
        <h3><?php echo $p['nombre']?></h3>
        <p>$<?php echo $p['precio']?></p>
        <?php if($p['stock'] <= 5){ ?>
            <div class="stock-bajo">
                <i class="fa-solid fa-triangle-exclamation"></i> Stock bajo
            </div>
        <?php } ?>
    </div>
<?php } ?>
</div>

<!-- MODAL PERFIL -->
<div class="modal" id="modalPerfil">
    <div class="modal-contenido">
        <span class="cerrar" onclick="cerrarPerfil()"><i class="fa-solid fa-xmark"></i></span>
        <h2>Editar Perfil</h2>
        <form id="formPerfil" enctype="multipart/form-data">
            <input type="hidden" name="usuario_id" value="<?php echo $vendedor ?>">
            <label>Nombre</label>
            <input type="text" name="nombre" value="<?php echo $perfil['nombre'] ?? '' ?>">
            <label>Email</label>
            <input type="email" name="email" value="<?php echo $perfil['email'] ?? '' ?>">
            <label>Teléfono</label>
            <input type="text" name="telefono" value="<?php echo $perfil['telefono'] ?? '' ?>">
            <label>Foto de perfil</label>
            <img id="perfil_imagen_preview" src="../uploads/perfiles/<?php echo $perfil['foto_perfil'] ?? 'default.png' ?>" width="100">
            <input type="file" name="foto_perfil" onchange="previewImagenPerfil(this)">
            <label>Ubicación</label>
            <div id="map" style="width:100%;height:250px;margin-top:10px;"></div>
            <input type="hidden" name="latitud" id="perfil_latitud" value="<?php echo $perfil['latitud'] ?? '' ?>">
            <input type="hidden" name="longitud" id="perfil_longitud" value="<?php echo $perfil['longitud'] ?? '' ?>">
            <input type="hidden" name="direccion" id="perfil_direccion" value="<?php echo $perfil['direccion'] ?? '' ?>">
            <input type="hidden" name="ciudad" id="perfil_ciudad" value="<?php echo $perfil['ciudad'] ?? '' ?>">
            <button type="submit"><i class="fa-solid fa-floppy-disk"></i> Guardar cambios</button>
        </form>
    </div>
</div>

<!-- MODAL AGREGAR PRODUCTO -->
<div class="modal" id="modalAgregar">
    <div class="modal-contenido">
        <span class="cerrar" onclick="cerrarAgregar()"><i class="fa-solid fa-xmark"></i></span>
        <h2>Agregar producto</h2>
        <form action="api/agregar_producto.php" method="POST" enctype="multipart/form-data">
            <input name="nombre" placeholder="Nombre" required>
            <textarea name="descripcion" placeholder="Descripción" required></textarea>
            <input name="precio" type="number" placeholder="Precio" required>
            <input name="stock" type="number" placeholder="Stock" required>
            <label>Categoría</label>
            <select name="categoria_id" required>
                <?php
                mysqli_data_seek($categorias,0); // resetear puntero
                while($c=mysqli_fetch_assoc($categorias)){ ?>
                    <option value="<?php echo $c['id'] ?>"><?php echo $c['nombre'] ?></option>
                <?php } ?>
            </select>
            <input type="file" name="imagen" required>
            <button><i class="fa-solid fa-plus"></i> Guardar</button>
        </form>
    </div>
</div>

<!-- MODAL EDITAR PRODUCTO -->
<div class="modal" id="modalEditar">
    <div class="modal-contenido">
        <span class="cerrar" onclick="cerrarEditar()"><i class="fa-solid fa-xmark"></i></span>
        <h2>Editar producto</h2>
        <form id="formEditar" enctype="multipart/form-data">
            <input type="hidden" name="id" id="edit_id">
            <input name="nombre" id="edit_nombre" required>
            <textarea name="descripcion" id="edit_descripcion" required></textarea>
            <input name="precio" type="number" id="edit_precio" required>
            <input name="stock" type="number" id="edit_stock" required>
            <label>Categoría</label>
            <select name="categoria_id" id="edit_categoria" required>
                <?php
                mysqli_data_seek($categorias,0); // resetear puntero
                while($c=mysqli_fetch_assoc($categorias)){ ?>
                    <option value="<?php echo $c['id'] ?>"><?php echo $c['nombre'] ?></option>
                <?php } ?>
            </select>
            <img id="edit_imagen_preview" width="120" style="margin-top:10px;">
            <input type="file" name="imagen">
            <button><i class="fa-solid fa-floppy-disk"></i> Actualizar</button>
        </form>
    </div>
</div>

<script>
let map, marker, geocoder;

// TOGGLE MENU PERFIL
function toggleMenu(){
    let menu = document.getElementById("menuPerfil");
    menu.style.display = menu.style.display=="block" ? "none" : "block";
}

// MODALES PERFIL
function abrirPerfil() { 
    document.getElementById("modalPerfil").style.display = "flex"; 
    toggleMenu();
    setTimeout(initMap, 200);
}
function cerrarPerfil() { document.getElementById("modalPerfil").style.display = "none"; }

// MODALES PRODUCTOS
function abrirAgregar(){ document.getElementById("modalAgregar").style.display="flex"; }
function cerrarAgregar(){ document.getElementById("modalAgregar").style.display="none"; }
function abrirEditar(id){
    fetch("api/obtener_producto.php?id="+id)
    .then(res=>res.json())
    .then(data=>{
        document.getElementById("modalEditar").style.display="flex";
        edit_id.value = data.id;
        edit_nombre.value = data.nombre;
        edit_descripcion.value = data.descripcion;
        edit_precio.value = data.precio;
        edit_stock.value = data.stock;
        edit_categoria.value = data.categoria_id;
        edit_imagen_preview.src = "../assets/productos/"+data.imagen;
    });
}
function cerrarEditar(){ document.getElementById("modalEditar").style.display="none"; }

// Preview imagen perfil
function previewImagenPerfil(input){
    const preview = document.getElementById("perfil_imagen_preview");
    if(input.files && input.files[0]){
        const reader = new FileReader();
        reader.onload = function(e){ preview.src = e.target.result; }
        reader.readAsDataURL(input.files[0]);
    }
}

// MAPA
function initMap() {
    geocoder = new google.maps.Geocoder();
    const lat = parseFloat(document.getElementById("perfil_latitud").value) || -16.5;
    const lng = parseFloat(document.getElementById("perfil_longitud").value) || -68.15;
    const pos = {lat:lat,lng:lng};

    map = new google.maps.Map(document.getElementById("map"), {center:pos, zoom:12});
    marker = new google.maps.Marker({position:pos, map:map, draggable:true});
    updateAddress(pos);

    google.maps.event.addListener(marker,'dragend', function(){
        const p = marker.getPosition();
        document.getElementById("perfil_latitud").value = p.lat();
        document.getElementById("perfil_longitud").value = p.lng();
        updateAddress(p);
    });
}

function updateAddress(latlng){
    geocoder.geocode({location:latlng}, function(results,status){
        if(status==='OK' && results[0]){
            const components = results[0].address_components;
            let direccion = results[0].formatted_address;
            let ciudad = '';
            for(let comp of components){
                if(comp.types.includes('locality')) ciudad = comp.long_name;
                else if(comp.types.includes('administrative_area_level_1') && !ciudad) ciudad = comp.long_name;
            }
            document.getElementById("perfil_direccion").value = direccion;
            document.getElementById("perfil_ciudad").value = ciudad;
        }
    });
}

// Guardar perfil
document.getElementById("formPerfil").addEventListener("submit", function(e){
    e.preventDefault();
    const datos = new FormData(this);
    fetch("api/actualizar_perfil.php", {method:"POST", body:datos})
        .then(res => res.json())
        .then(data => {
            if(data.success){
                alert("Perfil actualizado correctamente");
                location.reload();
            } else {
                alert("Error: "+data.error);
            }
        });
});

// Editar producto
document.getElementById("formEditar").addEventListener("submit", function(e){
    e.preventDefault();
    const datos = new FormData(this);
    fetch("api/actualizar_producto.php",{method:"POST",body:datos})
    .then(()=> location.reload());
});
</script>

<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDxMcpGysyt0iSe4pg8PUdakLrbuff9pV4&callback=initMap" async defer></script>
</body>
</html>