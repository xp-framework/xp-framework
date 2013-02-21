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
     * Entry point method
     *
     * @param   string[] args
     */
    public static function main(array $args) {
      $docroot= realpath($args[3] ?: getcwd().'/static');
      $webroot= dirname($docroot);
      Console::writeLine('xpws-', $args[0], ' @ ', $args[1], ':', $args[2], ', ', $docroot, ' {');

      // Dump configured applications
      $conf= new xp·scriptlet·WebConfiguration(new Properties($webroot.'/etc/web.ini'));
      foreach ($conf->mappedApplications($args[0]) as $url => $app) {
        Console::writeLine('  Route<', $url, '*> => ', str_replace("\n", "\n  ", $app->toString()));
      }

      Console::writeLine('}');
    }
  }
?>
