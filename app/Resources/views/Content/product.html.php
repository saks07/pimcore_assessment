<?php
/**
 * @var \Pimcore\Templating\PhpEngine $this
 * @var \Pimcore\Templating\PhpEngine $view
 * @var \Pimcore\Templating\GlobalVariables $app
 */

$this->extend('layout.html.php');

?>
product
<h1><?= $this->input("headline", ["width" => 540]); ?></h1>

<div class="product-info">
    <?php if($this->editmode):
        echo $this->relation('product');
    else: ?>

    <!-- Product information-->

    <?php endif; ?>
</div>