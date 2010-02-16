<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */
  uses(
    'rdbms.query.Query',
    'lang.IllegalStateException'
  );

  /**
   * Store a query to delete rows from a database
   *
   * <code>  
   *  $dq= new DeleteQuery(Person::getPeer());
   *
   *  // this query is to only allow the deletion of people who's surname is Maier
   *  $dq->addRestriction(Person::column('surname')->equal('Maier'));
   *  
   *  // .....
   *
   *  // will delete Peter Maier in the person table
   *  $dq->withRestriction(Person::column('name')->equal('Peter'))->execute();
   * </code>  
   *
   * @see      xp://rdbms.query.Query
   * @purpose  Query implementation
   */
  class DeleteQuery extends Query {

    /**
     * execute query
     *
     * @param  var[] values optional
     * @return int number of affected rows
     * @throws lang.IllegalStateException
     */
    public function execute($values= NULL) {
      if (is_null($this->peer)) throw new IllegalStateException('no peer set');
      if ($this->criteria->isJoin()) throw new IllegalStateException('can not delete from joins');
      return $this->peer->doDelete($this->criteria);
    }
  }
?>
