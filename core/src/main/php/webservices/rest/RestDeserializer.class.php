<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Deserializer abstract base class
   *
   */
  abstract class RestDeserializer extends Object {

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
     * Convert data based on type
     *
     * @param   lang.Type type
     * @param   [:var] data
     * @return  var
     */
    public function convert($type, $data) {
      if (NULL === $type || $type->equals(Type::$VAR)) {  // No conversion
        return $data;
      } else if (NULL === $data) {                        // Valid for any type
        return NULL;
      } else if ($type->equals(XPClass::forName('util.Date'))) {
        return $type->newInstance($data);
      } else if ($type instanceof XPClass) {              // Conversion to a class
        $return= $type->newInstance();
        foreach ($data as $name => $value) {
          foreach ($this->variantsOf($name) as $variant) {
            if ($type->hasField($variant)) {
              $field= $type->getField($variant);
              if ($field->getModifiers() & MODIFIER_PUBLIC) {
                if (NULL !== ($fType= $field->getType())) {
                  $field->set($return, $this->convert(Type::forName($fType), $value));
                } else {
                  $field->set($return, $value);
                }
                continue 2;
              }
            }
            if ($type->hasMethod('set'.$variant)) {
              $method= $type->getMethod('set'.$variant);
              if ($method->getModifiers() & MODIFIER_PUBLIC) {
                if (NULL !== ($param= $method->getParameter(0))) {
                  $method->invoke($return, $this->convert($param->getType(), $value)); 
                } else {
                  $method->invoke($return, array($value));
                }
                continue 2;
              }
            }
          }
        }
        return $return;
      } else if ($type instanceof ArrayType) {
        $return= array();
        foreach ($data as $element) {
          $return[]= $this->convert($type->componentType(), $element);
        }
        return $return;
      } else if ($type instanceof MapType) {
        $return= array();
        foreach ($data as $key => $element) {
          $return[$key]= $this->convert($type->componentType(), $element);
        }
        return $return;
      } else if ($type->equals(Primitive::$STRING)) {
        return (string)$data;
      } else if ($type->equals(Primitive::$INT)) {
        return (int)$data;
      } else if ($type->equals(Primitive::$DOUBLE)) {
        return (double)$data;
      } else if ($type->equals(Primitive::$BOOL)) {
        return (bool)$data;
      } else {
        throw new FormatException('Cannot convert to '.xp::stringOf($type));
      }
    }

    /**
     * Deserialize
     *
     * @param   io.streams.InputStream in
     * @param   lang.Type target
     * @return  var
     */
    public abstract function deserialize($in, $target);
  }
?>
