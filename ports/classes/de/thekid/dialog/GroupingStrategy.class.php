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
  interface GroupingStrategy {
  
    /**
     * Returns group for a given album image.
     *
     * @param   &de.thekid.dialog.AlbumImage
     * @return  string unique group identifier
     */
    public function groupFor($image);
  
  }
?>
