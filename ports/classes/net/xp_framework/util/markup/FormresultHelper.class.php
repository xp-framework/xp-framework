<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('net.xp_framework.util.markup.MarkupBuilder', 'xml.Node');

  /**
   * Helper class that provides an easy way to retrieve an xml.Node
   * object to be inserted into the formresult from an entry's body.
   *
   * @purpose  Helper class
   */
  class FormresultHelper extends Object {

    /**
     * Helper method that returns an xml.Node object for a specified
     * text.
     *
     * @model   static
     * @access  protected
     * @param   string name
     * @param   string string
     * @return  &xml.Node
     */
    public static function &markupNodeFor($name, $string) {
      static $builder= NULL;
      
      if (!$builder) $builder= new MarkupBuilder();
      return new Node($name, new PCData($builder->markupFor($string)));
    }
  }
?>
