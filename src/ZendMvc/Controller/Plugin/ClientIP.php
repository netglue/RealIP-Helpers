<?php

declare(strict_types=1);

namespace NetglueRealIP\ZendMvc\Controller\Plugin;

use Laminas\Mvc\Controller\Plugin\AbstractPlugin;
use NetglueRealIP\Helper\ClientIPFromSuperGlobals;

class ClientIP extends AbstractPlugin
{
    /** @var ClientIPFromSuperGlobals */
    private $helper;

    public function __construct(ClientIPFromSuperGlobals $helper)
    {
        $this->helper = $helper;
    }

    public function __invoke(): ?string
    {
        return ($this->helper)();
    }
}
