<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  $package= 'rdbms.finder';
  
  uses('rdbms.finder.FinderDelegate');

  /**
   * Expects to find either 0 or 1 rows.
   *
   * @see   xp://rdbms.finder.Finder#find
   */
  class rdbms·finder·FindDelegate extends FinderDelegate {

    /**
     * Select implementation
     *
     * @param   rdbms.Criteria criteria
     * @return  rdbms.DataSet
     * @throws  rdbms.finder.FinderException
     */
    public function select($criteria) {
      $peer= $this->finder->getPeer();
      try {
        $it= $peer->iteratorFor($criteria);
      } catch (SQLException $e) {
        throw new FinderException('Failed finding '.$peer->identifier, $e);
      }
      
      // Check for results. If we cannot find anything, return NULL
      if (!$it->hasNext()) return NULL;
      
      // Fetch first value, and if nothing is returned after that, return it,
      // throwing an exception otherwise
      $e= $it->next();
      if ($it->hasNext()) {
        throw new FinderException(
          'Query returned more than one result after '.$e->toString(), 
          new IllegalStateException('')
        );
      }
      
      return $e;
    }
  }
?>
