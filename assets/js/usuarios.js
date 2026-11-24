const formulario = document.getElementById("usuarioForm");

formulario.addEventListener("submit", function(event) {

    const nombre = document.getElementById("nombre").value.trim();
    const correo = document.getElementById("correo").value.trim();
    const contrasena = document.getElementById("contrasena").value;
    const rol = document.getElementById("rol").value;

    const correoValido = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    if (nombre === "") {
        alert("El nombre es obligatorio.");
        event.preventDefault();
        return;
    }

    if (!correoValido.test(correo)) {
        alert("Ingrese un correo electrónico válido.");
        event.preventDefault();
        return;
    }

    if (contrasena !== "" && contrasena.length < 8) {
        alert("La contraseña debe tener al menos 8 caracteres.");
        event.preventDefault();
        return;
    }

    if (contrasena === "" && formulario.querySelector('[name="accion"]').value === "guardar") {
        alert("La contraseña es obligatoria.");
        event.preventDefault();
        return;
    }

    if (rol === "") {
        alert("Seleccione un rol.");
        event.preventDefault();
    }
});

const campoBuscar = document.getElementById("buscar");
const filtroRol = document.getElementById("filtroRol");
const tablaUsuarios = document.getElementById("tablaUsuarios");
const totalResultados = document.getElementById("totalResultados");

let temporizador;

function escaparHTML(texto) {
    return String(texto).replace(/[&<>"']/g, function(caracter) {
        const caracteres = {
            "&": "&amp;",
            "<": "&lt;",
            ">": "&gt;",
            '"': "&quot;",
            "'": "&#039;"
        };

        return caracteres[caracter];
    });
}

function aplicarFiltro() {

    const rolSeleccionado = filtroRol.value;
    const filas = tablaUsuarios.querySelectorAll("tr[data-rol]");

    let total = 0;

    filas.forEach(function(fila) {

        const rol = fila.dataset.rol;

        if (rolSeleccionado === "" || rol === rolSeleccionado) {
            fila.style.display = "";
            total++;
        } else {
            fila.style.display = "none";
        }

    });

    totalResultados.textContent = total;
}


async function buscarUsuarios() {

    const texto = campoBuscar.value.trim();

    try {

        const respuesta = await fetch(
            "buscar.php?buscar=" + encodeURIComponent(texto)
        );

        if (!respuesta.ok) {
            throw new Error("Error al consultar los usuarios.");
        }

        const usuarios = await respuesta.json();

        tablaUsuarios.innerHTML = "";

        if (usuarios.error) {
            throw new Error(usuarios.error);
        }

        if (usuarios.length === 0) {
            tablaUsuarios.innerHTML = `
                <tr>
                    <td colspan="5">No se encontraron usuarios registrados.</td>
                </tr>
            `;

            totalResultados.textContent = "0";
            return;
        }

        usuarios.forEach(function(usuario) {

            const fila = document.createElement("tr");

            fila.dataset.rol = usuario.rol;

            fila.innerHTML = `
                <td>${escaparHTML(usuario.id)}</td>
                <td>${escaparHTML(usuario.nombre)}</td>
                <td>${escaparHTML(usuario.correo)}</td>
                <td>${escaparHTML(usuario.rol.charAt(0).toUpperCase() + usuario.rol.slice(1))}</td>
                <td>
                    <a href="index.php?editar=${encodeURIComponent(usuario.id)}" class="btn">
                        Editar
                    </a>

                    <form method="POST" action="index.php"
                          onsubmit="return confirm('¿Seguro que deseas eliminar este usuario?');"
                          style="display:inline;">

                        <input type="hidden" name="accion" value="eliminar">
                        <input type="hidden" name="id" value="${escaparHTML(usuario.id)}">

                        <button type="submit" class="btn btn-danger">
                            Eliminar
                        </button>
                    </form>
                </td>
            `;

            tablaUsuarios.appendChild(fila);

        });

        aplicarFiltro();

    } catch (error) {

        tablaUsuarios.innerHTML = `
            <tr>
                <td colspan="5">No se pudieron cargar los usuarios.</td>
            </tr>
        `;

        totalResultados.textContent = "0";
        console.error(error);
    }
}


campoBuscar.addEventListener("input", function() {

    clearTimeout(temporizador);

    temporizador = setTimeout(buscarUsuarios, 300);

});


filtroRol.addEventListener("change", aplicarFiltro);