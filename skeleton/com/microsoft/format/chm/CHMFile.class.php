<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'util.Date',
    'com.microsoft.format.chm.CHMHeader'
  );

  // Languages  
  define('CHM_LANG_ENGLISH', 	0x0409);
  define('CHM_LANG_GERMAN', 	0x0407);
  
  // Chunk types
  define('CHM_CHUNK_LISTING',	0x0000);
  define('CHM_CHUNK_INDEX',		0x0001);
  
  // Well known identifier for namelist
  define('CHM_FILE_NAMELIST',	'::DataSpace/NameList');
  
  // Compressed / uncompressed identifiers
  define('CHM_COMPRESSED',		'MSCompressed');
  define('CHM_UNCOMPRESSED',	'Uncompressed');

  /**
   * CHM (Compile HTML) reader
   *
   * Example (extract CHM header):
   * <code>
   *   uses('com.microsoft.format.chm.CHMFile', 'io.File');
   *
   *   $f= &new CHMFile(new File('file.chm'));
   *   try(); {
   *     $f->open();
   *     $header= $f->getHeader();
   *     $f->close();
   *   } if (catch('Exception', $e)) {
   *     $e->printStackTrace();
   *     exit(-1);
   *   }
   *   
   *   var_export($header);
   * </code>
   *
   * @see      http://www.speakeasy.org/~russotto/chm/chmformat.html
   * @purpose  CHM
   */
  class CHMFile extends Object {
    var
      $stream   = NULL,
      $header   = NULL;
    
    /**
     * Constructor
     *
     * @access  public
     * @param   &io.Stream stream
     * @return  
     */  
    function __construct(&$stream) {
      $this->stream= &$stream;
      parent::__construct();
    }

    /**
     * Create a GUID from an array
     *
     * @access  private
     * @param   array g
     * @return  string guid
     */
    function _guid($g) {
      return vsprintf('{%08X-%04X-%04X-%02X%02X-%02X%02X-%02X%02X-%02X%02X}', $g);
    }

    /**
     * Retreive a substring and set the pointer to after the 
     * substring's length,
     *
     * @access  private
     * @param   string str
     * @param   &int p
     * @param   int len
     * @return  string str
     */
    function _substr($str, &$p, $len) {
      $str= substr($str, ++$p, $len);
	  $p+= $len;
	  return $str;
    }

    /**
     * Get an int encoded within a string and set the pointer to
     * after the position
     *
     * @access  private
     * @param   string str
     * @param   &int p
     * @return  int
     */
    function _int($str, &$p) {
      $r= 0;
	  while (ord($str{$p}) & 0x80) {
	    $r= ($r << 7) | (ord($str{$p++}) & 0x7F);
	  }
	  $r= ($r << 7) | ord($str{$p++});
	  return $r;
    }
    
    /**
     * Read directory
     *
     * @access  private
     * @param   int length
     * @return  array entries
     */
    function _dir($length, $qref, $format) {
	  $str= $this->stream->read($length- $qref);
	  $pos= 0;
	  $max= strlen($str);
	  do {
	    $len= ord($str{$pos});
	    $name= $this->_substr($str, $pos, $len);
	    switch ($format) {
	      case CHM_CHUNK_LISTING: 
		    $entries[$name]= array(
		      'name'	=> $name,
			  'section' => $this->_int($str, $pos),
	    	  'offset'	=> $this->_int($str, $pos),
	    	  'length'	=> $this->_int($str, $pos)
	        );
  		    break;

		  case CHM_CHUNK_INDEX:
		    $entries[$name]= array(
		      'name'		=> $name,
			  'index'		=> $this->_int($str, $pos)
		    );
  		    break;
        }
      } while ($pos < $max);
	  $this->stream->seek($qref, SEEK_CUR);
      
	  return $entries;
    }

    /**
     * Open file
     *
     * @access  public
     * @return  bool success
     */
    function open() {
      return $this->stream->open(FILE_MODE_READ);
    }
    
    /**
     * Close file
     *
     * @access  public
     * @return  bool success
     */
    function close() {
      return $this->stream->open(FILE_MODE_READ);
    }
    
    /**
     * Retreive CHM header; extracts it if necessary
     *
     * @access  public
     * @return  &com.microsoft.format.chm.CHMHeader
     * @throws  lang.FormatException if the identifier is not correct
     */
    function &getHeader() {
      if (!empty($this->header)) return $this->header;
      
      $this->stream->seek(0x00, SEEK_SET);
      if ('ITSF' !== ($id= $this->stream->read(4))) {
        return throw(new FormatException(
          '"'.addcslashes($id, "\0..\17").'" is not a correct identifier, expecting "ITSF"'
        ));
      }
      
	  $this->header= &new CHMHeader(unpack(
        'Lversion/Llength/Lunknown/Ltime/Llang', 
	    $this->stream->read(0x14)
	  ));
      $this->header->setIdentifier($id);
 	  $this->header->setGuid1($this->_guid(unpack(
        'Lguid1/v2guid2/C8guid3',
	    $this->stream->read(0x10)
	  )));
	  $this->header->setGuid2($this->_guid(unpack(
        'Lguid1/v2guid2/C8guid3',
	    $this->stream->read(0x10)
	  )));

	  // Section table
	  $this->header->setSection(unpack(
        'Loffset/Llength',
	    $this->stream->read(0x16)
	  ));

	  // Content_offset
	  if (3 == $this->header->getVersion()) {
        $this->stream->seek(0x58, SEEK_SET);
	    $this->header->setContent_offset(array_shift(array_values(unpack('L', $this->stream->read(0x20)))));
	  }	

	  return $this->header;    
    }
  }
?>
