<?php
echo $this->Content->start('title');
    echo $title;
echo $this->Content->end('title');

echo $this->Content->start('main_content');
?>

<div class="full-w flex-center">
    <div class="error">
        <h1 class="title">Exception: <?= $message; ?></h1>
    </div>
</div>

<?= $this->Content->end('main_content'); ?>
