<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  /**
   * Reads data from an InputStream
   *
   * @purpose  Interface
   */
  interface InputStreamReader {

    /**
     * Constructor
     *
     * @param   io.streams.InputStream in
     */
    public function __construct($in);
  
    /**
     * Read a number of bytes
     *
     * @param   int size default 8192
     * @return  string
     */
    public function read($size= 8192);

    /**
     * Read an entire line
     *
     * @return  string
     */
    public function readLine();
  }
?>
