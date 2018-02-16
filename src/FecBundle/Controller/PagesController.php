<?php

namespace FecBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class PagesController extends Controller
{
    public function indexAction()
    {
        return $this->render('@Fec/Pages/index.html.twig');
    }
}
