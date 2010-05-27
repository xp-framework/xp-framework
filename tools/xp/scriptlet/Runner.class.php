<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  $package= 'xp.scriptlet';
  uses(
    'util.PropertyManager',
    'rdbms.ConnectionManager'
  );
  
  /**
   * Scriptlet runner
   *
   * @test      xp://net.xp_framework.unittest.scriptlet.RunnerTest
   * @purpose   Scriptlet runner
   */
  class xp·scriptlet·Runner extends Object {
    const
      XML         = 0x0001,
      ERRORS      = 0x0002,
      STACKTRACE  = 0x0004,
      TRACE       = 0x0008;
      
    protected
      $flags      = 0x0000,
      $webroot    = NULL,
      $profile    = NULL,
      $conf       = NULL,
      $scriptlet  = NULL;
    
    public static function main(array $args) {
      try {
        $webroot= $args[0];

        $url= getenv('SCRIPT_URL');
        $profile= getenv('SERVER_PROFILE');

        // This is not using the PropertyManager by intention: we'll postpone the
        // initialization of it until later, because there might be configuration
        // that indicates to use another properties directory.
        $self= new self(new Properties($webroot.'/etc/web.ini'), $webroot, $profile);

        $self->setup($url);
      } catch (Throwable $t) {
        header('HTTP/1.0 500 Scriptlet setup failed; very sorry.');
        throw $t;
      }
      
      try {
        $self->run();
      } catch (TargetInvocationException $e) {
        headers_sent() || header('HTTP/1.0 500 Internal server error');
        throw $e->getCause();
      }
    }

    /**
     * Constructor
     *
     * @param   scriptlet.HttpScriptlet scriptlet
     */
    public function __construct(Properties $conf, $webroot, $profile) {
      $this->conf= $conf;
      $this->webroot= $webroot;
      $this->profile= $profile;
    }

    /**
     * Find application name that has a mapping given which fits
     *
     * @param   util.Hashmap map
     * @param   string url
     * @return  mixed string or NULL if no match was found
     */
    public static function findApplication(Hashmap $map, $url) {
      foreach ($map->keys() as $pattern) {
        if (preg_match('#'.preg_quote($pattern, '#').'#', $url)) return $map->get($pattern);
      }

      return NULL;
    }

    /**
     * Replace variables in string
     *
     * @param   string value
     * @return  string
     */
    public function replaceVariables($value) {
      return strtr($value, array(
        '{WEBROOT}' => $this->webroot,
        '{PROFILE}' => $this->profile
      ));
    }

    /**
     * Find active section by [app] mappings
     *
     * @param   string URL
     * @return  string
     */
    public function activeSectionByMappings($url) {
      $mappings= $this->conf->readHash('app', 'mappings');

      if (!$mappings instanceof Hashmap) {
        $mappings= new Hashmap();

        foreach ($this->conf->readSection('app') as $key => $value) {
          if (0 == strncmp('map.', $key, 4)) {
            $mappings->put($value, substr($key, 4));
          }
        }
      }

      if (!$mappings instanceof Hashmap || $mappings->size() == 0)
        throw new IllegalStateException('Application misconfigured: "app" section missing or broken.');

      if (NULL === ($app= self::findApplication($mappings, $url))) {
        throw new IllegalArgumentException('Could not find app responsible for request to "'.$url.'"');
      }

      return $app;
    }

    /**
     * Set up scriptlet
     *
     * @param   string url
     * @throws  lang.IllegalStateException if application is misconfigured
     * @throws  lang.IllegalArgumentException if no app could be found
     */
    protected function setup($url) {
      $app= $this->activeSectionByMappings($url);

      // Load and configure scriptlet
      $scriptlet= 'app::'.$app;

      try {
        $class= XPClass::forName($this->readString($scriptlet, 'class'));
      } catch (ClassNotFoundException $e) {
        throw new IllegalArgumentException('Scriptlet "'.$scriptlet.'" misconfigured or missing: '.$e->getMessage());
      }
      $args= array();
      foreach ($this->readArray($scriptlet, 'init-params') as $value) {
        $args[]= $this->replaceVariables($value);
      }

      // Set environment variables
      $env= $this->readHash($scriptlet, 'init-envs', new HashMap());
      foreach ($env->keys() as $key) {
        putenv($key.'='.$env->get($key));
      }

      // Configure PropertyManager
      $pm= PropertyManager::getInstance();
      $pm->configure($this->replaceVariables($this->readString($scriptlet, 'prop-base', $this->webroot.'/etc')));

      // Always configure Logger (prior to ConnectionManager, so that one can pick up
      // categories from Logger)
      $pm->hasProperties('log') && Logger::getInstance()->configure($pm->getProperties('log'));

      // Always make connection manager available
      $pm->hasProperties('database') && ConnectionManager::getInstance()->configure($pm->getProperties('database'));

      $this->setScriptlet($class->hasConstructor()
        ? $class->getConstructor()->newInstance($args)
        : $class->newInstance()
      );

      // Determine debug level
      foreach ($this->readArray($scriptlet, 'debug', array()) as $lvl) {
        $this->flags|= $this->getClass()->getConstant($lvl);
      }
    }
    
    /**
     * Set scriptlet to run
     *
     * @param   scriptlet.HttpScriptlet scriptlet
     */
    public function setScriptlet(HttpScriptlet $scriptlet) {
      $this->scriptlet= $scriptlet;
    }
    
    /**
     * Run scriptlet instance
     *
     */
    protected function run() {
      $exception= NULL;
      $cat= Logger::getInstance()->getCategory('scriptlet');
      if ($this->flags & self::TRACE && $this->scriptlet instanceof Traceable) {
        $this->scriptlet->setTrace($cat);
      }
      
      try {
        $this->scriptlet->init();
        $response= $this->scriptlet->process();
      } catch (HttpScriptletException $e) {
        $cat->error('Web runner caught', $e);

        // Remember this exception to show it below the error page,
        // if this flag was set
        $exception= $e;

        // TODO: Instead of checking for a certain method, this should
        // check if the scriptlet class implements a certain interface
        if (is_callable(array($this->scriptlet, 'fail'))) {
          $response= $this->scriptlet->fail($e);
        } else {
          $response= $e->getResponse();
          $this->except($response, $e);
        }
      }

      // Send output
      if (!$response->headersSent()) $response->sendHeaders();
      $response->sendContent();
      flush();

      // Call scriptlet's finalizer
      $this->scriptlet->finalize();
      
      if (
        ($this->flags & self::XML) &&
        ($response && isset($response->document))
      ) {
        echo '<xmp>', $response->document->getDeclaration()."\n".$response->document->getSource(0), '</xmp>';
      }
      
      if (($this->flags & self::ERRORS)) {
        echo
          '<xmp>',
          $exception instanceof Throwable ? $exception->toString() : '',
          var_export(xp::registry('errors'), 1),
          '</xmp>'
        ;
      }
    }
    
    /**
     * Handle exception from scriptlet
     *
     * @param   scriptlet.HttpScriptletResponse response
     * @param   lang.Throwable e
     */
    protected function except(HttpScriptletResponse $response, Throwable $e) {
      $errorPage= ($this->getClass()->getPackage()->providesResource('error'.$response->statusCode.'.html')
        ? $this->getClass()->getPackage()->getResource('error'.$response->statusCode.'.html')
        : $this->getClass()->getPackage()->getResource('error500.html')
      );
      $response->setContent(str_replace(
        '<xp:value-of select="reason"/>',
        (($this->flags & self::STACKTRACE)
          ? $e->toString()
          : $e->getMessage()
        ),
        $errorPage
      ));
    }

    /**
     * Read string. First tries special section "section"@"specific", then defaults 
     * to "section"
     *
     * @param   string section
     * @param   string key
     * @param   var default default NULL
     * @return  string
     */
    protected function readString($section, $key, $default= NULL) {
      return $this->conf->readString($section.'@'.$this->profile, $key, $this->conf->readString($section, $key, $default));
    }
    
    /**
     * Read array. First tries special section "section"@"specific", then defaults 
     * to "section"
     *
     * @param   string section
     * @param   string key
     * @param   var default default NULL
     * @return  string
     */
    protected function readArray($section, $key, $default= NULL) {
      return $this->conf->readArray($section.'@'.$this->profile, $key, $this->conf->readArray($section, $key, $default));
    }
    
    /**
     * Read hashmap. First tries special section "section"@"specific", then defaults 
     * to "section"
     *
     * @param   string section
     * @param   string key
     * @param   var default default NULL
     * @return  string
     */
    protected function readHash($section, $key, $default= NULL) {
      return $this->conf->readHash($section.'@'.$this->profile, $key, $this->conf->readHash($section, $key, $default));
    }
  }
?>
