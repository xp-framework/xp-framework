<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'util.cmd.Command',
    'xp.command.Runner',
    'io.streams.MemoryOutputStream'
  );

  /**
   * TestCase
   *
   * @purpose  Unittest
   */
  class RunnerTest extends TestCase {
    protected
      $runner = NULL,
      $out    = NULL,
      $err    = NULL;

    /**
     * Sets up test case
     *
     */
    public function setUp() {
      $this->runner= new xp·command·Runner();
    }
    
    /**
     * Run with given args
     *
     * @param   string[] args
     * @return  int
     */
    protected function runWith(array $args) {
      $this->out= $this->runner->setOut(new MemoryOutputStream());
      $this->err= $this->runner->setErr(new MemoryOutputStream());
      return $this->runner->run(new ParamString($args));
    }

    /**
     * Asserts a given output stream contains the given bytes       
     *
     * @param   io.streams.MemoryOutputStream m
     * @param   string bytes
     * @throws  unittest.AssertionFailedError
     */
    protected function assertOnStream(MemoryOutputStream $m, $bytes, $message= 'Not contained') {
      strstr($m->getBytes(), $bytes) || $this->fail($message, $m->getBytes(), $bytes);
    }
    
    /**
     * Returns a simple command instance
     *
     * @return  util.cmd.Command
     */
    protected function newCommand() {
      return newinstance('util.cmd.Command', array(), '{
        public static $wasRun= FALSE;
        public function __construct() { self::$wasRun= FALSE; }
        public function run() { self::$wasRun= TRUE; }
        public function wasRun() { return self::$wasRun; }
      }');
    }
    
    /**
     * Test self usage - that is, when xpcli is invoked without any 
     * arguments
     *
     */
    #[@test]
    public function selfUsage() {
      $return= $this->runWith(array());
      $this->assertEquals(1, $return);
      $this->assertOnStream($this->err, 'Usage:');
      $this->assertEquals('', $this->out->getBytes());
    }

    /**
     * Test when invoked with a non-existant class
     *
     */
    #[@test]
    public function nonExistantClass() {
      $return= $this->runWith(array('@@NON-EXISTANT@@'));
      $this->assertEquals(1, $return);
      $this->assertOnStream($this->err, '*** No classloader provides class');
      $this->assertEquals('', $this->out->getBytes());
    }

    /**
     * Test when invoked with a non-existant class
     *
     */
    #[@test]
    public function nonExistantFile() {
      $return= $this->runWith(array('@@NON-EXISTANT@@.'.xp::CLASS_FILE_EXT));
      $this->assertEquals(1, $return);
      $this->assertTrue((bool)strstr($this->err->getBytes(), '*** Cannot load class from non-existant file'));
      $this->assertEquals('', $this->out->getBytes());
    }

    /**
     * Test when invoked with a class that does not implement the Runnable
     * interface
     *
     */
    #[@test]
    public function notRunnableClass() {
      $return= $this->runWith(array($this->getClassName()));
      $this->assertEquals(1, $return);
      $this->assertOnStream($this->err, '*** '.$this->getClassName().' is not runnable');
      $this->assertEquals('', $this->out->getBytes());
    }
    
    /**
     * Test class usage - that is, when xpcli is invoked with a
     * class name and "-?"
     *
     */
    #[@test]
    public function shortClassUsage() {
      $command= $this->newCommand();
      $return= $this->runWith(array($command->getClassName(), '-?'));
      $this->assertEquals(0, $return);
      $this->assertOnStream($this->err, 'Usage: $ xpcli '.$command->getClassName());
      $this->assertEquals('', $this->out->getBytes());
      $this->assertFalse($command->wasRun());
    }

    /**
     * Test class usage - that is, when xpcli is invoked with a
     * class name and "--help"
     *
     */
    #[@test]
    public function longClassUsage() {
      $command= $this->newCommand();
      $return= $this->runWith(array($command->getClassName(), '--help'));
      $this->assertEquals(0, $return);
      $this->assertOnStream($this->err, 'Usage: $ xpcli '.$command->getClassName());
      $this->assertEquals('', $this->out->getBytes());
      $this->assertFalse($command->wasRun());
    }

    /**
     * Test most simple form of running - no arguments, no injection.
     *
     */
    #[@test]
    public function runCommand() {
      $command= $this->newCommand();
      $return= $this->runWith(array($command->getClassName()));
      $this->assertEquals(0, $return);
      $this->assertEquals('', $this->err->getBytes());
      $this->assertEquals('', $this->out->getBytes());
      $this->assertTrue($command->wasRun());
    }

    /**
     * Test a command that outputs the word "UNITTEST" to standard output
     *
     */
    #[@test]
    public function runWritingToStandardOutput() {
      $command= newinstance('util.cmd.Command', array(), '{
        public function run() { $this->out->write("UNITTEST"); }
      }');

      $return= $this->runWith(array($command->getClassName()));
      $this->assertEquals(0, $return);
      $this->assertEquals('', $this->err->getBytes());
      $this->assertEquals('UNITTEST', $this->out->getBytes());
    }

    /**
     * Test a command that outputs the word "UNITTEST" to standard error
     *
     */
    #[@test]
    public function runWritingToStandardError() {
      $command= newinstance('util.cmd.Command', array(), '{
        public function run() { $this->err->write("UNITTEST"); }
      }');

      $return= $this->runWith(array($command->getClassName()));
      $this->assertEquals(0, $return);
      $this->assertEquals('UNITTEST', $this->err->getBytes());
      $this->assertEquals('', $this->out->getBytes());
    }

    /**
     * Test a command that receives a positional argument
     *
     */
    #[@test]
    public function positionalArgument() {
      $command= newinstance('util.cmd.Command', array(), '{
        protected $arg= NULL;

        #[@arg(position= 0)]
        public function setArg($arg) { $this->arg= $arg; }
        public function run() { $this->out->write($this->arg); }
      }');

      $return= $this->runWith(array($command->getClassName(), 'UNITTEST'));
      $this->assertEquals(0, $return);
      $this->assertEquals('', $this->err->getBytes());
      $this->assertEquals('UNITTEST', $this->out->getBytes());
    }

    /**
     * Test a command that receives a named positional for the situation 
     * that this argument is missing
     *
     */
    #[@test]
    public function missingPositionalArgumentt() {
      $command= newinstance('util.cmd.Command', array(), '{
        protected $arg= NULL;

        #[@arg(position= 0)]
        public function setArg($arg) { $this->arg= $arg; }
        public function run() { throw new AssertionFailedError("Should not be executed"); }
      }');

      $return= $this->runWith(array($command->getClassName()));
      $this->assertEquals(2, $return);
      $this->assertOnStream($this->err, '*** Argument #1 does not exist');
      $this->assertEquals('', $this->out->getBytes());
    }

    /**
     * Test a command that receives a short named argument (-a value)
     *
     */
    #[@test]
    public function shortNamedArgument() {
      $command= newinstance('util.cmd.Command', array(), '{
        protected $arg= NULL;

        #[@arg]
        public function setArg($arg) { $this->arg= $arg; }
        public function run() { $this->out->write($this->arg); }
      }');

      $return= $this->runWith(array($command->getClassName(), '-a', 'UNITTEST'));
      $this->assertEquals(0, $return);
      $this->assertEquals('', $this->err->getBytes());
      $this->assertEquals('UNITTEST', $this->out->getBytes());
    }

    /**
     * Test a command that receives a long named argument (--arg=value)
     *
     */
    #[@test]
    public function longNamedArgument() {
      $command= newinstance('util.cmd.Command', array(), '{
        protected $arg= NULL;

        #[@arg]
        public function setArg($arg) { $this->arg= $arg; }
        public function run() { $this->out->write($this->arg); }
      }');

      $return= $this->runWith(array($command->getClassName(), '--arg=UNITTEST'));
      $this->assertEquals(0, $return);
      $this->assertEquals('', $this->err->getBytes());
      $this->assertEquals('UNITTEST', $this->out->getBytes());
    }

    /**
     * Test a command that receives a short named argument (-p value)
     * which is declared with another name
     *
     */
    #[@test]
    public function shortRenamedArgument() {
      $command= newinstance('util.cmd.Command', array(), '{
        protected $arg= NULL;

        #[@arg(name= "pass")]
        public function setArg($arg) { $this->arg= $arg; }
        public function run() { $this->out->write($this->arg); }
      }');

      $return= $this->runWith(array($command->getClassName(), '-p', 'UNITTEST'));
      $this->assertEquals(0, $return);
      $this->assertEquals('', $this->err->getBytes());
      $this->assertEquals('UNITTEST', $this->out->getBytes());
    }

    /**
     * Test a command that receives a long named argument (--pass=value)
     * which is declared with another name
     */
    #[@test]
    public function longRenamedArgument() {
      $command= newinstance('util.cmd.Command', array(), '{
        protected $arg= NULL;

        #[@arg(name= "pass")]
        public function setArg($arg) { $this->arg= $arg; }
        public function run() { $this->out->write($this->arg); }
      }');

      $return= $this->runWith(array($command->getClassName(), '--pass=UNITTEST'));
      $this->assertEquals(0, $return);
      $this->assertEquals('', $this->err->getBytes());
      $this->assertEquals('UNITTEST', $this->out->getBytes());
    }

    /**
     * Test a command that receives a named argument for the situation 
     * that this argument is missing
     *
     */
    #[@test]
    public function missingNamedArgument() {
      $command= newinstance('util.cmd.Command', array(), '{
        protected $arg= NULL;

        #[@arg]
        public function setArg($arg) { $this->arg= $arg; }
        public function run() { throw new AssertionFailedError("Should not be executed"); }
      }');

      $return= $this->runWith(array($command->getClassName()));
      $this->assertEquals(2, $return);
      $this->assertOnStream($this->err, '*** Argument arg does not exist');
      $this->assertEquals('', $this->out->getBytes());
    }

    /**
     * Test a command that receives an existance argument not passed
     *
     */
    #[@test]
    public function existanceArgumentNotPassed() {
      $command= newinstance('util.cmd.Command', array(), '{
        protected $verbose= FALSE;

        #[@arg]
        public function setVerbose() { $this->verbose= TRUE; }
        public function run() { $this->out->write($this->verbose ? "true" : "false"); }
      }');

      $return= $this->runWith(array($command->getClassName()));
      $this->assertEquals(0, $return);
      $this->assertEquals('', $this->err->getBytes());
      $this->assertEquals('false', $this->out->getBytes());
    }

    /**
     * Test a command that receives an optional argument not passed
     *
     */
    #[@test]
    public function optionalArgument() {
      $command= newinstance('util.cmd.Command', array(), '{
        protected $verbose= FALSE;

        #[@arg]
        public function setName($name= "unknown") { $this->name= $name; }
        public function run() { $this->out->write($this->name); }
      }');

      $return= $this->runWith(array($command->getClassName(), '-n', 'UNITTEST'));
      $this->assertEquals(0, $return);
      $this->assertEquals('', $this->err->getBytes());
      $this->assertEquals('UNITTEST', $this->out->getBytes());
    }

    /**
     * Test a command that receives an optional argument not passed
     *
     */
    #[@test]
    public function optionalArgumentNotPassed() {
      $command= newinstance('util.cmd.Command', array(), '{
        protected $verbose= FALSE;

        #[@arg]
        public function setName($name= "unknown") { $this->name= $name; }
        public function run() { $this->out->write($this->name); }
      }');

      $return= $this->runWith(array($command->getClassName()));
      $this->assertEquals(0, $return);
      $this->assertEquals('', $this->err->getBytes());
      $this->assertEquals('unknown', $this->out->getBytes());
    }

    /**
     * Test a command that receives an existance argument passed as 
     * short option (-v)
     *
     */
    #[@test]
    public function shortExistanceArgumentPassed() {
      $command= newinstance('util.cmd.Command', array(), '{
        protected $verbose= FALSE;

        #[@arg]
        public function setVerbose() { $this->verbose= TRUE; }
        public function run() { $this->out->write($this->verbose ? "true" : "false"); }
      }');

      $return= $this->runWith(array($command->getClassName(), '-v'));
      $this->assertEquals(0, $return);
      $this->assertEquals('', $this->err->getBytes());
      $this->assertEquals('true', $this->out->getBytes());
    }

    /**
     * Test a command that receives an existance argument passed as 
     * short option (-v)
     *
     */
    #[@test]
    public function longExistanceArgumentPassed() {
      $command= newinstance('util.cmd.Command', array(), '{
        protected $verbose= FALSE;

        #[@arg]
        public function setVerbose() { $this->verbose= TRUE; }
        public function run() { $this->out->write($this->verbose ? "true" : "false"); }
      }');

      $return= $this->runWith(array($command->getClassName(), '--verbose'));
      $this->assertEquals(0, $return);
      $this->assertEquals('', $this->err->getBytes());
      $this->assertEquals('true', $this->out->getBytes());
    }
    
    /**
     * Assertion helper for "args" annotation tests
     *
     * @param   string args
     * @param   util.cmd.Command command
     */
    protected function assertAllArgs($args, Command $command) {
      $return= $this->runWith(array($command->getClassName(), 'a', 'b', 'c', 'd', 'e', 'f', 'g'));
      $this->assertEquals(0, $return);
      $this->assertEquals('', $this->err->getBytes());
      $this->assertEquals($args, $this->out->getBytes());
    }

    /**
     * Test a command that receives all arguments via "args" annotation,
     * selecting all via [0..]
     *
     */
    #[@test]
    public function allArgs() {
      $this->assertAllArgs('a, b, c, d, e, f, g', newinstance('util.cmd.Command', array(), '{
        protected $verbose= FALSE;

        #[@args(select= "[0..]")]
        public function setArgs($args) { $this->args= $args; }
        public function run() { $this->out->write(implode(", ", $this->args)); }
      }'));
    }

    /**
     * Test a command that receives all arguments via "args" annotation,
     * selecting all via *
     *
     */
    #[@test]
    public function allArgsCompactNotation() {
      $this->assertAllArgs('a, b, c, d, e, f, g', newinstance('util.cmd.Command', array(), '{
        protected $verbose= FALSE;

        #[@args(select= "*")]
        public function setArgs($args) { $this->args= $args; }
        public function run() { $this->out->write(implode(", ", $this->args)); }
      }'));
    }
 
    /**
     * Test a command that receives all arguments via "args" annotation,
     * selecting offsets 0, 1 and 2 via [0..2]
     *
     */
    #[@test]
    public function boundedArgs() {
      $this->assertAllArgs('a, b, c', newinstance('util.cmd.Command', array(), '{
        protected $verbose= FALSE;

        #[@args(select= "[0..2]")]
        public function setArgs($args) { $this->args= $args; }
        public function run() { $this->out->write(implode(", ", $this->args)); }
      }'));
    }

    /**
     * Test a command that receives all arguments via "args" annotation,
     * selecting offsets 2, 3 and 4 via [2..4]
     *
     */
    #[@test]
    public function boundedArgsFromOffset() {
      $this->assertAllArgs('c, d, e', newinstance('util.cmd.Command', array(), '{
        protected $verbose= FALSE;

        #[@args(select= "[2..4]")]
        public function setArgs($args) { $this->args= $args; }
        public function run() { $this->out->write(implode(", ", $this->args)); }
      }'));
    }

    /**
     * Test a command that receives all arguments via "args" annotation,
     * selecting offsets 0, 2, 3 and 4 via 0, [2..4]
     *
     */
    #[@test]
    public function positionalAndBoundedArgsFromOffset() {
      $this->assertAllArgs('a, c, d, e', newinstance('util.cmd.Command', array(), '{
        protected $verbose= FALSE;

        #[@args(select= "0, [2..4]")]
        public function setArgs($args) { $this->args= $args; }
        public function run() { $this->out->write(implode(", ", $this->args)); }
      }'));
    }

    /**
     * Test a command that receives all arguments via "args" annotation,
     * selecting offsets 0, 1, 2 and 2 (again)
     *
     */
    #[@test]
    public function boundedAndPositionalArgsWithOverlap() {
      $this->assertAllArgs('a, b, c, b', newinstance('util.cmd.Command', array(), '{
        protected $verbose= FALSE;

        #[@args(select= "[0..2], 1")]
        public function setArgs($args) { $this->args= $args; }
        public function run() { $this->out->write(implode(", ", $this->args)); }
      }'));
    }
 
    /**
     * Test a command that receives all arguments via "args" annotation,
     * selecting offsets 0, 2, 4 and 5
     *
     */
    #[@test]
    public function positionalArgs() {
      $this->assertAllArgs('a, c, e, f', newinstance('util.cmd.Command', array(), '{
        protected $verbose= FALSE;

        #[@args(select= "0, 2, 4, 5")]
        public function setArgs($args) { $this->args= $args; }
        public function run() { $this->out->write(implode(", ", $this->args)); }
      }'));
    }

    /**
     * Test xpcli -c option does not conflict with a Command class -c option.
     *
     */
    #[@test]
    public function configOption() {
      $command= newinstance('util.cmd.Command', array(), '{
        protected $choke= FALSE;

        #[@arg]
        public function setChoke() { 
          $this->choke= TRUE; 
        }
        
        public function run() { 
          $this->out->write($this->choke ? "true" : "false"); 
        }
      }');
      $return= $this->runWith(array('-c', 'etc', $command->getClassName(), '-c'));
      $this->assertEquals(0, $return);
      $this->assertEquals('', $this->err->getBytes());
      $this->assertEquals('true', $this->out->getBytes());
    }

    /**
     * Test xpcli -cp option does not conflict with a Command class -cp option.
     *
     */
    #[@test]
    public function classPathOption() {
      $command= newinstance('util.cmd.Command', array(), '{
        protected $copy= NULL;
        
        #[@arg(short= "cp")]
        public function setCopy($copy) { 
          $this->copy= Package::forName("net.xp_forge.instructions")->loadClass($copy); 
        }
        
        public function run() { 
          $this->out->write($this->copy); 
        }
      }');
      $return= $this->runWith(array('-cp', dirname(__FILE__).'/instructions.xar', $command->getClassName(), '-cp', 'Copy'));
      $this->assertEquals(0, $return);
      $this->assertEquals('', $this->err->getBytes());
      $this->assertEquals('lang.XPClass<net.xp_forge.instructions.Copy>', $this->out->getBytes());
    }
  }
?>
