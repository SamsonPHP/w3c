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
class Collection implements \ArrayAccess
{
    /** @var Violation Collection of violations  */
    protected $collection = array();

    /**
     * @param \SimpleXMLElement $xml XML violations collection
     * @param string $entity Class name for violation creation
     */
    public function __construct(\SimpleXMLElement & $xml, $entity = '\samsonphp\w3c\violation\Violation')
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

    /** @return Violation[] Collection of violation in array format */
    public function toArray()
    {
        return $this->collection;
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Whether a offset exists
     * @link http://php.net/manual/en/arrayaccess.offsetexists.php
     * @param mixed $offset <p>
     * An offset to check for.
     * </p>
     * @return boolean true on success or false on failure.
     * </p>
     * <p>
     * The return value will be casted to boolean if non-boolean was returned.
     */
    public function offsetExists($offset)
    {
        return isset($this->collection[$offset]);
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Offset to retrieve
     * @link http://php.net/manual/en/arrayaccess.offsetget.php
     * @param mixed $offset <p>
     * The offset to retrieve.
     * </p>
     * @return mixed Can return all value types.
     */
    public function offsetGet($offset)
    {
        return $this->collection[$offset];
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Offset to set
     * @link http://php.net/manual/en/arrayaccess.offsetset.php
     * @param mixed $offset <p>
     * The offset to assign the value to.
     * </p>
     * @param mixed $value <p>
     * The value to set.
     * </p>
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        $this->collection[$offset] = $value;
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Offset to unset
     * @link http://php.net/manual/en/arrayaccess.offsetunset.php
     * @param mixed $offset <p>
     * The offset to unset.
     * </p>
     * @return void
     */
    public function offsetUnset($offset)
    {
        unset($this->collection[$offset]);
    }
}
