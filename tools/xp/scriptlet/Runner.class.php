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
   * @purpose  Tool
   */
  class xp·scriptlet·Runner extends Object {
    const
      XML         = 0x0001,
      ERRORS      = 0x0002,
      STACKTRACE  = 0x0004,
      TRACE       = 0x0008;
      
    protected
      $flags      = 0x0000,
      $scriptlet  = NULL;
    
    public static function main(array $args) { 
      try {
        $self= self::setup($args);
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

    protected static function setup(array $args) {
      $webroot= $args[0];
      
      // This is not using the PropertyManager by intention: we'll postpone the
      // initialization of it until later, because there might be configuration
      // that indicates to use another properties directory.
      $pr= new Properties($webroot.'/etc/web.ini');

      $url= getenv('SCRIPT_URL');
      $specific= getenv('SERVER_PROFILE');

      $mappings= $pr->readHash('app', 'mappings');
      if (!$mappings instanceof Hashmap)
        throw new IllegalStateException('Application misconfigured: "app" section missing or broken.');

      foreach ($mappings->keys() as $pattern) {
        if (!preg_match('#'.preg_quote($pattern, '#').'#', $url)) continue;

        // Run first scriptlet that matches
        $scriptlet= 'app::'.$mappings->get($pattern);
        
        try {
          $class= XPClass::forName(self::readString($pr, $specific, $scriptlet, 'class'));
        } catch (ClassNotFoundException $e) {
          throw new IllegalArgumentException('Scriptlet "'.$scriptlet.'" misconfigured or missing: '.$e->getMessage());
        }
        $args= array();
        foreach (self::readArray($pr, $specific, $scriptlet, 'init-params') as $value) {
          $args[]= strtr($value, array('{WEBROOT}' => $webroot));
        }

        // Set environment variables
        $env= self::readHash($pr, $specific, $scriptlet, 'init-envs', new HashMap());
        foreach ($env->keys() as $key) {
          putenv($key.'='.$env->get($key));
        }
        
        // Configure PropertyManager
        $pm= PropertyManager::getInstance();
        $pm->configure(strtr(self::readString($pr, $specific, $scriptlet, 'prop-base', $webroot.'/etc'), array('{WEBROOT}' => $webroot)));
        
        // Always configure Logger (prior to ConnectionManager, so that one can pick up
        // categories from Logger)
        $pm->hasProperties('log') && Logger::getInstance()->configure($pm->getProperties('log'));
        
        // Always make connection manager available
        $pm->hasProperties('database') && ConnectionManager::getInstance()->configure($pm->getProperties('database'));
        
        $self= new self($class->hasConstructor()
          ? $class->getConstructor()->newInstance($args)
          : $class->newInstance()
        );
        
        // Determine debug level
        foreach (self::readArray($pr, $specific, $scriptlet, 'debug', array()) as $lvl) {
          $self->flags|= $self->getClass()->getConstant($lvl);
        }
        
        return $self;
      }
      
      throw new IllegalArgumentException('Could not find app responsible for request to '.$url);
    }
    
    /**
     * Constructor
     *
     * @param   scriptlet.HttpScriptlet scriptlet
     */
    protected function __construct(HttpScriptlet $scriptlet) {
      $this->scriptlet= $scriptlet;
    }
    
    /**
     * Run scriptlet instance
     *
     */
    protected function run() {
      $exception= NULL;
      if ($this->flags & self::TRACE && $this->scriptlet instanceof Traceable) {
        $this->scriptlet->setTrace(Logger::getInstance()->getCategory('scriptlet'));
      }
      
      try {
        $this->scriptlet->init();
        $response= $this->scriptlet->process();
      } catch (HttpScriptletException $e) {

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
      
      // HACK: Do not send headers when they've been sent before - there should
      // be support for this scenario within the scriptlet API itself
      headers_sent() || $response->sendHeaders();
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
     * @param   util.Properties pr
     * @param   string specific
     * @param   string section
     * @param   string key
     * @param   mixed default default NULL
     * @return  string
     */
    protected static function readString(Properties $pr, $specific, $section, $key, $default= NULL) {
      return $pr->readString($section.'@'.$specific, $key, $pr->readString($section, $key, $default));
    }
    
    /**
     * Read array. First tries special section "section"@"specific", then defaults 
     * to "section"
     *
     * @param   util.Properties pr
     * @param   string specific
     * @param   string section
     * @param   string key
     * @param   mixed default default NULL
     * @return  string
     */
    protected static function readArray(Properties $pr, $specific, $section, $key, $default= NULL) {
      return $pr->readArray($section.'@'.$specific, $key, $pr->readArray($section, $key, $default));
    }
    
    /**
     * Read hashmap. First tries special section "section"@"specific", then defaults 
     * to "section"
     *
     * @param   util.Properties pr
     * @param   string specific
     * @param   string section
     * @param   string key
     * @param   mixed default default NULL
     * @return  string
     */
    protected static function readHash(Properties $pr, $specific, $section, $key, $default= NULL) {
      return $pr->readHash($section.'@'.$specific, $key, $pr->readHash($section, $key, $default));
    }
  }
?>
