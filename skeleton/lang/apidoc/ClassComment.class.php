<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  uses('lang.apidoc.Comment', 'lang.apidoc.Reference');
  
  // Class models: static, generic
  define('APIDOC_CLASS_MODEL_STATIC',  'static');
  define('APIDOC_CLASS_MODEL_GENERIC', 'generic');
  
  /**
   * Class wrapping class comments. Class comments are written above
   * the class name and may contain any of the keywords @purpose, @see,
   * @model and any plain text you wish.
   *
   * @see xp-doc:README.DOC
   */
  class ClassComment extends Comment {
    var
      $references   = array(),
      $purpose      = '',
      $examples     = array(),
      $extends      = NULL,
      $name         = '',
      $model        = APIDOC_CLASS_MODEL_GENERIC;
      
    /**
     * Sets this class's name
     *
     * @access  public
     * @param   string name
     */
    function setClassName($name) {
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
    function setExtends($extends) {
      $this->extends= $extends;
    }
      
    /**
     * Sets class model. This information is parsed from the @purpose
     * documentation keyword.
     *
     * @access  public
     * @param   string model one of the APIDOC_CLASS_MODEL_* constants
     */
    function setModel($model) {
      $this->model= $model;
    }

    /**
     * Sets purpose. The purpose
     *
     * @access  
     * @param   
     * @return  
     */
    function setPurpose($purpose) {
      $this->purpose= $purpose;
    }

    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function &addExample($example) {
      $this->examples[]= $example;
      return $this->examples[sizeof($this->examples)- 1];
    }

    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function &addReference($see) {
      $this->references[]= &new Reference($see);
      return $this->references[sizeof($this->references)- 1];
    }
    
    /**
     * Handles tags
     *
     * @see lang.apidoc.Comment
     */
    function &_handleTag($tag, $line) {
      $descr= &parent::_handleTag($tag, $line); 
      
      switch ($tag) {
        case 'see':
          $descr= &$this->addReference($line);
          break;

        case 'purpose':
          $this->setPurpose($line);
          $descr= NULL;
          break;

        case 'example':
          $descr= &$this->addExample($line);
          break;
          
        case 'model':
          $this->setModel($line);
          $descr= NULL;
          break;


      }
      
      return $descr;
    }
  }
?>
