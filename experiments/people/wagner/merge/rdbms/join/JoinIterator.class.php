<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */
uses('util.XPIterator');

  /**
   * (Insert class' description here)
   *
   */
  class JoinIterator  extends Object implements XPIterator {
    private
      $resultObj= NULL,
      $record= array(),
      $obj= NULL,
      $obj_key= '',
      $jp= NULL,
      $rs= NULL;

    public function __construct(JoinProcessor $jp, ResultSet $rs) {
      $this->jp= $jp;
      $this->rs= $rs;
      $this->record= $this->rs->next();
    }

    /**
     * Returns true if the iteration has more elements. (In other words, 
     * returns true if next would return an element rather than throwing 
     * an exception.)
     *
     * @return  bool
     */
    public function hasNext() {
      return (FALSE !== $this->record);
    }
    
    /**
     * Returns the next element in the iteration.
     *
     * @return  mixed
     * @throws  util.NoSuchElementException when there are no more elements
     */
    public function next() {
      if (!$this->record) throw new NoSuchElementException('No more elements');
      do {
        $this->jp->joinpart->extract($this, 'setObj', 'hasObj', 'getObj', $this->record);
        if (!is_null($this->resultObj)) {
          $r= $this->resultObj;
          $this->resultObj= NULL;
          return $r;
        }
      } while ($this->record= $this->rs->next());
      return $this->obj;
    }

    /**
     * set "in construct" result object
     *
     * @param   string uniqie key
     * @param   lang.Object
     */
    public function setObj($key, $obj) {
      $this->resultObj= $this->obj;
      $this->obj= $obj;
      $this->obj_key= $key;
    }

    /**
     * get an object from the found objects
     *
     * @param   string uniqie key
     * @throws  util.NoSuchElementException
     */
    public function getObj($key) {
      if ($this->obj_key && $this->obj_key != $key) throw new NoSuchElementException('object under construct does not exist - maybe you should sort your query');
      return $this->obj;
    }

    /**
     * test an object for existance in the found objects
     *
     * @param   string uniqie key
     */
    public function hasObj($key) {
      return ($this->obj_key == $key);
    }
  }
?>
