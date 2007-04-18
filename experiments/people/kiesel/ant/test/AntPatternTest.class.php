<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'ant.AntPatternSet',
    'ant.AntPattern',
    'ant.AntEnvironment',
    'io.collections.iterate.FilteredIOCollectionIterator',
    'io.collections.IOCollection',
    'io.collections.IOElement',
    'io.streams.MemoryOutputStream'
  );

  /**
   * TestCase
   *
   * @see      reference
   * @purpose  purpose
   */
  class AntPatternTest extends TestCase {
  
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    protected function mockCollection($base, $elements) {
      $c= newinstance('io.collections.IOCollection', array($base), '{
        public $base  = "";
        protected $list= array();
        
        public function __constructur($name) { $this->base= $name; }
        public function setList($l) { $this->list= $l; }
        public function open() { return TRUE; }
        public function rewind() { reset($this->list); }
        public function next() { 
          $e= current($this->list); 
          next($this->list); 
          if (!$e) return NULL;
          return newinstance("io.collections.IOElement", array($e), \'{
            public $name  = "";
            public function __construct($name) {
              $this->name= $name;
            }
            public function getURI() { return $this->base; }
            public function getSize() { return 0; }
            public function createdAt() { return Date::now(); }
            public function lastAccessed() { return Date::now(); }
            public function lastModified() { return Date::now(); }
          }\');
        }
        public function close() { return TRUE; }
        public function getURI() { return $this->name; }
        public function getSize() { return 0; }
        public function createdAt() { return Date::now(); }
        public function lastAccessed() { return Date::now(); }
        public function lastModified() { return Date::now(); }
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
      while ($iterator->hasNext()) {
        // var_dump($iterator->next());
      }
    }
    
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    protected function newPatternSet($inc, $exc) {
      $p= new AntPatternSet();
      foreach ($inc as $i) $p->addIncludePattern(new AntPattern($i));
      foreach ($exc as $e) $p->addIncludePattern(new AntPattern($e));
      return $p;
    }    
    
    /**
     * Test
     *
     */
    #[@test, @ignore]
    public function testStar() {
      $env= new AntEnvironment(new StringWriter(new MemoryOutputStream()), new StringWriter(new MemoryOutputStream()));
      $list= $this->asArray(new FilteredIOCollectionIterator(
        $this->mockCollection('', array('entry/foo')),
        $this->newPatternSet(
          array('**/*'),
          array('**/CVS/*')
        )->createFilter($env, ''),
        TRUE
      ));
      var_dump($list);
    }
    
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    protected function asFilter($f) {
      $p= new AntPattern($f);
      $p->setDirectorySeparator('/');
      return $p->nameToRegex();
    }
    
    /**
     * Test
     *
     */
    #[@test]
    public function toFilter() {
      $this->assertEquals('#^.*/[^/]*~$#', $this->asFilter('**/*~'));
      $this->assertEquals('#^.*/\\#[^/]*\\#$#', $this->asFilter('**/#*#'));
      $this->assertEquals('#^.*/CVS$#', $this->asFilter('**/CVS'));
      $this->assertEquals('#^.*/CVS/.*$#', $this->asFilter('**/CVS/**'));
      $this->assertEquals('#^.*/\\.svn$#', $this->asFilter('**/.svn'));
      $this->assertEquals('#^.*/\\.svn/.*$#', $this->asFilter('**/.svn/**'));
    }

    /**
     * Test
     *
     */
    #[@test]
    public function dirSlashTest() {
      $this->assertEquals('#^\\.svn/.*$#', $this->asFilter('.svn/'));
    }

  }
?>
