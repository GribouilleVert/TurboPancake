<?php
namespace TurboPancake\Exceptions;

class SystemException extends \Exception {

    public const SEVERITY_HIGH = 0x1;
    public const SEVERITY_MEDIUM = 0x2;
    public const SEVERITY_LOW = 0x2;
    public const SEVERITY_CRITICAL = 0x3;

    /**
     * @var int
     */
    private $severity;

    public function __construct($message = "", $severity = self::SEVERITY_MEDIUM)
    {
        parent::__construct($message);
        $this->severity = $severity;
    }

    /**
     * @return int
     */
    public function getSeverity(): int
    {
        return $this->severity;
    }

}