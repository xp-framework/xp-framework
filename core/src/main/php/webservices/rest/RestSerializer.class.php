<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('webservices.rest.Payload');

  /**
   * A serializer
   *
   * @see   xp://webservices.rest.RestRequest#setPayload
   */
  abstract class RestSerializer extends Object {

    /**
     * Calculate variants of a given name
     *
     * @param   string name
     * @return  string[] names
     */
    protected function variantsOf($name) {
      $variants= array($name);
      $chunks= explode('_', $name);
      if (sizeof($chunks) > 1) {      // product_id => productId
        $variants[]= array_shift($chunks).implode(array_map('ucfirst', $chunks));
      }
      return $variants;
    }

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
          } else {
            foreach ($this->variantsOf($field->getName()) as $name) {
              if ($class->hasMethod($m= 'get'.$name)) {
                $r[$field->getName()]= $this->convert($class->getMethod($m)->invoke($data));
                continue 2;
              }
            }
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
