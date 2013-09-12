<?php namespace xp\scriptlet;

use util\PropertyManager;
use util\FilesystemPropertySource;
use util\ResourcePropertySource;
use util\log\Logger;
use util\log\context\EnvironmentAware;
use rdbms\ConnectionManager;
use scriptlet\HttpScriptlet;
use peer\http\HttpConstants;

/**
 * Scriptlet runner
 *
 * @test   xp://net.xp_framework.unittest.scriptlet.RunnerTest
 */
class Runner extends \lang\Object {
  protected
    $webroot    = null,
    $profile    = null,
    $mappings   = null;

  static function __static() {
    if (!function_exists('getallheaders')) {
      eval('function getallheaders() {
        $headers= array();
        foreach ($_SERVER as $name => $value) {
          if (0 !== strncmp("HTTP_", $name, 5)) continue;
          $headers[strtr(ucwords(strtolower(strtr(substr($name, 5), "_", " "))), " ", "-")]= $value;
        }
        return $headers;
      }');
    }
  }
  
  /**
   * Creates a new scriptlet runner
   *
   * @param   string webroot
   * @param   string profile
   */
  public function __construct($webroot, $profile= null) {
    $this->webroot= $webroot;
    $this->profile= $profile;
  }
  
  /**
   * Configure this runner with a web.ini
   *
   * @param   util.Properties conf
   * @throws  lang.IllegalStateException if the web is misconfigured
   */
  public function configure(\util\Properties $conf) {
    $conf= new WebConfiguration($conf);
    foreach ($conf->mappedApplications($this->profile) as $url => $application) {
      $this->mapApplication($url, $application);
    }
  }
  
  /**
   * Entry point method. Receives the following arguments from web.php:
   * <ol>
   *   <li>The web root</li>
   *   <li>The configuration directory</li>
   *   <li>The server profile</li>
   *   <li>The script URL</li>
   * </ol>
   *
   * @param   string[] args
   */
  public static function main(array $args) {
    $r= new self($args[0], $args[2]);
    $r->configure(new \util\Properties($args[1].DIRECTORY_SEPARATOR.'web.ini'));
    $r->run($args[3]);
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

    throw new \lang\IllegalArgumentException('Could not find app responsible for request to '.$url);
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
      $expanded= $this->expand($element);
      if (0 == strncmp('res://', $expanded, 6)) {
        $pm->appendSource(new ResourcePropertySource($expanded));
      } else {
        $pm->appendSource(new FilesystemPropertySource($expanded));
      }
    }
    
    $l= Logger::getInstance();
    $pm->hasProperties('log') && $l->configure($pm->getProperties('log'));

    $cm= ConnectionManager::getInstance();
    $pm->hasProperties('database') && $cm->configure($pm->getProperties('database'));

    // Setup logger context for all registered log categories
    foreach (Logger::getInstance()->getCategories() as $category) {
      if (null === ($context= $category->getContext()) || !($context instanceof EnvironmentAware)) continue;
      $context->setHostname($_SERVER['SERVER_NAME']);
      $context->setRunner($this->getClassName());
      $context->setInstance($application->getScriptlet());
      $context->setResource($url);
      $context->setParams($_SERVER['QUERY_STRING']);
    }

    // Set environment variables
    foreach ($application->getEnvironment() as $key => $value) {
      $_SERVER[$key]= $this->expand($value);
    }

    // Instantiate and initialize
    $cat= $l->getCategory('scriptlet');
    $instance= null;
    $e= null;
    try {
      $class= \lang\XPClass::forName($application->getScriptlet());
      if (!$class->hasConstructor()) {
        $instance= $class->newInstance();
      } else {
        $args= array();
        foreach ($application->getArguments() as $arg) {
          $args[]= $this->expand($arg);
        }
        $instance= $class->getConstructor()->newInstance($args);
      }
      
      if ($flags & WebDebug::TRACE && $instance instanceof \util\log\Traceable) {
        $instance->setTrace($cat);
      }
      $instance->init();
    
      // Service
      $response= $instance->process();
    } catch (\scriptlet\ScriptletException $e) {
      $cat->error($e);

      // TODO: Instead of checking for a certain method, this should
      // check if the scriptlet class implements a certain interface
      if (method_exists($instance, 'fail')) {
        $response= $instance->fail($e);
      } else {
        $response= $this->fail($e, $e->getStatus(), $flags & WebDebug::STACKTRACE);
      }
    } catch (\lang\SystemExit $e) {
      if (0 === $e->getCode()) {
        $response= new \scriptlet\HttpScriptletResponse();
        $response->setStatus(HttpConstants::STATUS_OK);
        if ($message= $e->getMessage()) $response->setContent($message);
      } else {
        $cat->error($e);
        $response= $this->fail($e, HttpConstants::STATUS_INTERNAL_SERVER_ERROR, false);
      }
    } catch (\lang\Throwable $e) {
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
      echo '<xmp>', $e ? $e->toString() : '', \xp::stringOf(\xp::$errors), '</xmp>';
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
  protected function fail(\lang\Throwable $t, $status, $trace) {
    $package= create(new \lang\XPClass(__CLASS__))->getPackage();
    $errorPage= ($package->providesResource('error'.$status.'.html')
      ? $package->getResource('error'.$status.'.html')
      : $package->getResource('error500.html')
    );

    $response= new \scriptlet\HttpScriptletResponse();
    $response->setStatus($status);
    $response->setContent(str_replace(
      '<xp:value-of select="reason"/>',
      $trace ? $t->toString() : $t->getMessage(),
      $errorPage
    ));
    return $response;
  }
}
