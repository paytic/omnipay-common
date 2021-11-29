<?php

namespace Paytic\Omnipay\Common\Tests\Models;

use Paytic\Omnipay\Common\Models\Token;
use Paytic\Omnipay\Common\Tests\AbstractTest;
use Nip\Utility\Date;

/**
 * Class TokenTest
 * @package Paytic\Omnipay\Common\Tests
 */
class TokenTest extends AbstractTest
{
    /**
     * @dataProvider data_getExpirationDate
     */
    public function test_getExpirationDate($expiration, $return)
    {
        $data = ['expiration_date' => $expiration];
        $token = new Token($data);

        self::assertEquals($return, $token->getExpirationDate());
    }

    public function test_hasExpirationDate()
    {
        $token = new Token(['expiration_date' => null]);
        self::assertFalse($token->hasExpirationDate());

        $token = new Token(['expiration_date' => '2022-02-22']);
        self::assertTrue($token->hasExpirationDate());
    }

    public function test_isExpired()
    {
        $token = new Token(['expiration_date' => null]);
        self::assertFalse($token->isExpired());

        $token = new Token(['expiration_date' => '2020-02-22']);
        self::assertTrue($token->isExpired());

        $token = new Token(['expiration_date' => \date('Y-m-d')]);
        self::assertTrue($token->isExpired());

        $token = new Token(['expiration_date' => \date('Y-m-d', strtotime('tomorrow'))]);
        self::assertFalse($token->isExpired());
    }

    public function data_getExpirationDate()
    {
        return [
            [null, null],
            ['2022-02-22', Date::create(2022, 02, 22)],
            ['2022-02-22 00:00:00', Date::create(2022, 02, 22)],
            ['2022-02-22 23:00:00', Date::create(2022, 02, 22, 23)],
        ];
    }
}