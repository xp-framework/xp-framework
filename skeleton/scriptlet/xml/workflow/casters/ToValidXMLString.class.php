<?php
/* This class is part of the XP framework
 *
 * $Id: ToValidXMLString.class.php
 */

  uses(
    'scriptlet.xml.workflow.casters.ParamCaster',
    'xml.Node',
    'xml.parser.XMLParser'
  );

  /**
   * Removes illegal characters from given string(s)
   *
   * @see       xp://net.xp_framework.unittest.scriptlet.workflow.ToValidXMLStringTest
   * @purpose  Check input for well formed XML
   */
  class ToValidXMLString extends ParamCaster {

    /**
     * Cast a given value
     *
     * @param   array value
     * @return  string error or array on success
     */
    public function castValue($value) { 
      $return= array();

      foreach ($value as $v) {
        if (!is_string($v)) return 'string_expected';
        if (strlen($v) > strcspn($v, Node::XML_ILLEGAL_CHARS)) return 'invalid_chars';
        try {
          $p= new XMLParser();
          $p->parse('<doc>'.$v.'</doc>');
        } catch (XMLFormatException $e) {
          return 'not_well_formed';
        }

        $return[]= $v;
      }
      return $return;
    }
  }
?>
