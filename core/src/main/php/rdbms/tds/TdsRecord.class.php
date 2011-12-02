<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Abstract base class
   *
   */
  abstract class TdsRecord extends Object {
    
    /**
     * Unmarshal from a given stream
     *
     * @param   rdbms.tds.TdsDataStream stream
     * @param   [:var] field
     * @return  var
     */
    public abstract function unmarshal($stream, $field);
  }
?>
