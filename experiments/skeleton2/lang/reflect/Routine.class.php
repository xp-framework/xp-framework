<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('lang.reflect.Argument');
  
  /**
   * Represents a routine
   *
   * @model    abstract
   * @see      xp://lang.reflect.Method
   * @see      xp://lang.reflect.Constructor
   * @purpose  Reflection
   */
  class Routine extends Object {
    protected
      $_reflection = NULL;

    /**
     * Constructor
     *
     * @access  public
     * @param   &mixed _reflection
     * @param   string name
     */    
    public function __construct($_reflection) {
      $this->_reflection= $_reflection;
    }

    /**
     * Retrieve parsed API docs from a doc comment. Note: Results from this 
     * method are cached!
     *
     * @access  protected
     * @param   string comment
     * @return  array
     */
    protected function _apidoc($comment) {
      static $apidoc= array();

      if (!isset($apidoc[$comment])) {
        $apidoc[$comment]= array(
          0 => 0,           // access
          1 => array(),     // arguments
          2 => 'void',      // return
          3 => array()      // throws
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
              $apidoc[$comment][0] |= constant('MODIFIER_'.strtoupper($match[2]));
              break;

            case 'param': 
              $apidoc[$comment][1][]= new Argument(
                $match[3],
                $match[2],
                'default' == $match[4]
              );
              break;

            case 'return':
              $apidoc[$comment][2]= $match[2];
              break;

            case 'throws': 
              $apidoc[$comment][3][]= $match[2];
              break;
          }
        }
      }
      return $apidoc[$comment];
    }

    /**
     * Get Name
     *
     * @access  public
     * @return  string
     */
    public function getName() {
      return $this->_reflection->getName();
    }

    /**
     * Retrieve this method's modifiers
     *
     * @access  public
     * @return  int
     */    
    public function getModifiers() {
      return $this->_reflection->getModifiers();
    }

    /**
     * Retrieve this method's modifiers as an array of strings
     *
     * @access  public
     * @return  string[]
     */    
    public function getModifierNames() {
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
    public function getArguments() {
      $apidoc= $this->_apidoc($this->_reflection->getDocComment());
      return $apidoc[1];
    }

    /**
     * Retrieve return type
     *
     * @access  public
     * @return  string
     */
    public function getReturnType() {
      $apidoc= $this->_apidoc($this->_reflection->getDocComment());
      return $apidoc[2];
    }
    
    /**
     * Retrieve exception names
     *
     * @access  public
     * @return  string[]
     */
    public function getExceptionNames() {
      $apidoc= $this->_apidoc($this->_reflection->getDocComment());
      return $apidoc[3];
    }

    /**
     * Retrieve exception types
     *
     * @access  public
     * @return  lang.XPClass[]
     */
    public function getExceptionTypes() {
      $r= array();
      foreach ($this->getExceptionNames() as $name) {
        $r[]= new XPClass($name);
      }
      return $r;
    }
    
    /**
     * Returns the XPClass object representing the class or interface 
     * that declares the method represented by this Method object.
     *
     * Note that this method returns the first class in the inheritance
     * chain this method was declared in. This is due to inefficiency
     * in PHP4.
     *
     * @access  public
     * @return  &lang.XPClass
     */
    public function getDeclaringClass() {
      return new XPClass($this->_reflection->getDeclaringClass());
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
    public function toString() {
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
