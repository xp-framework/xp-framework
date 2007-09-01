<?php
/* This class is part of the XP framework
 *
 * $Id: CSVParser.class.php 10594 2007-06-11 10:04:54Z friebe $
 */

  namespace text::parser;

  uses('io.Stream');
  
  /**
   * CSVParser provides comfortable way to parse csv (comma separated
   * value) - files. This class proposes "|" as default delimiter, tough.
   *
   * @purpose Interface for parsing CSV-files
   * @see     http://www.creativyst.com/Doc/Articles/CSV/CSV01.htm
   * @test    xp://net.xp_framework.unittest.text.parser.CSVParserTest
   */
  class CSVParser extends lang::Object {
    public
      $stream=    NULL,
      $hasHeader= FALSE,
      $colDelim=  '|',
      $escape=    '"',
      $colName=   NULL,
      $buffer=    '';
    
    /**
     * Tokenizes a string according to our needs.
     *
     * @param   string string string to take token of
     * @param   string delim delimiter
     * @return  string token
     */
    protected function _strtok(&$string, $delim) {

      // Note: don't use builtin strtok, because it does ignore an
      // empty field (two delimiters in a row). We need this information.
      if (empty($string)) return FALSE;
        
      if (FALSE === ($tpos= strpos($string, $delim))) {
        $token= $string;
        $string= '';
        return $token;
      }
      
      $token= substr($string, 0, $tpos);
      $string= substr($string, strlen($token)+1);
      return $token;
    }
    
    /**
     * Sets the input stream. This stream must support
     * isOpen(), open(), eof(), readLine().
     *
     * @param   Stream stream
     */    
    public function setInputStream($stream) {
      try {
        if (!$stream->isOpen()) $stream->open();
      } catch (io::IOException $e) {
        throw ($e);
      }
      $this->stream= $stream;
    }
    
    /**
     * Sets the new delimiter for columns. Once CSVs had comma "," as
     * delimiters, today this varies. The pipe "|" is often used
     * as delimiter. It only makes sense to call this before any
     * line was read.
     *
     * @param   char delimiter delimiter to set
     */
    public function setColDelimiter($delim) {
      $this->colDelim= $delim{0};
    }

    /**
     * Sets the new escape-delimiter for columns. It is used to quote
     * Strings with special chars e.g. " which is the default escape-delimiter.
     * It only makes sense to call this before any line was read. 
     *
     * @param   char escape-delimiter escape-delimiter to set
     */
    public function setEscapeDelimiter($escape) {
      $this->escape= $escape{0};
    }
    
    /**
     * Make an educated guess for the column delimiter. This reads
     * the next (first?) line from the stream, scans the frequency of
     * chars and returns the one with the highest occurrence (which
     * is in a certain range of chars).
     * Afterwards the stream is rewinded to the former position.
     *
     * @return  string guesseddelimiter
     */
    public function guessDelimiter() {
      $pos= $this->stream->tell();
      $line= $this->_getNextRecord();
      
      $freq= count_chars($line, 1);
      $max= 0; $ret= NULL;
      foreach ($freq as $chr => $f) {
        if (
          $f > $max && 
          !($chr > 65 && $chr < 90) &&
          !($chr > 96 && $chr < 123)
        ) {
          $ret= chr($chr); $max= $f; 
        }
      }
      
      // Rewind to former position
      $this->stream->seek($pos);
      return $ret;
    }

    /**
     * Returns whether the header record has already been read.
     * There is no information in the CSV itself that states whether
     * an header record is available, so this has to be decided by
     * the calling program (or user).
     *
     * @return  bool hasHeader TRUE, if header is available
     */
    public function hasHeader() {
      return is_array($this->colName) && !empty($this->colName);
    }
    
    /**
     * Checks whether a certain column exists in the csv.
     * If not existColumn return FALSE, otherwise the index
     * of the column.
     *
     * @param   string columnname
     * @return  int columnindex
     */    
    public function getColumnIndex($column) {
      $reverse= array_flip($this->colName);
      if (!isset($reverse[$column])) return FALSE; else return $reverse[$column];
    }
    
    /**
     * Reads as many lines as necessary from the stream until there is 
     * exactly one record in the buffer.
     * This function affects the member buffer.
     *
     * @return  string buffer
     */    
    protected function _getNextRecord() {
      try {
        if ($this->stream->eof()) return FALSE;

        $row= $this->stream->readLine();
        while (0 !== substr_count($row, $this->escape) % 2)
          $row.= "\n".$this->stream->readLine();
      
        $this->buffer= $row;
      } catch (io::IOException $e) {
        throw ($e);
      }
      
      return $this->buffer;
    }

    /**
     * Parse the next cell out of the buffer. If buffer is empty,
     * this returns FALSE. This function takes care of
     * quotedness of the data, and de-escapes any escaped chars.
     * It also removes the parsed cell from the internal buffer.
     *
     * @return  string buffer
     */
    protected function _parseColumn() {
      if (empty($this->buffer)) return FALSE;

      $tok= $this->_strtok($this->buffer, $this->colDelim);

      // Trick: when there is an odd number of escape characters
      // you know that this found delimiter is part of a string inside
      // the payload. Search for the next, until you have an even number 
      // of escapers (then all quoted parts are closed).
      while (0 !== substr_count($tok, $this->escape) % 2) {
        $add= $this->colDelim.$this->_strtok($this->buffer, $this->colDelim);
        $tok.= $add;
      }

      // Single escape characters become nothing, double escape
      // characters become the escape character itself.
      $tok= trim ($tok);
      $i= 0; $j= 0; $res= '';
      while (FALSE !== ($i= strpos($tok, $this->escape, $j))) {
        if (strlen($tok) > $i+1 && $tok{$i+1} == $this->escape) $i++;
        $res.= substr($tok, $j, $i-$j);
        $j= $i+1;
      }
      
      if (empty($res)) return $tok; else return $res;
    }
    
    /**
     * Read the record and save the result as the header record.
     *
     */    
    public function getHeaderRecord() {
      $this->colName= $this->getNextRecord();
    }
    
    /**
     * Manually set the header information to be able to supply
     * additional information and get nicer output (non-enumerated)
     *
     * @param   array headers
     */    
    public function setHeaderRecord($headers) {
      $this->colName= $headers;
    }
    
    /**
     * Retrieves the name of a column if one is available
     *
     * @param   int number
     * @return  string name or FALSE if none is available
     */
    public function getColumnName($nr) {
      if (!$this->hasHeader()) return FALSE;
      if (!isset($this->colName[$nr])) return FALSE;
      
      return $this->colName[$nr];
    }    
    
    /**
     * Read the next record from the stream. This returns a 
     * StdClass object with the members named as the header
     * record supposes. When no header was available, the
     * fields are enumerated.
     *
     * @return  array data
     * @throws  io.IOException if stream operation failed
     */    
    public function getNextRecord() {
      try {
        $this->_getNextRecord();
      } catch (io::IOException $e) {
        throw($e);
      }

      if (empty($this->buffer)) return FALSE;
        
      $data= array(); $idx= 0;
      while (FALSE !== ($cell= $this->_parseColumn())) {
        if (FALSE !== ($cn= $this->getColumnName($idx))) $data[$cn]= $cell; else $data[$idx]= $cell;
        $idx++;
      }

      return $data;
    }
  }  
?>
