<?php

declare(strict_types=1);

namespace NetglueRealIPTest;

use NetglueRealIP\Module;
use PHPUnit\Framework\TestCase;

class ModuleTest extends TestCase
{
    public function testBasic(): void
    {
        $module = new Module();
        $this->assertIsArray($module->getServiceConfig());
        $this->assertIsArray($module->getConfig());
        $this->assertIsArray($module->getControllerPluginConfig());
    }
}
