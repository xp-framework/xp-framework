<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'net.xp_framework.website.planet.cmd.ImportIterator',
    'net.xp_framework.website.planet.cmd.IndexerDocument'
  );

  /**
   * Returns documents to be indexed
   *
   * @purpose  Iterator
   */
  class SqlImportIterator extends Object implements ImportIterator {
    protected
      $type     = '',
      $pk       = '',
      $_rs      = NULL,
      $_record  = NULL;

    /**
     * Constructor
     *
     * @param   string type
     * @param   string pk a unique value to be used to identify the result
     * @param   rdbms.ResultSet results
     */
    public function __construct($type, $pk, ResultSet $results) {
      $this->type= $type;
      $this->pk= $pk;
      $this->_rs= $results;
    }
    
    /**
     * Creates a string representation of this import iterator
     *
     * @return  string
     */
    public function toString() {
      return $this->getClassName().'(type= '.$this->type.', pk= '.$this->pk.')@{'.xp::stringOf($this->_rs).'}';
    }
  
    /**
     * Return type
     *
     * @return  string type
     */
    public function getType() {
      return $this->type;
    }

    /**
     * Returns true if the iteration has more elements. (In other words, 
     * returns true if next would return an element rather than throwing 
     * an exception.)
     *
     * @return  bool
     */
    public function hasNext() {

      // Check to see if we have fetched a record previously. In this case,
      // short-cuircuit this to prevent hasNext() from forwarding the result
      // pointer every time we call it.
      if ($this->_record) return TRUE;

      $this->_record= $this->_rs->next();
      return !empty($this->_record);
    }
    
    /**
     * Returns the next element in the iteration.
     *
     * @return  de.schlund.intranet.search.index.IndexerDocument
     * @throws  util.NoSuchElementException when there are no more elements
     */
    public function next() {
      if (NULL === $this->_record) {
        $this->_record= $this->_rs->next();
        // Fall through
      }
      if (FALSE === $this->_record) {
        throw new NoSuchElementException('No more elements');
      }
      
      // Create an instance and set the _record member to NULL so that
      // hasNext() will fetch the next record.
      $document= new DocumentValue((string)$this->_record[$this->pk], $this->type);
      $operation= IndexerDocument::UPDATE;
      if (isset($this->_record['@valid']) && !$this->_record['@valid']) {
        $operation= IndexerDocument::DELETE;
      } else foreach ($this->_record as $key => $value) {
        
        // Ignore primary
        if ($this->pk == $key) continue;
        
        // Ignore keys starting with an @ - these have a special meaning to
        // indicate field options
        if ('@' == $key{0}) continue;

        // Check for flags field
        if (isset($this->_record[$f= '@'.$key.'_flags'])) {
          $options= 0;
          foreach (explode('|', $this->_record[$f]) as $constant) {
            $options |= constant(trim($constant));
          }
        } else {
          $options= FIELD_STORE_COMPRESS | FIELD_INDEX_TOKENIZED;
        }

        // Check for boost field
        if (isset($this->_record[$f= '@'.$key.'_boost'])) {
          $boost= (float)$this->_record[$f];
        } else {
          $boost= 1.0;
        }
        
        // Check for normalize field
        if (isset($this->_record[$f= '@'.$key.'_normalize'])) {
          $normalized= strtr((string)strtolower($value), array(
            'ä' => 'ae',
            'ö' => 'oe',
            'ü' => 'ue',
            'ß' => 'ss',
            'ñ' => 'n',
            'è' => 'e',
            'é' => 'e',
            '-' => ' ',
            '(' => ' ',
            ')' => ' '
          ));
            
          $document->setField($key.'_normalized', $normalized, $options, $boost);
        }
        
        // Set document's field
        $document->setField($key, (string)$value, $options, $boost);
      }
      $this->_record= NULL;
      return new IndexerDocument($document, $operation);
    }
  }
?>
