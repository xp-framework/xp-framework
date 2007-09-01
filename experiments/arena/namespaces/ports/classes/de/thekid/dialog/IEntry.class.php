<?php
/* This class is part of the XP framework
 *
 * $Id: IEntry.class.php 8971 2006-12-27 15:27:10Z friebe $ 
 */

  namespace de::thekid::dialog;

  /**
   * Interface for album entries
   *
   * @purpose  Interface
   */
  interface IEntry {

    /**
     * Get date
     *
     * @return  &util.Date
     */
    public function getDate();
  }
?>
