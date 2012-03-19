<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * A serializer
   *
   * @see   xp://webservices.rest.RestRequest#setPayload
   */
  abstract class RestSerializer extends Object {

    /**
     * Convert data
     *
     * @param   var data
     * @return  var
     */
    public function convert($data) {
      if ($data instanceof Date) {
        return $data->toString('r');
      } else if ($data instanceof Generic) {
        $class= $data->getClass();
        $r= array();
        foreach ($class->getFields() as $field) {
          if ($field->getModifiers() & MODIFIER_PUBLIC) {
            $r[$field->getName()]= $this->convert($field->get($data));
          } else if ($class->hasMethod($m= 'get'.$field->getName())) {
            $r[$field->getName()]= $this->convert($class->getMethod($m)->invoke($data));
          }
        }
        return $r;
      }
      return $data;
    }

    /**
     * Return the Content-Type header's value
     *
     * @return  string
     */
    public abstract function contentType();
    
    /**
     * Serialize
     *
     * @param   var value
     * @return  string
     */
    public abstract function serialize($payload);
    
  }
?>
