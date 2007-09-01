<?php
/* This class is part of the XP framework
 *
 * $Id: Seekable.class.php 10355 2007-05-13 20:53:00Z friebe $ 
 */

  namespace io::streams;

  /**
   * Defines a stream as being seekable
   *
   * @see      php://fseek
   * @purpose  Interface
   */
  interface Seekable {
  
    /**
     * Seek to a given offset
     *
     * @param   int offset
     * @param   int whence default SEEK_SET (one of SEEK_[SET|CUR|END])
     * @throws  io.IOException in case of error
     */
    public function seek($offset, $whence= SEEK_SET);
  
  }
?>
