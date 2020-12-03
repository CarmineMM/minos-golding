<!doctype html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Iniciar sesion</title>
</head>
<body>

    <p><?= get_notifications('register') ?></p>
    <form action="<?= route('login.in') ?>" method="post">
        <input type="email" placeholder="email" name="email">
        <input type="password" name="password" placeholder="Contraseña">
        <?= csrf_input(); ?>
        <button type="submit">Enviar</button>
    </form>

    <div>
        <?php foreach (get_warnings() as $warning): ?>
        <p><?= $warning; ?></p>
        <?php endforeach; ?>
    </div>
</body>
</html>