<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('io.Stream');

  /**
   * This class can be used to easily create correct csv-files.
   * It handles escaping of special characters and thus creates
   * csv-files, that can be used to be exchanged with other OSes
   * 
   * @see     xp://text.parser.CSVParser
   * @purpose Small and simple CSV Generator
   */ 
  class CSVGenerator extends Object {
    public
      $stream;
      
    public
      $colDelim= '|',
      $escape= '"';
    
    public
      $colName;
      
    public
      $headerWritten= false,
      $delimWritten= true;
    
    /**
     * Construct a CSVGenerator
     *
     * @access public
     */
    public function __construct() {
    }

    /**
     * Set the output stream. The stream must be writeable. If the
     * stream is not open, it will be opened.
     *
     * @access  public
     * @param   stream stream
     * @return  bool success
     */    
    public function setOutputStream(&$stream) {
      try {
        if (!$stream->isOpen()) $stream->open (STREAM_MODE_WRITE);
        $this->stream= $stream;
      } catch (XPException $e) {
        throw  ($e);
      }
      return true;
    }
    
    /**
     * Sets another column delimiter (standard is pipe "|").
     *
     * @access  public
     * @param   char delim
     */
    public function setColDelimiter($delim) {
      $this->colDelim= $delim{0};
    }

    /**
     * Sets the header information. The keys in this array will be
     * used to write the records, so be sure they are named exactly
     * as the data.
     *
     * @access  public
     * @param   array header
     */    
    public function setHeader($array) {
      $this->colName= $array;
      $this->headerWritten= false;
    }

    /**
     * Returns whether we have header information available
     *
     * @access  private
     * @return  bool hasHeader
     */    
    private function _hasHeader() {
      return (isset ($this->colName) && !empty ($this->colName));
    }

    /**
     * Writes the header line.
     *
     * @access  private
     */    
    private function _writeHeader() {
      $this->stream->writeLine (
        implode ($this->colDelim, array_values ($this->colName))
      );
      $this->headerWritten= true;
    }

    /**
     * Write a single column into the stream. This function takes
     * care of quotedness and escaping.
     *
     * @access  private
     * @param   string data
     */    
    private function _writeColumn($data= '') {
      if (!$this->delimWritten) $this->stream->write ($this->colDelim);
      $this->delimWritten= false;

      if (0 == strlen ($data)) {
        return;
      }
      
      $mustQuote= false;
      if (false !== strstr ($data, $this->colDelim)) $mustQuote= true;
      if (false !== strstr ($data, $this->escape)) $mustQuote= true;
      if (false !== strstr ($data, "\n")) $mustQuote= true;
      
      if ($mustQuote) {
        $data= '"'.str_replace ($this->escape, $this->escape.$this->escape, $data).'"';
      }
      
      $this->stream->write ($data);
    }

    /**
     * Writes a record into the stream.
     *
     * @access  public
     * @param   array data
     * @throws  Exception e if any error occurs
     */    
    public function writeRecord($data) {
      if (self::_hasHeader() && !$this->headerWritten)
        self::_writeHeader();
    
      $cols= array_keys ($data);
      
      if (self::_hasHeader())
        $cols= array_keys ($this->colName);
    
      foreach ($cols as $idx=> $colName) {
        if (isset ($data[$colName]))
          $this->_writeColumn ($data[$colName]);
        else
          self::_writeColumn('');
      }
      
      $this->stream->writeLine('');
      $this->delimWritten= true;
    }
  
  
  }
?>
