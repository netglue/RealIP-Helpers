<?php
declare(strict_types=1);

namespace NetglueRealIPTest;

use NetglueRealIP\Module;
use PHPUnit\Framework\TestCase;

class ModuleTest extends TestCase
{
    public function testBasic()
    {
        $module = new Module();
        $this->assertInternalType('array', $module->getServiceConfig());
        $this->assertInternalType('array', $module->getConfig());
        $this->assertInternalType('array', $module->getControllerPluginConfig());
    }
}
