<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'lang.apidoc.Comment',
    'lang.apidoc.Reference'
  );

  /**
   * Class wrapping class comments. Class comments are written above
   * the class name and may contain any of the keywords @purpose, @see,
   * @model and any plain text you wish.
   *
   * @purpose  Comment
   */
  class ClassComment extends Comment {
    const
      APIDOC_CLASS_MODEL_STATIC = 'static',
      APIDOC_CLASS_MODEL_GENERIC = 'generic';

    public
      $references   = array(),
      $purpose      = '',
      $examples     = array(),
      $extends      = NULL,
      $name         = '',
      $model        = APIDOC_CLASS_MODEL_GENERIC,
      $extensions   = NULL,
      $deprecated   = FALSE,
      $experimental = FALSE;
      
    /**
     * Sets this class's name
     *
     * @access  public
     * @param   string name
     */
    public function setClassName($name) {
      $this->name= $name;
    }

    /**
     * Sets which class this class extends. This information is parsed
     * from the class declaration, i.e. 
     * <code>
     *   class Model extends Object { }
     * </code>
     *
     * @access  public
     * @param   string extends classname of parent class
     */
    public function setExtends($extends) {
      $this->extends= $extends;
    }
      
    /**
     * Sets if this class is deprecated and use is discouraged
     *
     * @access  public
     * @param   boolean true if class is deprecated
     */
    public function setDeprecation($deprecated) {
      $this->deprecated= $deprecated;
    }

    /**
     * Sets if this class is experimental
     *
     * @access  public
     * @param   boolean true if class is experimental
     */
    public function setExperimental($experimental) {
      $this->experimental= $experimental;
    }
      
    /**
     * Sets class model. This information is parsed from the @purpose
     * documentation keyword.
     *
     * @access  public
     * @param   string model one of the APIDOC_CLASS_MODEL_* constants
     */
    public function setModel($model) {
      $this->model= $model;
    }

    /**
     * Sets purpose.
     *
     * @access  public
     * @param   string purpose
     */
    public function setPurpose($purpose) {
      $this->purpose= $purpose;
    }
    
    /**
     * Sets ext. Ext defines which PHP-Extensions a class depends on
     *
     * @access public
     * @param  string extension
     */
    public function setExtension($extension) {
      if (NULL === $this->extensions)
        $this->extensions= array ();
        
      $this->extensions[]= $extension;
    }
     
    /**
     * Add an example
     *
     * @access  public
     * @param   mixed example
     * @return  &mixed the example added
     */
    public function addExample($example) {
      $this->examples[]= $example;
      return $this->examples[sizeof($this->examples)- 1];
    }

    /**
     * Add an reference
     *
     * @access  public
     * @param   string see
     * @return  &lang.apidoc.Reference
     */
    public function addReference($see) {
      $this->references[]= new Reference($see);
      return $this->references[sizeof($this->references)- 1];
    }
    
    /**
     * Handles tags
     *
     * @access  protected
     * @param   string tag
     * @param   string line
     * @return  &mixed
     * @see     xp://lang.apidoc.Comment
     */
    protected function _handleTag($tag, $line) {
      $descr= parent::_handleTag($tag, $line); 
      
      switch ($tag) {
        case 'see':
          $descr= self::addReference($line);
          break;

        case 'purpose':
          self::setPurpose($line);
          $descr= NULL;
          break;

        case 'example':
          $descr= self::addExample($line);
          break;
          
        case 'model':
          self::setModel($line);
          $descr= NULL;
          break;
        
        case 'ext':
          self::setExtension($line);
          break;
        
        case 'deprecated':
          self::setDeprecation(TRUE);
          break;

        case 'experimental':
          self::setExperimental(TRUE);
          break;
      }
      
      return $descr;
    }
  }
?>
