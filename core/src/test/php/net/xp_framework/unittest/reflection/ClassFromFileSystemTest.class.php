<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'net.xp_framework.unittest.reflection.ClassFromUriTest',
    'lang.FileSystemClassLoader',
    'io.File',
    'io.FileUtil',
    'io.Folder',
    'lang.System'
  );

  /**
   * TestCase for classloading
   */
  class ClassFromFileSystemTest extends ClassFromUriTest {

    /**
     * Creates fixture
     *
     * @return   lang.IClassLoader
     */
    protected function newFixture() {
      return new FileSystemClassLoader(self::$base->path());
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
          $this->t= new Folder(System::tempDir(), "fsclt");
          $this->t->create();
        }

        public function delete() {
          $this->t->unlink();
        }

        public function newFile($name, $contents) {
          $file= new File($this->t, $name);
          $path= new Folder($file->getPath());
          $path->exists() || $path->create();

          FileUtil::setContents($file, $contents);
        }

        public function path() {
          return rtrim($this->t->getURI(), DIRECTORY_SEPARATOR);
        }
      }');
    }
  }
?>
