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
      SHOW_XML        = 0x0001,
      SHOW_ERRORS     = 0x0002,
      SHOW_STACKTRACE = 0x0004;
      
    protected
      $flags  = 0x0000;

    public static function main(array $args) {
      $webroot= $args[0];
      
      $pm= PropertyManager::getInstance();
      $pm->configure($webroot.'/etc');
      $pr= $pm->getProperties('web');

      $url= getenv('SCRIPT_URL');
      $specific= getenv('SERVER_PROFILE');

      $mappings= $pr->readHash('app', 'mappings');
      if (!$mappings instanceof Hashmap)
        throw new IllegalStateException('Application misconfigured: "app" section missing or broken.');

      foreach ($mappings->keys() as $pattern) {
        if (!preg_match('°'.$pattern.'°', $url)) continue;

        // Run first scriptlet that matches
        $scriptlet= 'app::'.$mappings->get($pattern);
        
        try {
          $class= XPClass::forName(self::readString($pr, $specific, $scriptlet, 'class'));
        } catch (ClassNotFoundException $e) {
          throw new IllegalArgumentException('Scriptlet "'.$scriptlet.'" misconfigured or missing: '.$e->getMessage());
        }
        $args= array();
        foreach (self::readArray($pr, $specific, $scriptlet, 'init-params') as $value) {
          // TBI: Inject properties?
          $args[]= strtr($value, array('{WEBROOT}' => $webroot));
        }

        // Set environment variables
        $env= self::readHash($pr, $specific, $scriptlet, 'init-envs', new HashMap());
        foreach ($env->keys() as $key) {
          putenv($key.'='.$env->get($key));
        }
        
        // HACK #1: Always configure Logger (prior to ConnectionManager, so that one can pick up
        // categories from Logger)
        $pm->hasProperties('log') && Logger::getInstance()->configure($pm->getProperties('log'));
        
        // HACK #2: Always make connection manager available - should be done inside scriptlet init
        $pm->hasProperties('database') && ConnectionManager::getInstance()->configure($pm->getProperties('database'));
        
        $self= new self();
        
        // Determine debug level
        foreach (self::readArray($pr, $specific, $scriptlet, 'debug', array()) as $lvl) {
          $self->flags|= $self->getClass()->getConstant($lvl);
        }
        
        try {
          $self->run($class->hasConstructor()
            ? $class->getConstructor()->newInstance($args)
            : $class->newInstance()
          );
        } catch (TargetInvocationException $e) {
          throw $e->getCause();
        }
         
        return;
      }
      
      throw new IllegalArgumentException('Could not find app responsible for request to '.$url);
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
    
    /**
     * Run scriptlet instance
     *
     * @param   scriptlet.HttpScriptlet scriptlet
     */
    protected function run(HttpScriptlet $scriptlet) {
      try {
        $scriptlet->init();
        $response= $scriptlet->process();
      } catch (HttpScriptletException $e) {
        $response= $e->getResponse();
        $this->except($response, $e);
      }

      // Send output
      
      // XXX HACK: Do not send headers when they've been sent before
      headers_sent() || $response->sendHeaders();
      $response->sendContent();
      flush();

      // Call scriptlet's finalizer
      $scriptlet->finalize();
      
      if (
        ($this->flags & self::SHOW_XML) &&
        ($response && isset($response->document))
      ) {
        echo '<xmp>', $response->document->getDeclaration()."\n".$response->document->getSource(0), '</xmp>';
      }
      
      if (($this->flags & self::SHOW_ERRORS)) {
        echo '<xmp>', var_export(xp::registry('errors'), 1), '</xmp>';
      }
    }
    
    /**
     * Handle exception from scriptlet
     *
     * @param   scriptlet.HttpScriptletResponse response
     * @param   lang.Throwable e
     */
    protected function except(HttpScriptletResponse $response, Throwable $e) {
      $response->setContent(str_replace(
        '<xp:value-of select="reason"/>',
        (($this->flags & self::SHOW_STACKTRACE)
          ? $e->toString()
          : $e->getMessage()
        ),
        $this->getClass()->getPackage()->getResource('error'.$response->statusCode.'.html')
      ));
    }
  }
?>
