let menuVisibe = false;
//funcion que oculta y muestra el menu
function mostrarOcultarMenu(){
    if(menuVisibe){
        document.getElementById("nav").classList="";
        menuVisibe=false;
    }else{
        document.getElementById("nav").classList="responsive";
        menuVisibe=true;
    }
}
function seleccionar(){
    //oculta menu una vez seleccionado una opcion
    document.getElementById("nav").classList="";
    menuVisibe=false;
}
function toggleProfileMenu() {
    const menu = document.getElementById("profileMenu");
    menu.classList.toggle("hidden");

    // Si el menú está visible, agregar un event listener para cerrar al hacer clic fuera
    if (!menu.classList.contains("hidden")) {
        document.addEventListener("click", closeMenuOnClickOutside);
    } else {
        document.removeEventListener("click", closeMenuOnClickOutside);
    }
}
function closeMenuOnClickOutside(event) {
    const menu = document.getElementById("profileMenu");
    const perfil = document.querySelector(".perfil");

    // Verificar si el clic fue fuera del menú o de la imagen de perfil
    if (!perfil.contains(event.target)) {
        menu.classList.add("hidden");
        document.removeEventListener("click", closeMenuOnClickOutside);
    }
}