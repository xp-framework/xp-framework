<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  uses('lang.apidoc.Comment');
  
  // Static classes
  define('APIDOC_CLASS_MODEL_STATIC',  'static');
  define('APIDOC_CLASS_MODEL_GENERIC', 'generic');
  
  /**
   * Class wrapping function comments
   *
   */
  class ClassComment extends Comment {
    var
      $references   = array(),
      $purpose      = '',
      $examples     = array(),
      $extends      = NULL;
      
    var
      $model= APIDOC_CLASS_MODEL_GENERIC;
      
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function setExtends($extends) {
      $this->extends= $extends;
    }
      
    /**
     * Sets 
     *
     * @access  
     * @param   
     * @return  
     */
    function setModel($model) {
      $this->model= $model;
    }

    /**
     * (Insert method's description here)
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
      $this->references[]= $see;
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
