<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'lang.types.Long',
    'lang.types.Double',
    'lang.types.Short',
    'lang.types.Byte',
    'lang.types.Float',
    'lang.types.ArrayList',
    'util.Date',
    'remote.UnknownRemoteObject',
    'remote.ExceptionReference',
    'remote.ClassReference'
  );

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
        case 'double':  return 'd:'.$var.';';
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
            case is_a($var, 'ArrayList'): {
              $s= 'A:'.sizeof($var->values).':{';
              foreach (array_keys($var->values) as $key) {
                $s.= Serializer::representationOf($var->values[$key]);
              }
              return $s.'}';
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
            case is_a($var, 'Integer'): {
              return 'i:'.$var->value.';';
            }
            case is_a($var, 'Byte'): {
              return 'B:'.$var->value.';';
            }
            case is_a($var, 'Short'): {
              return 'S:'.$var->value.';';
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
     * @param   &SerializerMapping mapping
     * @return  &SerializerMapping mapping
     * @throws  lang.IllegalArgumentException if the given argument is not a SerializerMapping
     */
    function &mapping($token, &$mapping) {
      static $mappings= array();
      
      if (NULL !== $mapping) {
        if (!is('SerializerMapping', $mapping)) return throw(new IllegalArgumentException(
          'Given argument is not a SerializerMapping ('.xp::typeOf($mapping).')'
        ));

        $mappings[$token]= &$mapping;
      }
      return $mappings[$token];
    }
    
    /**
     * Register or retrieve a mapping for a token
     *
     * @access  public
     * @param   string token
     * @param   string exception fully qualified class name
     * @return  string 
     */
    function exceptionName($name, $exception= NULL) {
      static $exceptions= array();

      if (NULL !== $exception) $exceptions[$name]= $exception;
      return $exceptions[$name];
    }
  
    /**
     * Register or retrieve a mapping for a package
     *
     * @access  public
     * @param   string token
     * @param   string class fully qualified class name
     * @return  string fully qualified class name
     */
    function packageMapping($name, $replace= NULL) {
      static $packages= array();

      if (NULL !== $replace) $packages[$name]= $replace;
      return strtr($name, $packages);
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
      static $types= NULL;
      
      if (!$types) $types= array(
        'N'   => 'void',
        'b'   => 'boolean',
        'i'   => 'integer',
        'd'   => 'double',
        's'   => 'string',
        'B'   => new ClassReference('lang.types.Byte'),
        'S'   => new ClassReference('lang.types.Short'),
        'f'   => new ClassReference('lang.types.Float'),
        'l'   => new ClassReference('lang.types.Long'),
        'a'   => 'array',
        'A'   => new ClassReference('lang.types.ArrayList'),
        'T'   => new ClassReference('util.Date')
      );

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

        case 'B': {     // bytes
          $v= substr($serialized, 2, strpos($serialized, ';', 2)- 2); 
          $length= strlen($v)+ 3;
          return new Byte($v);
        }

        case 'S': {     // shorts
          $v= substr($serialized, 2, strpos($serialized, ';', 2)- 2); 
          $length= strlen($v)+ 3;
          return new Short($v);
        }

        case 'f': {     // floats
          $v= substr($serialized, 2, strpos($serialized, ';', 2)- 2); 
          $length= strlen($v)+ 3;
          return new Float($v);
        }

        case 'l': {     // longs
          $v= substr($serialized, 2, strpos($serialized, ';', 2)- 2); 
          $length= strlen($v)+ 3;
          return new Long($v);
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
        
        case 'A': {     // strictly numeric arrays
          $a= &new ArrayList();
          $size= substr($serialized, 2, strpos($serialized, ':', 2)- 2);
          $offset= strlen($size)+ 2+ 2;
          for ($i= 0; $i < $size; $i++) {
            $a->values[$i]= &Serializer::valueOf(substr($serialized, $offset), $len, $context);
            $offset+= $len;
          }
          $length= $offset+ 1;
          return $a;
        }

        case 'e': {     // known exceptions
          $len= substr($serialized, 2, strpos($serialized, ':', 2)- 2);
          try(); {
            $class= &XPClass::forName(Serializer::exceptionName(substr($serialized, 2+ strlen($len)+ 2, $len)));
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

        case 'E': {     // generic exceptions
          $len= substr($serialized, 2, strpos($serialized, ':', 2)- 2);
          $instance= &new ExceptionReference(substr($serialized, 2+ strlen($len)+ 2, $len));
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
            NULL
          );
        }
        
        case 'T': {     // timestamp
          $v= substr($serialized, 2, strpos($serialized, ';', 2)- 2); 
          $length= strlen($v)+ 3;
          return new Date((int)$v);
        }

        case 'O': {     // generic objects
          $len= substr($serialized, 2, strpos($serialized, ':', 2)- 2);
          $name= Serializer::packageMapping(substr($serialized, 2+ strlen($len)+ 2, $len));
          try(); {
            $class= &XPClass::forName($name);
          } if (catch('ClassNotFoundException', $e)) {
            $instance= &new UnknownRemoteObject($name);
            $offset= 2 + 2 + strlen($len)+ $len + 2;
            $size= substr($serialized, $offset, strpos($serialized, ':', $offset)- $offset);
            $offset+= strlen($size)+ 2;
            $members= array();
            for ($i= 0; $i < $size; $i++) {
              $member= Serializer::valueOf(substr($serialized, $offset), $len, $context);
              $offset+= $len;
              $members[$member]= &Serializer::valueOf(substr($serialized, $offset), $len, $context);
              $offset+= $len;
            }
            $length= $offset+ 1;
            $instance->__members= $members;
            return $instance;
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

        case 'c': {     // builtin classes
          $length= 4;
          $token= substr($serialized, 2, strpos($serialized, ';', 2)- 2);
          if (!isset($types[$token])) {
            return throw(new FormatException('Unknown token "'.$token.'"'));
          }
          return $types[$token];
        }
        
        case 'C': {     // generic classes
          $len= substr($serialized, 2, strpos($serialized, ':', 2)- 2);
          $length= 2 + strlen($len) + 2 + $len + 2;
          return new ClassReference(Serializer::packageMapping(substr($serialized, 2+ strlen($len)+ 2, $len)));
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
