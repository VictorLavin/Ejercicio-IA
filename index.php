<?php
declare(strict_types=1);

/* GET  -> muestra el formulario
   POST -> procesa y muestra los datos */

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    readfile(__DIR__ . '/captura.html');
    exit;
}

const MAX_BYTES  = 10240;                       // 10 KB
const DIR_UPLOAD = __DIR__ . '/uploads/';
const ARMAS_OK   = ['Maza', 'Martillo', 'Espada', 'Arco'];

/* Evita inyección de código (HTML/JS) al mostrar texto del usuario */
function limpiar(string $s): string
{
    $s = trim(strip_tags($s));
    return htmlspecialchars($s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

$nombre = limpiar((string)($_POST['nombre'] ?? ''));
$alias  = limpiar((string)($_POST['alias'] ?? ''));
$edad   = filter_var($_POST['edad'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'max_range' => 120]]);
$magia  = ($_POST['magia'] ?? '') === 'Sí' ? 'Sí' : (($_POST['magia'] ?? '') === 'No' ? 'No' : '');

$armas = array_values(array_intersect(ARMAS_OK, (array)($_POST['armas'] ?? [])));
$armasTxt = $armas ? implode(', ', $armas) : 'Ninguna';

if ($nombre === '' || $alias === '' || $edad === false || $magia === '') {
    http_response_code(400);
    exit('Datos incompletos o no válidos. <a href="index.php">Volver</a>');
}

/* ---------- Imagen ---------- */
$rutaImagen = null;   // imagen subida correctamente
$hayError   = false;  // se intentó subir algo y falló

$f = $_FILES['imagen'] ?? null;
if ($f && $f['error'] !== UPLOAD_ERR_NO_FILE) {
    $ok = $f['error'] === UPLOAD_ERR_OK
        && $f['size'] > 0 && $f['size'] <= MAX_BYTES
        && is_uploaded_file($f['tmp_name'])
        && (new finfo(FILEINFO_MIME_TYPE))->file($f['tmp_name']) === 'image/png'
        && @getimagesize($f['tmp_name']) !== false;

    if ($ok) {
        if (!is_dir(DIR_UPLOAD)) {
            @mkdir(DIR_UPLOAD, 0755, true);
        }
        $nombreFichero = bin2hex(random_bytes(8)) . '.png'; // nombre generado: sin path traversal
        if (move_uploaded_file($f['tmp_name'], DIR_UPLOAD . $nombreFichero)) {
            $rutaImagen = 'uploads/' . $nombreFichero;
        } else {
            $hayError = true;
        }
    } else {
        $hayError = true;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Datos del Jugador</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <div class="tarjeta">
    <h2>Datos del Jugador</h2>
    <div class="contenido">
      <div class="datos">
        <p><strong>Nombre:</strong> <?= $nombre ?></p>
        <p><strong>Alias:</strong> <?= $alias ?></p>
        <p><strong>Edad:</strong> <?= (int)$edad ?></p>
        <p><strong>Armas seleccionadas:</strong> <?= htmlspecialchars($armasTxt) ?></p>
        <p><strong>¿Practica artes mágicas?:</strong> <?= $magia ?></p>
      </div>
      <div class="imagen">
<?php if ($rutaImagen): ?>
        <p class="titulo">Imagen subida:</p>
        <img src="<?= htmlspecialchars($rutaImagen) ?>" alt="Imagen del jugador">
<?php else: ?>
        <p class="titulo">No se subió ninguna imagen.</p>
        <img src="calavera.png" alt="Calavera">
<?php   if ($hayError): ?>
        <p>Error al subir la imagen</p>
<?php   endif; ?>
<?php endif; ?>
      </div>
    </div>
  </div>
</body>
</html>
