document.addEventListener("DOMContentLoaded",()=>{

cargarProductos()

})



function cargarProductos(){

fetch("api/obtener_productos.php")

.then(res=>res.json())

.then(data=>{

let tabla=document.getElementById("tablaProductos")

tabla.innerHTML=""

data.forEach(p=>{

tabla.innerHTML+=`

<tr>

<td>${p.id}</td>

<td>
<img src="../assets/productos/${p.imagen}" width="60">
</td>

<td>${p.nombre}</td>

<td>$${p.precio}</td>

<td>${p.stock}</td>

<td>

<button onclick="editarProducto(${p.id})"
class="btn btn-warning btn-sm">

Editar

</button>

<button onclick="eliminarProducto(${p.id})"
class="btn btn-danger btn-sm">

Eliminar

</button>

</td>

</tr>

`

})

})

}



function editarProducto(id){

fetch("api/obtener_producto.php?id="+id)

.then(res => res.json())

.then(p => {

document.getElementById("edit_id").value = p.id
document.getElementById("edit_nombre").value = p.nombre
document.getElementById("edit_descripcion").value = p.descripcion
document.getElementById("edit_precio").value = p.precio
document.getElementById("edit_stock").value = p.stock

document.getElementById("edit_imagen_preview").src =
"../assets/productos/"+p.imagen

let modal = new bootstrap.Modal(document.getElementById("modalEditar"))
modal.show()

})

}


document.getElementById("formEditar").addEventListener("submit",function(e){

e.preventDefault()

let datos=new FormData(this)

fetch("api/editar_producto.php",{

method:"POST",
body:datos

})

.then(res=>res.text())

.then(res=>{

alert("Producto actualizado")

location.reload()

})

})



function eliminarProducto(id){

if(confirm("¿Eliminar producto?")){

fetch("api/eliminar_producto.php?id="+id)

.then(res=>res.text())

.then(res=>{

alert("Producto eliminado")

cargarProductos()

})

}

}