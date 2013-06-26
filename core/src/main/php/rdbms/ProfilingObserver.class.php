<?php
/*
 * This class is part of the XP Framework
 *
 */
  uses('util.Observer', 'util.log.Logger');

  /**
   * Profiling database observer
   *
   * Attach to database by appending `&observer[rdbms.ProfilingObserver]=default` where
   * `default` denotes the log category to log to.
   * 
   */
  class ProfilingObserver extends Object implements Observer {
    const COUNT= 0x01;
    const TIMES= 0x02;

    protected $cat  = NULL;
    protected $name = NULL;
    private $timer  = NULL;
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
     * Update method
     *
     * @param   util.Observable obs
     * @param   var arg default NULL
     */
    public function update($obs, $arg= NULL) {
      if (!$arg instanceof DBEvent) return;
      if (!$obs instanceof DBConnection) return;

      // Store reference for later reuse
      if (NULL === $this->cat) $this->cat= Logger::getInstance()->getCategory($this->name);
      if (NULL === $this->dsn) $this->dsn= $obs->getDSN();

      $method= $arg->getName();
      switch ($method) {
        case 'connect':
        case 'query':
        case 'open': {
          $this->timer= new Timer();
          $this->timer->start();

          $sql= strtolower(ltrim($arg->getArgument()));
          $verb= substr($sql, 0, strpos($sql, ' '));

          // Count some well-known SQL keywords
          if (in_array($verb, array('update', 'insert', 'select', 'delete', 'set', 'show'))) {
            if (!isset($this->timing[$verb][self::COUNT])) $this->timing[$verb][self::COUNT]= 0;
            $this->timing[$verb][self::COUNT]++;
          }

          break;
        }

        case 'connected':
        case 'queryend': {
          if (!$this->timer) return;
          $this->timer->stop();

          if (!isset($this->timing[$method])) $this->timing[$method]= 0;
          $this->timing[$method]+= $this->timer->elapsedTime();

          $this->timer= NULL;
          break;
        }
      }
    }

    /**
     * Emit recorded timings to LogCategory
     * 
     */
    public function emitTimings() {
      if ($this->cat && $this->dsn) {
        $this->cat->info(__CLASS__, 'for', sprintf('%s://%s@%s/%s',
          $this->dsn->getDriver(),
          $this->dsn->getUser(),
          $this->dsn->getHost(),
          $this->dsn->getDatabase()
          ), array_merge($this->timing, $this->stats)
        );
      }
    }

    public function numberOfTimes($type) {
      if (!isset($this->timing[$type][self::COUNT])) return 0;
      return $this->timing[$type][self::COUNT];
    }

    /** 
     * Destructor; invoke emitTimings() if observer had recorded any activity.
     * 
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