<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'remote.protocol.SerializedData',
    'remote.protocol.DateMapping',
    'remote.protocol.LongMapping',
    'remote.protocol.ByteMapping',
    'remote.protocol.ShortMapping',
    'remote.protocol.FloatMapping',
    'remote.protocol.DoubleMapping',
    'remote.protocol.IntegerMapping',
    'remote.protocol.HashmapMapping',
    'remote.protocol.ArrayListMapping',
    'remote.protocol.ExceptionMapping',
    'remote.protocol.StackTraceElementMapping',
    'remote.protocol.ByteArrayMapping',
    'remote.protocol.EnumMapping',
    'remote.UnknownRemoteObject',
    'remote.ExceptionReference',
    'remote.ClassReference',
    'lang.Enum'
  );

  /**
   * Class that reimplements PHP's builtin serialization format.
   *
   * @see      php://serialize
   * @test     xp://net.xp_framework.unittest.remote.SerializerTest
   * @purpose  Serializer
   */
  class Serializer extends Object {
    public
      $mappings   = array(),
      $packages   = array(0 => array(), 1 => array()),
      $exceptions = array();
    
    public
      $_classMapping  = array();

    /**
     * Constructor. Initializes the default mappings
     *
     */
    public function __construct() {
      $this->mappings['T']= new DateMapping();
      $this->mappings['l']= new LongMapping();
      $this->mappings['B']= new ByteMapping();
      $this->mappings['S']= new ShortMapping();
      $this->mappings['f']= new FloatMapping();
      $this->mappings['d']= new DoubleMapping();
      $this->mappings['i']= new IntegerMapping();
      $this->mappings['A']= new ArrayListMapping();
      $this->mappings['e']= new ExceptionMapping();
      $this->mappings['t']= new StackTraceElementMapping();
      $this->mappings['Y']= new ByteArrayMapping();
      
      // A hashmap doesn't have its own token, because it'll be serialized
      // as an array. We use HASHMAP as the token, so it will never match
      // another one (can only be one char). This is a little bit hackish.
      $this->mappings['HASHMAP']= new HashmapMapping();
      
      // Setup default exceptions
      $this->exceptions['IllegalArgument']= 'lang.IllegalArgumentException';
      $this->exceptions['IllegalAccess']= 'lang.IllegalAccessException';
      $this->exceptions['ClassNotFound']= 'lang.ClassNotFoundException';
      $this->exceptions['NullPointer']= 'lang.NullPointerException';
    }

    /**
     * Retrieve serialized representation of a variable
     *
     * @param   var var
     * @return  string
     * @throws  lang.FormatException if an error is encountered in the format 
     */  
    public function representationOf($var, $ctx= array()) {
      switch ($type= xp::typeOf($var)) {
        case '<null>': case 'NULL': 
          return 'N;';

        case 'boolean': 
          return 'b:'.($var ? 1 : 0).';';

        case 'integer': 
          return 'i:'.$var.';';

        case 'double': 
          return 'd:'.$var.';';

        case 'string': 
          return 's:'.strlen($var).':"'.$var.'";';

        case 'array':
          $s= 'a:'.sizeof($var).':{';
          foreach (array_keys($var) as $key) {
            $s.= serialize($key).$this->representationOf($var[$key], $ctx);
          }
          return $s.'}';

        case 'resource': 
          return ''; // Ignore (resources can't be serialized)

        case $var instanceof Generic: {
          if (FALSE !== ($m= $this->mappingFor($var))) {
            return $m->representationOf($this, $var, $ctx);
          }
          
          // Default object serializing
          $props= get_object_vars($var);
          $type= strtr($type, $this->packages[1]);
          
          unset($props['__id']);
          $s= 'O:'.strlen($type).':"'.$type.'":'.sizeof($props).':{';
          foreach (array_keys($props) as $name) {
            $s.= serialize($name).$this->representationOf($var->{$name}, $ctx);
          }
          return $s.'}';
        }

        default: 
          throw new FormatException('Cannot serialize unknown type '.$type);
      }
    }
    
    /**
     * Fetch best fitted mapper for the given object
     *
     * @param   lang.Object var
     * @return  var FALSE in case no mapper could be found, &remote.protocol.SerializerMapping otherwise
     */
    public function mappingFor($var) {
      if (!($var instanceof Generic)) return FALSE;  // Safeguard

      // Check the mapping-cache for an entry for this object's class
      if (isset($this->_classMapping[$var->getClassName()])) {
        return $this->_classMapping[$var->getClassName()];
      }
      
      // Find most suitable mapping by calculating the distance in the inheritance
      // tree of the object's class to the class being handled by the mapping.
      $cinfo= array();
      foreach (array_keys($this->mappings) as $token) {
        $class= $this->mappings[$token]->handledClass();
        if (!is($class->getName(), $var)) continue;
        
        $distance= 0; $objectClass= $var->getClass();
        do {
        
          // Check for direct match
          if ($class->getName() != $objectClass->getName()) $distance++;
        } while (0 < $distance && NULL !== ($objectClass= $objectClass->getParentClass()));

        // Register distance to object's class in cinfo
        $cinfo[$distance]= $this->mappings[$token];

        if (isset($cinfo[0])) break;
      }
      
      // No handlers found...
      if (0 == sizeof($cinfo)) return FALSE;

      ksort($cinfo, SORT_NUMERIC);
      
      // First class is best class
      // Remember this, so we can take shortcut next time
      $this->_classMapping[$var->getClassName()]= $cinfo[key($cinfo)];
      return $this->_classMapping[$var->getClassName()];
    }

    /**
     * Register or retrieve a mapping for a token
     *
     * @param   string token
     * @param   remote.protocol.SerializerMapping mapping
     * @return  remote.protocol.SerializerMapping mapping
     * @throws  lang.IllegalArgumentException if the given argument is not a SerializerMapping
     */
    public function mapping($token, $mapping) {
      if (NULL !== $mapping) {
        if (!$mapping instanceof SerializerMapping) throw new IllegalArgumentException(
          'Given argument is not a SerializerMapping ('.xp::typeOf($mapping).')'
        );

        $this->mappings[$token]= $mapping;
        $this->_classMapping= array();
      }
      
      return $this->mappings[$token];
    }
    
    /**
     * Register or retrieve a mapping for a token
     *
     * @param   string token
     * @param   string exception fully qualified class name
     * @return  string 
     */
    public function exceptionName($name, $exception= NULL) {
      if (NULL !== $exception) $this->exceptions[$name]= $exception;
      return $this->exceptions[$name];
    }
  
    /**
     * Retrieve a mapping for a package.
     *
     * @param   string remote
     * @param   string mapped
     */
    public function packageMapping($remote) {
      return strtr($remote, $this->packages[0]);
    }

    /**
     * Map a remote package name to a local package
     *
     * @param   string remote
     * @param   lang.reflect.Package mapped
     */
    public function mapPackage($remote, Package $mapped) {
      $this->packages[0][$remote]= $mapped->getName();
      $this->packages[1][$mapped->getName()]= $remote;
    }
    
    /**
     * Retrieve serialized representation of a variable
     *
     * @param   remote.protocol.SerializedData serialized
     * @param   array context default array()
     * @return  var
     * @throws  lang.ClassNotFoundException if a class cannot be found
     * @throws  lang.FormatException if an error is encountered in the format 
     */  
    public function valueOf($serialized, $context= array()) {
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

      $token= $serialized->consumeToken();
      switch ($token) {
        case 'N': return NULL;
        case 'b': return (bool)$serialized->consumeWord();
        case 'i': return (int)$serialized->consumeWord();
        case 'd': return (float)$serialized->consumeWord();
        case 's': return $serialized->consumeString();

        case 'a': {     // arrays
          $a= array();
          $size= $serialized->consumeSize();
          $serialized->consume('{');
          for ($i= 0; $i < $size; $i++) {
            $key= $this->valueOf($serialized, $context);
            $a[$key]= $this->valueOf($serialized, $context);
          }
          $serialized->consume('}');
          return $a;
        }

        case 'E': {     // generic exceptions
          $instance= new ExceptionReference($serialized->consumeString());
          $size= $serialized->consumeSize();
          $serialized->consume('{');
          for ($i= 0; $i < $size; $i++) {
            $member= $this->valueOf($serialized, $context);
            $instance->{$member}= $this->valueOf($serialized, $context);
          }
          $serialized->consume('}');
          return $instance;
        }
        
        case 'O': {     // generic objects
          $name= $serialized->consumeString();
          $members= array();
          try {
            $class= XPClass::forName(strtr($name, $this->packages[0]));
          } catch (ClassNotFoundException $e) {
            $instance= new UnknownRemoteObject($name);
            $size= $serialized->consumeSize();
            $serialized->consume('{');
            for ($i= 0; $i < $size; $i++) {
              $member= $this->valueOf($serialized, $context);
              $members[$member]= $this->valueOf($serialized, $context);
            }
            $serialized->consume('}');
            $instance->__members= $members;
            return $instance;
          }
          
          $size= $serialized->consumeSize();
          $serialized->consume('{');

          if ($class->isEnum()) {
            if ($size != 1 || 'name' != $this->valueOf($serialized, $context)) {
              throw new FormatException(sprintf(
                'Local class %s is an enum but remote class is not serialized as one (%s)',
                $name,
                $serialized->toString()
              ));
            }
            $instance= Enum::valueOf($class, $this->valueOf($serialized, $context));
          } else {
            $instance= $class->newInstance();
            for ($i= 0; $i < $size; $i++) {
              $member= $this->valueOf($serialized, $context);
              $instance->{$member}= $this->valueOf($serialized, $context);
            }
          }
          
          $serialized->consume('}');
          return $instance;
        }

        case 'c': {     // builtin classes
          $type= $serialized->consumeWord();
          if (!isset($types[$type])) {
            throw new FormatException('Unknown type token "'.$type.'"');
          }
          return $types[$type];
        }
        
        case 'C': {     // generic classes
          return new ClassReference(strtr($serialized->consumeString(), $this->packages[0]));
        }

        default: {      // default, check if we have a mapping
          if (!($mapping= $this->mapping($token, $m= NULL))) {
            throw new FormatException(
              'Cannot deserialize unknown type "'.$token.'" ('.$serialized->toString().')'
            );
          }

          return $mapping->valueOf($this, $serialized, $context);
        }
      }
    }
  }
?>
