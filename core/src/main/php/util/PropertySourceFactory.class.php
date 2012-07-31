<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'util.FilesystemPropertySource',
    'util.ArchivePropertySource'
  );

  /**
   * Factory for property sources
   *
   * @test     xp://net.xp_framework.unittest.util.PropertySourceFactoryTest
   * @purpose  Factory
   * @see      xp://util.PropertySource
   */
  class PropertySourceFactory extends Object {

    /**
     * Create property source for given URI
     *
     * @param   string uri The URI
     * @return  util.PropertySource
     */
    public static function forUri($uri) {
      if (strrpos($uri, '.xar') == (strlen($uri)-4)) {
        return new ArchivePropertySource($uri);
      } else {
        return new FilesystemPropertySource($uri);
      }
    }
  }
?>
