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
     * or the data structure itself
     *
     * @param  var struct
     * @param  var
     */
    protected function key($struct) {
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
      } else if ($type->equals(XPClass::forName('util.Date'))) {
        return $type->newInstance($data);
      } else if ($type instanceof XPClass) {

        // Check if a one-arg public constructor exists and pass first element
        // E.g.: Assuming the target type has a __construct(string $id) and the
        // given payload data is { "id" : "4711" }, then pass "4711" to it.
        if ($type->hasConstructor()) {
          $c= $type->getConstructor();
          if (Modifiers::isPublic($c->getModifiers()) && 1 === $c->numParameters()) {
            return $c->newInstance(array($this->convert($c->getParameter(0)->getType(), $this->key($data))));
          }
        }

        // Check if a public static one-arg valueOf() method exists
        if ($type->hasMethod('valueOf')) {
          $m= $type->getMethod('valueOf');
          if (Modifiers::isStatic($m->getModifiers()) && Modifiers::isPublic($m->getModifiers()) && 1 === $m->numParameters()) {
            return $m->invoke(NULL, array($this->convert($m->getParameter(0)->getType(), $this->key($data))));
          }
        }

        // Generic approach
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
        return (string)$this->key($data);
      } else if ($type->equals(Primitive::$INT)) {
        return (int)$this->key($data);
      } else if ($type->equals(Primitive::$DOUBLE)) {
        return (double)$this->key($data);
      } else if ($type->equals(Primitive::$BOOL)) {
        return (bool)$this->key($data);
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
