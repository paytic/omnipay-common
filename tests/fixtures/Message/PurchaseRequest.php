<?php

namespace Paytic\Omnipay\Common\Tests\Fixtures\Message;

use Paytic\Omnipay\Common\Message\Traits\HasLanguageRequestTrait;
use Paytic\Omnipay\Common\Message\Traits\RequestDataGetWithValidationTrait;
use Paytic\Omnipay\Common\Message\Traits\SendDataRequestTrait;
use Omnipay\Common\Message\AbstractRequest as CommonAbstractRequest;

/**
 * Class PurchaseRequest
 * @package Paytic\Omnipay\Common\Tests\Fixtures\Message
 */
class PurchaseRequest extends CommonAbstractRequest
{
    use SendDataRequestTrait;
    use HasLanguageRequestTrait;
    use RequestDataGetWithValidationTrait;
}
