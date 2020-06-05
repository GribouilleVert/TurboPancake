<?php
namespace TurboPancake\Exceptions;

class SystemException extends \Exception {

    public const SEVERITY_CRITICAL = 0x0;
    public const SEVERITY_HIGH = 0x1;
    public const SEVERITY_MEDIUM = 0x2;
    public const SEVERITY_LOW = 0x3;

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
    /**
     * @return string|null
     */
    public function getSeverityText(): ?string
    {
        switch ($this->severity) {
            case 0x0:
                return 'critical';
            case 0x1:
                return 'high';
            case 0x2:
                return 'medium';
            case 0x3:
                return 'low';
            default:
                return null;
        }
    }

}