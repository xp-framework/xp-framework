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
    public
      $_ref = NULL,
      $name = '';

    /**
     * Constructor
     *
     * @access  private
     * @param   &mixed ref
     * @param   string name
     */    
    public function __construct(&$ref, $name) {
      $this->_ref= is_object($ref) ? get_class($ref) : $ref;
      $this->name= strtolower($name);
    }

    /**
     * Get method's name. If the optional parameter "asDeclared" is set to TRUE,
     * the name will be parsed from the sourcecode, thus preserving case.
     *
     * @access  public
     * @param   bool asDeclared default FALSE
     * @return  string
     */
    public function getName($asDeclared= FALSE) {
      if (!$asDeclared) return $this->name;

      if (!($details= XPClass::detailsForMethod($this->_ref, $this->name))) return NULL;
      return $details[DETAIL_NAME];
    }
    
    /**
     * Retrieve this method's modifiers
     *
     * @access  public
     * @return  int
     */    
    public function getModifiers() {
      if (!($details= XPClass::detailsForMethod($this->_ref, $this->name))) return NULL;
      return $details[DETAIL_MODIFIERS];
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
      if (!($details= XPClass::detailsForMethod($this->_ref, $this->name))) return NULL;
      return $details[DETAIL_ARGUMENTS];
    }

    /**
     * Retrieve one of this method's argument by its position
     *
     * @access  public
     * @param   int pos
     * @return  &lang.reflect.Argument
     */
    public function &getArgument($pos) {
      if (!($details= XPClass::detailsForMethod($this->_ref, $this->name))) return NULL;
      if (!isset($details[DETAIL_ARGUMENTS][$pos])) return NULL;
      return $details[DETAIL_ARGUMENTS][$pos];
    }

    /**
     * Retrieve how many arguments this method accepts (including optional ones)
     *
     * @access  public
     * @return  int
     */
    public function numArguments() {
      if (!($details= XPClass::detailsForMethod($this->_ref, $this->name))) return NULL;
      return sizeof($details[DETAIL_ARGUMENTS]);
    }

    /**
     * Retrieve return type
     *
     * @access  public
     * @return  string
     */
    public function getReturnType() {
      if (!($details= XPClass::detailsForMethod($this->_ref, $this->name))) return NULL;
      return ltrim($details[DETAIL_RETURNS], '&');
    }

    /**
     * Retrieve whether this method returns a reference
     *
     * @access  public
     * @return  string
     */
    public function returnsReference() {
      if (!($details= XPClass::detailsForMethod($this->_ref, $this->name))) return NULL;
      return '&' == $details[DETAIL_RETURNS]{0};
    }
    
    /**
     * Retrieve exception names
     *
     * @access  public
     * @return  string[]
     */
    public function getExceptionNames() {
      if (!($details= XPClass::detailsForMethod($this->_ref, $this->name))) return NULL;
      return $details[DETAIL_THROWS];
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
    public function &getDeclaringClass() {
      $class= $this->_ref;
      while ($details= XPClass::detailsForClass(xp::nameOf($class))) {
        if (isset($details[1][$this->name])) return new XPClass($class);
        $class= get_parent_class($class);
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
    public function getComment() {
      if (!($details= XPClass::detailsForMethod($this->_ref, $this->name))) return NULL;
      return $details[DETAIL_COMMENT];
    }
    
    /**
     * Check whether an annotation exists
     *
     * @access  public
     * @param   string name
     * @param   string key default NULL
     * @return  bool
     */
    public function hasAnnotation($name, $key= NULL) {
      $details= XPClass::detailsForMethod($this->_ref, $this->name);

      return $details && ($key 
        ? array_key_exists($key, (array)@$details[DETAIL_ANNOTATIONS][$name]) 
        : array_key_exists($name, (array)@$details[DETAIL_ANNOTATIONS])
      );
    }

    /**
     * Retrieve annotation by name
     *
     * @access  public
     * @param   string name
     * @param   string key default NULL
     * @return  mixed
     * @throws  lang.ElementNotFoundException
     */
    public function getAnnotation($name, $key= NULL) {
      $details= XPClass::detailsForMethod($this->_ref, $this->name);

      if (!$details || !($key 
        ? array_key_exists($key, @$details[DETAIL_ANNOTATIONS][$name]) 
        : array_key_exists($name, @$details[DETAIL_ANNOTATIONS])
      )) return raise(
        'lang.ElementNotFoundException', 
        'Annotation "'.$name.($key ? '.'.$key : '').'" does not exist'
      );

      return ($key 
        ? $details[DETAIL_ANNOTATIONS][$name][$key] 
        : $details[DETAIL_ANNOTATIONS][$name]
      );
    }

    /**
     * Retrieve whether a method has annotations
     *
     * @access  public
     * @return  bool
     */
    public function hasAnnotations() {
      $details= XPClass::detailsForMethod($this->_ref, $this->name);
      return $details ? !empty($details[DETAIL_ANNOTATIONS]) : FALSE;
    }

    /**
     * Retrieve all of a method's annotations
     *
     * @access  public
     * @return  array annotations
     */
    public function getAnnotations() {
      $details= XPClass::detailsForMethod($this->_ref, $this->name);
      return $details ? $details[DETAIL_ANNOTATIONS] : array();
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
          $args.= ', ['.$arguments[$i]->getType().' $'.$arguments[$i]->getName().'= '.$arguments[$i]->getDefault().']';
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
