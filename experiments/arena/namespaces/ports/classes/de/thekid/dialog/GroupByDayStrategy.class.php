<?php
/* This class is part of the XP framework
 *
 * $Id: GroupByDayStrategy.class.php 8971 2006-12-27 15:27:10Z friebe $
 */

  namespace de::thekid::dialog;

  ::uses('de.thekid.dialog.GroupingStrategy');

  /**
   * Groups images by Day
   *
   * @purpose  GroupingStrategy interface implementation
   */
  class GroupByDayStrategy extends lang::Object implements GroupingStrategy {
  
    /**
     * Returns group for a given album image.
     *
     * @param   &de.thekid.dialog.AlbumImage
     * @return  string unique group identifier
     */
    public function groupFor($image) { 
      return $image->exifData->dateTime->toString('Y-m-d');
    }
  
  } 
?>
