<?php

namespace ByTIC\Omnipay\Common\Tests;

use ByTIC\Omnipay\Common\Recurring;

/**
 * Class RecurringTest
 * @package ByTIC\Omnipay\Common\Tests
 */
class RecurringTest extends AbstractTest
{
    public function test_initialize()
    {
        $data = ['times' =>  10, 'interval' => '30'];
        $recurring = new Recurring($data);

        self::assertSame(10, $recurring->getTimes());
        self::assertSame(30, $recurring->getInterval());
    }
}