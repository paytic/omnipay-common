<?php

namespace ByTIC\Omnipay\Common\Tests;

use ByTIC\Omnipay\Common\Helper;

/**
 * Class HelperTest
 * @package ByTIC\Omnipay\Common\Tests
 */
class HelperTest extends AbstractTest
{
    /**
     * @param $name
     * @param $formatted
     * @dataProvider formatPurchaseNameProvider
     */
    public function testFormatPurchaseName($name, $formatted)
    {
        self::assertSame($formatted, Helper::formatPurchaseName($name));
    }

    /**
     * @return array
     */
    public function formatPurchaseNameProvider()
    {
        return [
            ['Test Iñtërnâtiônàlizætiøn', 'Test Internationalization'],
            ['Test "Ț"', 'Test T']
        ];
    }
}
