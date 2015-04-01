<?php
/**
 * Created by PhpStorm.
 * User: egorov
 * Date: 31.03.2015
 * Time: 10:37
 */
namespace samsonphp\w3c;

use samson\core\Service;
use samsonframework\w3c\Validator;

/**
 * W3C validator SamsonPHP service.
 * This service is performing request to W3C HTML validation service
 * ans outputs its error for improving html markup quality.
 *
 * @package samsonphp\w3c
 * @author Vitalii Iehorov <egorov@samsonos.com>
 */
class Controller extends Service
{
    /** @var string Validating URL prefix, used for local domains */
    public $urlSuffix = '.samsonos.com';

    /** @var bool Flag to perform w3c validation */
    protected $enabled = false;

    /**
     * Asynchronous validation controller action
     * @return array Asynchronous response array
     */
    public function __async_validate()
    {
        // Build current HTML page url and add internal URL if present
        $url = $_SERVER['HTTP_HOST'] . $this->urlSuffix . '/' . implode('/', func_get_args());

        // Create W3C validator object
        $validator = new Validator($url);

        /** @var  $validationResults */
        $validationResults = $validator->validate()->toArray();

        return array(
            'status' => 1,
            'html' => $this->view('panel')
                ->set('status', $validationResults['validity'])
                ->set('errors', $validationResults['errorsCount'])
                ->set('warnings', $validationResults['warningsCount'])
                ->set('invalid', $validationResults['validity'] == 0 ? 'invalid' : 'valid')
                ->set('link', $validationResults['refferer'])
                ->output()
        );
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
            $output .= $this->view('index')
                ->link($this->id . '/validate'.$_SERVER['REQUEST_URI'])
                ->output();

            // Disable further validation async calls
            $this->enabled = false;
        }
    }
}