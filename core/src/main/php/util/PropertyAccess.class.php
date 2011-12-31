<?php
/* This class is part of the XP Framework
 *
 * $Id$
 */

  /**
   * PropertyAccess interface
   */
  interface PropertyAccess {

    /**
     * Read array value
     *
     * @param   string section
     * @param   string key
     * @param   mixed default default array()
     * @return  string[]
     */
    public function readArray($section, $key, $default= array());

    /**
     * Read hash value
     *
     * @param   string section
     * @param   string key
     * @param   mixed default default NULL
     * @return  util.Hashmap
     */
    public function readHash($section, $key, $default= NULL);

    /**
     * Read bool value
     *
     * @param   string section
     * @param   string key
     * @param   bool default default FALSE
     * @return  bool
     */
    public function readBool($section, $key, $default= FALSE);

    /**
     * Read string value
     *
     * @param   string section
     * @param   string key
     * @param   mixed default default NULL
     * @return  string
     */
    public function readString($section, $key, $default= NULL);

    /**
     * Read integer value
     *
     * @param   string section
     * @param   string key
     * @param   mixed default default 0
     * @return  int
     */
    public function readInteger($section, $key, $default= 0);

    /**
     * Read float value
     *
     * @param   string section
     * @param   string key
     * @param   mixed default default array()
     * @return  double
     */
    public function readFloat($section, $key, $default= 0.0);

    /**
     * Read section
     *
     * @param   string section
     * @param   mixed default default array()
     * @return  [:string]
     */
    public function readSection($section, $default= array());

    /**
     * Read range value
     *
     * @param   string section
     * @param   string key
     * @param   mixed default default 0.0
     * @return  int[]
     */
    public function readRange($section, $key, $default= array());

    /**
     * Test whether a given section exists
     *
     * @param   string section
     * @return  bool
     */
    public function hasSection($section);

    /**
     * Retrieve first section name, set internal pointer
     *
     * @return  string
     */
    public function getFirstSection();

    /**
     * Retrieve next section name, NULL if no more sections exist
     *
     * @return  string
     */
    public function getNextSection();
  }

?>
