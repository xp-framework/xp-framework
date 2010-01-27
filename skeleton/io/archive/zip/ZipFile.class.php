<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'io.archive.zip.ZipArchiveWriter',
    'io.archive.zip.ZipArchiveReader',
    'io.streams.OutputStream',
    'io.streams.InputStream'
  );

  /**
   * Zip archives hanadling
   *
   * Usage (creating a zip file)
   * ~~~~~~~~~~~~~~~~~~~~~~~~~~~
   * <code>
   *   $z= ZipFile::create(new FileOutputStream(new File('dist.zip')));
   *   $z->addEntry(new ZipDirEntry('META-INF'));
   *   $e= $z->addEntry(new ZipFileEntry('META-INF/version.txt'));
   *   $e->getOutputStream()->write($contents);
   *   $z->close();
   * </code>
   *
   * Usage (reading a zip file)
   * ~~~~~~~~~~~~~~~~~~~~~~~~~~
   * <code>
   *   $z= ZipFile::open(new FileInputStream(new File('dist.zip')));
   *   foreach ($z->entries() as $entry) {
   *     if ($entry->isDirectory()) {
   *       // Create dir
   *     } else {
   *       // Extract
   *       Streams::readAll($entry->getInputStream());
   *     }
   *   }
   * </code>
   *
   * @test     xp://net.xp_framework.unittest.io.archive.vendors.InfoZipZipFileTest
   * @test     xp://net.xp_framework.unittest.io.archive.vendors.WindowsZipFileTest
   * @test     xp://net.xp_framework.unittest.io.archive.MalformedZipFileTest
   * @see      http://www.pkware.com/documents/casestudies/APPNOTE.TXT
   * @purpose  Entry point class
   */
  abstract class ZipFile extends Object {
    
    /**
     * Creation constructor
     *
     * @param   io.streams.OutputStream stream
     * @return  io.archive.zip.ZipArchiveWriter
     */
    public static function create(OutputStream $stream) {
      return new ZipArchiveWriter($stream);
    }

    /**
     * Read constructor
     *
     * @param   io.streams.InputStream stream
     * @return  io.archive.zip.ZipArchiveReader
     */
    public static function open(InputStream $stream) {
      return new ZipArchiveReader($stream);
    }   
  }
?>
