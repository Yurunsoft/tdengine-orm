<?php

declare(strict_types=1);

namespace Yurun\TDEngine\Orm\Test;

use Yurun\TDEngine\Orm\ClientHandler\Extension\Handler;
use Yurun\TDEngine\Orm\TDEngineOrm;
use Yurun\TDEngine\TDEngineManager;

class ExtensionModelTest extends ModelTest
{
    protected function setUp(): void
    {
        if (!\extension_loaded('tdengine'))
        {
            $this->markTestSkipped();
        }
        TDEngineManager::setDefaultClientName('test-extension');
        TDEngineOrm::setClientHandler(new Handler());
    }
}
