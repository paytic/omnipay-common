<?php

namespace ByTIC\Omnipay\Common\Library\View;

use ArrayAccess;

/**
 * Class View
 */
class View implements ArrayAccess
{

    /**
     * The path to the view file.
     *
     * @var string
     */
    protected $path;
    protected $basePath = null;
    /**
     * View data
     * @var array
     */
    private $data;

    /**
     * render Renders the view using the given data
     *
     * @param $path
     * @param array $data
     * @return mixed
     */
    public function render($path, $data = [])
    {
        $this->data = array_merge($this->data, $data);
        $this->setPath($path);
        $contents = $this->renderContents();

        return $contents;
    }

    /**
     * @return string
     */
    protected function renderContents()
    {
        $path = $this->buildPath();
        ob_start();
        /** @noinspection PhpIncludeInspection */
        include($path);
        $html = ob_get_contents();
        ob_end_clean();
        return $html;
    }


    /**
     * Builds path for including
     * If $view starts with / the path will be relative to the root of the views folder.
     * Otherwise to caller file location.
     *
     * @param string $path
     * @return string
     */
    protected function buildPath($path = '')
    {
        $path = $path ? $path : $this->getPath();
        return $this->getBasePath() . ltrim($path, "/") . '.php';
    }

    //
    // GETTERS AND SETTERS
    //

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @param string $path
     */
    public function setPath($path)
    {
        $this->path = $path;
    }

    /**
     * @return null
     */
    public function getBasePath()
    {
        return $this->basePath;
    }

    /**
     * @param null $basePath
     */
    public function setBasePath($basePath)
    {
        $this->basePath = $basePath;
    }

    /**
     * @param  string $name
     * @return mixed|null
     */
    public function get($name)
    {
        if ($this->has($name)) {
            return $this->data[$name];
        } else {
            return null;
        }
    }

    /**
     * @param string $name
     * @return bool
     */
    public function has($name)
    {
        return isset($this->data[$name]);
    }

    /**
     * @param string $name
     * @param mixed $value
     * @return $this
     */
    public function set($name, $value)
    {
        $this->data[$name] = $value;
        return $this;
    }

    public function append($name, $value)
    {
        $valueOld = $this->get($name);
        return $this->set($name, $valueOld . $value);
    }

    //
    // ARRAY ACCESS METHODS
    //

    /**
     * @param mixed $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        return isset($this->data[$offset]);
    }

    /**
     * @param mixed $offset
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->data[$offset];
    }

    /**
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value)
    {
        $this->data[$offset] = $value;
    }

    /**
     * @param mixed $offset
     */
    public function offsetUnset($offset)
    {
        unset($this->data[$offset]);
    }
}
