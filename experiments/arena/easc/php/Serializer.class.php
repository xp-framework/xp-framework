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
     * Register or retrieve a mapping for a token
     *
     * @access  public
     * @param   string token
     * @param   &Mapping mapping
     * @return  &Mapping mapping
     */
    function &mapping($token, &$mapping) {
      static $mappings= array();
      
      if (NULL !== $mapping) $mappings[$token]= &$mapping;
      return $mappings[$token];
    }
  
    /**
     * Retrieve serialized representation of a variable
     *
     * @access  protected
     * @param   string serialized
     * @param   &int length
     * @param   array context default array()
     * @return  &mixed
     * @throws  lang.ClassNotFoundException if a class cannot be found
     * @throws  lang.FormatException if an error is encountered in the format 
     */  
    function &valueOf($serialized, &$length, $context= array()) {
      switch ($serialized{0}) {
        case 'N': {     // null
          $length= 2; 
          return NULL;
        }

        case 'b': {     // booleans
          $length= 4; 
          return (bool)substr($serialized, 2, strpos($serialized, ';', 2)- 2);
        }

        case 'i': {     // integers
          $v= substr($serialized, 2, strpos($serialized, ';', 2)- 2); 
          $length= strlen($v)+ 3;
          return (int)$v;
        }

        case 'd': {     // decimals
          $v= substr($serialized, 2, strpos($serialized, ';', 2)- 2); 
          $length= strlen($v)+ 3;
          return (float)$v;
        }

        case 's': {     // strings
          $strlen= substr($serialized, 2, strpos($serialized, ':', 2)- 2);
          $length= 2 + strlen($strlen) + 2 + $strlen + 2;
          return substr($serialized, 2+ strlen($strlen)+ 2, $strlen);
        }

        case 'a': {     // arrays
          $a= array();
          $size= substr($serialized, 2, strpos($serialized, ':', 2)- 2);
          $offset= strlen($size)+ 2+ 2;
          for ($i= 0; $i < $size; $i++) {
            $key= Serializer::valueOf(substr($serialized, $offset), $len, $context);
            $offset+= $len;
            $a[$key]= &Serializer::valueOf(substr($serialized, $offset), $len, $context);
            $offset+= $len;
          }
          $length= $offset+ 1;
          return $a;
        }

        case 'E': {     // exceptions
          $len= substr($serialized, 2, strpos($serialized, ':', 2)- 2);
          $classname= substr($serialized, 2+ strlen($len)+ 2, $len);
          $instance= &new Exception(NULL);
          $offset= 2 + 2 + strlen($len)+ $len + 2;
          $size= substr($serialized, $offset, strpos($serialized, ':', $offset)- $offset);
          $offset+= strlen($size)+ 2;
          for ($i= 0; $i < $size; $i++) {
            $member= Serializer::valueOf(substr($serialized, $offset), $len, $context);
            $offset+= $len;
            $instance->{$member}= &Serializer::valueOf(substr($serialized, $offset), $len, $context);
            $offset+= $len;
          }
          $instance->message= $classname.': '.$instance->message;
          $length= $offset+ 1;
          return $instance;
        }
        
        case 't': {     // stack trace elements
          $size= substr($serialized, 2, strpos($serialized, ':', 2)- 2);
          $offset= strlen($size)+ 2+ 2;
          $details= array();
          for ($i= 0; $i < $size; $i++) {
            $detail= Serializer::valueOf(substr($serialized, $offset), $len, $context);
            $offset+= $len;
            $details[$detail]= Serializer::valueOf(substr($serialized, $offset), $len, $context);
            $offset+= $len;
          }
          $length= $offset+ 1;
          return new StackTraceElement(
            $details['file'],
            $details['class'],
            $details['method'],
            $details['line'],
            array(),
            array()
          );
        }

        case 'O': {     // generic objects
          $len= substr($serialized, 2, strpos($serialized, ':', 2)- 2);
          try(); {
            $class= &XPClass::forName(substr($serialized, 2+ strlen($len)+ 2, $len));
          } if (catch('ClassNotFoundException', $e)) {
            return throw($e);
          }
          $instance= &$class->newInstance();
          $offset= 2 + 2 + strlen($len)+ $len + 2;
          $size= substr($serialized, $offset, strpos($serialized, ':', $offset)- $offset);
          $offset+= strlen($size)+ 2;
          for ($i= 0; $i < $size; $i++) {
            $member= Serializer::valueOf(substr($serialized, $offset), $len, $context);
            $offset+= $len;
            $instance->{$member}= &Serializer::valueOf(substr($serialized, $offset), $len, $context);
            $offset+= $len;
          }
          $length= $offset+ 1;
          return $instance;
        }

        default: {      // default, check if we have a mapping
          if (!($mapping= &Serializer::mapping($serialized{0}, $m= NULL))) {
            return throw(new FormatException(
              'Cannot deserialize unknown type "'.$serialized{0}.'" ('.$serialized.')'
            ));
          }

          return $mapping->valueOf($serialized, $length, $context);
        }
      }
    }
  }
?>
