const productosDiv = document.getElementById("productos")
const categoriasDiv = document.getElementById("categorias")

let carrito = []

function cargarProductos(categoria=null){

let url = "api/productos.php"

if(categoria){
url += "?categoria="+categoria
}

axios.get(url)
.then(res =>{

productosDiv.innerHTML=""

res.data.forEach(p =>{

productosDiv.innerHTML += `

<div class="col-md-4 mb-4">

<div class="card producto-card">

<img src="../uploads/productos/${p.imagen}" class="card-img-top">

<div class="card-body">

<h6>${p.nombre}</h6>

<p class="text-muted">${p.categoria}</p>

<h5 class="text-success">Bs ${p.precio}</h5>

<button class="btn btn-success w-100"
onclick="agregarCarrito(${p.id})">

<i class="fa-solid fa-cart-plus"></i>
Agregar

</button>

</div>

</div>

</div>

`

})

})

}


function cargarCategorias(){

axios.get("api/categorias.php")
.then(res =>{

categoriasDiv.innerHTML = `
<li class="list-group-item categoria" onclick="cargarProductos()">
Todos
</li>
`

res.data.forEach(c =>{

categoriasDiv.innerHTML += `

<li class="list-group-item categoria"
onclick="cargarProductos(${c.id})">

${c.nombre}

</li>

`

})

})

}

function agregarCarrito(id){

carrito.push(id)

document.getElementById("cart-count").innerText = carrito.length

}

cargarCategorias()
cargarProductos()