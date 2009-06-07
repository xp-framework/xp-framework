<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('de.fraunhofer.mp3.ID3Genre');
  
  // Versions 
  define('ID3_VERSION_UNKNOWN',               '?');
  define('ID3_VERSION_1',                     '1');
  define('ID3_VERSION_1_1',                 '1.1');
  define('ID3_VERSION_2',                     '2');
  
  /**
   * This class represents an ID3 tag
   *
   * Usage:
   * <code>
   *   $mp3= new MP3File(new File($file));
   *
   *   $id3= $mp3->getID3Tag();
   *   echo $id3->toString();
   * </code>
   *
   * @purpose  ID3 Tag object
   * @see      http://www.id3.org/id3v1.html
   */
  class ID3Tag extends Object {
    public 
      $version  = ID3_VERSION_UNKNOWN,
      $tag      = '',
      $name     = '',
      $artist   = '',
      $album    = '',
      $year     = 0,
      $comment  = '',
      $genre    = -1,
      $track    = 0;
    
    /**
     * Create a string representation
     *
     * @return  string
     */
    public function toString() {
      static $ver= array(
        ID3_VERSION_UNKNOWN => 'ID3_VERSION_UNKNOWN',
        ID3_VERSION_1       => 'ID3_VERSION_1',
        ID3_VERSION_1_1     => 'ID3_VERSION_1_1',
        ID3_VERSION_2       => 'ID3_VERSION_2'
      );
      
      return sprintf(
        "%s {\n".
        "  [version] %s\n".
        "  [tag    ] %s\n".
        "  [name   ] %s\n".
        "  [artist ] %s\n".
        "  [album  ] %s\n".
        "  [year   ] %s\n".
        "  [comment] %s\n".
        "  [genre  ] %d {%s}\n".
        "}",
        $this->getClassName(),
        $ver[$this->version],
        $this->tag,
        $this->name,
        $this->artist,
        $this->album,
        $this->year,
        $this->comment,
        $this->genre->id,
        $this->genre->toString()
      );
    }
    
    /**
     * Creates an ID3 Tag from a string
     *
     * @param   string buf
     * @param   string version one of the ID3_VERSION_* constants
     * @return  de.fraunhofer.mp3.ID3Tag a tag
     */
    public static function fromString($buf, $version) {
      $tag= new ID3Tag();
      
      switch ($version) {
        case ID3_VERSION_1:
          $data= unpack('a3tag/a30name/a30artist/a30album/a4year/a30comment/C1genre', $buf);
          if ('TAG' != $data['tag']) throw new FormatException('Tag corrupt');
          break;
          
        case ID3_VERSION_1_1:
          $data= unpack('a3tag/a30name/a30artist/a30album/a4year/a28comment/x1/C1track/C1genre', $buf);
          if ('TAG' != $data['tag']) throw new FormatException('Tag corrupt');
          break;
        
        default:
          throw new IllegalArgumentException('Version '.$version.' not supported');
      }

      // Copy information
      $tag->version=    $version;
      $tag->tag=        $data['tag'];
      $tag->name=       $data['name'];
      $tag->artist=     $data['artist'];
      $tag->album=      $data['album'];
      $tag->year=       (int)$data['year'];
      $tag->comment=    $data['comment'];
      $tag->genre=      new ID3Genre((int)$data['genre']);
      return $tag;
    }
  }
?>
