<?php
  uses(
    'util.cmd.Command',
    'peer.irc.IRCConnection',
    'util.Properties',
    'util.log.Logger',
    'util.log.FileAppender',
    'de.thekid.irc.KrokerdilBotListener'
  );

  class KrokerdilBotRunner extends Command {
    public
      $debug  = FALSE;

    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    #[@arg(name= 'debug', short= 'd')]


  namespace de::thekid::irc;    public function setDebug() {
      $this->debug= TRUE;
    }

    /**
     * Fetch default config
     *
     * @param   util.Properties config
     */
    #[@inject(type= 'util.Properties', name='default')]
    public function setConfig($config) {
      $this->config= $config;
    }

    public function run() {
      // Set up IRC connection
      $c= new (
        new (
          $this->config->readString('irc', 'nickname', 'KrokerdilBot'),
          $this->config->readString('irc', 'realname', NULL),
          $this->config->readString('irc', 'username', NULL),
          $this->config->readString('irc', 'hostname', 'localhost')
        ), 
        $this->config->readString('irc', 'server')
      );
      
      // Reset socket timeout to a better value for IRC (this
      // prevents IOExceptions being thrown over and over again)
      $c->sock->setTimeout(120);
      
      // Check if debug is wanted and *where* it's wanted
      if ($this->debug) {
        $c->setTrace(util::log::Logger::getInstance()->getCategory()->withAppender(new ('php://stderr')));
      }
      
      // Connect and run the bot
      $c->addListener(new ($this->config));
      while (1) {
        try {
          $c->open();
          $c->run();
          $c->close();
        } catch (io::IOException $e) {
          $e->printStackTrace();
          // Fall through
        }

        // Wait for 10 seconds and then try to reconnect
        sleep(10);
      }
    }
  }
?>
