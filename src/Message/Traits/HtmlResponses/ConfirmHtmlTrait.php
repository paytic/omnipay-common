<?php

namespace Paytic\Omnipay\Common\Message\Traits\HtmlResponses;

use Paytic\Omnipay\Common\Message\Traits\DataAccessorsTrait;
use Paytic\Omnipay\Common\Message\Traits\HasViewTrait;
use Omnipay\Common\Message\RequestInterface;

/**
 * Class RedirectHtmlTrait
 * @package Paytic\Omnipay\Common\Message\Traits
 *
 * @method RequestInterface getRequest
 * @method array getData
 */
trait ConfirmHtmlTrait
{
    use DataAccessorsTrait;
    use HasViewTrait;
    use HasButtonTrait;
    use HasRedirectUrlTrait;

    /**
     * @return string
     */
    public function getIconClass()
    {
        $type = $this->getMessageType();
        switch ($type) {
            case 'success':
                return 'fa fa-check-circle';
            case 'error':
                return 'fa fa-exclamation-triangle';
        }

        return 'fa fa-info-circle';
    }

    /**
     * @return string
     */
    public function getMessageType()
    {
        $type = 'error';

        if ($this->isSuccessful()) {
            $type = 'success';
        }
        if ($this->isCancelled()) {
            $type = 'error';
        }
        if ($this->isPending()) {
            $type = 'info';
        }

        return $type;
    }

    /**
     * @return string
     */
    public function getIconColor()
    {
        $type = $this->getMessageType();
        switch ($type) {
            case 'success':
                return '#3c763d';
            case 'error':
                return '#e45a5a';
        }

        return '#5aa5e4';
    }

    /**
     * @return string
     */
    public function getViewFile()
    {
        return '/confirm';
    }
}
