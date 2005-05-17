<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'SeeTaglet', 
    'ParamTaglet', 
    'ReturnTaglet', 
    'ThrowsTaglet',
    'SimpleTaglet',
    'ModelTaglet'
  );

  /**
   * Manages the taglets used by doclets. 
   *
   * @purpose  purpose
   */
  class TagletManager extends Object {
    var
      $taglets= array();
      
    /**
     * Static initializer. Registers builtin taglets
     *
     * @model   static
     * @access  public
     */
    function __static() {
      with ($self= &TagletManager::getInstance()); {
        $self->taglets['see']= &new SeeTaglet();
        $self->taglets['param']= &new ParamTaglet();
        $self->taglets['return']= &new ReturnTaglet();
        $self->taglets['throws']= &new ThrowsTaglet();
        $self->taglets['model']= &new ModelTaglet;
        
        // Simple taglets
        $s= &new SimpleTaglet();
        $self->taglets['purpose']= &$s;
        $self->taglets['access']= &$s;
        $self->taglets['deprecated']= &$s;
        $self->taglets['experimental']= &$s;
        $self->taglets['platform']= &$s;
      }
    }

    /**
     * Return the TagletManager's instance
     * 
     * @model   static
     * @access  public
     * @return  &TagletManager
     */
    function &getInstance() {
      static $instance= NULL;
      
      if (!$instance) $instance= new TagletManager();
      return $instance;
    }
    
    /**
     * Add a new tag
     *
     * @access  public
     * @param   string kind
     * @param   &Taglet taglet
     */
    function addCustomTag($kind, &$taglet) {
      $this->taglets[$kind]= &$taglet;
    }

    /**
     * Factory method
     *
     * @access  public
     * @param   &Doc holder
     * @param   string kind
     * @param   string text
     * @return  &Tag
     */
    function &make(&$holder, $kind, $text) {
      if (!isset($this->taglets[$kind])) {
        return throw(new IllegalArgumentException('Unknown taglet kind "'.$kind.'"'));
      }
      
      return $this->taglets[$kind]->tagFrom($holder, $kind, $text);
    }
  }
?>
