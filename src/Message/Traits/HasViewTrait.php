<?php

namespace ByTIC\Omnipay\Common\Message\Traits;

use ByTIC\Omnipay\Common\Library\View\View;

/**
 * Trait HasViewTrait
 * @package ByTIC\Omnipay\Common\Message\Traits
 */
trait HasViewTrait
{

    /**
     * @var View
     */
    protected $view = null;

    /**
     * @return $this
     */
    public function send()
    {
        echo $this->getViewContent();

        return $this;
    }

    /**
     * @return string
     */
    public function getViewContent()
    {
        $this->getView()->set('response', $this);
        $this->initViewVars();

        return $this->getView()->render($this->getViewFile());
    }

    /**
     * @return View|null
     */
    public function getView()
    {
        if ($this->view === null) {
            $this->initView();
        }

        return $this->view;
    }

    /**
     * @param View $view
     */
    public function setView($view)
    {
        $this->view = $view;
    }

    protected function initView()
    {
        $view = $this->newView();
        $view->setBasePath($this->generateViewPath());
        $this->setView($view);
    }

    /**
     * @return string
     */
    protected function generateViewPath()
    {
        return dirname(dirname(dirname(__FILE__)))
            . DIRECTORY_SEPARATOR . 'Resources'
            . DIRECTORY_SEPARATOR . 'views'
            . DIRECTORY_SEPARATOR;
    }

    /**
     * @return View
     */
    protected function newView()
    {
        return new View();
    }

    protected function initViewVars()
    {
    }

    /**
     * @return string
     */
    abstract protected function getViewFile();
}
