<?php
/*
 * This class is part of the XP Framework
 *
 */

  uses(
    'unittest.TestCase',
    'rdbms.DBEvent',
    'rdbms.ProfilingObserver',
    'rdbms.sqlite3.SQLite3Connection'
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

    #[@test]
    public function update_with_query_and_queryend_does_count_timing() {
      $o= new ProfilingObserver();
      $conn= $this->conn();
      $o->update($conn, new DBEvent('query', 'select * from world.'));
      $o->update($conn, new DBEvent('queryend', 5));

      $this->assertEquals(1, $o->numberOfTimes('select'));
    }

    #[@test]
    public function update_with_query_and_queryend_does_time_aggregation() {
      $o= new ProfilingObserver();
      $conn= $this->conn();
      $o->update($conn, new DBEvent('query', 'select * from world.'));
      usleep(100000);
      $o->update($conn, new DBEvent('queryend', 5));

      $this->assertFalse(0 == $o->elapsedTimeOfAll('queryend'));
      $this->assertTrue(0.1 <= $o->elapsedTimeOfAll('queryend'));
      var_dump($o);
    }
  }
?>