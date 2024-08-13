document.addEventListener('DOMContentLoaded', function() {

    //animacion categorias
    const elements = document.querySelectorAll('.animable', '.img_prenda animable');
    
    const observer = new IntersectionObserver(entries => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
                observer.unobserve(entry.target); // Dejar de observar una vez que la animación ha comenzado
            }
        });
    }, {
        threshold: 0.2
    });
    
    elements.forEach(element => {
        observer.observe(element);
    });

    //animacion estrellas
    const stars = document.querySelectorAll('.rating input');

    stars.forEach(star => {
        star.addEventListener('change', function() {
            const value = this.value;
            stars.forEach(s => {
                const label = document.querySelector(`label[for="${s.id}"]`);
                if (s.value <= value) {
                    label.classList.add('selected');
                } else {
                    label.classList.remove('selected');
                }
            });
        });
    });

    //animaciones talla y asignar valor a talla y cantidad para pagar ahora
    const tallaButtons = document.querySelectorAll('.boton-talla');
    const tallaInput = document.getElementById('Talla');
    const cantidadInput = document.getElementById('Cantidad');
    const pagarAhoraForm = document.getElementById('pagarAhoraForm');
    const tallaPagarInput = document.getElementById('TallaPagar');
    const cantidadPagarInput = document.getElementById('CantidadPagar');

    tallaButtons.forEach(button => {
        button.addEventListener('click', function() {
            tallaButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
            tallaInput.value = this.getAttribute('data-talla');
        });
        
    });

    pagarAhoraForm.addEventListener('submit', function(event) {
        tallaPagarInput.value = tallaInput.value;
        cantidadPagarInput.value = cantidadInput.value;
    });

    mostrarSeccionIndex('inicio');
    mostrarSeccionIndex('blog');
    mostrarSeccionBlogs('Blogs');
});

//cambiar foto de perfil en editar perfil y mensaje de archivo seleccionado en editar perfil, crear blog, agregar prenda, editar prenda

const fileInput = document.getElementById('file');
const fileMessage = document.getElementById('file-message');
const profileImage = document.getElementById('profileImage');

fileInput.addEventListener('change', function() {
    const file = this.files[0];
    if (file) {
        fileMessage.textContent = 'Archivo seleccionado: ' + file.name;
        const reader = new FileReader();
        reader.onload = function(e) {
            profileImage.src = e.target.result;
        }
        reader.readAsDataURL(file);
    } else {
        fileMessage.textContent = 'Archivo seleccionado: Ninguno';
        profileImage.src = "data:image/jpeg;base64,<?= base64_encode($imgData) ?>";
    }
});

//mostrar seccion deseada en index

function mostrarSeccionIndex(id) {
    const secciones = document.querySelectorAll('section');
    secciones.forEach(seccion => {
        if (seccion.id === id || seccion.id === 'inicio') {
            seccion.classList.remove('hidden');
        } else if (id === 'crear-blog' && seccion.id === 'blog') {
            seccion.classList.add('hidden');
        } else {
            seccion.classList.add('hidden');
        }
    });
}

//mostrar seccion deseada en DetalleBlog

function mostrarSeccionBlogs(id) {
    document.getElementById('Blogs').classList.remove('hidden');
    document.getElementById('editar-blog').classList.add('hidden');

}

function editarBlog(id) {
    // Ocultar la sección de blogs y mostrar la sección de editar blog
    document.getElementById('Blogs').classList.add('hidden');
    document.getElementById('editar-blog').classList.remove('hidden');

    // Desplazar hacia la parte superior de la página
    window.scrollTo(0, 0);

    // Hacer una solicitud AJAX para obtener los datos del blog
    var xhr = new XMLHttpRequest();
    xhr.open('GET', 'obtenerBlog.php?id=' + id, true);
    xhr.onload = function () {
        if (xhr.status === 200) {
            var blog = JSON.parse(xhr.responseText);
            document.querySelector('#blog-id').value = id; 
            document.querySelector('#editar-blog input[name="Titulo"]').value = blog.Titulo;
            document.querySelector('#editar-blog textarea[name="Contenido"]').value = blog.Contenido;
        } else {
            console.error('Error al obtener los datos del blog');
        }
    };
    xhr.send();
}

//mostrar seccion deseada en Categorias

function mostrarSeccionCategorias(id) {
    document.getElementById('Categorias').classList.remove('hidden');
    document.getElementById('Agregar-Prenda').classList.add('hidden');

}

function agregarPrenda(id) {
    // Ocultar la sección de categorias y mostrar la sección de agregar prenda
    document.getElementById('Categorias').classList.add('hidden');
    document.getElementById('Agregar-Prenda').classList.remove('hidden');

    // Desplazar hacia la parte superior de la página
    window.scrollTo(0, 0);

    // Hacer una solicitud AJAX para obtener los datos del tipo de prenda
    var xhr = new XMLHttpRequest();
    xhr.open('GET', 'obtenerTipo.php?id=' + id, true);
    xhr.onload = function () {
        if (xhr.status === 200) {
            var tipo = JSON.parse(xhr.responseText);
            document.querySelector('#PK_Tipo').value = id; 
            document.querySelector('#Agregar-Prenda input[name="Tipo"]').value = tipo.Nombre;
        } else {
            console.error('Error al obtener los datos del tipo de prenda');
        }
    };
    xhr.send();
}


//mostrar seccion deseada en Prenda

function mostrarSeccionPrenda(id) {
    document.getElementById('Prenda').classList.remove('hidden');
    document.getElementById('Editar-Prenda').classList.add('hidden');

}

function editarPrenda(id) {
    // Ocultar la sección de categorias y mostrar la sección de agregar prenda
    document.getElementById('Prenda').classList.add('hidden');
    document.getElementById('Editar-Prenda').classList.remove('hidden');

    // Desplazar hacia la parte superior de la página
    window.scrollTo(0, 0);

    // Hacer una solicitud AJAX para obtener los datos del tipo de prenda
    var xhr = new XMLHttpRequest();
    xhr.open('GET', 'obtenerPrenda.php?Codigo=' + id, true);
    xhr.onload = function () {
        if (xhr.status === 200) {
            var prenda = JSON.parse(xhr.responseText);
            document.querySelector('#Codigo').value = id; 
            document.querySelector('#Editar-Prenda input[name="IdCodigo"]').value = id;
            document.querySelector('#Editar-Prenda input[name="Nombre"]').value = prenda.Nombre;
            document.querySelector('#Editar-Prenda input[name="Precio"]').value = prenda.Precio;
            document.querySelector('#Editar-Prenda select[name="Tipo"]').value = prenda.Tipo;
        } else {
            console.error('Error al obtener los datos del tipo de prenda');
        }
    };
    xhr.send();

    var xhr2 = new XMLHttpRequest();
    xhr2.open('GET', 'obtenerTallas.php?Prenda=' + id, true);
    xhr2.onload = function () {
        if (xhr2.status === 200) {
            var tallas = JSON.parse(xhr2.responseText);
            document.querySelector('#Editar-Prenda input[name="XS"]').value = tallas.XS || 0;
            document.querySelector('#Editar-Prenda input[name="S"]').value = tallas.S || 0;
            document.querySelector('#Editar-Prenda input[name="M"]').value = tallas.M || 0;
            document.querySelector('#Editar-Prenda input[name="L"]').value = tallas.L || 0;
            document.querySelector('#Editar-Prenda input[name="XL"]').value = tallas.XL || 0;
            document.querySelector('#Editar-Prenda input[name="Unitalla"]').value = tallas.Unitalla || 0;

        } else {
            console.error('Error al obtener los datos del tipo de prenda');
        }
    };
    xhr2.send();
}
