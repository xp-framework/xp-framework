<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('webservices.soap.SoapTypeMapper');

  /**
   * Represents supported SoapTypes
   *
   * @test     TODO
   * @see      TODO
   * @purpose  Type implementation
   */
  class NativeSoapType extends SoapTypeMapper {

    protected function boxSoapType($object) {
      return $object->asSoapType();
    }

    protected function boxParameter($object) {
      if ($this->supports($object->value)) {
        return new SoapParam($this->box($object->value), $object->name);
      }

      return new SoapParam($object->value, $object->name);
    }

    protected function boxLong($object) {
      return new SoapVar($object->value, XSD_LONG);
    }

    protected function boxShort($object) {
      return new SoapVar($object->value, XSD_SHORT);
    }

    protected function boxDouble($object) {
      return new SoapVar($object->doubleValue(), XSD_DOUBLE);
    }

    protected function boxInteger($object) {
      return new SoapVar($object->intValue(), XSD_INTEGER);
    }

    protected function boxString($object) {
      return new SoapVar($object->toString(), XSD_STRING);
    }

    protected function boxDate($object) {
      return new SoapVar($object, XSD_DATETIME);
    }

    protected function boxBytes($object) {
      return new SoapVar(base64_encode($object->__toString()), XSD_BASE64BINARY);
    }

    protected function boxBoolean($object) {
      return new SoapVar($object->value, XSD_BOOLEAN);
    }

    protected function boxCharacter($object) {
      return new SoapVar($object->value, XSD_STRING);
    }
  }
?>
