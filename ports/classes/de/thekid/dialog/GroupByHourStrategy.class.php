<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  /**
   * Groups images by hour
   *
   * @purpose  GroupingStrategy interface implementation
   */
  class GroupByHourStrategy extends Object {
  
    /**
     * Returns group for a given album image.
     *
     * @access  public
     * @param   &de.thekid.dialog.AlbumImage
     * @return  string unique group identifier
     */
    function groupFor(&$image) { 
      return $image->exifData->dateTime->toString('Y-m-d H');
    }
  
  } implements(__FILE__, 'de.thekid.dialog.GroupingStrategy');
?>
