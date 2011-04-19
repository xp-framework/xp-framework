<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'io.streams.ChannelOutputStream',
    'io.streams.ChannelInputStream',
    'net.xp_framework.unittest.io.streams.ChannelWrapper'
  );

  /**
   * TestCase
   *
   * @see      xp://io.streams.ChannelOutputStream
   * @see      xp://io.streams.ChannelInputStream
   * @purpose  purpose
   */
  class ChannelStreamTest extends TestCase {

    /**
     * Test ChannelOutputStream constructed with an invalid channel name
     *
     */
    #[@test, @expect('io.IOException')]
    public function invalidOutputChannelName() {
      new ChannelOutputStream('@@invalid@@');
    }

    /**
     * Test ChannelInputStream constructed with an invalid channel name
     *
     */
    #[@test, @expect('io.IOException')]
    public function invalidInputChannelName() {
      new ChannelInputStream('@@invalid@@');
    }
    
    /**
     * Test "stdin" channel cannot be written to
     *
     */
    #[@test, @expect('io.IOException')]
    public function stdinIsNotAnOutputStream() {
      new ChannelOutputStream('stdin');
    }

    /**
     * Test "input" channel cannot be written to
     *
     */
    #[@test, @expect('io.IOException')]
    public function inputIsNotAnOutputStream() {
      new ChannelOutputStream('input');
    }

    /**
     * Test "stdout" channel cannot be read from
     *
     */
    #[@test, @expect('io.IOException')]
    public function stdoutIsNotAnInputStream() {
      new ChannelInputStream('stdout');
    }

    /**
     * Test "stderr" channel cannot be read from
     *
     */
    #[@test, @expect('io.IOException')]
    public function stderrIsNotAnInputStream() {
      new ChannelInputStream('stderr');
    }

    /**
     * Test "output" channel cannot be read from
     *
     */
    #[@test, @expect('io.IOException')]
    public function outputIsNotAnInputStream() {
      new ChannelInputStream('outpit');
    }

    /**
     * Test writing to a closed channel results in an IOException
     *
     */
    #[@test, @expect('io.IOException')]
    public function writeToClosedChannel() {
      $r= ChannelWrapper::capture(newinstance('lang.Runnable', array(), '{
        public function run() {
          $s= new ChannelOutputStream("output");
          $s->close();
          $s->write("whatever");
        }
      }'));
    }

    /**
     * Test readin from a closed channel results in an IOException
     *
     */
    #[@test, @expect('io.IOException')]
    public function readingFromClosedChannel() {
      $r= ChannelWrapper::capture(newinstance('lang.Runnable', array(), '{
        public function run() {
          $s= new ChannelInputStream("input");
          $s->close();
          $s->read();
        }
      }'));
    }
  
    /**
     * Test "output" channel
     *
     */
    #[@test]
    public function output() {
      $r= ChannelWrapper::capture(newinstance('lang.Runnable', array(), '{
        public function run() {
          $s= new ChannelOutputStream("output");
          $s->write("+OK Hello");
        }
      }'));
        
      $this->assertEquals('+OK Hello', $r['output']);
    }

    /**
     * Test "stdout" channel
     *
     */
    #[@test]
    public function stdout() {
      $r= ChannelWrapper::capture(newinstance('lang.Runnable', array(), '{
        public function run() {
          $s= new ChannelOutputStream("stdout");
          $s->write("+OK Hello");
        }
      }'));
        
      $this->assertEquals('+OK Hello', $r['stdout']);
    }

    /**
     * Test "stderr" channel
     *
     */
    #[@test]
    public function stderr() {
      $r= ChannelWrapper::capture(newinstance('lang.Runnable', array(), '{
        public function run() {
          $s= new ChannelOutputStream("stderr");
          $s->write("+OK Hello");
        }
      }'));
        
      $this->assertEquals('+OK Hello', $r['stderr']);
    }

    /**
     * Test "stdin" channel
     *
     */
    #[@test]
    public function stdin() {
      $r= ChannelWrapper::capture(newinstance('lang.Runnable', array(), '{
        public function run() {
          $i= new ChannelInputStream("stdin");
          $o= new ChannelOutputStream("stdout");
          while ($i->available()) {
            $o->write($i->read());
          }
        }
      }'), array('stdin' => '+OK Piped input'));
        
      $this->assertEquals('+OK Piped input', $r['stdout']);
    }

    /**
     * Test "input" channel
     *
     */
    #[@test]
    public function input() {
      $r= ChannelWrapper::capture(newinstance('lang.Runnable', array(), '{
        public function run() {
          $i= new ChannelInputStream("input");
          $o= new ChannelOutputStream("stdout");
          while ($i->available()) {
            $o->write($i->read());
          }
        }
      }'), array('input' => '+OK Piped input'));
        
      $this->assertEquals('+OK Piped input', $r['stdout']);
    }
  }
?>
