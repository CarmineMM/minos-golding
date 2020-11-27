<!doctype html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?= title($status) ?></title>
    <style>
        * {
            box-sizing: border-box;
            padding: 0;
            margin: 0;
        }
        :root {
            --primary: #354b58;
            --secondary: #f3d01c;
        }
        a {
            text-decoration: none;
            color: inherit;
        }
        a:hover {
            text-decoration: underline;
        }
        .text-secondary { color: var(--secondary); }
        body {
            font-family: Arial, sans-serif;
            font-weight: 100;
            line-height: 1.5;
            color: white;
            background: var(--primary);
            font-size: 16px;
            text-align: center;
        }
        h1 { font-weight: lighter }
        h2 {
            font-size: 6.3rem;
            letter-spacing: .1em;
            line-height: 1;
            font-weight: bold;
        }

        h3 { font-size: 2rem; }
        p {
            font-size: 1.1rem;
        }
        small { font-size: .8rem; }

        main {
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            height: 100vh;
            position: relative;
            width: calc(100% - 4rem);
            margin: 0 auto;
        }
        nav {
            position: absolute;
            top: 20px;
            text-align: left;
            width: 100%;
            font-size: .85rem;
            display: flex;
            justify-content: space-between;
        }
        .mb-1 { margin-bottom: 1rem; }
        .mb-2 { margin-bottom: 2rem; }
        .copyright { max-width: 500px; }
        .copyright p, .copyright ul { font-size: .8rem; }
        .copyright strong { font-weight: bold; }
    </style>
</head>
<body>
<main>
    <nav>
        <h1><?= app_name(); ?></h1>

        <?php if ( app_environment() !== 'production' ): ?>
        <h1><a href="mailto:carminemaggiom@gmail.com"><?= framework_developer() ?></a></h1>
        <?php endif; ?>
    </nav>

    <div class="mb-1">
        <h2 class="text-secondary"><?= $status ?></h2>
        <h3 ><?= $statusText ?></h3>
    </div>

    <div class="mb-2">
        <?php if ( $status === 404 ): ?>
        <p>Vaya, vaya, al parecer no deberías estar aquí.</p>
        <?php elseif ($status === 405): ?>
        <p>Ok amigo, por aquí no es...</p>
        <?php elseif ($status === 500): ?>
        <p>Algo raro esta pasando...</p>
        <?php elseif ($status === 406): ?>
        <p>Wow, ¿para donde vas?</p>
        <?php endif; ?>

        <p>Intenta <a href="<?= app_url(); ?>" class="text-secondary">otra cosa.</a></p>
    </div>

    <?php if ( app_environment() !== 'production' ): ?>
    <div class="copyright">
        <p class="mb-1"><strong><?= framework_name() ?> &copy;</strong> es un Framework desarrollado en PHP para soluciones rápidas y sencillas, siendo desarrollado y mantenido actualmente por <strong><a href="mailto:carminemaggiom@gmail.com"><?= framework_developer() ?></a></strong>, sus principales puntos fuertes son:</p>
        <ul>
            <li>Usa <strong>MVC</strong> como patron de diseño.</li>
            <li>Fácil de usar, solo se necesita saber <strong>PHP.</strong></li>
            <li>Viene incorporado con <strong>Composer</strong> y lo usa como raíz.</li>
<!--            <li>No tiene dependencias de terceros, pero puede expandirse gracias a <strong>Composer</strong></li>-->
            <li>100% escalable</li>
            <li>Entre <strong>muchas</strong> otras..</li>
        </ul>
    </div>
    <?php endif; ?>
</main>
</body>
</html>