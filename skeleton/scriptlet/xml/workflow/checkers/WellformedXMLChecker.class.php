<?php
/* This class is part of the XP framework
 *
 * $Id: ToValidXMLString.class.php
 */

  uses(
    'scriptlet.xml.workflow.checkers.ParamChecker',
    'xml.Node',
    'xml.parser.XMLParser'
  );

  /**
   * Removes illegal characters from given string(s)
   *
   * @see       xp://net.xp_framework.unittest.scriptlet.workflow.ToValidXMLStringTest
   * @purpose  Check input for well formed XML
   */
  class WellformedXMLChecker extends ParamChecker {

    /**
     * Cast a given value
     *
     * Error codes returned are:
     * <ul>
     *   <li>invalid_chars - if input contains characters not allowed for XML</li>
     *   <li>not_well_formed - if input cannot be parsed to XML</li>
     * </ul>
     *
     * @param   array value
     * @return  string error or array on success
     */
    public function check($value) { 
      $return= array();

      foreach ($value as $v) {
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
