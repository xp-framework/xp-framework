<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  uses('text.apidoc.Comment', 'text.apidoc.Reference');
  
  // Function access
  define('APIDOC_FUNCTION_ACCESS_PUBLIC',  'public');
  define('APIDOC_FUNCTION_ACCESS_PRIVATE', 'private');
  define('APIDOC_FUNCTION_MODEL_GENERIC',  '');
  
  /**
   * Class wrapping function comments
   *
   * @deprecated
   * @see xp-doc:README.DOC
   */
  class FunctionComment extends Comment {
    public
      $references   = array(),
      $return       = NULL,
      $access       = APIDOC_FUNCTION_ACCESS_PUBLIC,
      $model        = APIDOC_FUNCTION_MODEL_GENERIC,
      $throws       = array(),
      $params       = array(),
      $annotations  = array();
      
    /**
     * Adds a reference
     *
     * @access  public
     * @param   string see
     * @return  &text.apidoc.Reference added reference
     */
    public function &addReference($see) {
      $this->references[]= &new Reference($see);
      return $this->references[sizeof($this->references)- 1];
    }
    
    /**
     * Set access
     *
     * @access  public
     * @param   string access
     */
    public function setAccess($access) {
      $this->access= $access;
    }
    
    /**
     * Set return value
     *
     * @access  public
     * @param   string type
     * @param   string description
     * @return  &string description
     */
    public function &setReturn($type, $description) {
      $this->return= &new stdClass();
      $this->return->type= $type;
      $this->return->description= $description;
      return $this->return->description;
    }
    
    /**
     * Set Model
     *
     * @access  public
     * @param   mixed model
     */
    public function setModel($model) {
      $this->model= $model;
    }

    /**
     * Get Model
     *
     * @access  public
     * @return  mixed
     */
    public function getModel() {
      return $this->model;
    }

    /**
     * Add a throws
     *
     * @access  public
     * @param   string exception
     * @param   string condition
     * @return  &stdClass added exception
     */
    public function &addThrows($exception, $condition) {
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
    public function &addDefaultParam($type, $name, $default, $description) {
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
    public function &addParam($type, $name, $description) {
      $p= &new stdClass();
      $p->type= $type;
      $p->name= $name;
      $p->description= $description;
      $this->params[]= &$p;
      return $this->params[sizeof($this->params)- 1];
    }
    
    /**
     * Sets the annotation string.
     *
     * @access  public
     * @param   string string
     */
    public function setAnnotations($string) {
      $this->annotations= eval('return array('.preg_replace(
        array('/@([a-z_]+),/i', '/@([a-z_]+)\(\'([^\']+)\'\)/i', '/@([a-z_]+)\(/i', '/([a-z_]+) *= */i'),
        array('\'$1\' => NULL,', '\'$1\' => \'$2\'', '\'$1\' => array(', '\'$1\' => '),
        trim($string, "[]# \t\n\r").','
      ).');');
    }    
    
    /**
     * Retrieve function annotations.
     *
     * @access  public
     * @return  &mixed[] annotations
     */
    public function &getAnnotations() {
      return $this->annotations;
    }

    /**
     * Handles tags
     *
     * @see text.apidoc.Comment
     */
    public function &_handleTag($tag, $line) {
      $descr= &parent::_handleTag($tag, $line);
      
      switch ($tag) {
        case 'see':
          $descr= &$this->addReference($line);
          break;

        case 'access':
          $this->setAccess($line);
          $descr= NULL;
          break;
        
        case 'model':
          $this->setModel($line);
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
          $args= preg_split('/[, ]+/', $line, 2);
          $exception= $condition= '';
          switch (sizeof($args)) {
            case 1: $exception= $args[0]; break;
            case 2: list($exception, $condition)= $args; break;
          }
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
        
        case 'annotation':
          $this->setAnnotations($line);
          break;
      }
      
      return $descr;
    }
  }
?>
