<?php
/* This class is part of the XP framework
 *
 * $Id: FormresultHelper.class.php,v 1.1 2004/04/28 14:40:25 friebe Exp $ 
 */

  uses(
    'de.uska.markup.MarkupBuilder',
    'xml.parser.XMLParser'
  );

  /**
   * Helper class that provides an easy way to retrieve an xml.Node
   * object to be inserted into the formresult from an entry's body.
   *
   * @purpose  Helper class
   */
  class FormresultHelper extends Object {

    /**
     * Helper method that returns an xml.Node object for a specified
     * text. If the given text is well-formed (according to the rules
     * for valid XML), the node contains the XML in its contents,
     * otherwise all HTML is stripped (using strip_tags) and then 
     * escaped.
     *
     * @model   static
     * @access  protected
     * @param   string name
     * @param   string string
     * @return  &xml.Node
     */
    function markupNodeFor($name, $string) {
      static $parser= NULL, $builder= NULL;
      
      if (!$parser) $parser= new XMLParser();
      if (!$builder) $builder= new MarkupBuilder();

      // Convert string to XML - TBD: Use more fault-tolerant method
      $markup= $builder->markupFor($string);
      try(); {
        $parser->parse(
          '<?xml version="1.0" encoding="iso-8859-1"?>'.
          '<body>'.$markup.'</body>'
        );
      } if (catch('XMLFormatException', $e)) {
        return new Node($name, strip_tags($string));
      }

      // We've successfully parse the XML, it's valid and we may add
      // it to the formresult "AS IS"
      return new Node($name, new PCData($markup));
    }
  }
?>
