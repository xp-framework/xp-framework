<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'classes.db.Job',
    'rdbms.finder.Finder'
  );

  /**
   * (Insert class' description here)
   *
   * @purpose  Finder
   */
  class JobFinder extends Finder {
  
    public function getPeer() {
      return Job::getPeer();
    }
    
    #[@finder(kind= COLLECTION)]
    public function newest() {
      return Criteria::newInstance()
        ->add('bz_id', 10000, EQUAL)
        ->addOrderBy('valid_from', DESCENDING)
      ;
    }

    #[@finder(kind= COLLECTION)]
    public function category($category) {
      return Criteria::newInstance()
        ->add('category', $category, EQUAL)
        ->addOrderBy('valid_from', DESCENDING)
      ;
    }


    #[@finder(kind= COLLECTION)]
    public function developer() {
      return $this->category('Developer');
    }
  }
?>
