<?php

/**
 * NG Framework
 * Version 0.1 Beta
 * Copyright (c) 2012, Nick Gejadze
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a copy 
 * of this software and associated documentation files (the "Software"), 
 * to deal in the Software without restriction, including without limitation 
 * the rights to use, copy, modify, merge, publish, distribute, sublicense, 
 * and/or sell copies of the Software, and to permit persons to whom the 
 * Software is furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included 
 * in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, 
 * INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A 
 * PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR 
 * COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, 
 * WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, 
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */

namespace NG;

/**
 * Uri
 * @package NG
 * @subpackage library
 * @version 0.1
 * @copyright (c) 2012, Nick Gejadze
 */
class Uri {

    /**
     * $instance
     * Holds Class Instance
     * @access protected
     * @var object
     */
    protected static $instance = null;

    /**
     * $requestedPath
     * Holds $_SERVER['REQUEST_URI']
     * @access protected
     * @var string
     */
    protected $requestedPath;

    /**
     * $rootPath
     * Holds ROOT path
     * @access protected
     * @var string
     */
    protected $rootPath;

    /**
     * $baseURL
     * Holds base URL of application
     * @access protected 
     * @var string 
     */
    protected static $baseURL;

    /**
     * __construct
     * Sets reuqestedPath and rootPath, ROOT must be defined
     * @access public
     * @return void
     */
    public function __construct() {
        $this->requestedPath = $_SERVER['REQUEST_URI'];
        $this->rootPath = ROOT;
    }

    /**
     * init()
     * if $instance is not set starts new \NG\Uri and return instance
     * @access public
     * @return object
     */
    public static function init() {
        if (self::$instance === null):
            self::$instance = new Uri;
        endif;
        return self::$instance;
    }

    /**
     * baseUrl()
     * Checks if baseURL was set, if not returns $_SERVER['HTTP_HOST']
     * @access public
     * @return string
     */
    public static function baseUrl() {
        if (!isset(self::$baseURL) OR empty(self::$baseURL)):
            self::$baseURL = 'http://' . $_SERVER['HTTP_HOST'] . "/";
        endif;
        return self::$baseURL;
    }

    /**
     * setBaseUrl()
     * sets application baseURL
     * @access public
     * @param string $url
     * @return void
     */
    public static function setBaseUrl($url) {
        self::init()->baseURL = $url;
    }

    /**
     * getPath()
     * if is set requestedPath object reutns, otherwise false is returned 
     * @access public
     * @return string|boolean
     */
    public function getPath() {
        if (isset($this->requestedPath)):
            return $this->requestedPath;
        endif;
        return false;
    }

    /**
     * getPathArray()
     * Returns path as array     
     * e.g.:  /category/music/page/123 will be trnaslated to array("category" => "music", "page" => "123")
     * @see pathToArray()
     * @access public
     * @return array|boolean
     */
    public function getPathArray() {
        return $this->pathToArray();
    }

    /**
     * pathToArray()
     * Translates path to array, sets array as $key => $value
     * @see getPathChunks()
     * @access public
     * @return array|boolean
     */
    public function pathToArray() {
        $pathChunks = $this->getPathChunks();
        if ($pathChunks):
            $result = array();
            for ($i = 0; $i < sizeof($pathChunks); $i+=2):
                $result[preg_replace("/\\.[^.\\s]{3,4}$/", "", $pathChunks[$i])] = isset($pathChunks[$i + 1]) ? preg_replace("/\\.[^.\\s]{3,4}$/", "", $pathChunks[$i + 1]) : false;
            endfor;
            return $result;
        endif;
        return false;
    }

    /**
     * getPathChunks()
     * explodes requestedPath and rootPath, determines parameters and returns as array, false is returned if no segment is found in the requestedPath
     * @access public
     * @return array|boolean
     */
    public function getPathChunks() {
        if (isset($this->requestedPath)):
            $pathChunks = explode('/', trim($this->requestedPath, '/'));
            $rootChunks = explode('/', trim($this->rootPath, '/'));
            $subdirectories = array_intersect($pathChunks, $rootChunks);
            foreach ($subdirectories as $key => $directory):
                unset($pathChunks[$key]);
            endforeach;
            $pathChunks = array_values($pathChunks);
            if (!empty($pathChunks[0])):
                return $pathChunks;
            endif;
        endif;
        return false;
    }

}

?>
