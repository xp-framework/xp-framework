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
      $webroot    = NULL,
      $profile    = NULL,
      $conf       = NULL,
      $mappings   = NULL;
    
    /**
     * Creates a new scriptlet runner
     *
     * @param   string webroot
     * @param   util.Properties conf
     * @param   string profile
     * @throws  lang.IllegalStateException if the web is misconfigured
     */
    public function __construct($webroot, Properties $conf, $profile= NULL) {
      $mappings= $conf->readHash('app', 'mappings', NULL);

      // Verify configuration
      if (NULL === $mappings) {
        $this->mappings= array();
        foreach ($conf->readSection('app') as $key => $url) {
          if (0 !== strncmp('map.', $key, 4)) continue;
          $application= 'app::'.substr($key, 4);
          if (!$conf->hasSection($application)) {
            throw new IllegalStateException('Web misconfigured: Section '.$application.' mapped by '.$url.' missing');
          }
          $this->mappings[$url]= $application;
        }
      } else {
        foreach ($mappings->keys() as $url) {
          $application= 'app::'.$mappings->get($url);
          if (!$conf->hasSection($application)) {
            throw new IllegalStateException('Web misconfigured: Section '.$application.' mapped by '.$url.' missing');
          }
          $this->mappings[$url]= $application;
        }
      }

      if (0 === sizeof($this->mappings)) {
        throw new IllegalStateException('Web misconfigured: "app" section missing or broken');
      }

      $this->webroot= $webroot;
      $this->conf= $conf;
      $this->profile= $profile;
    }
    
    /**
     * Entry point method. Receives the following arguments from web.php:
     * <ol>
     *   <li>The web root</li>
     *   <li>The server profile</li>
     *   <li>The script URL</li>
     * </ol>
     *
     * @param   string[] args
     */
    public static function main(array $args) {
      create(new self($args[0], new Properties($args[0].'/etc/web.ini'), $args[1]))->run($args[2]);
    }
    
    /**
     * Read string. First tries special section "section"@"profile", then defaults 
     * to "section"
     *
     * @param   string section
     * @param   string key
     * @param   var default default NULL
     * @return  string
     */
    protected function readString($section, $key, $default= NULL) {
      if (NULL === ($s= $this->conf->readString($section.'@'.$this->profile, $key, NULL))) {
        return $this->conf->readString($section, $key, $default);
      }
      return $s;
    }
    
    /**
     * Read array. First tries special section "section"@"profile", then defaults 
     * to "section"
     *
     * @param   string section
     * @param   string key
     * @param   var default default NULL
     * @return  string[]
     */
    protected function readArray($section, $key, $default= NULL) {
      if (NULL === ($a= $this->conf->readArray($section.'@'.$this->profile, $key, NULL))) {
        return $this->conf->readArray($section, $key, $default);
      }
      return $a;
    }
    
    /**
     * Read hashmap. First tries special section "section"@"profile", then defaults 
     * to "section"
     *
     * @param   string section
     * @param   string key
     * @param   var default default NULL
     * @return  util.Hashmap
     */
    protected function readHash($section, $key, $default= NULL) {
      if (NULL === ($h= $this->conf->readHash($section.'@'.$this->profile, $key, NULL))) {
        return $this->conf->readHash($section, $key, $default);
      }
      return $h;
    }

    /**
     * Find which application the given url maps to
     *
     * @param   string url
     * @return  string
     * @throws  lang.IllegalArgumentException if no app can be found
     */
    public function applicationAt($url) {
      $url= '/'.ltrim($url, '/');
      foreach ($this->mappings as $pattern => $application) {
        if ('/' !== $pattern && !preg_match('#^('.preg_quote($pattern, '#').')($|/.+)#', $url)) continue;
        return $application;
      }

      throw new IllegalArgumentException('Could not find app responsible for request to '.$url);
    }

    /**
     * Return mappings
     *
     * @return  [string:string]
     */
    public function mappedApplications() {
      return $this->mappings;
    }


    /**
     * Expand variables in string. Handles the following placeholders:
     * <ul>
     *   <li>WEBROOT</li>
     *   <li>PROFILE</li>
     * </ul>
     *
     * @param   string value
     * @return  string
     */
    public function expand($value) {
      return strtr($value, array(
        '{WEBROOT}' => $this->webroot,
        '{PROFILE}' => $this->profile,
      ));
    }
    
    /**
     * Creates the scriptlet instance for the given URL and runs it
     *
     * @param   string url default '/'
     */
    public function run($url= '/') {
    
      // Determine which scriptlet should be run
      $application= $this->applicationAt($url);

      // Determine debug level
      $flags= 0x0000;
      foreach ($this->readArray($application, 'debug', array()) as $lvl) {
        $flags |= $this->getClass()->getConstant($lvl);
      }
      
      // Initializer logger, properties and connections to property base, 
      // defaulting to the same directory the web.ini resides in
      $pm= PropertyManager::getInstance();
      $pm->configure($this->expand($this->readString($application, 'prop-base', $this->webroot.'/etc')));
      
      $l= Logger::getInstance();
      $pm->hasProperties('log') && $l->configure($pm->getProperties('log'));

      $cm= ConnectionManager::getInstance();
      $pm->hasProperties('database') && $cm->configure($pm->getProperties('database'));
      
      // Set environment variables
      $env= $this->readHash($application, 'init-envs', new Hashmap());
      foreach ($env->keys() as $key) {
        putenv($key.'='.$env->get($key));
      }

      // Instantiate and initialize
      $class= XPClass::forName($this->readString($application, 'class'));
      if (!$class->hasConstructor()) {
        $instance= $class->newInstance();
      } else {
        $args= array();
        foreach ($this->readArray($application, 'init-params') as $value) {
          $args[]= $this->expand($value);
        }
        $instance= $class->getConstructor()->newInstance($args);
      }
      $cat= Logger::getInstance()->getCategory('scriptlet');
      if ($flags & self::TRACE && $instance instanceof Traceable) {
        $instance->setTrace($cat);
      }
      $instance->init();

      // Service
      $e= NULL;
      try {
        $response= $instance->process();
      } catch (ScriptletException $e) {
        $cat->error($e);

        // TODO: Instead of checking for a certain method, this should
        // check if the scriptlet class implements a certain interface
        if (is_callable(array($instance, 'fail'))) {
          $response= $instance->fail($e);
        } else {
          $response= $this->fail($e, $e->getStatus(), $flags & self::STACKTRACE);
        }
      }

      // Send output
      $response->isCommitted() || $response->flush();
      $response->sendContent();

      // Call scriptlet's finalizer
      $instance->finalize();

      // Debugging
      if (($flags & self::XML) && isset($response->document)) {
        flush();
        echo '<xmp>', $response->document->getDeclaration()."\n".$response->document->getSource(0), '</xmp>';
      }
      
      if (($flags & self::ERRORS)) {
        flush();
        echo '<xmp>', $e ? $e->toString() : '', xp::stringOf(xp::registry('errors')), '</xmp>';
      }
    }

    /**
     * Handle exception from scriptlet
     *
     * @param   lang.Throwable t
     * @param   int status
     * @param   bool trace whether to show stacktrace
     * @return  scriptlet.HttpScriptletResponse
     */
    protected function fail(Throwable $t, $status, $trace) {
      $package= create(new XPClass(__CLASS__))->getPackage();
      $errorPage= ($package->providesResource('error'.$status.'.html')
        ? $package->getResource('error'.$status.'.html')
        : $package->getResource('error500.html')
      );

      $response= new HttpScriptletResponse();
      $response->setStatus($status);
      $response->setContent(str_replace(
        '<xp:value-of select="reason"/>',
        $trace ? $t->toString() : $t->getMessage(),
        $errorPage
      ));
      return $response;
    }
  }
?>
