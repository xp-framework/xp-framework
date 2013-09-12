<?php namespace net\xp_framework\unittest\rdbms;

use unittest\TestCase;
use rdbms\DBEvent;
use rdbms\ProfilingObserver;
use rdbms\sqlite3\SQLite3Connection;
use util\log\LogCategory;
use util\log\StreamAppender;


/**
 * Testcase for the profiling observer class
 *
 * @see   xp://rdbms.ProfilingObserver
 */
class ProfilingObserverTest extends TestCase {

  /**
   * Returns a database connection to test with
   *
   * @return rdbms.DBConnection
   */
  protected function conn() {
    return new SQLite3Connection(new \rdbms\DSN('sqlite+3:///foo.sqlite'));
  }

  /**
   * Returns a profiling observer instance which has already "run" a query
   *
   * @return rdbms.ProfilingObserver
   */
  private function observerWithSelect() {
    $o= new ProfilingObserver();
    $conn= $this->conn();
    $o->update($conn, new DBEvent('query', 'select * from world'));
    usleep(100000);
    $o->update($conn, new DBEvent('queryend', 5));

    return $o;
  }

  #[@test]
  public function create() {
    new ProfilingObserver('default');
  }

  #[@test]
  public function createNoArg() {
    new ProfilingObserver();
  }

  #[@test, @values(array(
  #  'select * from world',
  #  ' select * from world',
  #  '  select * from world',
  #  "\rselect * from world",
  #  "\r\nselect * from world",
  #  "\nselect * from world",
  #  "\tselect * from world",
  #  'SELECT * from world',
  #  'Select * from world'
  #))]
  public function select_type($sql) {
    $this->assertEquals('select', create(new ProfilingObserver())->typeOf($sql));
  }

  public function update_type() {
    $this->assertEquals('update', create(new ProfilingObserver())->typeOf('update world set ...'));
  }

  public function insert_type() {
    $this->assertEquals('insert', create(new ProfilingObserver())->typeOf('insert into world ...'));
  }

  public function delete_type() {
    $this->assertEquals('delete', create(new ProfilingObserver())->typeOf('delete from world ...'));
  }

  public function set_type() {
    $this->assertEquals('set', create(new ProfilingObserver())->typeOf('set showplan on'));
  }

  public function show_type() {
    $this->assertEquals('show', create(new ProfilingObserver())->typeOf('show keys from ...'));
  }

  public function unknown_type() {
    $this->assertEquals('unknown', create(new ProfilingObserver())->typeOf('explain ...'));
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
    $o->update($this->conn(), new DBEvent('hello', 'select * from world'));
  }

  #[@test]
  public function update_with_query_and_queryend_does_count_timing() {
    $o= $this->observerWithSelect();

    $this->assertEquals(1, $o->numberOfTimes('select'));
  }

  #[@test]
  public function update_with_query_and_queryend_does_time_aggregation() {
    $o= $this->observerWithSelect();

    $elapsed= $o->elapsedTimeOfAll('queryend');
    $this->assertFalse(0 == $elapsed, $elapsed.'!= 0');
    $this->assertTrue(0.099 <= $elapsed, $elapsed.' >= 0.099');
  }

  #[@test]
  public function timing_as_string() {
    $o= $this->observerWithSelect();
    $this->assertTrue(0 < strlen($o->getTimingAsString()));
  }

  #[@test]
  public function destructor_emits_timing() {
    $o= $this->observerWithSelect();
    $stream= new \MemoryOUtputStream();
    $lc= create(new LogCategory('profiling'))->withAppender(new StreamAppender($stream));
    $o->setTrace($lc);
    $o= null;

    $this->assertTrue(0 < strlen($stream->getBytes()));
  }

  #[@test]
  public function dbevent_in_illegal_order_is_ignored() {
    $o= new ProfilingObserver();
    $conn= $this->conn();

    $o->update($conn, new DBEvent('queryend', 5));
    $this->assertEquals(0.0, $o->elapsedTimeOfAll('queryend'));
  }

  #[@test]
  public function connect_is_counted_as_verb() {
    $o= new ProfilingObserver();

    $c1= $this->conn();
    $o->update($c1, new DBEvent('connect'));
    $o->update($c1, new DBEvent('connected'));

    $this->assertEquals(1, $o->numberOfTimes('connect'));
  }

  #[@test, @ignore('Expected behavior not finally decided')]
  public function observer_only_listens_to_one_dbconnection() {
    $o= new ProfilingObserver();

    $c1= $this->conn();
    $o->update($c1, new DBEvent('connect'));
    $o->update($c1, new DBEvent('connected'));

    $c2= $this->conn();
    $o->update($c2, new DBEvent('connect'));
    $o->update($c2, new DBEvent('connected'));

    $this->assertEquals(1, $o->numberOfTimes('connect'));
  }

  #[@test]
  public function unknown_sql_token_is_classified_as_unknown() {
    $o= new ProfilingObserver();

    $c1= $this->conn();
    $o->update($c1, new DBEvent('query', 'encrypt foo from bar'));;
    $o->update($c1, new DBEvent('queryend'));

    $this->assertEquals(1, $o->numberOfTimes('unknown'));
  }

  #[@test]
  public function update_sql_token_is_classified_as_unknown() {
    $o= new ProfilingObserver();

    $c1= $this->conn();
    $o->update($c1, new DBEvent('query', 'update foo from bar'));;
    $o->update($c1, new DBEvent('queryend'));

    $this->assertEquals(1, $o->numberOfTimes('update'));
  }
}
