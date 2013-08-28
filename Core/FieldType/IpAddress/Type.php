<?php

namespace Netgen\EzscIpAddressBundle\Core\FieldType\IpAddress;

use eZ\Publish\Core\Base\Exceptions\InvalidArgumentType;
use eZ\Publish\Core\FieldType\FieldType;
use eZ\Publish\Core\FieldType\Value as BaseValue;
use eZ\Publish\SPI\FieldType\Value as SPIValue;
use eZ\Publish\API\Repository\Values\ContentType\FieldDefinition;
use eZ\Publish\Core\FieldType\ValidationError;
use Netgen\EzscIpAddressBundle\Core\FieldType\Validator\IpAddressValidator;

class Type extends FieldType
{
    protected $validatorConfigurationSchema = array(
        'IpAddressValidator' => array()
    );

    /**
     * Validates the validatorConfiguration of a FieldDefinitionCreateStruct or FieldDefinitionUpdateStruct
     *
     * This method expects that given $validatorConfiguration is complete, for this purpose method
     * {@link self::applyDefaultValidatorConfiguration()} is provided.
     *
     * This is a base implementation, returning a validation error for each
     * specified validator, since by default no validators are supported.
     * Overwrite in derived types, if validation is supported.
     *
     * @param mixed $validatorConfiguration
     *
     * @return \eZ\Publish\SPI\FieldType\ValidationError[]
     */
    public function validateValidatorConfiguration( $validatorConfiguration )
    {
        $validationErrors = array();
        $validator = new IpAddressValidator();

        foreach ( $validatorConfiguration as $validatorIdentifier => $constraints )
        {
            if ( $validatorIdentifier !== 'IpAddressValidator' )
            {
                $validationErrors[] = new ValidationError(
                    "Validator '%validator%' is unknown",
                    null,
                    array(
                        "validator" => $validatorIdentifier
                    )
                );

                continue;
            }

            $validationErrors += $validator->validateConstraints( $constraints );
        }

        return $validationErrors;
    }

    /**
     * Validates a field based on the validators in the field definition
     *
     * This is a base implementation, returning an empty array() that indicates
     * that no validation errors occurred. Overwrite in derived types, if
     * validation is supported.
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     *
     * @param \eZ\Publish\API\Repository\Values\ContentType\FieldDefinition $fieldDefinition The field definition of the field
     * @param \eZ\Publish\SPI\FieldType\Value $fieldValue The field value for which an action is performed
     *
     * @return \eZ\Publish\SPI\FieldType\ValidationError[]
     */
    public function validate( FieldDefinition $fieldDefinition, SPIValue $fieldValue )
    {
        $errors = array();

        if ( $this->isEmptyValue( $fieldValue ) )
        {
            return $errors;
        }

        $validatorConfiguration = $fieldDefinition->getValidatorConfiguration();
        $constraints = isset( $validatorConfiguration['IpAddressValidator'] ) ?
            $validatorConfiguration['IpAddressValidator'] :
            array();

        $validator = new IpAddressValidator();
        $validator->initializeWithConstraints( $constraints );

        if ( !$validator->validate( $fieldValue ) )
            return $validator->getMessage();

        return array();
    }

    /**
     * Returns the field type identifier for this field type
     *
     * This identifier should be globally unique and the implementer of a
     * FieldType must take care for the uniqueness. It is therefore recommended
     * to prefix the field-type identifier by a unique string that identifies
     * the implementer. A good identifier could for example take your companies main
     * domain name as a prefix in reverse order.
     *
     * @return string
     */
    public function getFieldTypeIdentifier()
    {
        return "ezscip";
    }

    /**
     * Returns a human readable string representation from the given $value
     *
     * It will be used to generate content name and url alias if current field
     * is designated to be used in the content name/urlAlias pattern.
     *
     * The used $value can be assumed to be already accepted by {@link
     * acceptValue()}.
     *
     * @param \eZ\Publish\SPI\FieldType\Value|\Netgen\EzscIpAddressBundle\Core\FieldType\IpAddress\Value $value
     *
     * @return string
     */
    public function getName( SPIValue $value )
    {
        return $value->ipAddress;
    }

    /**
     * Returns the empty value for this field type.
     *
     * This value will be used, if no value was provided for a field of this
     * type and no default value was specified in the field definition. It is
     * also used to determine that a user intentionally (or unintentionally) did not
     * set a non-empty value.
     *
     * @return \eZ\Publish\SPI\FieldType\Value
     */
    public function getEmptyValue()
    {
        return new Value();
    }

    /**
     * Converts an $hash to the Value defined by the field type
     *
     * This is the reverse operation to {@link toHash()}. At least the hash
     * format generated by {@link toHash()} must be converted in reverse.
     * Additional formats might be supported in the rare case that this is
     * necessary. See the class description for more details on a hash format.
     *
     * @param mixed $hash
     *
     * @return \eZ\Publish\SPI\FieldType\Value
     */
    public function fromHash( $hash )
    {
        return new Value( $hash );
    }

    /**
     * Converts the given $value into a plain hash format
     *
     * Converts the given $value into a plain hash format, which can be used to
     * transfer the value through plain text formats, e.g. XML, which do not
     * support complex structures like objects. See the class level doc block
     * for additional information. See the class description for more details on a hash format.
     *
     * @param \eZ\Publish\SPI\FieldType\Value|\Netgen\EzscIpAddressBundle\Core\FieldType\IpAddress\Value $value
     *
     * @return mixed
     */
    public function toHash( SPIValue $value )
    {
        return $value->ipAddress;
    }

    /**
     * Inspects given $inputValue and potentially converts it into a dedicated value object.
     *
     * If given $inputValue could not be converted or is already an instance of dedicate value object,
     * the method should simply return it.
     *
     * This is an operation method for {@see acceptValue()}.
     *
     * Example implementation:
     * <code>
     *  protected function createValueFromInput( $inputValue )
     *  {
     *      if ( is_array( $inputValue ) )
     *      {
     *          $inputValue = \eZ\Publish\Core\FieldType\CookieJar\Value( $inputValue );
     *      }
     *
     *      return $inputValue;
     *  }
     * </code>
     *
     * @param mixed $inputValue
     *
     * @return mixed The potentially converted input value.
     */
    protected function createValueFromInput( $inputValue )
    {
        if ( is_string( $inputValue ) && !empty( $inputValue ) )
        {
            return new Value( $inputValue );
        }

        if ( $inputValue === null )
        {
            return new Value();
        }

        return $inputValue;
    }

    /**
     * Throws an exception if value structure is not of expected format.
     *
     * Note that this does not include validation after the rules
     * from validators, but only plausibility checks for the general data
     * format.
     *
     * This is an operation method for {@see acceptValue()}.
     *
     * Example implementation:
     * <code>
     *  protected function checkValueStructure( Value $value )
     *  {
     *      if ( !is_array( $value->cookies ) )
     *      {
     *          throw new InvalidArgumentException( "An array of assorted cookies was expected." );
     *      }
     *  }
     * </code>
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException If the value does not match the expected structure.
     *
     * @param \eZ\Publish\Core\FieldType\Value|\Netgen\EzscIpAddressBundle\Core\FieldType\IpAddress\Value $value
     *
     * @return void
     */
    protected function checkValueStructure( BaseValue $value )
    {
        if ( is_string( $value->ipAddress ) && empty( $value->ipAddress ) )
        {
            throw new InvalidArgumentType( "$value", "\\Netgen\\EzscIpAddressBundle\\Core\\FieldType\\IpAddress\\Value", $value );
        }

        if ( !is_string( $value->ipAddress ) && $value->ipAddress !== null )
        {
            throw new InvalidArgumentType( "$value", "\\Netgen\\EzscIpAddressBundle\\Core\\FieldType\\IpAddress\\Value", $value );
        }
    }

    /**
     * Returns information for FieldValue->$sortKey relevant to the field type.
     *
     * Return value is mixed. It should be something which is sensible for
     * sorting.
     *
     * It is up to the persistence implementation to handle those values.
     * Common string and integer values are safe.
     *
     * For the legacy storage it is up to the field converters to set this
     * value in either sort_key_string or sort_key_int.
     *
     * @param \eZ\Publish\Core\FieldType\Value|\Netgen\EzscIpAddressBundle\Core\FieldType\IpAddress\Value $value
     *
     * @return mixed
     */
    protected function getSortInfo( BaseValue $value )
    {
        return $value->ipAddress;
    }
}
