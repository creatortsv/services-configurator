<?php

namespace App\Controller;

use Creatortsv\ServicesConfiguratorBundle\Services\ServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{
    Request,
    Response
};
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class SettingsController extends AbstractController
{
    /**
     * @param ServiceInterface $service
     * @return Response
     */
    public function show(ServiceInterface $service): Response
    {
        return $this->json($service->fetchSettings());
    }

    /**
     * @param Request $request
     * @param ServiceInterface $service
     * @return Response
     */
    public function save(Request $request, ServiceInterface $service): Response
    {
        try {
            return $this->json($service->saveSettings($request->request->all()));
        } catch (UnprocessableEntityHttpException $e) {
            return $this->json($e->getMessage(), $e->getStatusCode());
        }
    }
}
