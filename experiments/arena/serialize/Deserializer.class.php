<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('SerializationException');

  /**
   * Deserializer
   *
   * @purpose  Abstract base class
   */
  abstract class Deserializer extends Object {
  
    /**
     * Retrieve serialized representation of a variable
     *
     * @access  protected
     * @param   string serialized
     * @param   &int length default 0
     * @return  mixed
     */  
    protected function valueOf($serialized, &$length= 0) {
      switch ($serialized{0}) {
        case 'N': $length= 2; return NULL;
        case 'b': $length= 4; return (bool)substr($serialized, 2, strpos($serialized, ';', 2)- 2);
        case 'i': 
          $v= substr($serialized, 2, strpos($serialized, ';', 2)- 2); 
          $length= strlen($v)+ 3;
          return (int)$v;
        case 'd': 
          $v= substr($serialized, 2, strpos($serialized, ';', 2)- 2); 
          $length= strlen($v)+ 3;
          return (float)$v;
        case 's':
          $strlen= substr($serialized, 2, strpos($serialized, ':', 2)- 2);
          $length= 2 + strlen($strlen) + 2 + $strlen + 2;
          return substr($serialized, 2+ strlen($strlen)+ 2, $strlen);
        case 'a':
          $a= array();
          $size= substr($serialized, 2, strpos($serialized, ':', 2)- 2);
          $offset+= strlen($size)+ 2+ 2;
          for ($i= 0; $i < $size; $i++) {
            $key= $this->valueOf(substr($serialized, $offset), $len);
            $offset+= $len;
            $value= $this->valueOf(substr($serialized, $offset), $len);
            $offset+= $len;
            $a[$key]= $value;
          }
          $length= $offset+ 1;
          return $a;
        case 'O':
          $len= substr($serialized, 2, strpos($serialized, ':', 2)- 2);
          $instance= XPClass::forName(substr($serialized, 2+ strlen($len)+ 2, $len))->newInstance();
          $offset= 2 + 2 + strlen($len)+ $len + 2;
          $size= substr($serialized, $offset, strpos($serialized, ':', $offset)- $offset);
          $offset+= strlen($size)+ 2;
          for ($i= 0; $i < $size; $i++) {
            $member= $this->valueOf(substr($serialized, $offset), $len);
            $offset+= $len;
            $value= $this->valueOf(substr($serialized, $offset), $len);
            $offset+= $len;
            $instance->{$member}= $value;
          }
          $length= $offset+ 1;
          return $instance;
        default: throw new SerializationException(
          'Cannot deserialize unknown type "'.$serialized{0}.'" ('.$serialized.')'
        );          
      }
    }
  
    /**
     * Serialize an object
     *
     * @access  public
     * @return  &io.Serializable object
     */
    abstract public function deserialize();
  }
?>
