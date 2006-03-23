<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  uses ('io.Stream');

  /**
   * This class can be used to easily create correct csv-files.
   * It handles escaping of special characters and thus creates
   * csv-files, that can be used to be exchanged with other OSes
   * 
   * @see     xp://text.parser.CSVParser
   * @purpose Small and simple CSV Generator
   */ 
  class CSVGenerator extends Object {
    var
      $stream;
      
    var
      $colDelim= '|',
      $lineDelim = "\n",  // on unix-based systems we expect an \n as delimiter
      $escape= '"';
    
    var
      $colName;
      
    var
      $headerWritten= FALSE,
      $delimWritten= TRUE;
    
    /**
     * Set the output stream. The stream must be writeable. If the
     * stream is not open, it will be opened.
     *
     * @access  public
     * @param   stream stream
     * @return  bool success
     */    
    function setOutputStream(&$stream) {
      try(); {
        if (!$stream->isOpen()) $stream->open (STREAM_MODE_WRITE);
        $this->stream= &$stream;
      } if (catch('Exception', $e)) {
        return throw ($e);
      }
      return true;
    }
    
    /**
     * Sets another column delimiter (standard is pipe "|").
     *
     * @access  public
     * @param   char delim
     */
    function setColDelimiter($delim) {
      $this->colDelim= $delim{0};
    }
    
    /**
     * Sets another line delimiter (standard is "\n")
     * 
     * if we want to generate files for other oses as unix, we need to change the delimiter
     * e.g. windows: "\r\n"
     *
     * @access public 
     * @param  string delim 
     */
    function setLineDelimiter($delim) {
      $this->lineDelim= $delim; // could be more than one character
    }

    /**
     * Sets the header information. The keys in this array will be
     * used to write the records, so be sure they are named exactly
     * as the data.
     *
     * @access  public
     * @param   array header
     */    
    function setHeader($array) {
      $this->colName= $array;
      $this->headerWritten= FALSE;
    }

    /**
     * Returns whether we have header information available
     *
     * @access  private
     * @return  bool hasHeader
     */    
    function _hasHeader() {
      return (isset ($this->colName) && !empty ($this->colName));
    }

    /**
     * Writes the header line.
     *
     * @access  private
     */    
    function _writeHeader() {
      $this->stream->write(
        implode ($this->colDelim, array_values ($this->colName))
      );
      // Insert Newline
      $this->stream->write($this->lineDelim);
      $this->headerWritten= TRUE;
    }

    /**
     * Write a single column into the stream. This function takes
     * care of quotedness and escaping.
     *
     * @access  private
     * @param   string data
     */    
    function _writeColumn($data= '') {
      if (!$this->delimWritten) $this->stream->write ($this->colDelim);
      $this->delimWritten= FALSE;

      if (0 == strlen ($data)) {
        return;
      }
      
      $mustQuote= false;
      if (FALSE !== strstr ($data, $this->colDelim)) $mustQuote= true;
      if (FALSE !== strstr ($data, $this->escape)) $mustQuote= true;
      if (FALSE !== strstr ($data, "\n")) $mustQuote= true;
      
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
    function writeRecord($data) {
      if ($this->_hasHeader() && !$this->headerWritten)
        $this->_writeHeader();
    
      $cols= array_keys ($data);
      
      if ($this->_hasHeader())
        $cols= array_keys ($this->colName);
    
      foreach ($cols as $idx => $colName) {
        if (isset ($data[$colName]))
          $this->_writeColumn ($data[$colName]);
        else
          $this->_writeColumn('');
      }
      
      // Insert Newline
      $this->stream->write($this->lineDelim);
      $this->delimWritten= TRUE;
    }
  
  
  }

?>
