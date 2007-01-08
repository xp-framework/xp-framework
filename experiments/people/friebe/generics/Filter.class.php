<?php
/* This class is part of the XP framework's experiments
 *
 * $Id$
 */

  /**
   * Generic filter
   *
   * @purpose  Generics demonstration
   */
  interface Filter<T> {
  
    /**
     * Returns TRUE if a given subject should be accepted, FALSE otherwise.
     *
     * @param   <T> subject
     * @return  bool
     */
    public function accept(T $subject);
  }
?>
