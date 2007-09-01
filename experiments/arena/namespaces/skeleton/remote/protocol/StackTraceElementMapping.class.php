<?php
/* This class is part of the XP framework
 *
 * $Id: StackTraceElementMapping.class.php 10594 2007-06-11 10:04:54Z friebe $ 
 */

  namespace remote::protocol;

  uses('remote.protocol.SerializerMapping', 'remote.RemoteStackTraceElement');

  /**
   * Mapping for lang.StackTraceElement
   *
   * @see      xp://remote.protocol.Serializer
   * @purpose  Mapping
   */
  class StackTraceElementMapping extends lang::Object implements SerializerMapping {

    /**
     * Returns a value for the given serialized string
     *
     * @param   server.protocol.Serializer serializer
     * @param   remote.protocol.SerializedData serialized
     * @param   array<string, mixed> context default array()
     * @return  mixed
     */
    public function valueOf($serializer, $serialized, $context= array()) {
      $size= $serialized->consumeSize();
      $details= array();
      $serialized->offset++;  // Opening "{"
      for ($i= 0; $i < $size; $i++) {
        $detail= $serializer->valueOf($serialized, $context);
        $details[$detail]= $serializer->valueOf($serialized, $context);
      }
      $serialized->offset++;  // Closing "}"
      
      $value= new remote::RemoteStackTraceElement(
        $details['file'],
        $details['class'],
        $details['method'],
        $details['line'],
        array(),
        NULL
      );
      return $value;
    }

    /**
     * Returns an on-the-wire representation of the given value
     *
     * @param   server.protocol.Serializer serializer
     * @param   lang.Object value
     * @param   array<string, mixed> context default array()
     * @return  string
     */
    public function representationOf($serializer, $value, $context= array()) {
      return 't:4:{'.
        's:4:"file";'.$serializer->representationOf($value->file).
        's:5:"class";'.$serializer->representationOf($value->class).
        's:6:"method";'.$serializer->representationOf($value->method).
        's:4:"line";'.$serializer->representationOf($value->line).
      '}';
    }
    
    /**
     * Return XPClass object of class supported by this mapping
     *
     * @return  lang.XPClass
     */
    public function handledClass() {
      return lang::XPClass::forName('lang.StackTraceElement');
    }
  } 
?>
