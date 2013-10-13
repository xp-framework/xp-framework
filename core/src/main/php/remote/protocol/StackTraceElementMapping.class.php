<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('remote.protocol.SerializerMapping', 'remote.RemoteStackTraceElement');

  /**
   * Mapping for lang.StackTraceElement
   *
   * @see      xp://remote.protocol.Serializer
   * @purpose  Mapping
   */
  class StackTraceElementMapping extends Object implements SerializerMapping {

    /**
     * Returns a value for the given serialized string
     *
     * @param   server.protocol.Serializer serializer
     * @param   remote.protocol.SerializedData serialized
     * @param   [:var] context default array()
     * @return  var
     */
    public function valueOf($serializer, $serialized, $context= array()) {
      $size= $serialized->consumeSize();
      $details= array();
      $serialized->consume('{');
      for ($i= 0; $i < $size; $i++) {
        $detail= $serializer->valueOf($serialized, $context);
        $details[$detail]= $serializer->valueOf($serialized, $context);
      }
      $serialized->consume('}');
      
      return new RemoteStackTraceElement(
        $details['file'],
        $details['class'],
        $details['method'],
        $details['line'],
        array(),
        NULL
      );
    }

    /**
     * Returns an on-the-wire representation of the given value
     *
     * @param   server.protocol.Serializer serializer
     * @param   lang.Object value
     * @param   [:var] context default array()
     * @return  string
     */
    public function representationOf($serializer, $value, $context= array()) {
      return 't:4:{'.
        's:4:"file";'.$serializer->representationOf(NULL == $value->file ? NULL : basename($value->file)).
        's:5:"class";'.$serializer->representationOf(NULL == $value->class ? NULL : xp::nameOf($value->class)).
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
      return XPClass::forName('lang.StackTraceElement');
    }
  } 
?>
