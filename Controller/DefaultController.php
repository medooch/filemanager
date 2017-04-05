<?php

namespace Core\FilemanagerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('FilemanagerBundle:Default:index.html.twig');
    }
}
