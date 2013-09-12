<?php namespace net\xp_framework\unittest\reflection;

use lang\FileSystemClassLoader;

/**
 * TestCase for classloading
 *
 * @see  xp://lang.FileSystemClassLoader#loadUri
 */
class ClassFromFileSystemTest extends ClassFromUriTest {

  /**
   * Creates fixture
   *
   * @return   lang.IClassLoader
   */
  protected function newFixture() {
    return new FileSystemClassLoader(realpath(self::$base->path()));
  }

  /**
   * Creates underlying base for class loader, e.g. a directory or a .XAR file
   *
   * @return  net.xp_framework.unittest.reflection.ClassFromUriBase
   */
  protected static function baseImpl() {
    return newinstance('net.xp_framework.unittest.reflection.ClassFromUriBase', array(), '{
      protected $t= NULL;

      public function create() {
        $this->t= new \io\Folder(\lang\System::tempDir(), "fsclt");
        $this->t->create();
      }

      public function delete() {
        $this->t->unlink();
      }

      public function newFile($name, $contents) {
        $file= new \io\File($this->t, $name);
        $path= new \io\Folder($file->getPath());
        $path->exists() || $path->create();

        \io\FileUtil::setContents($file, $contents);
      }

      public function path() {
        return rtrim($this->t->getURI(), DIRECTORY_SEPARATOR);
      }
    }');
  }
}
