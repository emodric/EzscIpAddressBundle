<?php

namespace Netgen\EzscIpAddressBundle\Core\FieldType\IpAddress;

use \eZ\Publish\Core\FieldType\Value as BaseValue;

class Value extends BaseValue
{
    /**
     * @var string
     */
    public $ipAddress;

    public function __construct( $ipAddress = null )
    {
        $this->ipAddress = $ipAddress;
    }

    /**
     * Returns a string representation of the field value.
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->ipAddress;
    }
}
