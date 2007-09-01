<?php
/* This class is part of the XP framework
 *
 * $Id: OutputStreamWriter.class.php 8969 2006-12-27 15:19:08Z friebe $ 
 */

  namespace io::streams;

  /**
   * Writes data to an OutputStream
   *
   * @purpose  Interface
   */
  interface OutputStreamWriter {

    /**
     * Constructor
     *
     * @param   io.streams.OutputStream out
     */
    public function __construct($out);
  
    /**
     * Flush output buffer
     *
     */
    public function flush();

    /**
     * Print arguments
     *
     * @param   mixed* args
     */
    public function write();
    
    /**
     * Print arguments and append a newline
     *
     * @param   mixed* args
     */
    public function writeLine();
    
    /**
     * Print a formatted string
     *
     * @param   string format
     * @param   mixed* args
     * @see     php://writef
     */
    public function writef();

    /**
     * Print a formatted string and append a newline
     *
     * @param   string format
     * @param   mixed* args
     */
    public function writeLinef();
  
  }
?>
