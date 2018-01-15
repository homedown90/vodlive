<?php
/**
 * Created by PhpStorm.
 * User: changtao
 * Date: 2018/1/11
 * Time: 17:53
 */
namespace Extend\Symfony;
use \Extend\Util\UtilTool;
use \Symfony\Component\HttpFoundation as HtttpFundation;
class FilterRequest extends     HtttpFundation\Request
{
    /**
     * Sets the parameters for this request.
     *
     * This method also re-initializes all properties.
     *
     * @param array           $query      The GET parameters
     * @param array           $request    The POST parameters
     * @param array           $attributes The request attributes (parameters parsed from the PATH_INFO, ...)
     * @param array           $cookies    The COOKIE parameters
     * @param array           $files      The FILES parameters
     * @param array           $server     The SERVER parameters
     * @param string|resource $content    The raw body data
     */
    public function initialize(array $query = array(), array $request = array(), array $attributes = array(), array $cookies = array(), array $files = array(), array $server = array(), $content = null)
    {

        $this->request = new HtttpFundation\ParameterBag(UtilTool::html_entity_encode_array($request));
        $this->query = new HtttpFundation\ParameterBag(UtilTool::html_entity_encode_array($query));
        $this->attributes = new HtttpFundation\ParameterBag($attributes);
        $this->cookies = new HtttpFundation\ParameterBag($cookies);
        $this->files = new HtttpFundation\FileBag($files);
        $this->server = new HtttpFundation\ServerBag($server);
        $this->headers = new HtttpFundation\HeaderBag($this->server->getHeaders());

        $this->content = $content;
        $this->languages = null;
        $this->charsets = null;
        $this->encodings = null;
        $this->acceptableContentTypes = null;
        $this->pathInfo = null;
        $this->requestUri = null;
        $this->baseUrl = null;
        $this->basePath = null;
        $this->method = null;
        $this->format = null;
    }
}