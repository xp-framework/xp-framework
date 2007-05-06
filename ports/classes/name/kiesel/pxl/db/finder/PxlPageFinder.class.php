<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('rdbms.finder.Finder');

  /**
   * (Insert class' description here)
   *
   * @ext      extension
   * @see      reference
   * @purpose  purpose
   */
  class PxlPageFinder extends Finder {
  
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    public function getPeer() {
      return PxlPage::getPeer();
    }  
  
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    public function mostRecent() {
      return Criteria::newInstance()
        ->add('published', Date::now(), LESS_THAN)
        ->addOrderBy('sequence', DESCENDING)
      ;
    }
  }
?>
