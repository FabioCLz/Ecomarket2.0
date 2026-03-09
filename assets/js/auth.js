function togglePassword(inputId, icon){

const input = document.getElementById(inputId);

if(input.type === "password"){
input.type = "text";
icon.classList.replace("fa-eye","fa-eye-slash");
}else{
input.type = "password";
icon.classList.replace("fa-eye-slash","fa-eye");
}

}

function previewImage(event){

const reader = new FileReader();

reader.onload = function(){
document.getElementById("preview").src = reader.result;
}

reader.readAsDataURL(event.target.files[0]);

}