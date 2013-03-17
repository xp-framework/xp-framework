<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('webservices.rest.Payload');

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
     * Returns the first element of a given traversable data structure
     * or the data structure itself, or NULL if the structure has more
     * than one element.
     *
     * @param  var struct
     * @param  var[]
     */
    protected function keyOf($struct) {
      if (is_array($struct) || $struct instanceof Traversable) {
        $return= NULL;
        foreach ($struct as $element) {
          if (NULL === $return) {
            $return= array($element);
            continue;
          }
          return NULL;    // Found a second element, return NULL
        }
        return $return;   // Will be NULL if we have no elements
      }
      return array($struct);
    }

    /**
     * Returns the first element of a given traversable data structure
     * or the data structure itself
     *
     * @param  var struct
     * @param  var
     */
    protected function valueOf($struct) {
      if (is_array($struct) || $struct instanceof Traversable) {
        foreach ($struct as $element) {
          return $element;
        }
      }
      return $struct;
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
      } else if ($type->equals(XPClass::forName('lang.types.String'))) {
        return new String($this->valueOf($data));
      } else if ($type->equals(XPClass::forName('util.Date'))) {
        return $type->newInstance($data);
      } else if ($type instanceof XPClass) {

        // Check if a public static one-arg valueOf() method exists
        // E.g.: Assuming the target type has a valueOf(string $id) and the
        // given payload data is either a map or an array with one element, or
        // a primitive, then pass that as value. Examples: { "id" : "4711" }, 
        // [ "4711" ] or "4711" - in all cases pass just "4711".
        if ($type->hasMethod('valueOf')) {
          $m= $type->getMethod('valueOf');
          if (Modifiers::isStatic($m->getModifiers()) && Modifiers::isPublic($m->getModifiers()) && 1 === $m->numParameters()) {
            if (NULL !== ($arg= $this->keyOf($data))) {
              return $m->invoke(NULL, array($this->convert($m->getParameter(0)->getType(), $arg[0])));
            }
          }
        }

        // Generic approach
        $return= $type->newInstance();
        if (NULL === $data) {
          $iter= array();
        } else if (is_array($data) || $data instanceof Traversable) {
          $iter= $data;
        } else {
          $iter= array($data);
        }
        foreach ($iter as $name => $value) {
          foreach ($this->variantsOf($name) as $variant) {
            if ($type->hasField($variant)) {
              $field= $type->getField($variant);
              $m= $field->getModifiers();
              if ($m & MODIFIER_STATIC) {
                continue;
              } else if ($m & MODIFIER_PUBLIC) {
                if (NULL !== ($fType= $field->getType())) {
                  $field->set($return, $this->convert($fType, $value));
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
                  $method->invoke($return, array($this->convert($param->getType(), $value)));
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
        return (string)$this->valueOf($data);
      } else if ($type->equals(Primitive::$INT)) {
        return (int)$this->valueOf($data);
      } else if ($type->equals(Primitive::$DOUBLE)) {
        return (double)$this->valueOf($data);
      } else if ($type->equals(Primitive::$BOOL)) {
        return (bool)$this->valueOf($data);
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
     * @throws  lang.FormatException
     */
    public abstract function deserialize($in, $target);
  }
?>
