<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('lang.reflect.Argument');

  /**
   * Base class for methods and constructors. Note that the methods provided
   * in this class (except for getName()) are implemented using a tokenizer
   * on the class files, gathering its information from the API docs.
   *
   * This, of course, will not be as fast as if the details were provided by
   * PHP itself and will also rely on the API docs being consistent and 
   * correct.
   *
   * @model    abstract
   * @see      xp://lang.reflect.Method
   * @see      xp://lang.reflect.Constructor
   * @purpose  Reflection
   */
  class Routine extends Object {
    var
      $_ref = NULL,
      $name = '';

    /**
     * Constructor
     *
     * @access  private
     * @param   &mixed ref
     * @param   string name
     */    
    function __construct(&$ref, $name) {
      $this->_ref= is_object($ref) ? get_class($ref) : $ref;
      $this->name= $name;
    }

    /**
     * Get Name
     *
     * @access  public
     * @return  string
     */
    function getName() {
      return $this->name;
    }
    
    /**
     * Retrieve API docs for a specified class. Note: Results from this 
     * method are cached!
     *
     * @access  protected
     * @param   string class
     * @return  array
     */
    function _apidoc($class) {
      static $apidoc= array();
      
      if (!$class) return FALSE;        // Border case
      if (!isset($apidoc[$class])) {
        $apidoc[$class]= array();
        $name= strtr(xp::nameOf($class), '.', DIRECTORY_SEPARATOR);
        $l= strlen($name);
        foreach (get_included_files() as $file) {
          if ($name != substr($file, -10- $l, -10)) continue;
          
          // Found the class, now get API documentation
          $tokens= token_get_all(file_get_contents($file));
          for ($i= 0, $s= sizeof($tokens); $i < $s; $i++) {
            switch ($tokens[$i][0]) {
              case T_COMMENT: 
                $comment= $tokens[$i][1];
                break;

              case T_FUNCTION:
                while (T_STRING !== $tokens[$i][0]) $i++;
                $m= strtolower($tokens[$i][1]);
                $apidoc[$class][$m]= array(
                  0 => 0,           // access
                  1 => array(),     // arguments
                  2 => 'void',      // return
                  3 => array(),     // throws
                  4 => preg_replace('/\n     \* ?/', "\n", "\n".substr(
                    $comment, 
                    4,                              // "/**\n"
                    strpos($comment, '* @')- 2      // position of first apidoc token
                  ))
                );
                preg_match_all(
                  '/@([a-z]+)\s*([^\r\n ]+) ?([^\r\n ]+)? ?([^\r\n ]+)?/', 
                  $comment, 
                  $matches, 
                  PREG_SET_ORDER
                );
                foreach ($matches as $match) {
                  switch ($match[1]) {
                    case 'access':
                    case 'model':
                      $apidoc[$class][$m][0] |= constant('MODIFIER_'.strtoupper($match[2]));
                      break;

                    case 'param': 
                      $apidoc[$class][$m][1][]= &new Argument(
                        $match[3],
                        $match[2],
                        'default' == @$match[4]
                      );
                      break;

                    case 'return':
                      $apidoc[$class][$m][2]= $match[2];
                      break;

                    case 'throws': 
                      $apidoc[$class][$m][3][]= $match[2];
                      break;
                  }
                }
                break;

              default:
                // Empty
            }
          }
          
          // Break out of search loop
          break;
        }
      }
      
      // Return API doc for specified class
      return $apidoc[$class];
    }

    /**
     * Retrieve this method's modifiers
     *
     * @access  public
     * @return  int
     */    
    function getModifiers() {
      $c= $this->_ref;
      while ($apidoc= $this->_apidoc($c)) {
        if (isset($apidoc[$this->name])) return $apidoc[$this->name][0];
        $c= get_parent_class($c);
      }
      return 0;    
    }

    /**
     * Retrieve this method's modifiers as an array of strings
     *
     * @access  public
     * @return  string[]
     */    
    function getModifierNames() {
      $m= $this->getModifiers();
      $names= array();
      if ($m & MODIFIER_ABSTRACT) $names[]= 'abstract';
      if ($m & MODIFIER_FINAL) $names[]= 'final';
      switch ($m & (MODIFIER_PUBLIC | MODIFIER_PROTECTED | MODIFIER_PRIVATE)) {
        case MODIFIER_PRIVATE: $names[]= 'private'; break;
        case MODIFIER_PROTECTED: $names[]= 'protected'; break;
        case MODIFIER_PUBLIC:
        default: $names[]= 'public'; break;
      }
      if ($m & MODIFIER_STATIC) $names[]= 'static';
      return $names;
    }
    
    /**
     * Retrieve this method's arguments
     *
     * @access  public
     * @return  lang.reflect.Argument[]
     */
    function getArguments() {
      $c= $this->_ref;
      while ($apidoc= $this->_apidoc($c)) {
        if (isset($apidoc[$this->name])) return $apidoc[$this->name][1];
        $c= get_parent_class($c);
      }
      return array();      
    }

    /**
     * Retrieve return type
     *
     * @access  public
     * @return  string
     */
    function getReturnType() {
      $c= $this->_ref;
      while ($apidoc= $this->_apidoc($c)) {
        if (isset($apidoc[$this->name])) return $apidoc[$this->name][2];
        $c= get_parent_class($c);
      }
      return NULL;
    }
    
    /**
     * Retrieve exception names
     *
     * @access  public
     * @return  string[]
     */
    function getExceptionNames() {
      $c= $this->_ref;
      while ($apidoc= $this->_apidoc($c)) {
        if (isset($apidoc[$this->name])) return $apidoc[$this->name][3];
        $c= get_parent_class($c);
      }
      return array();      
    }

    /**
     * Retrieve exception types
     *
     * @access  public
     * @return  lang.XPClass[]
     */
    function getExceptionTypes() {
      $r= array();
      foreach ($this->getExceptionNames() as $name) {
        $r[]= &new XPClass($name);
      }
      return $r;
    }
    
    /**
     * Returns the XPClass object representing the class or interface 
     * that declares the method represented by this Method object.
     *
     * @access  public
     * @return  &lang.XPClass
     */
    function &getDeclaringClass() {
      $c= $this->_ref;
      while ($apidoc= $this->_apidoc($c)) {
        if (isset($apidoc[$this->name])) return new XPClass($c);
        $c= get_parent_class($c);
      }
      return xp::null();
    }
    
    /**
     * Retrieves the api doc comment for this method. Returns NULL if
     * no documentation is present.
     *
     * @access  public
     * @return  string
     */
    function getComment() {
      $c= $this->_ref;
      while ($apidoc= $this->_apidoc($c)) {
        if (isset($apidoc[$this->name])) return $apidoc[$this->name][4];
        $c= get_parent_class($c);
      }
      return NULL;  
    }
    
    /**
     * Retrieve string representation. Examples:
     *
     * <pre>
     *   public &lang.XPClass getclass()
     *   public static &util.Date now()
     *   public open(string $mode) throws io.FileNotFoundException, io.IOException
     * </pre>
     *
     * @access  public
     * @return  string
     */
    function toString() {
      $args= '';
      for ($arguments= $this->getArguments(), $i= 0, $s= sizeof($arguments); $i < $s; $i++) {
        if ($arguments[$i]->isOptional()) {
          $args.= ', ['.$arguments[$i]->getType().' $'.$arguments[$i]->getName().']';
        } else {
          $args.= ', '.$arguments[$i]->getType().' $'.$arguments[$i]->getName();
        }
      }
      if ($exceptions= $this->getExceptionNames()) {
        $throws= ' throws '.implode(', ', $exceptions);
      } else {
        $throws= '';
      }
      return sprintf(
        '%s %s %s(%s)%s',
        implode(' ', $this->getModifierNames()),
        $this->getReturnType(),
        $this->getName(),
        substr($args, 2),
        $throws
      );
    }
  }
?>
