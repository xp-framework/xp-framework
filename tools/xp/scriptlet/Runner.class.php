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
      $specific= 'app@'.getenv('SERVER_NAME');

      // TBD: How do we find 'specifics' - environment, server name, ...?      
      $mappings= $pr->readHash($specific, 'mappings', $pr->readHash('app', 'mappings'));
      foreach ($mappings->keys() as $pattern) {
        if (!preg_match('°'.$pattern.'°', $url)) continue;

        // Run first scriptlet that matches
        $scriptlet= $mappings->get($pattern);
        $class= XPClass::forName($pr->readString($scriptlet, 'class'));
        $args= array();
        foreach ($pr->readArray($scriptlet, 'init-params') as $value) {
          // TBI: Inject properties?
          $args[]= strtr($value, array('{WEBROOT}' => $webroot));
        }

        // Set environment variables
        $env= $pr->readHash($scriptlet, 'init-envs', new HashMap());
        foreach ($env->keys() as $key) {
          putenv($key.'='.$env->get($key));
        }
        
        // HACK #1: Always configure Logger (prior to ConnectionManager, so that one can pick up
        // categories from Logger)
        Logger::getInstance()->configure($pm->getProperties('log'));
        
        // HACK #2: Always make connection manager available - should be done inside scriptlet init
        ConnectionManager::getInstance()->configure($pm->getProperties('database'));
        
        $self= new self();
        
        // Determine debug level
        foreach ($pr->readArray($scriptlet, 'debug', array()) as $lvl) {
          $self->flags|= $self->getClass()->getConstant($lvl);
        }
        
        $self->run($class->hasConstructor()
          ? $class->getConstructor()->newInstance($args)
          : $class->newInstance()
        );
        return;
      }
      
      throw new IllegalArgumentException('Could not find app responsible for request to '.$url);
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
