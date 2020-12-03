<!doctype html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?= title($title??false) ?></title>
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
            color: var(--primary);
            background: rgba(250, 250, 250, 1);
            font-size: 16px;
            text-align: center;
        }
        h1 { font-weight: lighter }
        h2 {
            font-size: 5rem;
            /*letter-spacing: .1em;*/
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
        <?= $this->section('main') ?>
    </main>
</body>
</html>