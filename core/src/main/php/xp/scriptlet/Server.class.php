<?php namespace xp\scriptlet;

use util\cmd\Console;
use util\PropertyManager;
use util\ResourcePropertySource;
use util\FilesystemPropertySource;
use util\log\Logger;
use util\log\context\EnvironmentAware;
use rdbms\ConnectionManager;
use lang\XPClass;
use peer\http\HttpConstants;

/**
 * Web server
 */
class Server extends \lang\Object {
  protected static $modes= array(
    'serve'   => 'peer.server.Server',
    'prefork' => 'peer.server.PreforkingServer',
    'fork'    => 'peer.server.ForkingServer'
  );

  static function __static() {
    \lang\XPClass::forName('lang.ResourceProvider');
  }

  /**
   * Entry point method. Receives the following arguments from xpws:
   *
   * - The web root - defaults to $CWD
   * - The configuration directory - defaults to "etc"
   * - The server profile - default to "dev"
   * - The server address - default to "localhost:8080"
   * - The mode - default to "serve"
   *
   * @param   string[] args
   * @return  int
   */
  public static function main(array $args) {
    $webroot= isset($args[0]) ? realpath($args[0]) : getcwd();
    $configd= isset($args[1]) ? $args[1] : 'etc';
    $profile= isset($args[2]) ? $args[2] : 'dev';
    $address= isset($args[3]) ? $args[3] : 'localhost:8080';
    if (!($class= @self::$modes[isset($args[4]) ? $args[4] : 'serve'])) {
      Console::writeLine('*** Unkown server mode "', $args[4], '", supported: ', self::$modes);
      return 2;
    }

    $expand= function($in) use($webroot, $profile) {
      return strtr($in, array(
        '{WEBROOT}'       => $webroot,
        '{PROFILE}'       => $profile,
        '{DOCUMENT_ROOT}' => getenv('DOCUMENT_ROOT')
      ));
    };

    Console::writeLine('---> Startup ', $class, '(', $address, ')');
    sscanf($address, '%[^:]:%d', $host, $port);
    $server= XPClass::forName($class)->newInstance($host, $port);

    with ($pm= PropertyManager::getInstance(), $protocol= $server->setProtocol(new HttpProtocol())); {
      $conf= new WebConfiguration(new \util\Properties($configd.DIRECTORY_SEPARATOR.'web.ini'));

      $resources= $conf->staticResources($args[2]);
      if (null === $resources) {
        $protocol->setUrlHandler('default', '#^/#', new FileHandler(
          $expand('{DOCUMENT_ROOT}'),
          $notFound= function() { return HttpConstants::STATUS_CONTINUE; }
        ));
      } else {
        foreach ($conf->staticResources($args[2]) as $pattern => $location) {
          $protocol->setUrlHandler('default', '#'.strtr($pattern, array('#' => '\\#')).'#', new FileHandler($expand($location)));
        }
      }
      foreach ($conf->mappedApplications($args[2]) as $url => $application) {
        foreach (explode('|', $application->getConfig()) as $element) {
          $expanded= $expand($element);
          if (0 == strncmp('res://', $expanded, 6)) {
            $pm->appendSource(new ResourcePropertySource(substr($expanded, 6)));
          } else {
            $pm->appendSource(new FilesystemPropertySource($expanded));
          }
        }
        $protocol->setUrlHandler('default', '/' == $url ? '##' : '#^('.preg_quote($url, '#').')($|/.+)#', new ScriptletHandler(
          $application->getScriptlet(),
          array_map($expand, $application->getArguments()),
          array_map($expand, array_merge($application->getEnvironment(), array(
            'DOCUMENT_ROOT' => getenv('DOCUMENT_ROOT')
          )))
        ));
      }

      $l= Logger::getInstance();
      $pm->hasProperties('log') && $l->configure($pm->getProperties('log'));
      $cm= ConnectionManager::getInstance();
      $pm->hasProperties('database') && $cm->configure($pm->getProperties('database'));
      Console::writeLine($protocol);
    }
    $server->init();

    Console::writeLine('===> Server started');
    $server->service();
    $server->shutdown();
    return 0;
  }
}