<?php
/*
 * This class is part of the XP Framework
 *
 */

  uses(
    'unittest.TestCase',
    'rdbms.DBEvent',
    'rdbms.ProfilingObserver',
    'rdbms.sqlite3.SQLite3Connection',
    'util.log.LogCategory',
    'util.log.StreamAppender'
  );

  class ProfilingObserverTest extends TestCase {
    
    protected function conn() {
      return new SQLite3Connection(new DSN('sqlite+3:///foo.sqlite'));
    }

    #[@test]
    public function create() {
      new ProfilingObserver('default');
    }

    #[@test]
    public function createNoArg() {
      new ProfilingObserver();
    }

    #[@test]
    public function emitTiming_without_actually_having_any_timing_does_not_fatal() {
      create(new ProfilingObserver())->emitTimings();
    }

    #[@test, @expect('lang.IllegalArgumentException')]
    public function update_with_anyarg() {
      $o= new ProfilingObserver();
      $o->update(1);
    }

    #[@test]
    public function update_with_event() {
      $o= new ProfilingObserver();
      $o->update($this->conn(), new DBEvent('hello', 'select * from world.'));
    }

    private function observerWithSelect() {
      $o= new ProfilingObserver();
      $conn= $this->conn();
      $o->update($conn, new DBEvent('query', 'select * from world.'));
      usleep(100000);
      $o->update($conn, new DBEvent('queryend', 5));
      
      return $o;      
    }

    #[@test]
    public function update_with_query_and_queryend_does_count_timing() {
      $o= $this->observerWithSelect();

      $this->assertEquals(1, $o->numberOfTimes('select'));
    }

    #[@test]
    public function update_with_query_and_queryend_does_time_aggregation() {
      $o= $this->observerWithSelect();

      $this->assertFalse(0 == $o->elapsedTimeOfAll('queryend'));
      $this->assertTrue(0.1 <= $o->elapsedTimeOfAll('queryend'));
    }

    #[@test]
    public function timing_as_string() {
      $o= $this->observerWithSelect();
      $this->assertTrue(0 < strlen($o->getTimingAsString()));
    }

    #[@test]
    public function destructor_emits_timing() {
      $o= $this->observerWithSelect();
      $stream= new MemoryOUtputStream();
      $lc= create(new LogCategory('profiling'))->withAppender(new StreamAppender($stream));
      $o->setTrace($lc);
      $o= NULL;

      $this->assertTrue(0 < strlen($stream->getBytes()));
    }


  }
?>