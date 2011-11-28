<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('webservices.soap.SoapTypeMapper');

  /**
   * Represents supported SoapTypes
   *
   * @see      webservices.soap.native.NativeSoapClient
   */
  class NativeSoapTypeMapper extends SoapTypeMapper {

    /**
     * Box SoapType
     *
     * @param   webservices.soap.types.SoapType object
     * @return  mixed
     */
    protected function boxSoapType($object) {
      return $object->asSoapType();
    }

    /**
     * Box named parameter
     *
     * @param   webservices.soap.Parameter
     * @return  mixed
     */
    protected function boxParameter($object) {
      if ($this->supports($object->value)) {
        return new SoapParam($this->box($object->value), $object->name);
      }

      return new SoapParam($object->value, $object->name);
    }

    /**
     * Box long
     *
     * @param   lang.types.Long object
     * @return  mixed
     */
    protected function boxLong($object) {
      return new SoapVar($object->value, XSD_LONG);
    }

    /**
     * Box short
     *
     * @param   lang.types.Short object
     * @return  mixed
     */
    protected function boxShort($object) {
      return new SoapVar($object->value, XSD_SHORT);
    }

    /**
     * Box double
     *
     * @param   lang.types.Double object
     * @return  mixed
     */
    protected function boxDouble($object) {
      return new SoapVar($object->doubleValue(), XSD_DOUBLE);
    }

    /**
     * Box integer
     *
     * @param   lang.types.Integer object
     * @return  mixed
     */
    protected function boxInteger($object) {
      return new SoapVar($object->intValue(), XSD_INTEGER);
    }

    /**
     * Box string
     *
     * @param   lang.types.String object
     * @return  mixed
     */
    protected function boxString($object) {
      return new SoapVar($object->toString(), XSD_STRING);
    }

    /**
     * Box date
     *
     * @param   util.Date object
     * @return  mixed
     */
    protected function boxDate($object) {
      return new SoapVar($object->toString(DATE_ISO8601), XSD_DATETIME);
    }

    /**
     * Box bytes
     *
     * @param   lang.types.Bytes object
     * @return  mixed
     */
    protected function boxBytes($object) {
      return new SoapVar(base64_encode($object->__toString()), XSD_BASE64BINARY);
    }

    /**
     * Box boolean
     *
     * @param   lang.types.Boolean object
     * @return  mixed
     */
    protected function boxBoolean($object) {
      return new SoapVar($object->value, XSD_BOOLEAN);
    }

    /**
     * Box character
     *
     * @param   lang.types.Character object
     * @return  mixed
     */
    protected function boxCharacter($object) {
      return new SoapVar((string)$object, XSD_STRING);
    }
  }
?>
