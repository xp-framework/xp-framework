<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  namespace rdbms::query;
  uses(
    'lang.IllegalArgumentException',
    'lang.IllegalStateException',
    'rdbms.query.QueryExecutable',
    'rdbms.Peer',
    'rdbms.Criteria'
  );

  /**
   * store complete queries with criteria, method and peer
   * base class for SelectQuery, DeleteQuery and UpdateQuery
   *
   * There is no InsertQuery class implemented, I can not think up any practical UseCase
   * for it.
   * 
   * <?php
   *  
   *  $dq= new DeleteQuery(Person::getPeer());
   *  // this query is to only allow the deletion of people who's surname is Maier
   *  $dq->addRestriction(Person::column('surname')->equal('Maier'));
   *  
   *  // .....
   *
   *  // will delete Peter Maier in the person table
   *  $dq->withRestriction(Person::column('name')->equal('Peter'))->execute();
   *  
   * ?>
   *
   * @see      xp://rdbms.query.SelectQuery
   * @see      xp://rdbms.query.InsertQuery
   * @see      xp://rdbms.query.UpdateQuery
   * @purpose  rdbms.query
   */
  abstract class Query extends lang::Object implements QueryExecutable {
    protected
      $criteria=     NULL,
      $peer=         NULL;
    
    /**
     * Constructor
     *
     * @param rdbms.Peer peer optional
     */
    public function __construct(rdbms::Peer $peer= ) {
      $this->criteria= new rdbms::Criteria();
      $this->peer= $peer;
    }

    /**
     * set criteria
     *
     * @param  rdbms.Criteria criteria
     */
    public function setCriteria(rdbms::Criteria $criteria) {
      $this->criteria= $criteria;
    }
    
    /**
     * get criteria
     *
     * @return  rdbms.Criteria
     */
    public function getCriteria() {
      return $this->criteria;
    }
    
    /**
     * set peer
     *
     * @param  rdbms.Peer peer
     */
    public function setPeer(rdbms::Peer $peer) {
      $this->peer= $peer;
    }
    
    /**
     * get peer
     *
     * @return rdbms.Peer
     */
    public function getPeer() {
      return $this->peer;
    }
    
    /**
     * get connection for peer
     * proxy method for rdbms.Peer::getConnection()
     * if peer is not set Null is returned
     *
     * @return rdbms.DBConnection
     */
    public function getConnection() {
      if (NULL === $this->peer) return NULL;
      return $this->peer->getConnection();
    }
    
    /**
     * add a new restriction to the criteria
     *
     * @param  rdbms.criteria.Criterion criterion
     * @return rdbms.Query
     */
    public function addRestriction(rdbms::criterion::Criterion $criterion) {
      $this->getCriteria()->add($criterion);
      return $this;
    }
    
    /**
     * make copy with added restriction restriction
     *
     * @param  rdbms.criteria.Criterion criterion
     * @return rdbms.Query
     */
    public function withRestriction(rdbms::criterion::Criterion $criterion) {
      $q= clone($this);
      $q->getCriteria()->add($criterion);
      return $q;
    }
    
  }
?>
