<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'ant.AntPatternSet'
    'io.collections.IOCollection',
    'io.collections.IOElement'
  );

  /**
   * TestCase
   *
   * @see      reference
   * @purpose  purpose
   */
  class AntPatternTest extends TestCase {
  
    /**
     * Sets up test case
     *
     */
    protected function patternFor($p) {
      $pattern= new AntPattern($p);
      return $pattern->toFilter('');
    }
    
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    protected function mockCollection($elements) {
      $c= newinstance('io.collections.IOCollection', array(), '{
        protected $list= array();
        public function setList($l) { $this->list= $l; }
        public function open() { return TRUE; }
        public function rewind() { reset($this->list); }
        public function next() { 
          $e= current($this->list); 
          next($this->list); 
          return newinstance("io.collections.IOElement", array($e), "'.'{
            public $name  = "";
            public function __construct($name) {
              $this->name= $name;
            }
            public function getURI() { return $this->name; }
            public function getSize() { return 0; }
            public function createdAt() { return Date::now(); }
            public function lastAccessed() { return Date::now(); }
            public function lastModified() { return Date::now(); }
          }");
        }
        public function close() { return TRUE; }
      }');
      
      $c->setList($elements);
      return $c;
    }
    
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    protected function asArray($iterator) {
      // TBI
    }    
    
    /**
     * Test
     *
     */
    #[@test]
    public function testStar() {
      $list= $this->asArray(new FilteredIOCollectionIterator(
        $this->mockCollection(),
        
    }
  }
?>
