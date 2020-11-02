<?php

declare(strict_types=1);

namespace NetglueRealIP;

use Laminas\ModuleManager\Feature\ConfigProviderInterface;
use Laminas\ModuleManager\Feature\ControllerPluginProviderInterface;
use Laminas\ModuleManager\Feature\ServiceProviderInterface;

class Module implements ServiceProviderInterface, ControllerPluginProviderInterface, ConfigProviderInterface
{
    /** @var ConfigProvider */
    private $configProvider;

    public function __construct()
    {
        $this->configProvider = new ConfigProvider();
    }

    /** @return mixed[] */
    public function getConfig(): array
    {
        return [
            'proxy_headers' => $this->configProvider->getProxyHeaderSetup(),
        ];
    }

    /** @return mixed[] */
    public function getControllerPluginConfig(): array
    {
        return $this->configProvider->getZendMvcControllerPluginConfig();
    }

    /** @return mixed[] */
    public function getServiceConfig(): array
    {
        return $this->configProvider->getDependencies();
    }
}
