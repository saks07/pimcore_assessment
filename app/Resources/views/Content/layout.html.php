<?php
/**
 * @var \Pimcore\Templating\PhpEngine $this
 * @var \Pimcore\Templating\PhpEngine $view
 * @var \Pimcore\Templating\GlobalVariables $app
 */ 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Example</title>
    <link rel="stylesheet" type="text/css" href="/static/css/global.css" />
</head>
<body>
    <div id="site">
        <div id="logo">
            <a href="http://www.pimcore.org/"><img src="/bundles/pimcoreadmin/img/logo-gray.svg" style="width: 200px;" /></a>
            <hr />
            <div class="claim">
                THE OPEN-SOURCE ENTERPRISE PLATFORM FOR PIM, CMS, DAM & COMMERCE
            </div>
        </div>
        <div class="info">
            <?php $this->slots()->output('_content') ?>
        </div>
    </div>
</body>
</html>