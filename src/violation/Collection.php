<?php
/**
 * Created by PhpStorm.
 * User: egorov
 * Date: 31.03.2015
 * Time: 8:13
 */

namespace samsonphp\w3c\violation;

/**
 * Collection of W3C HTML markup violations
 * @package samsonphp\w3c\violation
 */
class Collection
{
    /** @var Violation Collection of violations  */
    protected $collection = array();

    /**
     * @param \SimpleXMLElement $xml XML violations collection
     * @param string $entity Class name for violation creation
     */
    public function __construct(SimpleXMLElement & $xml, $entity = '\samsonphp\w3c\violation\Violation')
    {
        // Get all errors and fill error list
        foreach ($xml as $violation) {
            // Create violation entity
            $this->collection[] = new $entity(
                (int)$violation->line,
                (int)$violation->col,
                (string)$violation->message,
                (int)$violation->messageid,
                (string)$violation->explanation,
                (string)$violation->source
            );
        }
    }
}
