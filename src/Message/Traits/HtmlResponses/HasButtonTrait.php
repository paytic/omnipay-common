<?php

namespace ByTIC\Omnipay\Common\Message\Traits\HtmlResponses;

/**
 * Trait HasButtonTrait
 * @package ByTIC\Omnipay\Common\Message\Traits\HtmlResponses
 */
trait HasButtonTrait
{
    protected $button = null;

    /**
     * @param $label
     * @param $href
     * @return $this
     */
    public function setButton($label, $href)
    {
        $this->button = [
            'label' => $label,
            'href' => $href,
        ];

        return $this;
    }

    /**
     * @return null|string
     */
    public function getButtonLabel()
    {
        return $this->hasButton() ? $this->button['label'] : null;
    }

    /**
     * @return bool
     */
    public function hasButton()
    {
        return is_array($this->button);
    }

    /**
     * @return null|string
     */
    public function getButtonHref()
    {
        return $this->hasButton() ? $this->button['href'] : null;
    }
}
