<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('io.Stream');

  /**
   * CSVParser provides comfortable way to parse csv (comma separated
   * value) - files. This class proposes "|" as default delimiter, tough.
   *
   * @purpose Interface for parsing CSV-files
   * @see     http://www.creativyst.com/Doc/Articles/CSV/CSV01.htm
   */
  class CSVParser extends Object {
    public
      $stream;
    
    public
      $hasHeader,
      $colDelim= '|',
      $escape= '"';
    
    public
      $colName;
      
    public 
      $buffer;
    
    /**
     * Creates a CSVParser object
     *
     * @access  public
     * @param   int mode header or headerless mode
     */    
    public function __construct() {
      $this->buffer= '';
      $this->colName= NULL;
    }

    /**
     * Tokenizes a string according to our needs.
     *
     * @access  private
     * @param   string &string string to take token of
     * @param   char delim delimiter
     * @return  string token
     */
    private function _strtok(&$string, $delim) {

      // Note: don't use builtin strtok, because it does ignore an
      // empty field (two delimiters in a row). We need this information.
      if (empty ($string))
        return FALSE;
      if (FALSE === ($tpos= strpos ($string, $delim))) {
        $token= $string;
        $string= '';
        return $token;
      }
      
      $token= substr ($string, 0, $tpos);
      $string= substr ($string, strlen ($token)+1);
      return $token;
    }
    
    /**
     * Sets the input stream. This stream must support
     * isOpen(), open(), eof(), readLine().
     *
     * @access  public
     * @param   Stream stream
     */    
    public function setInputStream(&$stream) {
      try {
        if (!$stream->isOpen ()) $stream->open ();
      } catch (IOException $e) {
        throw  ($e);
      }
      $this->stream= $stream;
    }
    
    /**
     * Sets the new delimiter for columns. Once CSVs had comma "," as
     * delimiters, today this varies. The pipe "|" is often used
     * as delimiter. It only makes sense to call this before any
     * line was read.
     *
     * @access  public
     * @param   char delimiter delimiter to set
     */
    public function setColDelimiter($delim) {
      $this->colDelim= $delim{0};
    }

    /**
     * Returns whether the header record has already been read.
     * There is no information in the CSV itself that states whether
     * an header record is available, so this has to be decided by
     * the calling program (or user).
     *
     * @access  public
     * @return  bool hasHeader TRUE, if header is available
     */
    public function hasHeader() {
      return is_array ($this->colName) && count ($this->colName);
    }
    
    /**
     * Checks whether a certain column exists in the csv.
     * If not existColumn return FALSE, otherwise the index
     * of the column.
     *
     * @access  public
     * @param   string columnname
     * @return  int columnindex
     */    
    public function getColumnIndex($column) {
      $reverse= array_flip ($this->colName);
      if (!isset ($reverse[$column]))
        return FALSE;
    
      return $reverse[$column];
    }
    
    /**
     * Reads as many lines as necessary from the stream until there is 
     * exactly one record in the buffer.
     * This function affects the member buffer.
     *
     * @access  private
     * @return  string buffer
     */    
    private function _getNextRecord() {
      try {
        if ($this->stream->eof())
          return FALSE;

        $row= $this->stream->readLine();
        while (0 !== substr_count ($row, $this->escape) % 2)
          $row.= "\n".$this->stream->readLine();
      
        $this->buffer= $row;
      } catch (IOException $e) {
        throw  ($e);
      }
      
      return $this->buffer;
    }

    /**
     * Parse the next cell out of the buffer. If buffer is empty,
     * this returns FALSE. This function takes care of
     * quotedness of the data, and de-escapes any escaped chars.
     * It also removes the parsed cell from the internal buffer.
     *
     * @access  private
     * @return  string buffer
     */
    private function _parseColumn() {
      if (empty ($this->buffer))
        return FALSE;

      $tok= $this->_strtok ($this->buffer, $this->colDelim);

      // Trick: when there is an odd number of escape characters
      // you know that this found delimiter is part of a string inside
      // the payload. Search for the next, until you have an even number 
      // of escapers (then all quoted parts are closed).
      while (0 !== substr_count ($tok, $this->escape) % 2) {
        $add= $this->colDelim.$this->_strtok ($this->buffer, $this->colDelim);
        $tok.= $add;
      }
      // $this->buffer= substr ($this->buffer, strlen ($tok)+1);

      // Single escape characters become nothing, double escape
      // characters become the escape character itself.
      $tok= trim ($tok);
      $i= 0; $j= 0; $res= '';
      while (FALSE !== ($i= strpos ($tok, $this->escape, $j))) {
        if (strlen ($tok) > $i+1 && $tok{$i+1} == $this->escape) $i++;
        $res.= substr ($tok, $j, $i-$j);
        $j= $i+1;
      }
      
      if (empty ($res))
        return $tok;
      
      return $res; 
    }
    
    /**
     * Read the record and save the result as the header record.
     *
     * @access  public
     */    
    public function getHeaderRecord() {
      $this->colName= (array)self::getNextRecord();
    }
    
    /**
     * Manually set the header information to be able to supply
     * additional information and get nicer output (non-enumerated)
     *
     * @access  public
     * @param   array headers
     */    
    public function setHeaderRecord($headers) {
      $this->colName= $headers;
    }
    
    /**
     * Retrieves the name of a column if one is available
     *
     * @access  public
     * @param   int number
     * @return  string name or FALSE if none is available
     */
    public function getColumnName($nr) {
      if (!self::hasHeader())
        return FALSE;
      
      if (!isset ($this->colName[$nr]))
        return FALSE;
      
      return $this->colName[$nr];
    }    
    
    /**
     * Read the next record from the stream. This returns a 
     * StdClass object with the members named as the header
     * record supposes. When no header was available, the
     * fields are enumerated.
     *
     * @access  public
     * @return  StdClass data
     * @throws  IOException if stream operation failed
     */    
    public function getNextRecord() {
      try {
        self::_getNextRecord();
      } catch (IOException $e) {
        throw  ($e);
      }

      if (empty ($this->buffer))
        return FALSE;
        
      $data= array(); $idx= 0;
      while (FALSE !== ($cell= self::_parseColumn())) {
        if (FALSE !== ($cn= self::getColumnName($idx)))
          $data[$cn]= $cell;
        else 
          $data[$idx]= $cell;
        
        $idx++;
      }

      return (object)$data;
    }
  }
?>
