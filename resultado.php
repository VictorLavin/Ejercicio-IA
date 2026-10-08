<?php
declare(strict_types=1);
session_start();

/* Página de resultados: solo se muestra si antes se envió el formulario */
if (empty($_SESSION['jugador'])) {
    header('Location: index.php');
    exit;
}
$j = $_SESSION['jugador'];
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
        <p><strong>Nombre:</strong> <?= $j['nombre'] ?></p>
        <p><strong>Alias:</strong> <?= $j['alias'] ?></p>
        <p><strong>Edad:</strong> <?= (int)$j['edad'] ?></p>
        <p><strong>Armas seleccionadas:</strong> <?= htmlspecialchars($j['armas']) ?></p>
        <p><strong>¿Practica artes mágicas?:</strong> <?= $j['magia'] ?></p>
      </div>
      <div class="imagen">
<?php if ($j['rutaImagen']): ?>
        <p class="titulo">Imagen subida:</p>
        <img src="<?= htmlspecialchars((string)$j['rutaImagen']) ?>" alt="Imagen del jugador">
<?php else: ?>
        <p class="titulo">No se subió ninguna imagen.</p>
        <img src="calavera.png" alt="Calavera">
<?php   if ($j['hayError']): ?>
        <p>Error al subir la imagen</p>
<?php   endif; ?>
<?php endif; ?>
      </div>
    </div>
  </div>
</body>
</html>
