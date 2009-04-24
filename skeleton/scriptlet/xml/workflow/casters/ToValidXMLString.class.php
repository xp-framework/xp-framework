<?php
/* This class is part of the XP framework
 *
 * $Id: ToValidXMLString.class.php
 */

  uses(
    'scriptlet.xml.workflow.casters.ParamCaster',
    'xml.Node'
  );

  /**
   * Removes illegal characters vrom given string(s)
   *
   *
   * @purpose  Checker
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
      $illegal_chars = str_split(XML_ILLEGAL_CHARS);

      foreach ($value as $v) {
        if (!is_string($v)) return 'string_expected';
        $retstring= '';
        foreach (str_split($v, 1) as $char) {
          if (!in_array($char, $illegal_chars)) {
            $retstring .= $char;
          }
        }
        $return[]= $retstring;
      }
      return $return;
    }
  }
?>