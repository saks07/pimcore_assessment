<?php

namespace MagentoProductsBundle;

use Pimcore\Extension\Bundle\AbstractPimcoreBundle;

class MagentoProductsBundle extends AbstractPimcoreBundle
{
    public function getJsPaths()
    {
        return [
            '/bundles/magentoproducts/js/pimcore/startup.js'
        ];
    }
}