<?php

namespace FullSIX\Bundle\WordPressBundle;

use Symfony\Component\HttpFoundation\Response;

class WordPressResponse extends Response
{
    protected $targetUrl;
    protected $params;

    /**
     * {@inheritDoc}
     */
    public function __construct($url = null, $params = array(), $status = 200, $headers = array())
    {
        parent::__construct('', $status, $headers);
        $this->setTargetUrl($url);
        $this->setParams($params);
    }

    /**
     * {@inheritDoc}
     */
    public static function page($url = null, $params = array(), $status = 200, $headers = array())
    {
        return new static($url, $params, $status, $headers);
    }

    public static function currentPage($params = array(), $status = 200, $headers = array())
    {
        return new static(null, $params, $status, $headers);
    }

    public function getTargetUrl()
    {
        return $this->targetUrl;
    }

    public function setTargetUrl($url)
    {
        $this->targetUrl = $url;
        return $this;
    }

    public function getParams()
    {
        return $this->params;
    }

    public function setParams($params)
    {
        $this->params = $params;
    }

}
