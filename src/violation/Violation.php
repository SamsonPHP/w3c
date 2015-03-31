<?php
/**
 * Created by PhpStorm.
 * User: egorov
 * Date: 31.03.2015
 * Time: 7:57
 */
namespace samsonphp\w3c\violation;

/**
 * W3C HTML markup violation
 * @package samsonphp\w3c\violation
 */
class Violation
{
    /** @var int Line number */
    public $line;

    /** @var int Column number */
    public $column;

    /** @var string Message text */
    public $message;

    /** @var int Message identifier */
    public $messageID;

    /** @var string Violation explanation */
    public $explanation;

    /** @var string Violation source */
    public $source;

    /**
     * @param int $line Line number
     * @param int $column Column number
     * @param string $message Message text
     * @param int $messageID Message identifier
     * @param string $explanation Violation explanation
     * @param string $source Violation source
     */
    public function __construct($line, $column, $message, $messageID, $explanation, $source)
    {
        $this->line = $line;
        $this->column = $column;
        $this->message = $source;
        $this->messageID = $messageID;
        $this->explanation = $explanation;
        $this->source = $source;
    }
}
