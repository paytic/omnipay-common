<?php

namespace ByTIC\Omnipay\Common\Tests\Fixtures\Message;

use ByTIC\Omnipay\Common\Message\Traits\HasLanguageRequestTrait;
use ByTIC\Omnipay\Common\Message\Traits\RequestDataGetWithValidationTrait;
use ByTIC\Omnipay\Common\Message\Traits\SendDataRequestTrait;
use Omnipay\Common\Message\AbstractRequest as CommonAbstractRequest;

/**
 * Class PurchaseRequest
 * @package ByTIC\Omnipay\Common\Tests\Fixtures\Message
 */
class PurchaseRequest extends CommonAbstractRequest
{
    use SendDataRequestTrait;
    use HasLanguageRequestTrait;
    use RequestDataGetWithValidationTrait;
}
