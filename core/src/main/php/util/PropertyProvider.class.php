<?php
/* This class is part of the XP Framework
 *
 * $Id$
 */

  /**
   * PropertyProvider interface
   */
  interface PropertyProvider {
    // public function readArray($section, $key, $default= array());
    public function readBool($section, $key, $default= FALSE);
    public function readString($section, $key, $default= NULL);
  }

?>
