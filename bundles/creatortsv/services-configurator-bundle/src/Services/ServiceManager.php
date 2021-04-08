<?php

namespace Creatortsv\ServicesConfiguratorBundle\Services;

use ReflectionClass;
use Symfony\Component\String\CodePointString;

class ServiceManager
{
    /**
     * @var ServiceInterface[]
     */
    protected $services;

    /**
     * @param ServiceInterface[] $services
     * @return void
     */
    public function __construct(ServiceInterface ...$services)
    {
        $this->services = array_combine(
            array_map(
                fn(ServiceInterface $service): string => (new CodePointString((new ReflectionClass($service))->getShortName()))->snake(),
                $services
            ),
            $services
        );
    }
}
