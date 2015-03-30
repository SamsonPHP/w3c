<?php
/**
 * Created by PhpStorm.
 * User: egorov
 * Date: 30.03.2015
 * Time: 20:56
 */
namespace samsonphp\w3c;

use samson\core\Service;

class Validator extends Service
{
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
