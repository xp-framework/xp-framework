<?php
/* This class is part of the XP Framework
 *
 * $Id$
 */

  /**
   * PropertyProvider interface
   */
  interface PropertyAccess {
    public function readArray($section, $key, $default= array());
    public function readHash($section, $key, $default= NULL);
    public function readBool($section, $key, $default= FALSE);
    public function readString($section, $key, $default= NULL);
    public function readInteger($section, $key, $default= 0);
    public function readFloat($section, $key, $default= 0.0);
    public function readSection($section, $default= array());
    public function readRange($section, $key, $default= array());

    public function hasSection($section);
    public function getFirstSection();
    public function getNextSection();
  }

?>
