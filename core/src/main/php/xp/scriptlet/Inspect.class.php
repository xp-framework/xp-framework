<?php namespace xp\scriptlet;

use util\Properties;
use util\cmd\Console;

/**
 * Inspect scriptlet coniguration
 */
class Inspect extends \lang\Object {

  /**
   * Entry point method. Gets passed the following arguments from "xpws -i":
   * <ol>
   *   <li>The web root - defaults to $CWD</li>
   *   <li>The configuration directory - defaults to "etc"</li>
   *   <li>The server profile - default to "dev"</li>
   *   <li>The server address - default to "localhost:8080"</li>
   * </ol>
   *
   * @param   string[] args
   */
  public static function main(array $args) {
    $webroot= isset($args[0]) ? realpath($args[0]) : getcwd();
    $configd= isset($args[1]) ? $args[1] : 'etc';
    $profile= isset($args[2]) ? $args[2] : 'dev';
    $address= isset($args[3]) ? $args[3] : 'localhost:8080';
    Console::writeLine('xpws-', $profile, ' @ ', $address, ', ', $webroot, ' {');

    // Dump configured applications
    $conf= new WebConfiguration(new Properties($configd.DIRECTORY_SEPARATOR.'web.ini'));
    foreach ($conf->mappedApplications($profile) as $url => $app) {
      Console::writeLine('  Route<', $url, '*> => ', \xp::stringOf($app, '  '));
    }

    Console::writeLine('}');
  }
}
