<?php namespace net\xp_framework\unittest\logging;

use util\log\FileAppender;
use io\streams\Streams;
use io\streams\MemoryOutputStream;
use util\log\layout\PatternLayout;


/**
 * TestCase for FileAppender
 *
 * @see   xp://util.log.FileAppender
 */
class FileAppenderTest extends AppenderTest {

  /**
   * Defines stream wrapper
   */
  #[@beforeClass]
  public static function defineStreamWrapper() {
    $sw= \lang\ClassLoader::defineClass('FileAppender_StreamWrapper', 'lang.Object', array(), '{
      public static $buffer= array();
      private $handle;

      public function stream_open($path, $mode, $options, $opened_path) {
        if (strstr($mode, "r")) {
          if (!isset(self::$buffer[$path])) return FALSE;
          self::$buffer[$path][0]= $mode;
          self::$buffer[$path][1]= 0;
        } else if (strstr($mode, "w")) {
          self::$buffer[$path]= array($mode, 0, "", array());
        } else if (strstr($mode, "a")) {
          if (!isset(self::$buffer[$path])) {
            self::$buffer[$path]= array($mode, 0, "", array());
          } else {
            self::$buffer[$path][0]= $mode;
          }
        }
        $this->handle= &self::$buffer[$path];
        return TRUE;
      }

      public function stream_write($data) {
        $this->handle[1]+= strlen($data);
        $this->handle[2].= $data;
        return strlen($data);
      }

      public function stream_read($count) {
        $chunk= substr($this->handle[2], $this->handle[1], $count);
        $this->handle[1]+= strlen($chunk);
        $this->handle[2]= substr($this->handle[2], $this->handle[1]);
        return $chunk;
      }

      public function stream_flush() {
        return TRUE;
      }

      public function stream_seek($offset, $whence) {
        if (SEEK_SET === $whence) {
          $this->handle[1]= $offset;
        } else if (SEEK_END === $whence) {
          $this->handle[1]= strlen($this->handle[2]);
        } else if (SEEK_CUR === $whence) {
          $this->handle[1]+= $offset;
        }
        return 0;   // Success
      }

      public function stream_eof() {
        return $this->handle[1] >= strlen($this->handle[2]);
      }

      public function stream_stat() {
        return array("size" => $this->handle[1]);
      }

      public function stream_close() {
        return TRUE;
      }

      public static function stream_metadata($path, $option, $value) {
        if (!isset(self::$buffer[$path])) return FALSE;
        self::$buffer[$path][3][$option]= $value;
        return TRUE;
      }

      public static function url_stat($path) {
        if (!isset(self::$buffer[$path])) return FALSE;
        return array(
          "size" => strlen(self::$buffer[$path][2]),
          "mode" => self::$buffer[$path][3][STREAM_META_ACCESS]
        );
      }
    }');
    stream_wrapper_register('test', $sw->literal());
  }

  /**
   * Creates new appender fixture
   *
   * @return  util.log.BufferedAppender
   */
  protected function newFixture() {
    return create(new FileAppender('test://file'))->withLayout(new PatternLayout("[%l] %m\n"));
  }

  /**
   * Test append() method
   */
  #[@test]
  public function append_one_message() {
    $fixture= $this->newFixture();
    $fixture->append($this->newEvent(\util\log\LogLevel::WARN, 'Test'));
    $this->assertEquals(
      "[warn] Test\n",
      file_get_contents($fixture->filename)
    );
  }

  /**
   * Test append() method
   */
  #[@test]
  public function append_two_messages() {
    $fixture= $this->newFixture();
    $fixture->append($this->newEvent(\util\log\LogLevel::WARN, 'Test'));
    $fixture->append($this->newEvent(\util\log\LogLevel::INFO, 'Just testing'));
    $this->assertEquals(
      "[warn] Test\n[info] Just testing\n",
      file_get_contents($fixture->filename)
    );
  }

  /**
   * Test append() method
   */
  #[@test]
  public function chmod_called_when_perms_given() {
    if (!defined('STREAM_META_ACCESS')) return;

    $fixture= $this->newFixture();
    $fixture->perms= '0666';  // -rw-rw-rw
    $fixture->append($this->newEvent(\util\log\LogLevel::WARN, 'Test'));
    $this->assertEquals(0666, fileperms($fixture->filename));
  }

  #[@test]
  public function filename_syncs_with_time() {
    $fixture= $this->newFixture();
    $fixture->filename= 'test://file-%H:%M:%I:%S';

    $fn1= $fixture->filename();
    $fixture->append($this->newEvent(\util\log\LogLevel::INFO, 'One'));

    sleep(1);

    $fn2= $fixture->filename();
    $fixture->append($this->newEvent(\util\log\LogLevel::INFO, 'Two'));

    sleep(1);

    $fn3= $fixture->filename();

    $this->assertTrue(file_exists($fn1));
    $this->assertTrue(file_exists($fn2));
    $this->assertFalse(file_exists($fn3));
  }

  #[@test]
  public function filename_does_not_sync_with_time() {
    $fixture= $this->newFixture();
    $fixture->filename= 'test://file-%H:%M:%I:%S';
    $fixture->syncDate= false;

    $fixture->filename();
    $this->assertFalse(strpos($fixture->filename, '%'));
  }
}
