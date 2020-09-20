<html>
    <head>
        <meta charset="UTF-8">
        <title><?= APP_CONFIG['application']['name']; ?></title>

        <!-- Stylesheets -->
        <?php
            //echo $this->include('styles');
        ?>

        <!-- Javascript -->
        <?php
            //echo $this->include('scripts');
        ?>
    </head>

    <body>
        <?= $this->include('main'); ?>
    </body>
</html>
