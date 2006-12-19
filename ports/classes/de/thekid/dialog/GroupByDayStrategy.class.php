<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('de.thekid.dialog.GroupingStrategy');

  /**
   * Groups images by Day
   *
   * @purpose  GroupingStrategy interface implementation
   */
  class GroupByDayStrategy extends Object implements GroupingStrategy {
  
    /**
     * Returns group for a given album image.
     *
     * @access  public
     * @param   &de.thekid.dialog.AlbumImage
     * @return  string unique group identifier
     */
    public function groupFor(&$image) { 
      return $image->exifData->dateTime->toString('Y-m-d');
    }
  
  } 
?>
