// Validación en el cliente: solo PNG y máximo 10 KB
const MAX_BYTES = 10 * 1024;
const form = document.getElementById('formJugador');
const inputImagen = document.getElementById('imagen');
const mensaje = document.getElementById('mensaje');

function validarImagen() {
  const f = inputImagen.files[0];
  if (!f) return true; // es opcional
  const esPng = f.type === 'image/png' && /\.png$/i.test(f.name);
  if (!esPng) {
    mensaje.textContent = 'Solo se permiten imágenes PNG.';
    return false;
  }
  if (f.size > MAX_BYTES) {
    mensaje.textContent = 'La imagen no puede superar los 10 KB.';
    return false;
  }
  return true;
}

inputImagen.addEventListener('change', () => {
  mensaje.textContent = '';
  if (!validarImagen()) inputImagen.value = '';
});

form.addEventListener('submit', (e) => {
  mensaje.textContent = '';
  if (!form.nombre.value.trim() || !form.alias.value.trim()) {
    mensaje.textContent = 'Nombre y alias son obligatorios.';
    e.preventDefault();
    return;
  }
  const edad = parseInt(form.edad.value, 10);
  if (isNaN(edad) || edad < 1 || edad > 120) {
    mensaje.textContent = 'Indica una edad válida (1-120).';
    e.preventDefault();
    return;
  }
  if (!form.magia.value) {
    mensaje.textContent = 'Indica si practica artes mágicas.';
    e.preventDefault();
    return;
  }
  if (!validarImagen()) e.preventDefault();
});
