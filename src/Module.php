<?php
declare(strict_types=1);

namespace NetglueRealIP;

use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\ModuleManager\Feature\ControllerPluginProviderInterface;
use Zend\ModuleManager\Feature\ServiceProviderInterface;

class Module implements ServiceProviderInterface, ControllerPluginProviderInterface, ConfigProviderInterface
{

    private $configProvider;

    public function __construct()
    {
        $this->configProvider = new ConfigProvider();
    }

    public function getConfig() : array
    {
        return [
            'proxy_headers' => $this->configProvider->getProxyHeaderSetup()
        ];
    }

    public function getControllerPluginConfig() : array
    {
        return $this->configProvider->getZendMvcControllerPluginConfig();
    }

    public function getServiceConfig() : array
    {
        return $this->configProvider->getDependencies();
    }
}
