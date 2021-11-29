<?php

namespace Paytic\Omnipay\Common\Tests\Library\View;

use Paytic\Omnipay\Common\Library\View\View;
use Paytic\Omnipay\Common\Tests\AbstractTest;

/**
 * Class ViewTest
 * @package Paytic\Omnipay\Common\Tests\Library\View
 */
class ViewTest extends AbstractTest
{
    public function testGetSetPath()
    {
        $view = new View();

        self::assertEquals('', $view->getPath());

        $view->setPath('123456');
        self::assertEquals('123456', $view->getPath());
    }
}
