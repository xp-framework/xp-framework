<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  uses('lang.apidoc.Comment', 'lang.apidoc.Reference');
  
  // Function access
  define('APIDOC_FUNCTION_ACCESS_PUBLIC',  'public');
  define('APIDOC_FUNCTION_ACCESS_PRIVATE', 'private');
  
  /**
   * Class wrapping function comments
   *
   * @see xp-doc:README.DOC
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
     * @access  public
     * @param   string see
     * @return  &lang.apidoc.Reference added reference
     */
    function &addReference($see) {
      $this->references[]= &new Reference($see);
      return $this->references[sizeof($this->references)- 1];
    }
    
    /**
     * Set access
     *
     * @access  public
     * @param   string access
     */
    function setAccess($access) {
      $this->access= $access;
    }
    
    /**
     * Set return value
     *
     * @access  public
     * @param   string type
     * @param   string description
     * @return  string description
     */
    function &setReturn($type, $description) {
      $this->return= &new stdClass();
      $this->return->type= $type;
      $this->return->description= $description;
      return $this->return->description;
    }
    
    /**
     * Add a throws
     *
     * @access  public
     * @param   string exception
     * @param   string condition
     * @return  &stdClass added exception
     */
    function &addThrows($exception, $condition) {
      $t= &new stdClass();
      $t->exception= $exception;
      $t->condition= $condition;
      $this->throws[]= &$t;
      return $this->throws[sizeof($this->throws)- 1];
    }
    
    /**
     * Add a default parameter
     *
     * @access  public
     * @param   string type
     * @param   string name
     * @param   string default
     * @param   string descriptions
     * @return  &stdClass added parameter
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
     * Add a parameter
     *
     * @access  public
     * @param   string type
     * @param   string name
     * @param   string descriptions
     * @return  &stdClass added parameter
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
          $args= explode(' ', $line, 2);
          $type= $description= '';
          switch (sizeof($args)) {
            case 1: $type= $args[0]; break;
            case 2: list($type, $description)= $args; break;
          }
          $descr= &$this->setReturn($type, $description);
          break;

        case 'throws':
          list($exception, $condition)= preg_split('/[, ]+/', $line, 2);
          $descr= &$this->addThrows($exception, $condition);
          break;

        case 'param':
          $args= explode(' ', $line, 3);
          $type= $name= $description= '';
          switch (sizeof($args)) {
            case 1: $type= $args[0]; break;
            case 2: list($type, $name)= $args; break;
            case 3: list($type, $name, $description)= $args; break;
          }
          if ('default' == substr($description, 0, 7)) {
            $args= explode(' ', $description, 3);
            $default= $description= '';
            switch (sizeof($args)) {
              case 2: $default= $args[1]; break;
              case 3: list(, $default, $description)= $args; break;
            }
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
