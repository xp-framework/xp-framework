<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('Deserializer', 'io.File');

  /**
   * Serializer that reads from files
   *
   * @purpose  Serializer
   */
  class FileDeserializer extends Deserializer {
    protected
      $file  = NULL;
      
    /**
     * Constructor
     *
     * @access  public
     * @param   io.File file
     */
    public function __construct(File $file) {
      $this->file= $file;
    }
  
    /**
     * Serialize an object
     *
     * @access  public
     * @return  mixed
     */
     public function deserialize() {
       $this->file->open(FILE_MODE_READ);
       $data= $this->file->read($this->file->size());
       $this->file->close();
       return $this->valueOf($data);
     }
  }
?>
