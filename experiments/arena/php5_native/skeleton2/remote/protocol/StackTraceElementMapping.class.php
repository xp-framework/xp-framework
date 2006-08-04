<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('remote.protocol.SerializerMapping');

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
     * @access  public
     * @param   &server.protocol.Serializer serializer
     * @param   string serialized
     * @param   &int length
     * @param   array<string, mixed> context default array()
     * @return  &mixed
     */
    public function &valueOf(&$serializer, $serialized, &$length, $context= array()) {
      $size= substr($serialized, 2, strpos($serialized, ':', 2)- 2);
      $offset= strlen($size)+ 2+ 2;
      $details= array();
      for ($i= 0; $i < $size; $i++) {
        $detail= $serializer->valueOf(substr($serialized, $offset), $len, $context);
        $offset+= $len;
        $details[$detail]= $serializer->valueOf(substr($serialized, $offset), $len, $context);
        $offset+= $len;
      }
      $length= $offset+ 1;
      
      $value= new StackTraceElement(
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
     * @access  public
     * @param   &server.protocol.Serializer serializer
     * @param   &lang.Object value
     * @param   array<string, mixed> context default array()
     * @return  string
     */
    public function representationOf(&$serializer, &$value, $context= array()) {
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
     * @access  public
     * @return  &lang.XPClass
     */
    public function &handledClass() {
      return XPClass::forName('lang.StackTraceElement');
    }
  } 
?>
