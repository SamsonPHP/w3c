<?php
/**
 * Created by PhpStorm.
 * User: egorov
 * Date: 30.03.2015
 * Time: 20:56
 */
namespace samsonphp\w3c;

use samson\core\Service;
use samsonphp\w3c\violation\Collection;

/**
 * W3C validator SamsonPHP service.
 * This service is performing request to W3C HTML validation service
 * ans outputs its error for improving html markup quality.
 *
 * @package samsonphp\w3c
 * @author Vitalii Iehorov <egorov@samsonos.com>
 */
class Validator extends Service
{
    /** @var string Validating URL prefix, used for local domains */
    public $urlPrefix = '.samsonos.com';

    /** @var string URL for validating */
    public $w3cUrl = 'http://validator.w3.org/check';

    /** @var bool Validation result status */
    protected $w3cStatus;

    /** @var int Amount of validation errors found */
    protected $w3cErrorsCount = 0;

    /** @var int Amount of validation warnings found */
    protected $w3cWarningsCount = 0;

    /** @var \samsonphp\w3c\violation\Collection W3C Errors collection */
    protected $w3cErrors = array();

    /** @var \samsonphp\w3c\violation\Collection W3C Warnings collection */
    protected $w3cWarnings = array();

    /** @var bool Flag to perform w3c validation */
    protected $enabled = false;

    /**
     * Asynchronous validation controller action
     * @return array Asynchronous response array
     */
    public function __async_validate()
    {
        return array('status' => 1, 'validation' => $this->validate());
    }


    /** Module initialization logic */
    public function init(array $params = array())
    {
        $this->enabled = true;

        // Subscribe to resourcer event
        \samsonphp\event\Event::subscribe('resourcer.updated', array($this, 'enable'));
        \samsonphp\event\Event::subscribe('core.rendered', array($this, 'renderToken'));
    }

    /** Trigger function to enable automatic validation if resource ahs been changed */
    public function enable()
    {
        // Switch flag to enable validation if resource has been changed
        $this->enabled = true;
    }

    /** Event callback for rendering special HTML token to perform W3C validation */
    public function renderToken(&$output)
    {
        // If validation is enabled
        if ($this->enabled) {
            // Render HTML asynchronous validation token
            $output .= $this->view('index')->link($this->id . '/validate')->output();

            // Disable further validation async calls
            $this->enabled = false;
        }
    }

    /**
     * Perform HTTP request
     * @throws RequestException If W3C request has been failed
     * @returns string HTTP response results
     */
    protected function httpRequest()
    {
        // Build current HTML page url
        $url = $_SERVER['HTTP_HOST'] . $this->urlPrefix;

        // Add internal URL if present
        if ($_SERVER['REQUEST_URI'] !== '/') {
            $url .= '/' . $_SERVER['REQUEST_URI'];
        }

        // Build request URL with GET parameters
        $uri = $this->w3cUrl . '?' . http_build_query(array('output' => 'soap12', 'uri' => $url));

        // Create a stream
        $opts = array(
            'http' => array(
                'method' => "GET",
                'header' => "User-Agent: W3C Validation bot\r\n"
            )
        );

        // Perform HTTP request
        $response = trim(file_get_contents($uri, false, stream_context_create($opts)));

        // If we have completed HTTP request
        if ($response === false) {
            // Throw http request failed exception
            throw new RequestException('W3C API http request failed');
        }

        return $response;
    }

    /**
     * W3C validator function
     * @throws RequestException
     */
    public function validate()
    {
        // If we need validation
        if ($this->enabled) {
            // Block errors reporting
            libxml_use_internal_errors(false);

            // W3C validator response
            $w3cResponse = simplexml_load_string($this->httpRequest());

            // Get document namespaces declaration
            $nameSpaces = $w3cResponse->getNamespaces(true);

            // Get validation data
            $validationResponse = $w3cResponse
                ->children($nameSpaces['env'])  // Get 'http://www.w3.org/2003/05/soap-envelope/'
                ->children($nameSpaces['m'])    // Get 'http://www.w3.org/2005/10/markup-validator'
                ->markupvalidationresponse;

            // Create errors collection
            $this->w3cErrors = new Collection(
                $validationResponse->errors->errorlist->error,
                __NAMESPACE__.'\violation\Error'
            );

            // Create warnings collection
            $this->w3cWarnings = new Collection(
                $validationResponse->warnings->warninglist->warning,
                __NAMESPACE__.'\violation\Warning'
            );

            // Set validation summary results
            $this->w3cStatus = (bool)$validationResponse->validity;
            $this->w3cErrorsCount = (int)$validationResponse->errors->errorcount;
            $this->w3cWarningsCount = (int)$validationResponse->warnings->warningcount;
        }

        return array(
            'errorsCount' => $this->w3cErrorsCount,
            'errors' => $this->w3cErrors->toArray(),
            'warningsCount' => $this->w3cWarningsCount,
            'warnings' => $this->w3cWarnings->toArray(),
        );
    }
}
