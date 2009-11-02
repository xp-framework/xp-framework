<?php
/* This class is part of the XP framework
 *
 * $Id: FindDataSet.class.php 11512 2009-09-15 18:13:54Z friebe $
 */

  uses('text.csv.CellProcessor', 'rdbms.finder.FinderMethod');

  /**
   * Returns cell values as a DataSet
   *
   * @test    xp://net.xp_framework.unittest.text.csv.DataSetCellProcessorTest
   * @see     xp://text.csv.CellProcessor
   */
  class FindDataSet extends CellProcessor {
    protected $method= NULL;

    /**
     * Creates a new instance of this processor.
     *
     * @param   rdbms.finder.FinderMethod
     * @param   rdbms.Criteria c if omitted, the peer's primary key is used
     * @param   text.csv.CellProcessor if omitted, no further processing will be done
     */
    public function __construct(FinderMethod $method, CellProcessor $next= NULL) {
      parent::__construct($next);
      $this->method= $method;
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
        return $this->method->getFinder()->find($this->method->invoke(array($in)));
      } catch (FinderException $e) {
        throw new FormatException($e->getMessage());
      }
    }
  }
?>
