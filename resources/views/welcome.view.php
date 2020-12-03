<?php

$this->extend('layouts/main');

$this->start('main');
?>

<nav>
    <h1><?= framework_name(); ?></h1>

    <?php if ( app_environment() !== 'production' ): ?>
        <h1><a href="mailto:carminemaggiom@gmail.com"><?= framework_developer() ?></a></h1>
    <?php endif; ?>
</nav>


<div class="mb-1">
    <h2><?= framework_name() ?></h2>
    <h3><?= $title ?></h3>
</div>

<div class="copyright">
    <p class="mb-1"><strong><?= framework_name() ?> &copy;</strong> es un Framework desarrollado en PHP para soluciones rápidas y sencillas, siendo desarrollado y mantenido actualmente por <strong><a href="mailto:carminemaggiom@gmail.com"><?= framework_developer() ?></a></strong>, sus principales puntos fuertes son:</p>
    <ul>
        <li>100% escalable.</li>
        <li>Trabaja con rutas amigables.</li>
        <li>Usa <strong>MVC</strong> como patron de diseño.</li>
        <li>Fácil de usar, solo se necesita saber <strong>PHP.</strong></li>
        <li>Viene incorporado con <strong>Composer</strong> y lo usa como raíz.</li>
        <li>No tiene dependencias de terceros, pero puede expandirse gracias a <strong>Composer.</strong></li>
        <li>Entre <strong>muchas</strong> otras..</li>
    </ul>
</div>

<?= $this->end() ?>
