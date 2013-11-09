<?php namespace net\xp_framework\unittest\img;

/**
 * Base class for EXIF- and IPTC-Data tests
 *
 * @see  xp://net.xp_framework.unittest.img.ExifDataTest
 * @see  xp://net.xp_framework.unittest.img.IptcDataTest
 */
abstract class MetaDataTest extends \unittest\TestCase {

  /**
   * Returns a file for a classloader resource
   *
   * @param   string $name
   * @param   string $sub default NULL subpackage
   * @return  io.File
   */
  protected function resourceAsFile($name, $sub= null) {
    $package= $this->getClass()->getPackage();
    $container= $sub ? $package->getPackage($sub) : $package;
    return $container->getResourceAsStream($name);
  }

  /**
   * Extract from file and return the instance
   *
   * @param   io.File $f
   * @return  lang.Generic the instance
   */
  protected abstract function extractFromFile(\io\File $f);

  #[@test, @expect('img.ImagingException')]
  public function fromNonImageFile() {
    $this->extractFromFile(new \io\File(__FILE__));
  }

  #[@test, @expect('img.ImagingException')]
  public function fromEmptyFile() {
    $this->extractFromFile($this->resourceAsFile('empty.jpg'));
  }
}
