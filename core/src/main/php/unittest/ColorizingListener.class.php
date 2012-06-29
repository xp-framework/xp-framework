<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  /**
   * Provide color enabling functionality to a testlistener
   *
   */
  interface ColorizingListener {

    /**
     * Enable color mode
     *
     * @param   bool enable or NULL to autodetect capabilities
     */
    public function setColor($enable);

    /**
     * Set color mode
     *
     * @param   bool enable or NULL to autodetect capabilities
     * @return  self
     */
    public function withColor($enable);
  }