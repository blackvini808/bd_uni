// Mostrar el div de carreras al seleccionar un alumno
function mostrarCarreras() {
    const alumno = document.getElementById("alumno").value;
    const carrerasDiv = document.getElementById("carrerasDiv");

    if (alumno !== "") {
        carrerasDiv.style.display = "block";
    } else {
        carrerasDiv.style.display = "none";
    }
}

// Bloquear los radios al enviar el formulario
function bloquearRadios() {
    const radios = document.getElementsByName("carrera");
    radios.forEach(radio => radio.disabled = true);
}

// Se recomienda usar este evento en el form para bloquear los radios
document.addEventListener("DOMContentLoaded", function() {
    const form = document.querySelector("form");
    form.addEventListener("submit", function() {
        bloquearRadios();
    });
});
