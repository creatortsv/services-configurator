<?php

namespace Creatortsv\ServicesConfiguratorBundle\Services;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ServiceFactory
{
    /**
     * @var RequestStack $requestStack
     * @var Container $container
     * @return ServiceInterface
     */
    public function __invoke(
        RequestStack $requestStack,
        Container $container
    ): ServiceInterface
    {
        $name = $requestStack
            ->getCurrentRequest()
            ->query
            ->get('service_name');

        if (is_string($name) && $container->has($name) && ($service = $container->get($name)) instanceOf ServiceInterface) {
            return $service->configure();
        }

        throw new NotFoundHttpException('Service Not Found!');
    }
}