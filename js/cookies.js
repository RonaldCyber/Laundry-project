// Configurar cookie
function setCookie(nombre, valor, dias) {
    const fecha = new Date();
    fecha.setTime(fecha.getTime() + (dias * 24 * 60 * 60 * 1000));
    const expira = "expires=" + fecha.toUTCString();
    document.cookie = nombre + "=" + valor + ";" + expira + ";path=/";
}

// Obtener cookie
function getCookie(nombre) {
    const nombreCookie = nombre + "=";
    const cookies = document.cookie.split(';');
    for(let i = 0; i < cookies.length; i++) {
        let cookie = cookies[i];
        while (cookie.charAt(0) == ' ') {
            cookie = cookie.substring(1);
        }
        if (cookie.indexOf(nombreCookie) == 0) {
            return cookie.substring(nombreCookie.length, cookie.length);
        }
    }
    return "";
}

// Eliminar cookie
function deleteCookie(nombre) {
    document.cookie = nombre + "=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
}

// Mostrar mensaje de bienvenida si hay cookie de sesión
function verificarBienvenida() {
    const usuario = getCookie('remember_username');
    if (usuario && window.location.pathname === '/index.html') {
        console.log('Bienvenido de vuelta, ' + usuario);
    }
}

// Ejecutar al cargar la página
document.addEventListener('DOMContentLoaded', verificarBienvenida);