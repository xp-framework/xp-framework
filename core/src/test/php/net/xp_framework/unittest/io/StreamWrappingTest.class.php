<?php namespace net\xp_framework\unittest\io;

use unittest\TestCase;
use io\streams\MemoryInputStream;
use io\streams\MemoryOutputStream;
use io\streams\Streams;

/**
 * TestCase
 *
 * @see   xp://io.streams.Streams
 */
class StreamWrappingTest extends TestCase {

  #[@test]
  public function reading() {
    $buffer= 'Hello World';
    $m= new MemoryInputStream($buffer);

    $fd= Streams::readableFd($m);
    $read= fread($fd, strlen($buffer));
    fclose($fd);
    
    $this->assertEquals($buffer, $read);
  }

  #[@test]
  public function endOfFile() {
    $fd= Streams::readableFd(new MemoryInputStream(str_repeat('x', 10)));
    $this->assertFalse(feof($fd), 'May not be at EOF directly after opening');

    fread($fd, 5);
    $this->assertFalse(feof($fd), 'May not be at EOF after reading only half of the bytes');

    fread($fd, 5);
    $this->assertTrue(feof($fd), 'Must be at EOF after having read all of the bytes');

    fclose($fd);
  }

  #[@test]
  public function fstat() {
    $fd= Streams::readableFd(new MemoryInputStream(str_repeat('x', 10)));
    $stat= fstat($fd);
    $this->assertEquals(10, $stat['size']);

    fread($fd, 5);
    $stat= fstat($fd);
    $this->assertEquals(10, $stat['size']);
    
    fclose($fd);
  }

  #[@test]
  public function statExistingReadableUri() {
    $uri= Streams::readableUri(new MemoryInputStream(str_repeat('x', 10)));
    $stat= stat($uri);
    $this->assertEquals(0, $stat['size']);
  }

  #[@test]
  public function statExistingWriteableUri() {
    $uri= Streams::writeableUri(new MemoryOutputStream());
    $stat= stat($uri);
    $this->assertEquals(0, $stat['size']);
  }

  #[@test]
  public function statNonExistingReadableUri() {
    $uri= Streams::readableUri(new MemoryInputStream(str_repeat('x', 10)));
    fclose(fopen($uri, 'r'));
    $this->assertFalse(@stat($uri));
  }

  #[@test]
  public function statNonExistingWriteableUri() {
    $uri= Streams::writeableUri(new MemoryOutputStream());
    fclose(fopen($uri, 'w'));
    $this->assertFalse(@stat($uri));
  }

  #[@test]
  public function tellFromReadable() {
    $fd= Streams::readableFd(new MemoryInputStream(str_repeat('x', 10)));
    $this->assertEquals(0, ftell($fd));

    fread($fd, 5);
    $this->assertEquals(5, ftell($fd));

    fread($fd, 5);
    $this->assertEquals(10, ftell($fd));
    
    fclose($fd);
  }

  #[@test]
  public function tellFromWriteable() {
    $fd= Streams::writeableFd(new MemoryOutputStream());
    $this->assertEquals(0, ftell($fd));

    fwrite($fd, str_repeat('x', 5));
    $this->assertEquals(5, ftell($fd));

    fwrite($fd, str_repeat('x', 5));
    $this->assertEquals(10, ftell($fd));
    
    fclose($fd);
  }

  #[@test]
  public function writing() {
    $buffer= 'Hello World';
    $m= new MemoryOutputStream();

    $fd= Streams::writeableFd($m);
    $written= fwrite($fd, $buffer);
    fclose($fd);
    
    $this->assertEquals(strlen($buffer), $written);
    $this->assertEquals($buffer, $m->getBytes());
  }

  #[@test, @expect('io.IOException')]
  public function reading_from_writeable_fd_raises_exception() {
    $fd= Streams::writeableFd(new MemoryOutputStream());
    fread($fd, 1024);
  }

  #[@test, @expect('io.IOException')]
  public function writing_to_readable_fd_raises_exception() {
    $fd= Streams::readableFd(new MemoryInputStream(''));
    fwrite($fd, 1024);
  }

  #[@test, @values(['', 'Hello', "Hello\nWorld\n"])]
  public function readAll($value) {
    $this->assertEquals($value, Streams::readAll(new MemoryInputStream($value)));
  }

  #[@test, @expect('io.IOException')]
  public function readAll_propagates_exception() {
    Streams::readAll(newinstance('io.streams.InputStream', array(), '{
      public function read($limit= 8192) { throw new IOException("FAIL"); }
      public function available() { return 1; }
      public function close() { }
    }'));
  }

  #[@test]
  public function read_while_not_eof() {
    $fd= Streams::readableFd(new MemoryInputStream(str_repeat('x', 1024)));
    $l= array();
    while (!feof($fd)) {
      $c= fread($fd, 128);
      $l[]= strlen($c);
    }
    fclose($fd);
    $this->assertEquals(array(128, 128, 128, 128, 128, 128, 128, 128), $l);
  }

  #[@test, @values([0, 10, 10485760])]
  public function file_get_contents($length) {
    $data= str_repeat('x', $length);
    $this->assertEquals(
      $data,
      file_get_contents(Streams::readableUri(new MemoryInputStream($data)))
    );
  }

  #[@test]
  public function is_file() {
    $this->assertTrue(is_file(Streams::readableUri(new MemoryInputStream('Hello'))));
  }
}
