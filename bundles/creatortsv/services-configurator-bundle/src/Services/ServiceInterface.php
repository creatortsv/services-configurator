<?php

namespace Creatortsv\ServicesConfiguratorBundle\Services;

interface ServiceInterface
{
    /**
     * @return ServiceInterface
     */
    public function configure(): self;

    /**
     * @return array
     */
    public function fetchSettings(): array;

    /**
     * @param array $settings
     * @return array
     */
    public function saveSettings(array $settings): array;

    /**
     * @return array
     */
    public static function defaultValues(): array;
}
