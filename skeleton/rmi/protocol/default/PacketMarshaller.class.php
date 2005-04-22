<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  define('DEFAULT_PROTOCOL_MAGIC_NUMBER', 0x3c872747);

  /**
   * Marshal packets for sending
   *
   * @see      xp://rmi.protocol.default.DefaultProtocolHandler
   * @purpose  Helper class
   */
  class PacketMarshaller extends Object {
    var
      $versionMajor   = 0,
      $versionMinor   = 0;
  
    /**
     * Constructor
     *
     * @access  public
     * @param   string version
     */
    function __construct($version) {
      sscanf($version, '%d.%d', $this->versionMajor, $this->versionMinor);
    }
  
    /**
     * Marshals a package
     *
     * @access  public
     * @param   int type
     * @param   string data
     * @return  string
     */
    function marshal($type, &$data) {
      return pack(
        'Nc4Na*', 
        DEFAULT_PROTOCOL_MAGIC_NUMBER, 
        $this->versionMajor,
        $this->versionMinor,
        $type,
        FALSE,                  // compressed
        strlen($data),
        $data
      );
    }
    
    /**
     * Unmarshals a package's header
     *
     * @access  public
     * @param   string header
     * @return  array
     */
    function unmarshal($header) {
      return unpack(
        'Nmagic/cvmajor/cvminor/ctype/ccompressed/Nlength', 
        $header
      );
    }
  }
?>
