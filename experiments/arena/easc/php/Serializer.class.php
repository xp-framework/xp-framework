<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  /**
   * Class that reimplements PHP's builtin serialization format.
   *
   * @see      php://serialize
   * @purpose  Serializer
   */
  class Serializer extends Object {

    /**
     * Retrieve serialized representation of a variable
     *
     * @access  protected
     * @param   &mixed var
     * @return  string
     * @throws  lang.FormatException if an error is encountered in the format 
     */  
    function representationOf(&$var) {
      switch (gettype($var)) {
        case 'NULL':    return 'N;';
        case 'boolean': return 'b:'.($var ? 1 : 0).';';
        case 'integer': return 'i:'.$var.';';
        case 'double':  return 'f:'.$var.';';
        case 'string':  return 's:'.strlen($var).':"'.$var.'";';
        case 'array':
          $s= 'a:'.sizeof($var).':{';
          foreach (array_keys($var) as $key) {
            $s.= serialize($key).Serializer::representationOf($var[$key]);
          }
          return $s.'}';
        case 'object':
          switch (1) {
            case is_a($var, 'Date'): {
              return 'T:'.$var->getTime().';';
            }
            case is_a($var, 'HashMap'): {
              return Serializer::representationOf($var->_hash);
            }
            case is_a($var, 'Long'): {
              return 'l:'.$var->value.';';
            }
            case is_a($var, 'Double'): {
              return 'd:'.$var->value.';';
            }
            case is_a($var, 'Byte'): {
              return 'B:'.$var->value.';';
            }
            case is_a($var, 'Short'): {
              return 'S:'.$var->value.';';
            }
            case is_a($var, 'Integer'): {
              return 'i:'.$var->value.';';
            }
            case is_a($var, 'Float'): {
              return 'f:'.$var->value.';';
            }
            default: {
              $name= xp::typeOf($var);
              $props= get_object_vars($var);
              unset($props['__id']);
              $s= 'O:'.strlen($name).':"'.$name.'":'.sizeof($props).':{';
              foreach (array_keys($props) as $name) {
                $s.= serialize($name).Serializer::representationOf($var->{$name});
              }
              unset($r);
              return $s.'}';
            }
          }
        case 'resource': return ''; // Ignore (resources can't be serialized)
        default: throw(new FormatException(
          'Cannot serialize unknown type '.xp::typeOf($var)
        ));
      }
    }
  
    /**
     * Retrieve serialized representation of a variable
     *
     * @access  protected
     * @param   string serialized
     * @param   &int length
     * @return  &mixed
     * @throws  lang.ClassNotFoundException if a class cannot be found
     * @throws  lang.FormatException if an error is encountered in the format 
     */  
    function &valueOf($serialized, &$length, &$handler) {
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
            $key= Serializer::valueOf(substr($serialized, $offset), $len, $handler);
            $offset+= $len;
            $a[$key]= &Serializer::valueOf(substr($serialized, $offset), $len, $handler);
            $offset+= $len;
          }
          $length= $offset+ 1;
          return $a;
        case 'O':
          $len= substr($serialized, 2, strpos($serialized, ':', 2)- 2);
          try(); {
            $class= &XPClass::forName(substr($serialized, 2+ strlen($len)+ 2, $len));
          } if (catch('ClassNotFoundException', $e)) {
            $class= &XPClass::forName('lang.Object');   // FIXME: Use UnknownClass or sth.
            #return throw($e);
          }
          $instance= &$class->newInstance();
          $offset= 2 + 2 + strlen($len)+ $len + 2;
          $size= substr($serialized, $offset, strpos($serialized, ':', $offset)- $offset);
          $offset+= strlen($size)+ 2;
          for ($i= 0; $i < $size; $i++) {
            $member= Serializer::valueOf(substr($serialized, $offset), $len, $handler);
            $offset+= $len;
            $instance->{$member}= &Serializer::valueOf(substr($serialized, $offset), $len, $handler);
            $offset+= $len;
          }
          $length= $offset+ 1;
          return $instance;
        case 'I':
          $len= substr($serialized, 2, strpos($serialized, ':', 2)- 2);
          $interface= substr($serialized, 2+ strlen($len)+ 2, $len);
          $offset= 2 + 2 + strlen($len)+ $len + 2;
          $size= substr($serialized, $offset, strpos($serialized, ':', $offset)- $offset);
          $offset+= strlen($size)+ 2;
          $cl= &ClassLoader::getDefault();
          try(); {
            $instance= &Proxy::newProxyInstance(
              $cl, 
              array(XPClass::forName($interface, $cl)), 
              $handler->newInstance(Serializer::valueOf(substr($serialized, $offset), $len, $handler))
            );
          } if (catch('ClassNotFoundException', $e)) {
            return throw($e);
          }
          $length= $offset+ 1;
          return $instance;
          
        default: throw(new FormatException(
          'Cannot deserialize unknown type "'.$serialized{0}.'" ('.$serialized.')'
        ));
      }
    }
  }
?>
