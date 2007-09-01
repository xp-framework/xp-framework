<?php
/* This class is part of the XP framework
 *
 * $Id: GroupingStrategy.class.php 8971 2006-12-27 15:27:10Z friebe $
 */

  namespace de::thekid::dialog;

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
