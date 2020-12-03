<!doctype html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Hola</title>
</head>
<body>
    <form action="<?= route('register.in'); ?>" method="post">
        <p>
            <input type="email" name="email" placeholder="Email" value="<?= get_notifications('email') ?>">
        </p>
        <p>
            <input type="password" name="password" placeholder="Contraseña">
            <?php
                if ( is_array(get_warnings('level_password')) ):
                foreach (get_warnings('level_password') as $message): ?>
            <p><?= $message ?></p>
            <?php
                endforeach;
                endif;
            ?>
        </p>

        <p>
            <input type="password" name="password_confirm" placeholder="Repite la Contraseña">
            <?= get_warnings('password_confirm') ?>
            <?= get_errors('email_pass_required') ?>
        </p>

        <p>
            <?= csrf_input() ?>
            <button type="submit">Enviar</button>
        </p>
    </form>

    <?php showDev($_SESSION); ?>

    <ul>
        <?php foreach (get_errors() as $key => $error): ?>
        <li><?= $key ?> - <?= $error ?></li>
        <?php endforeach; ?>
    </ul>

    <ul>
        <?php foreach (get_warnings() as $key => $error): ?>
         <?php
            if ( is_string($error) )
                echo "<li>{$key} - {$error}</li>";
        ?>
        <?php endforeach; ?>
    </ul>
</body>
</html>