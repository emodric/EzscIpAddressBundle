<?php

namespace Netgen\EzscIpAddressBundle\Core\FieldType\Validator;

use eZ\Publish\Core\FieldType\Validator;
use eZ\Publish\Core\FieldType\ValidationError;
use eZ\Publish\Core\FieldType\Value;

class IpAddressValidator extends Validator
{
    /**
     * @param mixed $constraints
     *
     * @return mixed
     */
    public function validateConstraints( $constraints )
    {
        return array();
    }

    /**
     * Perform validation on $value.
     *
     * Will return true when all constraints are matched. If one or more
     * constraints fail, the method will return false.
     *
     * When a check against a constraint has failed, an entry will be added to the
     * $errors array.
     *
     * @param \eZ\Publish\Core\FieldType\Value|\Netgen\EzscIpAddressBundle\Core\FieldType\IpAddress\Value $value
     *
     * @return boolean
     */
    public function validate( Value $value )
    {
        $pattern = '/^\b(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\b$/';

        if ( preg_match( $pattern, $value->ipAddress ) )
        {
            return true;
        }

        $this->errors[] = new ValidationError(
            "The value must be a valid IP address.",
            null,
            array()
        );

        return false;
    }
}
