<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  /**
   * Interface for album grouping strategies
   *
   * @purpose  Interface
   */
  class GroupingStrategy extends Interface {
  
    /**
     * Returns group for a given album image.
     *
     * @access  public
     * @param   &de.thekid.dialog.AlbumImage
     * @return  string unique group identifier
     */
    function groupFor(&$image) { }
  
  }
?>
