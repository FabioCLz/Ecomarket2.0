<?php
session_start();
include("../../config/db.php");

if(!isset($_SESSION['usuario_id'])){
    echo "No has iniciado sesión";
    exit;
}

$id = $_SESSION['usuario_id'];

/* ======================
PROCESAR FORMULARIO
====================== */
if($_SERVER["REQUEST_METHOD"] == "POST"){

    $nombre = $_POST['nombre'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Obtener foto actual
    $q = mysqli_query($conn,"SELECT foto_perfil FROM usuarios WHERE id=$id");
    $data = mysqli_fetch_assoc($q);
    $foto_actual = $data['foto_perfil'];

    // Subir nueva foto si existe
    if(isset($_FILES['foto']) && $_FILES['foto']['name'] != ""){
        $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
        $nombre_foto = time()."_".rand(1000,9999).".".$ext;
        $ruta_servidor = "../../uploads/perfiles/".$nombre_foto;

        if(move_uploaded_file($_FILES['foto']['tmp_name'],$ruta_servidor)){
            // Borrar foto anterior si no es default
            if($foto_actual != "default.png" && file_exists("../../uploads/perfiles/".$foto_actual)){
                unlink("../../uploads/perfiles/".$foto_actual);
            }
        } else {
            $nombre_foto = $foto_actual; // si falla la subida, conservar actual
        }
    } else {
        $nombre_foto = $foto_actual;
    }

    // Actualizar usuario
    if($password != ""){
        $password = password_hash($password,PASSWORD_DEFAULT);
        $sql = "UPDATE usuarios SET 
                nombre='$nombre',
                email='$email',
                password='$password',
                foto_perfil='$nombre_foto'
                WHERE id=$id";
    } else {
        $sql = "UPDATE usuarios SET 
                nombre='$nombre',
                email='$email',
                foto_perfil='$nombre_foto'
                WHERE id=$id";
    }

    if(mysqli_query($conn,$sql)){
        echo "<script>alert('Perfil actualizado correctamente'); window.location.reload();</script>";
    } else {
        echo "Error: ".mysqli_error($conn);
    }
}

/* ======================
CARGAR DATOS
====================== */
$query = mysqli_query($conn,"SELECT * FROM usuarios WHERE id=$id");
$usuario = mysqli_fetch_assoc($query);

/* ======================
FOTO PERFIL
====================== */
$foto = !empty($usuario['foto_perfil']) && file_exists("../../uploads/perfiles/".$usuario['foto_perfil']) 
        ? $usuario['foto_perfil'] 
        : "default.png";
?>

<style>
.perfil-container{
    display:flex;
    justify-content:center;
    padding:40px;
    font-family:Poppins, sans-serif;
}
.perfil-card{
    width:700px;
    background:white;
    padding:35px;
    border-radius:12px;
    box-shadow:0 10px 30px rgba(0,0,0,0.08);
}
.perfil-foto{
    width:130px;
    height:130px;
    border-radius:50%;
    overflow:hidden;
    margin:auto;
    margin-bottom:20px;
    border:4px solid #874fff;
}
.perfil-foto img{
    width:100%;
    height:100%;
    object-fit:cover;
}
.form-group{
    margin-bottom:18px;
    display:flex;
    flex-direction:column;
}
.form-group label{
    margin-bottom:5px;
    font-size:14px;
}
.form-group input{
    height:42px;
    border:1px solid #ddd;
    border-radius:6px;
    padding:0 10px;
}
.btn-guardar{
    width:100%;
    height:45px;
    background:#874fff;
    border:none;
    border-radius:8px;
    color:white;
    font-weight:500;
    cursor:pointer;
    transition:0.2s;
}
.btn-guardar:hover{
    background:#6f3fe0;
}
</style>

<div class="perfil-container">
    <div class="perfil-card">
        <div class="perfil-foto">
            <img id="preview" src="../../uploads/perfiles/<?php echo $foto ?>" alt="Foto de perfil">
        </div>

        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label>Nombre</label>
                <input type="text" name="nombre" value="<?php echo $usuario['nombre']; ?>" required>
            </div>

            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" value="<?php echo $usuario['email']; ?>" required>
            </div>

            <div class="form-group">
                <label>Nueva contraseña</label>
                <input type="password" name="password" placeholder="Dejar en blanco para no cambiar">
            </div>

            <div class="form-group">
                <label>Cambiar foto</label>
                <input type="file" name="foto" id="foto">
            </div>

            <button class="btn-guardar" type="submit">
                Guardar cambios
            </button>
        </form>
    </div>
</div>

<script>
document.getElementById("foto").addEventListener("change", function(){
    const file = this.files[0];
    if(file){
        const reader = new FileReader();
        reader.onload = function(e){
            document.getElementById("preview").src = e.target.result;
        }
        reader.readAsDataURL(file);
    }
});
</script>