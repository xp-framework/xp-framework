<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */
  uses('rdbms.join.JoinTable');

  /**
   * Represents a relation between two tables
   * Helper (bean) class for JoinPart and JoinProcessor
   *
   * @see     xp://rdbms.join.JoinPart
   * @see     xp://rdbms.join.JoinPRocessor
   * @purpose rdbms.join
   *
   */
  class JoinRelation extends Object {
    private
      $source= NULL,
      $target= NULL,
      $conditions= array();

    /**
     * Constructor
     *
     * @param   string name
     * @param   string alias
     * @param   string[] optional conditions
     */
    public function __construct(JoinTable $source, JoinTable $target, $conditions= array()) {
      $this->source= $source;
      $this->target= $target;
      $this->conditions= $conditions;
    }

   /**
     * Set source
     *
     * @param   rdbms.join.JoinTable source
     */
    public function setSource(JoinTable $source) {
      $this->source= $source;
    }

    /**
     * Get source
     *
     * @return  rdbms.join.JoinTable
     */
    public function getSource() {
      return $this->source;
    }

    /**
     * Set target
     *
     * @param   rdbms.join.JoinTable target
     */
    public function setTarget(JoinTable $target) {
      $this->target= $target;
    }

    /**
     * Get target
     *
     * @return  rdbms.join.JoinTable
     */
    public function getTarget() {
      return $this->target;
    }

    /**
     * Set conditions
     *
     * @param   string[] conditions
     */
    public function setConditions($conditions) {
      $this->conditions= $conditions;
    }

    /**
     * Get conditions
     *
     * @return  string[]
     */
    public function getConditions() {
      return $this->conditions;
    }
  }
?>
