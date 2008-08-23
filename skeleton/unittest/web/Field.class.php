<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  $package= 'unittest.web';

  /**
   * Represents a HTML field
   *
   * @see      xp://unitform.web.Form#getFields
   * @purpose  Base class
   */
  abstract class unittest·web·Field extends Object {
    protected
      $form   = NULL,
      $node   = NULL;
    
    /**
     * Constructor
     *
     * @param   unittest.web.Form form
     * @param   php.DOMNode node
     */
    public function __construct(unittest·web·Form $form, DOMNode $node) {
      $this->form= $form;
      $this->node= $node;
    }
    
    /**
     * Get this field's name
     *
     * @return  string
     */
    public function getName() {
      return $this->node->getAttribute('name');
    }

    /**
     * Get this field's value
     *
     * @return  string
     */
    public abstract function getValue();

    /**
     * Set this field's value
     *
     * @param   string value
     */
    public abstract function setValue($value);
    
    /**
     * Creates a string representation
     *
     * @return  string
     */
    public function toString() {
      return $this->getClassName().'{'.$this->form->getTest()->getDom()->saveXML($this->node).'}';
    }
  }
?>
