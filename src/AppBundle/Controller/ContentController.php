<?php

namespace AppBundle\Controller;

use Pimcore\Controller\FrontendController;
use Symfony\Component\HttpFoundation\Request;

class ContentController extends FrontendController
{
    public function defaultAction(Request $request)
    {
        echo 'default';
    }

    public function productAction(Request $request)
    {
        echo 'products';
    }
}