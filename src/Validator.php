<?php
/**
 * Created by PhpStorm.
 * User: egorov
 * Date: 30.03.2015
 * Time: 20:56
 */
namespace samsonphp\w3c;

use samson\core\Service;

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
    /** Module initialization logic */
    public function init(array $params = array())
    {
        // Subscribe to resourcer event
        \samsonphp\event\Event::subscribe('resourcer.update', array($this, 'validate'));
    }

    /**
     * Validator function
     * @param string $resource Updated resource type
     * @param string $path Updated resource path
     */
    public function validate($resource, $path)
    {
        echo 'W3C validation:';
    }
}
