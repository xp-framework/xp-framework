<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('remote.protocol.SerializerMapping');

  /**
   * Mapping for lang.Throwable
   *
   * @see      xp://remote.protocol.Serializer
   * @purpose  Mapping for 
   */
  class ExceptionMapping extends Object implements SerializerMapping {

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
      $len= substr($serialized, 2, strpos($serialized, ':', 2)- 2);
      try {
        $class= &XPClass::forName($serializer->exceptionName(substr($serialized, 2+ strlen($len)+ 2, $len)));
      } catch (ClassNotFoundException $e) {
        throw($e);
      }
      
      $offset= 2 + 2 + strlen($len)+ $len + 2;
      $size= substr($serialized, $offset, strpos($serialized, ':', $offset)- $offset);
      $offset+= strlen($size)+ 2;
      
      $data= array();
      for ($i= 0; $i < $size; $i++) {
        $member= $serializer->valueOf(substr($serialized, $offset), $len, $context);
        $offset+= $len;
        $data[$member]= &$serializer->valueOf(substr($serialized, $offset), $len, $context);
        $offset+= $len;
      }
      $length= $offset+ 1;
      
      $instance= &$class->newInstance($data['message']);
      unset($data['message']);
      foreach ($data as $name => $member) {
        $instance->{$name}= &$member;
      }

      return $instance;
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
      $trace= &$value->getStackTrace();
      
      if (FALSE !== ($token= array_search($value->getClassName(), $serializer->exceptions, TRUE))) {
      
        // It's a known exception
        $s= 'e:'.strlen($token).':"'.$token.'":2:{';
      } else {
      
        // Generic exceptions
        $s= 'E:'.strlen($value->getClassName()).':"'.$value->getClassName().'":2:{';
      }
      $s.= 's:7:"message";';
      $s.= $serializer->representationOf($value->getMessage());
      
      $s.= 's:5:"trace";a:'.sizeof($trace).':{';
      
      $i= 0;
      foreach ($trace as $element) {
        $s.= 'i:'.$i++.';'.$serializer->representationOf($element, $context);
      }
      
      return $s.'}}';
    }
    
    /**
     * Return XPClass object of class supported by this mapping
     *
     * @access  public
     * @return  &lang.XPClass
     */
    public function &handledClass() {
      return XPClass::forName('lang.Throwable');
    }
  } 
?>
