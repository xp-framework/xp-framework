<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  $package= 'xp.scriptlet';

  uses(
    'xp.scriptlet.WebApplication',
    'xp.scriptlet.WebConfiguration',
    'util.Properties'
  );

  /**
   * Inspect scriptlet coniguration
   *
   */
  class xp·scriptlet·Inspect extends Object {

    /**
     * Entry point method. Gets passed the following arguments from "xpws -i":
     * <ul>
     *   <li>#0: The document root - defaults to $CWD/static</li>
     *   <li>#1: The server profile - default to "dev"</li>
     *   <li>#2: The server address - default to "localhost:8080"</li>
     * </ul>
     *
     * @param   string[] args
     */
    public static function main(array $args) {
      $docroot= realpath(isset($args[0]) ? $args[0] : getcwd().'/static');
      $webroot= dirname($docroot);
      $profile= isset($args[1]) ? $args[1] : 'dev';
      $address= isset($args[2]) ? $args[2] : 'localhost:8080';
      Console::writeLine('xpws-', $profile, ' @ ', $address, ', ', $docroot, ' {');

      // Dump configured applications
      $conf= new xp·scriptlet·WebConfiguration(new Properties($webroot.'/etc/web.ini'));
      foreach ($conf->mappedApplications($profile) as $url => $app) {
        Console::writeLine('  Route<', $url, '*> => ', str_replace("\n", "\n  ", $app->toString()));
      }

      Console::writeLine('}');
    }
  }
?>
