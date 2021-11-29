<?php

namespace Paytic\Omnipay\Common\Tests\Models;

use Paytic\Omnipay\Common\Models\Recurring;
use Paytic\Omnipay\Common\Tests\AbstractTest;

/**
 * Class RecurringTest
 * @package Paytic\Omnipay\Common\Tests
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