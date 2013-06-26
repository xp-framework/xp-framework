<?php
/*
 * This class is part of the XP Framework
 *
 */

  uses(
    'unittest.TestCase',
    'rdbms.DBEvent',
    'rdbms.ProfilingObserver'
  );

  class ProfilingObserverTest extends TestCase {
    
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

    #[@test]
    public function update() {
      $o= new ProfilingObserver();
      $o->update(1);
    }

    #[@test]
    public function update_with_event() {
      $o= new ProfilingObserver();
      $o->update(new DBEvent('hello', 'select * from world.'));
    }

    #[@test]
    public function update_with_query_and_queryend_does_count_timing() {
      $o= new ProfilingObserver();
      $o->update($this, new DBEvent('query', 'select * from world.'));
      $o->update($this, new DBEvent('queryend', 5));

      $this->assertEquals(1, $o->numberOfTimes('select'));
    }

  }
?>