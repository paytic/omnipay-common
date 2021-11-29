<?php

namespace Paytic\Omnipay\Common\Tests;

use Paytic\Omnipay\Common\Helper;

/**
 * Class HelperTest
 * @package Paytic\Omnipay\Common\Tests
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
        self::assertSame($formatted, Helper::stripNonAscii($name));
    }

    /**
     * @return array
     */
    public function formatPurchaseNameProvider()
    {
        return [
            ['Test Iñtërnâtiônàlizætiøn', 'Test Internationalization'],
            ['Test "Ț"', 'Test T'],
            ['Test for &rdquo;Html Entities&rdquo;&#039;', 'Test for Html Entities'],
            ['Test for permited symbols #$[]', 'Test for permited symbols #$[]']
        ];
    }
}
