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
   * @test     xp://net.xp_framework.unittest.remote.SerializerTest
   * @purpose  Serializer
   */
  class Serializer extends Object {
    var
      $mappings   = array(),
      $packages   = array(),
      $exceptions = array();
    
    var
      $_classMapping  = array();

    /**
     * Retrieve serialized representation of a variable
     *
     * @access  public
     * @param   &mixed var
     * @return  string
     * @throws  lang.FormatException if an error is encountered in the format 
     */  
    function representationOf(&$var, $ctx= array()) {
      switch (gettype($var)) {
        case 'NULL':    return 'N;';
        case 'boolean': return 'b:'.($var ? 1 : 0).';';
        case 'integer': return 'i:'.$var.';';
        case 'double':  return 'd:'.$var.';';
        case 'string':  return 's:'.strlen($var).':"'.$var.'";';
        case 'array':
          $s= 'a:'.sizeof($var).':{';
          foreach (array_keys($var) as $key) {
            $s.= serialize($key).$this->representationOf($var[$key], $ctx);
          }
          return $s.'}';
        case 'object':
          if (FALSE !== ($m= &$this->mappingFor($var))) {
            return $m->representationOf($this, $var, $ctx);
          }
          
          switch (1) {
            case is_a($var, 'Date'): {
              return 'T:'.$var->getTime().';';
            }
            case is_a($var, 'ArrayList'): {
              $s= 'A:'.sizeof($var->values).':{';
              foreach (array_keys($var->values) as $key) {
                $s.= $this->representationOf($var->values[$key], $ctx);
              }
              return $s.'}';
            }
            case is_a($var, 'HashMap'): {
              return $this->representationOf($var->_hash, $ctx);
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
                $s.= serialize($name).$this->representationOf($var->{$name}, $ctx);
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
     * Fetch best fitted mapper for the given object
     *
     * @access  protected
     * @param   &lang.Object var
     * @return  mixed FALSE in case no mapper could be found, &remote.protocol.SerializerMapping otherwise
     */
    function &mappingFor(&$var) {
      if (!is('lang.Object', $var)) return FALSE;
      
      // Check the mapping-cache for an entry for this object's class
      if (isset($this->_classMapping[$var->getClassName()])) {
        return $this->_classMapping[$var->getClassName()];
      }
      
      // Find most suitable mapping by calculating the distance in the inheritance
      // tree of the object's class to the class being handled by the mapping.
      $cinfo= array();
      foreach (array_keys($this->mappings) as $token) {
        $class= &$this->mappings[$token]->handledClass();
        if (!is($class->getName(), $var)) continue;

        $distance= 0;
        do {

          // Check for direct match
          if ($class->getName() != $var->getClassName()) $distance++;
        } while (0 < $distance && NULL !== ($class= &$class->getParentclass()));

        // Register distance to object's class in cinfo
        $cinfo[$distance]= &$this->mappings[$token];

        if (isset($cinfo[0])) break;
      }
      
      // No handlers found...
      if (0 == sizeof($cinfo)) return FALSE;

      ksort($cinfo, SORT_NUMERIC);
      
      // First class is best class
      $handlerClass= &$cinfo[key($cinfo)];

      // Remember this, so we can take shortcut next time
      $this->_classMapping[$var->getClassName()]= &$cinfo[key($cinfo)];
      return $this->_classMapping[$var->getClassName()];
    }

    /**
     * Register or retrieve a mapping for a token
     *
     * @access  public
     * @param   string token
     * @param   &remote.protocol.SerializerMapping mapping
     * @return  &remote.protocol.SerializerMapping mapping
     * @throws  lang.IllegalArgumentException if the given argument is not a SerializerMapping
     */
    function &mapping($token, &$mapping) {
      if (NULL !== $mapping) {
        if (!is('SerializerMapping', $mapping)) return throw(new IllegalArgumentException(
          'Given argument is not a SerializerMapping ('.xp::typeOf($mapping).')'
        ));

        $this->mappings[$token]= &$mapping;
        $this->_classMapping= array();
      }
      
      return $this->mappings[$token];
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
      if (NULL !== $exception) $this->exceptions[$name]= $exception;
      return $this->exceptions[$name];
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
      if (NULL !== $replace) $this->packages[$name]= $replace;
      return strtr($name, $this->packages);
    }

    /**
     * Retrieve serialized representation of a variable
     *
     * @access  public
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
          $value= NULL;
          return $value;
        }

        case 'b': {     // booleans
          $length= 4; 
          $value= (bool)substr($serialized, 2, strpos($serialized, ';', 2)- 2);
          return $value;
        }

        case 'i': {     // integers
          $v= substr($serialized, 2, strpos($serialized, ';', 2)- 2); 
          $length= strlen($v)+ 3;
          $value= (int)$v;
          return $value;
        }

        case 'd': {     // decimals
          $v= substr($serialized, 2, strpos($serialized, ';', 2)- 2); 
          $length= strlen($v)+ 3;
          $value= (float)$v;
          return $value;
        }

        case 's': {     // strings
          $strlen= substr($serialized, 2, strpos($serialized, ':', 2)- 2);
          $length= 2 + strlen($strlen) + 2 + $strlen + 2;
          $value= substr($serialized, 2+ strlen($strlen)+ 2, $strlen);
          return $value;
        }

        case 'B': {     // bytes
          $v= substr($serialized, 2, strpos($serialized, ';', 2)- 2); 
          $length= strlen($v)+ 3;
          $value= &new Byte($v);
          return $value;
        }

        case 'S': {     // shorts
          $v= substr($serialized, 2, strpos($serialized, ';', 2)- 2); 
          $length= strlen($v)+ 3;
          $value= &new Short($v);
          return $value;
        }

        case 'f': {     // floats
          $v= substr($serialized, 2, strpos($serialized, ';', 2)- 2); 
          $length= strlen($v)+ 3;
          $value= &new Float($v);
          return $value;
        }

        case 'l': {     // longs
          $v= substr($serialized, 2, strpos($serialized, ';', 2)- 2); 
          $length= strlen($v)+ 3;
          $value= &new Long($v);
          return $value;
        }

        case 'a': {     // arrays
          $a= array();
          $size= substr($serialized, 2, strpos($serialized, ':', 2)- 2);
          $offset= strlen($size)+ 2+ 2;
          for ($i= 0; $i < $size; $i++) {
            $key= $this->valueOf(substr($serialized, $offset), $len, $context);
            $offset+= $len;
            $a[$key]= &$this->valueOf(substr($serialized, $offset), $len, $context);
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
            $a->values[$i]= &$this->valueOf(substr($serialized, $offset), $len, $context);
            $offset+= $len;
          }
          $length= $offset+ 1;
          return $a;
        }

        case 'e': {     // known exceptions
          $len= substr($serialized, 2, strpos($serialized, ':', 2)- 2);
          try(); {
            $class= &XPClass::forName($this->exceptionName(substr($serialized, 2+ strlen($len)+ 2, $len)));
          } if (catch('ClassNotFoundException', $e)) {
            return throw($e);
          }
          $instance= &$class->newInstance();
          $offset= 2 + 2 + strlen($len)+ $len + 2;
          $size= substr($serialized, $offset, strpos($serialized, ':', $offset)- $offset);
          $offset+= strlen($size)+ 2;
          for ($i= 0; $i < $size; $i++) {
            $member= $this->valueOf(substr($serialized, $offset), $len, $context);
            $offset+= $len;
            $instance->{$member}= &$this->valueOf(substr($serialized, $offset), $len, $context);
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
            $member= $this->valueOf(substr($serialized, $offset), $len, $context);
            $offset+= $len;
            $instance->{$member}= &$this->valueOf(substr($serialized, $offset), $len, $context);
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
            $detail= $this->valueOf(substr($serialized, $offset), $len, $context);
            $offset+= $len;
            $details[$detail]= $this->valueOf(substr($serialized, $offset), $len, $context);
            $offset+= $len;
          }
          $length= $offset+ 1;
          $value= &new StackTraceElement(
            $details['file'],
            $details['class'],
            $details['method'],
            $details['line'],
            array(),
            NULL
          );
          return $value;
        }
        
        case 'T': {     // timestamp
          $v= substr($serialized, 2, strpos($serialized, ';', 2)- 2); 
          $length= strlen($v)+ 3;
          $value= &new Date((int)$v);
          return $value;
        }

        case 'O': {     // generic objects
          $len= substr($serialized, 2, strpos($serialized, ':', 2)- 2);
          $name= $this->packageMapping(substr($serialized, 2+ strlen($len)+ 2, $len));
          try(); {
            $class= &XPClass::forName($name);
          } if (catch('ClassNotFoundException', $e)) {
            $instance= &new UnknownRemoteObject($name);
            $offset= 2 + 2 + strlen($len)+ $len + 2;
            $size= substr($serialized, $offset, strpos($serialized, ':', $offset)- $offset);
            $offset+= strlen($size)+ 2;
            $members= array();
            for ($i= 0; $i < $size; $i++) {
              $member= $this->valueOf(substr($serialized, $offset), $len, $context);
              $offset+= $len;
              $members[$member]= &$this->valueOf(substr($serialized, $offset), $len, $context);
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
            $member= $this->valueOf(substr($serialized, $offset), $len, $context);
            $offset+= $len;
            $instance->{$member}= &$this->valueOf(substr($serialized, $offset), $len, $context);
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
          $value= &new ClassReference($this->packageMapping(substr($serialized, 2+ strlen($len)+ 2, $len)));
          return $value;
        }

        default: {      // default, check if we have a mapping
          if (!($mapping= &$this->mapping($serialized{0}, $m= NULL))) {
            return throw(new FormatException(
              'Cannot deserialize unknown type "'.$serialized{0}.'" ('.$serialized.')'
            ));
          }

          return $mapping->valueOf($this, $serialized, $length, $context);
        }
      }
    }
  }
?>
