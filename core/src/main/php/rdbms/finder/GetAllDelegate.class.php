<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  $package= 'rdbms.finder';
  
  uses('rdbms.finder.FinderDelegate');

  /**
   * Expects to find 1 row or more
   *
   * @see   xp://rdbms.finder.Finder#getAll
   */
  class rdbms·finder·GetAllDelegate extends FinderDelegate {

    /**
     * Select implementation
     *
     * @param   rdbms.Criteria criteria
     * @return  rdbms.DataSet[]
     * @throws  rdbms.finder.FinderException
     * @throws  rdbms.finder.NoSuchEntityException
     */
    public function select($criteria) {
      $peer= $this->finder->getPeer();
      try {
        $list= $peer->doSelect($criteria);
      } catch (SQLException $e) {
        throw new FinderException('Failed finding '.$peer->identifier, $e);
      }
      
      // Check for results. If we cannot find anything, throw a NSEE
      if (empty($list)) {
        throw new NoSuchEntityException(
          'Entity does not exist', 
          new IllegalStateException('No results for '.$criteria->toString())
        );
      }

      return $list;
    }
  }
?>
