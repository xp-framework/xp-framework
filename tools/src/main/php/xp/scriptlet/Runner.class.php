<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  $package= 'xp.scriptlet';

  uses(
    'xp.scriptlet.WebApplication',
    'util.PropertyManager',
    'util.log.Logger',
    'rdbms.ConnectionManager',
    'scriptlet.HttpScriptlet',
    'peer.http.HttpConstants'
  );
  
  /**
   * Scriptlet runner
   *
   * @test      xp://net.xp_framework.unittest.scriptlet.RunnerTest
   * @purpose   Scriptlet runner
   */
  class xp�scriptlet�Runner extends Object {
    protected
      $webroot    = NULL,
      $profile    = NULL,
      $mappings   = NULL;

    static function __static() {
      if (!function_exists('getallheaders')) {
        function getallheaders() {
          $headers= array();
          foreach ($_SERVER as $name => $value) {
            if (0 !== strncmp('HTTP_', $name, 5)) continue;
            $headers[strtr(ucwords(strtolower(strtr(substr($name, 5), '_', ' '))), ' ', '-')]= $value;
          }
          return $headers;
        }
      }
    }
    
    /**
     * Creates a new scriptlet runner
     *
     * @param   string webroot
     * @param   string profile
     */
    public function __construct($webroot, $profile= NULL) {
      $this->webroot= $webroot;
      $this->profile= $profile;
    }

    /**
     * Read string. First tries special section "section"@"profile", then defaults 
     * to "section"
     *
     * @param   util.Properties conf
     * @param   string section
     * @param   string key
     * @param   var default default NULL
     * @return  string
     */
    protected function readString($conf, $section, $key, $default= NULL) {
      if (NULL === ($s= $conf->readString($section.'@'.$this->profile, $key, NULL))) {
        return $conf->readString($section, $key, $default);
      }
      return $s;
    }
    
    /**
     * Read array. First tries special section "section"@"profile", then defaults 
     * to "section"
     *
     * @param   util.Properties conf
     * @param   string section
     * @param   string key
     * @param   var default default NULL
     * @return  string[]
     */
    protected function readArray($conf, $section, $key, $default= NULL) {
      if (NULL === ($a= $conf->readArray($section.'@'.$this->profile, $key, NULL))) {
        return $conf->readArray($section, $key, $default);
      }
      return $a;
    }
    
    /**
     * Read hashmap. First tries special section "section"@"profile", then defaults 
     * to "section"
     *
     * @param   util.Properties conf
     * @param   string section
     * @param   string key
     * @param   var default default NULL
     * @return  util.Hashmap
     */
    protected function readHash($conf, $section, $key, $default= NULL) {
      if (NULL === ($h= $conf->readHash($section.'@'.$this->profile, $key, NULL))) {
        return $conf->readHash($section, $key, $default);
      }
      return $h;
    }
    
    /**
     * Creates a web application object from a given configuration section
     *
     * @param   util.Properties conf
     * @param   string application app name
     * @param   string url
     * @return  xp.scriptlet.WebApplication
     * @throws  lang.IllegalStateException if the web is misconfigured
     */
    protected function configuredApp($conf, $application, $url) {
      $section= 'app::'.$application;
      if (!$conf->hasSection($section)) {
        throw new IllegalStateException('Web misconfigured: Section '.$section.' mapped by '.$url.' missing');
      }

      $app= new WebApplication($application);
      $app->setScriptlet($this->readString($conf, $section, 'class', ''));
      
      // Configuration base
      $app->setConfig($this->expand($this->readString($conf, $section, 'prop-base', $this->webroot.'/etc')));

      // Determine debug level
      $flags= WebDebug::NONE;
      foreach ($this->readArray($conf, $section, 'debug', array()) as $lvl) {
        $flags |= WebDebug::flagNamed($lvl);
      }
      $app->setDebug($flags);
      
      // Initialization arguments
      $args= array();
      foreach ($this->readArray($conf, $section, 'init-params', array()) as $value) {
        $args[]= $this->expand($value);
      }
      $app->setArguments($args);
 
      // Environment
      $app->setEnvironment($this->readHash($conf, $section, 'init-envs', new Hashmap())->toArray());
     
      return $app;
    }
    
    /**
     * Configure this runner with a web.ini
     *
     * @param   util.Properties conf
     * @throws  lang.IllegalStateException if the web is misconfigured
     */
    public function configure(Properties $conf) {
      $mappings= $conf->readHash('app', 'mappings', NULL);

      // Verify configuration
      if (NULL === $mappings) {
        foreach ($conf->readSection('app') as $key => $url) {
          if (0 !== strncmp('map.', $key, 4)) continue;
          $this->mappings[$url]= $this->configuredApp($conf, substr($key, 4), $url);
        }
      } else {
        foreach ($mappings->keys() as $url) {
          $this->mappings[$url]= $this->configuredApp($conf, $mappings->get($url), $url);
        }
      }

      if (0 === sizeof($this->mappings)) {
        throw new IllegalStateException('Web misconfigured: "app" section missing or broken');
      }
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
      $r= new self($args[0], $args[1]);
      $r->configure(new Properties($args[0].'/etc/web.ini'));
      $r->run($args[2]);
    }
    
    /**
     * Find which application the given url maps to
     *
     * @param   string url
     * @return  xp.scriptlet.WebApplication
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
     * @return  [string:xp.scriptlet.WebApplication]
     */
    public function mappedApplications() {
      return $this->mappings;
    }

    /**
     * Adds an application
     *
     * @param   string url
     * @param   xp.scriptlet.WebApplication application
     * @return  xp.scriptlet.WebApplication the added application
     */
    public function mapApplication($url, WebApplication $application) {
      $this->mappings[$url]= $application;
      return $application;
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
      $flags= $application->getDebug();
      
      // Initializer logger, properties and connections to property base, 
      // defaulting to the same directory the web.ini resides in
      $pm= PropertyManager::getInstance();
      foreach (explode('|', $application->getConfig()) as $element) {
        $pm->appendSource(new FilesystemPropertySource($element));
      }
      
      $l= Logger::getInstance();
      $pm->hasProperties('log') && $l->configure($pm->getProperties('log'));

      $cm= ConnectionManager::getInstance();
      $pm->hasProperties('database') && $cm->configure($pm->getProperties('database'));
      
      // Set environment variables
      foreach ($application->getEnvironment() as $key => $value) {
        putenv($key.'='.$value);
      }

      // Instantiate and initialize
      $cat= $l->getCategory('scriptlet');
      $instance= NULL;
      $e= NULL;
      try {
        $class= XPClass::forName($application->getScriptlet());
        if (!$class->hasConstructor()) {
          $instance= $class->newInstance();
        } else {
          $instance= $class->getConstructor()->newInstance($application->getArguments());
        }
        
        if ($flags & WebDebug::TRACE && $instance instanceof Traceable) {
          $instance->setTrace($cat);
        }
        $instance->init();
      
        // Service
        $response= $instance->process();
      } catch (ScriptletException $e) {
        $cat->error($e);

        // TODO: Instead of checking for a certain method, this should
        // check if the scriptlet class implements a certain interface
        if (method_exists($instance, 'fail')) {
          $response= $instance->fail($e);
        } else {
          $response= $this->fail($e, $e->getStatus(), $flags & WebDebug::STACKTRACE);
        }
      } catch (SystemExit $e) {
        if (0 === $e->getCode()) {
          $response= new HttpScriptletResponse();
          $response->setStatus(HttpConstants::STATUS_OK);
          if ($message= $e->getMessage()) $response->setContent($message);
        } else {
          $cat->error($e);
          $response= $this->fail($e, HttpConstants::STATUS_INTERNAL_SERVER_ERROR, FALSE);
        }
      } catch (Throwable $e) {
        $cat->error($e);

        // Here, we might not have a scriptlet
        $response= $this->fail($e, HttpConstants::STATUS_PRECONDITION_FAILED, $flags & WebDebug::STACKTRACE);
      }

      // Send output
      $response->isCommitted() || $response->flush();
      $response->sendContent();

      // Call scriptlet's finalizer
      $instance && $instance->finalize();

      // Debugging
      if (($flags & WebDebug::XML) && isset($response->document)) {
        flush();
        echo '<xmp>', $response->document->getDeclaration()."\n".$response->document->getSource(0), '</xmp>';
      }
      
      if (($flags & WebDebug::ERRORS)) {
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
