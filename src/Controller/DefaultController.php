<?php

namespace App\Controller;

use Creatortsv\ServicesConfiguratorBundle\Services\ServiceManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends AbstractController
{
    public function show(ServiceManager $manager)
    {
        var_dump($manager);
        die;
    }
}
