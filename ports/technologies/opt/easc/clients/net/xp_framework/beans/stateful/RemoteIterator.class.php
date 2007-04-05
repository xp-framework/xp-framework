<?php
/* This file is part of the XP framework
 *
 * $Id$
 */

  /**
   * Remote interface for xp/demo/RemoteIterator
   *
   * @purpose  EASC Client stub
   */
  interface RemoteIterator {

    /**
     * hasNext()
     *
     * @return  bool
     */
    public function hasNext();
    
    /**
     * Next
     *
     * @return  &lang.Object
     */
    public function next();
  }
?>
