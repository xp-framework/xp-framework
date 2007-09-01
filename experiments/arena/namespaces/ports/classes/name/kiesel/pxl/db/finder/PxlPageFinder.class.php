<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  namespace name::kiesel::pxl::db::finder;

  ::uses('rdbms.finder.Finder');

  /**
   * (Insert class' description here)
   *
   * @ext      extension
   * @see      reference
   * @purpose  purpose
   */
  class PxlPageFinder extends rdbms::finder::Finder {
  
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    public function getPeer() {
      return ::getPeer();
    }  
  
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    public function mostRecent() {
      return ::newInstance()
        ->add('published', util::Date::now(), LESS_THAN)
        ->addOrderBy('sequence', DESCENDING)
      ;
    }
  }
?>
