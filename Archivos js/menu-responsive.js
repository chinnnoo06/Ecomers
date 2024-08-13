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
    document.getElementById("profileMenu").classList.toggle("hidden");
}