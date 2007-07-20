<?php
/* This file is part of the XP framework
 *
 * $Id$
 */
  uses(
    'util.log.ColoredConsoleAppender',
    'util.Date',
    'rdbms.criterion.Restrictions',
    'util.cmd.Command'
  );

  /**
   * test SQL function implementation
   *
   * @ext      mysql
   * @see      xp://rdbms.SQLFunctions
   * @purpose  test
   */
  abstract class SQLTest extends Command {
    protected
      $logger=     NULL,
      $conn=       NULL,
      $showSource= false;

    /**
     * define criteria
     *
     * @return array<rdbms.Criteria>
     */
    abstract protected function getCrits();

    /**
     * define peer
     *
     * @return rdbms.Peer
     */
    abstract protected function getPeer();

    /**
     * Main runner method
     *
     */
    public function run() {
      foreach ($this->getCrits() as $name => $crit) {
        $this->out->writeline('');
        if ($this->showSource) $this->logger->debug($crit.';');
        eval('$this->out->writeline("'.
          $name.': ",
          xp::stringOf($this->getPeer()->iteratorFor('.$crit.')->next())
        );');
      }
    }

    /**
     * set logger
     *
     * @param util.log.LogCategory logger
     */
    #[@inject(type='util.log.LogCategory', name= 'default')]
    public function setLogger($logger) {
      $this->logger= $logger;
      $this->logger->addAppender(new ColoredConsoleAppender());
    }    

    /**
     * verbose output
     *
     * @param rdbms.DBConnection conn
     */
    #[@inject(type='rdbms.DBConnection', name= 'localhost')]
    public function setConnection($conn) {
      $this->conn= $conn;
    }    

    /**
     * show source
     *
     */
    #[@arg(short= 's')]
    public function showSource() {
      $this->showSource= true;
    }    
  }
?>
