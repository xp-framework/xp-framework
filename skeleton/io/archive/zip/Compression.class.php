<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'lang.Enum', 
    'io.streams.InputStream', 
    'io.streams.InflatingInputStream',
    'io.streams.DeflatingOutputStream',
    'io.streams.Bz2DecompressingInputStream',
    'io.streams.Bz2CompressingOutputStream'
  );

  /**
   * Compression algorithm enumeration
   *
   * @ext      bz2
   * @ext      zlib
   * @see      xp://io.archive.zip.ZipArchive
   * @test     xp://net.xp_framework.unittest.io.archive.CompressionTest
   * @purpose  Compressions
   */
  abstract class Compression extends Enum {
    public static $NONE, $GZ, $BZ;
    
    static function __static() {
      self::$NONE= newinstance(__CLASS__, array(0, 'NONE'), '{
        static function __static() { }
        
        public function getCompressionStream(OutputStream $out, $level= 6) {
          return $out;
        }

        public function getDecompressionStream(InputStream $in) {
          return $in;
        }
      }');
      self::$GZ= newinstance(__CLASS__, array(8, 'GZ'), '{
        static function __static() { }
        
        public function getCompressionStream(OutputStream $out, $level= 6) {
          return new DeflatingOutputStream($out, $level);
        }

        public function getDecompressionStream(InputStream $in) {
          return new InflatingInputStream($in);
        }
      }');
      self::$BZ= newinstance(__CLASS__, array(12, 'BZ'), '{
        static function __static() { }
        
        public function getCompressionStream(OutputStream $out, $level= 6) {
          return new Bz2CompressingOutputStream($out, $level);
        }

        public function getDecompressionStream(InputStream $in) {
          return new Bz2DecompressingInputStream($in);
        }
      }');
    }
    
    /**
     * Returns all enum members
     *
     * @return  lang.Enum[]
     */
    public static function values() {
      return parent::membersOf(__CLASS__);
    }

    /**
     * Gets compression stream. Implemented in members.
     *
     * @param   io.streams.OutputStream out
     * @param   int level default 6 the compression level
     * @return  io.streams.OutputStream
     * @throws  lang.IllegalArgumentException if the level is not between 0 and 9
     */
    public abstract function getCompressionStream(OutputStream $out, $level= 6);

    /**
     * Gets decompression stream. Implemented in members.
     *
     * @param   io.streams.InputStream in
     * @return  io.streams.InputStream
     */
    public abstract function getDecompressionStream(InputStream $in);

    /**
     * Get a compression instance by a given id
     *
     * @param   int n
     * @return  io.archive.zip.Compression
     * @throws  lang.IllegalArgumentException
     */
    public static function getInstance($n) {
      switch ($n) {
        case 0: return self::$NONE;
        case 8: return self::$GZ;
        case 12: return self::$BZ;
        default: throw new IllegalArgumentException('Unknown compression algorithm #'.$n);
      }
    }
  }
?>
