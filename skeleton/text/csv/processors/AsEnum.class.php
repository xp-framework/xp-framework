<?php
/* This class is part of the XP framework
 *
 * $Id: AsEnum.class.php 11504 2009-09-15 13:36:13Z friebe $ 
 */

  uses('text.csv.CellProcessor', 'lang.Enum');

  /**
   * Returns cell values as an enum. Uses the enum's name member to 
   * construct an enumeration member.
   *
   * @test    xp://net.xp_framework.unittest.text.csv.CellProcessorTest
   * @see     xp://text.csv.CellProcessor
   */
  class AsEnum extends CellProcessor {
    protected $enum= NULL;

    /**
     * Creates a new instance of this processor.
     *
     * @param   lang.XPClass<? extends lang.Enum> enum
     * @param   text.csv.CellProcessor if omitted, no further processing will be done
     */
    public function __construct(XPClass $enum, CellProcessor $next= NULL) {
      parent::__construct($next);
      $this->enum= $enum;
    }
    
    /**
     * Processes cell value
     *
     * @param   var in
     * @return  var
     * @throws  lang.FormatException
     */
    public function process($in) {
      try {
        return $this->proceed(Enum::valueOf($this->enum, $in));
      } catch (IllegalArgumentException $e) {
        throw new FormatException($e->getMessage());
      }
    }
  }
?>
