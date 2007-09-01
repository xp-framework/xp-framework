<?php
/* This class is part of the XP framework
 *
 * $Id: UpdateQuery.class.php 10778 2007-07-11 15:45:40Z ruben $ 
 */

  namespace rdbms::query;
  uses('rdbms.query.Query');

  /**
   * store complete queries with criteria, method and peer
   *
   * @see      xp://rdbms.query.Query
   * @purpose  rdbms.query
   */
  class UpdateQuery extends Query {

    /**
     * execute query without set operation
     *
     * @param  mixed[] values
     * @return int number of affected rows
     * @throws lang.IllegalStateException
     */
    public function execute($values= ) {
      if (is_null($this->peer))      throw new lang::IllegalStateException('no peer set');
      if ($this->criteria->isJoin()) throw new lang::IllegalStateException('can not update into joins');
      return $this->peer->doUpdate($values, $this->criteria);
    }
    
  }
?>
