<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  uses('lang.apidoc.Comment');
  
  // Function access
  define('APIDOC_FUNCTION_ACCESS_PUBLIC',  'public');
  define('APIDOC_FUNCTION_ACCESS_PRIVATE', 'private');
  
  /**
   * Class wrapping function comments
   *
   */
  class FunctionComment extends Comment {
    var
      $references   = array(),
      $return       = NULL,
      $access       = APIDOC_FUNCTION_ACCESS_PUBLIC,
      $throws       = array(),
      $params       = array();
      
    /**
     * Adds a reference
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
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function setAccess($access) {
      $this->access= $access;
    }
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function &setReturn($type, $description) {
      $this->return= &new stdClass();
      $this->return->type= $type;
      $this->return->description= $description;
      return $this->return->description;
    }
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function &addThrows($exception, $condition) {
      $t= &new stdClass();
      $t->exception= $exception;
      $t->condition= $condition;
      $this->throws[]= &$t;
      return $this->throws[sizeof($this->throws)- 1];
    }
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function &addDefaultParam($type, $name, $default, $description) {
      $p= &new stdClass();
      $p->type= $type;
      $p->name= $name;
      $p->default= $default;
      $p->description= $description;
      $this->params[]= &$p;
      return $this->params[sizeof($this->params)- 1];
    }

    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function addParam($type, $name, $description) {
      $p= &new stdClass();
      $p->type= $type;
      $p->name= $name;
      $p->description= $description;
      $this->params[]= &$p;
      return $this->params[sizeof($this->params)- 1];
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

        case 'access':
          $this->setAccess($line);
          $descr= NULL;
          break;

        case 'return':
          list($type, $description)= explode(' ', $line, 2);
          $descr= &$this->setReturn($type, $description);
          break;

        case 'throws':
          list($exception, $condition)= preg_split('/[, ]+/', $line, 2);
          $descr= &$this->addThrows($exception, $condition);
          break;

        case 'param':
          list($type, $name, $description)= explode(' ', $line, 3);
          if ('default' == substr($description, 0, 7)) {
            list(, $default, $description)= explode(' ', $description, 3);
            $descr= &$this->addDefaultParam($type, $name, $default, $description);
            break;
          }
          $descr= &$this->addParam($type, $name, $description);
          break;
      }
      
      return $descr;
    }
  }
?>
