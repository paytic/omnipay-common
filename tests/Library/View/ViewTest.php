<?php

namespace ByTIC\Omnipay\Common\Tests\Library\View;

use ByTIC\Omnipay\Common\Library\View\View;
use ByTIC\Omnipay\Common\Tests\AbstractTest;

/**
 * Class ViewTest
 * @package ByTIC\Omnipay\Common\Tests\Library\View
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
