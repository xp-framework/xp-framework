<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  uses('text.doclet.AnnotatedDoc', 'text.doclet.ClassIterator');

  define('EXCEPTION_CLASS',   'exception');
  define('ERROR_CLASS',       'error');
  define('INTERFACE_CLASS',   'interface');
  define('ORDINARY_CLASS',    'class');
  define('ENUM_CLASS',        'enum');

  /**
   * Represents an XP class or interface and provides access to 
   * information about it, its comment and tags, and members.
   *
   * @test     xp://net.xp_framework.unittest.text.doclet.ClassDocTest
   * @purpose  Documents a class
   */
  class ClassDoc extends AnnotatedDoc {
    public
      $fields         = array(),
      $methods        = array(),
      $constants      = array(),
      $interfaces     = NULL,
      $usedClasses    = NULL,
      $superclass     = NULL,
      $type           = NULL,
      $qualifiedName  = '',
      $modifiers      = array();

    /**
     * Constructor
     *
     */
    public function __construct() {
      $this->interfaces= new ClassIterator();
      $this->usedClasses= new ClassIterator();
    }

    /**
     * Set rootdoc
     *
     * @param   text.doclet.RootDoc root
     */
    public function setRoot($root) {
      parent::setRoot($root);
      $this->interfaces->root= $root;
      $this->usedClasses->root= $root;    
    }

    /**
     * Returns the source file name this doc was parsed from.
     *
     * @return  string
     */
    public function sourceFile() {
      return $this->root->findClass($this->qualifiedName);
    }
    
    /**
     * Retrieve class type, which one of the following constants
     * 
     * <ul>
     *   <li>EXCEPTION_CLASS</li>
     *   <li>ERROR_CLASS</li>
     *   <li>INTERFACE_CLASS</li>
     *   <li>ORDINARY_CLASS</li>
     * </ul>
     *
     * @return  string
     */
    public function classType() {
      static $map= array(
        'lang.XPException' => EXCEPTION_CLASS,
        'lang.Error'       => ERROR_CLASS,
        'lang.Enum'        => ENUM_CLASS
      );

      if ($this->type) return $this->type;    // Already known

      $cmp= $this;
      do {
        if (isset($map[$cmp->qualifiedName])) {
          return $this->type= $map[$cmp->qualifiedName];
        }
      } while ($cmp= $cmp->superclass);

      return $this->type= ORDINARY_CLASS;
    }
    
    /**
     * Returns whether this class is an exception class.
     *
     * @return  bool
     */
    public function isException() {
      return EXCEPTION_CLASS == $this->classType();
    }
    
    /**
     * Returns whether this class is an error class.
     *
     * @return  bool
     */
    public function isError() {
      return ERROR_CLASS == $this->classType();
    }

    /**
     * Returns whether this class is an interface.
     *
     * @return  bool
     */
    public function isInterface() {
      return INTERFACE_CLASS == $this->classType();
    }

    /**
     * Returns whether this class is an interface.
     *
     * @return  bool
     */
    public function isEnum() {
      return ENUM_CLASS == $this->classType();
    }

    /**
     * Returns whether this class is an ordinary class
     *
     * @return  bool
     */
    public function isOrdinaryClass() {
      return ORDINARY_CLASS == $this->classType();
    }
    
    /**
     * Returns whether this class is a subclass of a given class.
     *
     * @return  bool
     */
    public function subclassOf($classdoc) {
      $cmp= $this;
      do {
        if ($cmp->qualifiedName == $classdoc->qualifiedName) return TRUE;
      } while ($cmp= $cmp->superclass);

      return FALSE;
    }
    
    /**
     * Get the fully qualified name of this program element. For example, 
     * for the class util.Date, return "util.Date". 
     *
     * @return  string
     */
    public function qualifiedName() {
      return $this->qualifiedName;
    }

    /**
     * Returns the package this class is contained in
     *
     * @return  text.doclet.PackageDoc
     */
    public function containingPackage() {
      return $this->root->packageNamed(substr($this->qualifiedName, 0, strrpos($this->qualifiedName, '.')));
    }
    
    /**
     * Returns a string representation of this object
     *
     * @return  string
     */
    public function toString() {
      return $this->getClassName().'<'.$this->classType().' '.$this->qualifiedName.'>';
    }

    /**
     * Returns modifiers as a hashmap (modifier names as keys for easy
     * O(1) lookup).
     *
     * @return  array<string, TRUE> 
     */
    public function getModifiers() {
      return $this->modifiers;
    }
    
    /**
     * Returns a hashcode for this object
     *
     * @return  string
     */
    public function hashCode() {
      return $this->getClassName().$this->qualifiedName;
    }
  }
?>
