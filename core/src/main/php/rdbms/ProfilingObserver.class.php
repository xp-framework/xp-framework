<?php
/* This class is part of the XP Framework
 *
 * $Id$
 */

  uses('util.Observer', 'util.log.Logger', 'util.log.Traceable', 'util.profiling.Timer');

  /**
   * Profiling database observer
   *
   * Attach to database by appending `&observer[rdbms.ProfilingObserver]=default` where
   * `default` denotes the log category to log to.
   */
  class ProfilingObserver extends Object implements Observer, Traceable {
    const COUNT= 0x01;
    const TIMES= 0x02;

    protected $cat  = NULL;
    protected $name = NULL;

    private $timer  = NULL;
    private $lastq  = NULL;
    private $dsn    = NULL;
    private $timing = array();
    
    /**
     * Creates a new log observer with a given log category.
     *
     * @param   string cat
     */
    public function __construct($name= NULL) {
      if (NULL === $name) $name= 'default';
      $this->name= $name;
    }

    /**
     * Set log category
     * 
     * @param util.log.LogCategory $cat
     */
    public function setTrace($cat) {
      $this->cat= $cat;
    }

    /**
     * Returns the type of SQL query, one of update, insert, select,
     * delete, set, show, or unknown if the type cannot be determined.
     *
     * @param  string $sql The raw SQL
     * @return string
     */
    public function typeOf($sql) {
      $sql= strtolower(ltrim($sql));
      $verb= substr($sql, 0, strpos($sql, ' '));

      if (in_array($verb, array('update', 'insert', 'select', 'delete', 'set', 'show'))) {
        return $verb;
      }

      return 'unknown';
    }

    /**
     * Update method
     *
     * @param   util.Observable obs
     * @param   var arg default NULL
     */
    public function update($obs, $arg= NULL) {
      if (!$obs instanceof DBConnection) {
        throw new IllegalArgumentException('Argument 1 must be instanceof "rdbms.DBConnection", "'.xp::typeOf($obs).'" given.');
      }
      if (!$arg instanceof DBEvent) return;

      // Store reference for later reuse
      if (NULL === $this->cat) $this->setTrace(Logger::getInstance()->getCategory($this->name));
      if (NULL === $this->dsn) $this->dsn= $obs->getDSN()->withoutPassword();

      $method= $arg->getName();
      switch ($method) {
        case 'query':
        case 'open':
          $this->lastq= $this->typeOf($arg->getArgument());
          // Fallthrough intentional

        case 'connect': {
          if ('connect' == $method) $this->lastq= $method;
          $this->timer= new Timer();
          $this->timer->start();

          // Count some well-known SQL keywords
          $this->countFor($this->lastq);

          break;
        }

        case 'connected':
        case 'queryend': {

          // Protect against illegal order of events (should not occur)
          if (!$this->timer) return;
          $this->timer->stop();

          $this->addElapsedTimeTo($method, $this->timer->elapsedTime());
          if ($this->lastq) {
            $this->addElapsedTimeTo($this->lastq, $this->timer->elapsedTime());
            $this->lastq= NULL;
          }

          $this->timer= NULL;
          break;
        }
      }
    }

    /**
     * Emit recorded timings to LogCategory
     */
    public function emitTimings() {
      if ($this->cat && $this->dsn) {
        $this->cat->info(__CLASS__, 'for', sprintf('%s://%s@%s/%s',
          $this->dsn->getDriver(),
          $this->dsn->getUser(),
          $this->dsn->getHost(),
          $this->dsn->getDatabase()
          ), $this->getTimingAsString()
        );
      }
    }

    /**
     * Get gathered timing values as string
     *
     * @return string
     */
    public function getTimingAsString() {
      $s= '';

      foreach ($this->timing as $type => $details) {
        $s.= sprintf("%s: [%0.3fs%s], ",
          $type,
          $details[self::TIMES],
          isset($details[self::COUNT]) ? sprintf(' in %d queries', $details[self::COUNT]) : ''
        );
      }

      return substr($s, 0, -2);
    }

    /**
     * Count statements per type
     *
     * @param  string $type
     */
    protected function countFor($type) {
      if (!isset($this->timing[$type][self::COUNT])) $this->timing[$type][self::COUNT]= 0;
      $this->timing[$type][self::COUNT]++;
    }

    /**
     * Add timing values for a given type
     *
     * @param string $type
     * @param double $elapsed
     */
    protected function addElapsedTimeTo($type, $elapsed) {
      if (!isset($this->timing[$type][self::TIMES])) $this->timing[$type][self::TIMES]= 0;
      $this->timing[$type][self::TIMES]+= $elapsed;
    }

    /**
     * Returns number of statemens per type counted via `countFor()`
     *
     * @param  string $type
     * @return int
     */
    public function numberOfTimes($type) {
      if (!isset($this->timing[$type][self::COUNT])) return 0;
      return $this->timing[$type][self::COUNT];
    }

    /**
     * Returns sum of timings per type counted via `addElapsedTimeTo()`
     *
     * @param  string $type
     * @return double
     */
    public function elapsedTimeOfAll($type) {
      if (!isset($this->timing[$type][self::TIMES])) return 0.0;
      return $this->timing[$type][self::TIMES];
    }

    /** 
     * Destructor; invoke emitTimings() if observer had recorded any activity.
     */
    public function __destruct() {

      // Check if we're holding a reference to a LogCategory - then update() had been
      // called once, and we probably have something to say
      if ($this->cat) {
        $this->emitTimings();
      }
    }
  }
?>