<html>
    <head>
        <meta charset="UTF-8">
        <title><?= $this->Content->placeholder('title'); ?></title>

        <!-- Stylesheets -->
        <?= $this->HtmlTags->css('error'); ?>
    </head>

    <body>
        <?= $this->Content->placeholder('main_content'); ?>
    </body>
</html>
