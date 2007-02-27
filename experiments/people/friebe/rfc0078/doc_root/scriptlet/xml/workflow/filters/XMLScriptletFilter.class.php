<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * (Insert class' description here)
   *
   * @purpose  Filter
   */
  interface XMLScriptletFilter {

    /**
     * Filters request and/or response
     *
     * @param   scriptlet.xml.XMLScriptletRequest request 
     * @param   scriptlet.xml.XMLScriptletResponse response 
     */
    public function filter($request, $response);
  }
?>
