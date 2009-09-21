<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('xml.Node');

  /**
   * Represents an xsl:template node
   *
   * @see   xp://xml.Stylesheet#addTemplate
   * @see   xp://xml.Stylesheet#withTemplate
   */
  class XslTemplate extends Node {
  
    /**
     * Constructor
     *
     * @param   string name default 'xsl:template'
     * @param   string content default NULL
     * @param   array<string, string> attribute default array() attributes
     */
    public function __construct($name= 'xsl:template', $content= NULL, $attributes= array()) {
      parent::__construct($name, $content, $attributes); 
    }
    
    /**
     * Set expression to match - xsl:template match="[expression]"
     *
     * @param   string expression
     * @return  xml.XslTemplate this
     */
    public function matching($expression) {
      $this->attribute['match']= $expression;
      return $this;
    }

    /**
     * Set name for template - xsl:template name="[name]"
     *
     * @param   string expression
     * @return  xml.XslTemplate this
     */
    public function named($name) {
      $this->attribute['name']= $name;
      return $this;
    }
  }
?>
