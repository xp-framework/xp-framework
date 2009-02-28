<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  // Definitions of well-known MIME types
  define('MIME_APPLICATION_ANDREW_INSET',       'application/andrew-inset');
  define('MIME_APPLICATION_EXCEL',              'application/excel');
  define('MIME_APPLICATION_MSWORD',             'application/msword');
  define('MIME_APPLICATION_OCTET_STREAM',       'application/octet-stream');
  define('MIME_APPLICATION_ODA',                'application/oda');
  define('MIME_APPLICATION_PDF',                'application/pdf');
  define('MIME_APPLICATION_PGP',                'application/pgp');
  define('MIME_APPLICATION_POSTSCRIPT',         'application/postscript');
  define('MIME_APPLICATION_RTF',                'application/rtf');
  define('MIME_APPLICATION_X_ARJ_COMPRESSED',   'application/x-arj-compressed');
  define('MIME_APPLICATION_X_BCPIO',            'application/x-bcpio');
  define('MIME_APPLICATION_X_CHESS_PGN',        'application/x-chess-pgn');
  define('MIME_APPLICATION_X_CPIO',             'application/x-cpio');
  define('MIME_APPLICATION_X_CSH',              'application/x-csh');
  define('MIME_APPLICATION_X_DEBIAN_PACKAGE',   'application/x-debian-package');
  define('MIME_APPLICATION_X_MSDOS_PROGRAM',    'application/x-msdos-program');
  define('MIME_APPLICATION_X_DVI',              'application/x-dvi');
  define('MIME_APPLICATION_X_GTAR',             'application/x-gtar');
  define('MIME_APPLICATION_X_GUNZIP',           'application/x-gunzip');
  define('MIME_APPLICATION_X_HDF',              'application/x-hdf');
  define('MIME_APPLICATION_X_LATEX',            'application/x-latex');
  define('MIME_APPLICATION_X_MIF',              'application/x-mif');
  define('MIME_APPLICATION_X_NETCDF',           'application/x-netcdf');
  define('MIME_APPLICATION_X_PERL',             'application/x-perl');
  define('MIME_APPLICATION_X_PHP',              'application/x-php');
  define('MIME_APPLICATION_X_XP',               'application/x-xp');
  define('MIME_APPLICATION_X_JAVA',             'application/x-java');
  define('MIME_APPLICATION_X_JAVASCRIPT',       'application/x-javascript');
  define('MIME_APPLICATION_X_C',                'application/x-c');
  define('MIME_APPLICATION_X_H',                'application/x-h');
  define('MIME_APPLICATION_X_CPP',              'application/x-cpp');
  define('MIME_APPLICATION_X_CSHARP',           'application/x-csharp');
  define('MIME_APPLICATION_X_RAR_COMPRESSED',   'application/x-rar-compressed');
  define('MIME_APPLICATION_X_SH',               'application/x-sh');
  define('MIME_APPLICATION_X_SHAR',             'application/x-shar');
  define('MIME_APPLICATION_X_SV',               'application/x-sv');
  define('MIME_APPLICATION_X_TAR',              'application/x-tar');
  define('MIME_APPLICATION_X_TAR_GZ',           'application/x-tar-gz');
  define('MIME_APPLICATION_X_TCL',              'application/x-tcl');
  define('MIME_APPLICATION_X_TEX',              'application/x-tex');
  define('MIME_APPLICATION_X_TEXINFO',          'application/x-texinfo');
  define('MIME_APPLICATION_X_TROFF',            'application/x-troff');
  define('MIME_APPLICATION_X_TROFF_MAN',        'application/x-troff-man');
  define('MIME_APPLICATION_X_TROFF_ME',         'application/x-troff-me');
  define('MIME_APPLICATION_X_TROFF_MS',         'application/x-troff-ms');
  define('MIME_APPLICATION_X_USTAR',            'application/x-ustar');
  define('MIME_APPLICATION_X_WAIS_SOURCE',      'application/x-wais-source');
  define('MIME_APPLICATION_X_ZIP_COMPRESSED',   'application/x-zip-compressed');
  define('MIME_AUDIO_BASIC',                    'audio/basic');
  define('MIME_AUDIO_MIDI',                     'audio/midi');
  define('MIME_AUDIO_ULAW',                     'audio/ulaw');
  define('MIME_AUDIO_X_AIFF',                   'audio/x-aiff');
  define('MIME_AUDIO_X_WAV',                    'audio/x-wav');
  define('MIME_IMAGE_GIF',                      'image/gif');
  define('MIME_IMAGE_IEF',                      'image/ief');
  define('MIME_IMAGE_JPEG',                     'image/jpeg');
  define('MIME_IMAGE_PNG',                      'image/png');
  define('MIME_IMAGE_TIFF',                     'image/tiff');
  define('MIME_IMAGE_X_CMU_RASTER',             'image/x-cmu-raster');
  define('MIME_IMAGE_X_PORTABLE_ANYMAP',        'image/x-portable-anymap');
  define('MIME_IMAGE_X_PORTABLE_BITMAP',        'image/x-portable-bitmap');
  define('MIME_IMAGE_X_PORTABLE_GRAYMAP',       'image/x-portable-graymap');
  define('MIME_IMAGE_X_PORTABLE_PIXMAP',        'image/x-portable-pixmap');
  define('MIME_IMAGE_X_RGB',                    'image/x-rgb');
  define('MIME_IMAGE_X_XBITMAP',                'image/x-xbitmap');
  define('MIME_IMAGE_X_XPIXMAP',                'image/x-xpixmap');
  define('MIME_IMAGE_X_XWINDOWDUMP',            'image/x-xwindowdump');
  define('MIME_TEXT_HTML',                      'text/html');
  define('MIME_TEXT_PLAIN',                     'text/plain');
  define('MIME_TEXT_CSS',                       'text/css');
  define('MIME_TEXT_XML',                       'text/xml');
  define('MIME_TEXT_RICHTEXT',                  'text/richtext');
  define('MIME_TEXT_TAB_SEPARATED_VALUES',      'text/tab-separated-values');
  define('MIME_TEXT_COMMA_SEPARATED_VALUES',    'text/comma-separated-values');
  define('MIME_TEXT_X_SETEXT',                  'text/x-setext');
  define('MIME_VIDEO_DL',                       'video/dl');
  define('MIME_VIDEO_FLI',                      'video/fli');
  define('MIME_VIDEO_GL',                       'video/gl');
  define('MIME_VIDEO_MPEG',                     'video/mpeg');
  define('MIME_VIDEO_QUICKTIME',                'video/quicktime');
  define('MIME_VIDEO_X_MSVIDEO',                'video/x-msvideo');
  define('MIME_VIDEO_X_SGI_MOVIE',              'video/x-sgi-movie');
  define('MIME_X_WORLD_X_VRML',                 'x-world/x-vrml');
  
  /**
   * MIME Type
   *
   * @see http://www.stalkpire.de/mime-types-liste.aspx
   */
  class MimeType extends Object {
  
    /**
     * Get mime type by filename (guess)
     *
     * @param   string name
     * @param   string default default MIME_APPLICATION_OCTET_STREAM
     * @return  string type
     */
    public static function getByFilename($name, $default= MIME_APPLICATION_OCTET_STREAM) {
      static $map= array(
        '.ez'      => MIME_APPLICATION_ANDREW_INSET,
        '.xls'     => MIME_APPLICATION_EXCEL,
        '.doc'     => MIME_APPLICATION_MSWORD,
        '.bin'     => MIME_APPLICATION_OCTET_STREAM,
        '.oda'     => MIME_APPLICATION_ODA,
        '.pdf'     => MIME_APPLICATION_PDF,
        '.pgp'     => MIME_APPLICATION_PGP,
        '.ps'      => MIME_APPLICATION_POSTSCRIPT,
        '.eps'     => MIME_APPLICATION_POSTSCRIPT,
        '.rtf'     => MIME_APPLICATION_RTF,
        '.arj'     => MIME_APPLICATION_X_ARJ_COMPRESSED,
        '.bcpio'   => MIME_APPLICATION_X_BCPIO,
        '.pgn'     => MIME_APPLICATION_X_CHESS_PGN,
        '.cpio'    => MIME_APPLICATION_X_CPIO,
        '.csh'     => MIME_APPLICATION_X_CSH,
        '.deb'     => MIME_APPLICATION_X_DEBIAN_PACKAGE,
        '.com'     => MIME_APPLICATION_X_MSDOS_PROGRAM,
        '.exe'     => MIME_APPLICATION_X_MSDOS_PROGRAM,
        '.bat'     => MIME_APPLICATION_X_MSDOS_PROGRAM,
        '.dvi'     => MIME_APPLICATION_X_DVI,
        '.gtar'    => MIME_APPLICATION_X_GTAR,
        '.gz'      => MIME_APPLICATION_X_GUNZIP,
        '.hdf'     => MIME_APPLICATION_X_HDF,
        '.latex'   => MIME_APPLICATION_X_LATEX,
        '.mif'     => MIME_APPLICATION_X_MIF,
        '.cdf'     => MIME_APPLICATION_X_NETCDF,
        '.nc'      => MIME_APPLICATION_X_NETCDF,
        '.pl'      => MIME_APPLICATION_X_PERL,
        '.pm'      => MIME_APPLICATION_X_PERL,
        '.php'     => MIME_APPLICATION_X_PHP,
        '.xp'      => MIME_APPLICATION_X_XP,
        '.java'    => MIME_APPLICATION_X_JAVA,
        '.js'      => MIME_APPLICATION_X_JAVASCRIPT,
        '.c'       => MIME_APPLICATION_X_C,
        '.h'       => MIME_APPLICATION_X_H,
        '.cpp'     => MIME_APPLICATION_X_CPP,
        '.hpp'     => MIME_APPLICATION_X_CPP,
        '.cs'      => MIME_APPLICATION_X_CSHARP,
        '.rar'     => MIME_APPLICATION_X_RAR_COMPRESSED,
        '.sh'      => MIME_APPLICATION_X_SH,
        '.shar'    => MIME_APPLICATION_X_SHAR,
        '.4cpio'   => MIME_APPLICATION_X_SV,
        '.sv4cpio' => MIME_APPLICATION_X_SV,
        '.4crc'    => MIME_APPLICATION_X_SV,
        '.sv4crc'  => MIME_APPLICATION_X_SV,
        '.tar'     => MIME_APPLICATION_X_TAR,
        '.tgz'     => MIME_APPLICATION_X_TAR_GZ,
        '.tar.gz'  => MIME_APPLICATION_X_TAR_GZ,
        '.tcl'     => MIME_APPLICATION_X_TCL,
        '.tex'     => MIME_APPLICATION_X_TEX,
        '.texi'    => MIME_APPLICATION_X_TEXINFO,
        '.texinfo' => MIME_APPLICATION_X_TEXINFO,
        '.t'       => MIME_APPLICATION_X_TROFF,
        '.tr'      => MIME_APPLICATION_X_TROFF,
        '.roff'    => MIME_APPLICATION_X_TROFF,
        '.man'     => MIME_APPLICATION_X_TROFF_MAN,
        '.me'      => MIME_APPLICATION_X_TROFF_ME,
        '.ms'      => MIME_APPLICATION_X_TROFF_MS,
        '.ustar'   => MIME_APPLICATION_X_USTAR,
        '.src'     => MIME_APPLICATION_X_WAIS_SOURCE,
        '.zip'     => MIME_APPLICATION_X_ZIP_COMPRESSED,
        '.snd'     => MIME_AUDIO_BASIC,
        '.mid'     => MIME_AUDIO_MIDI,
        '.midi'    => MIME_AUDIO_MIDI,
        '.au'      => MIME_AUDIO_ULAW,
        '.aif'     => MIME_AUDIO_X_AIFF,
        '.aifc'    => MIME_AUDIO_X_AIFF,
        '.aiff'    => MIME_AUDIO_X_AIFF,
        '.wav'     => MIME_AUDIO_X_WAV,
        '.gif'     => MIME_IMAGE_GIF,
        '.ief'     => MIME_IMAGE_IEF,
        '.jpe'     => MIME_IMAGE_JPEG,
        '.jpeg'    => MIME_IMAGE_JPEG,
        '.jpg'     => MIME_IMAGE_JPEG,
        '.png'     => MIME_IMAGE_PNG,
        '.tif'     => MIME_IMAGE_TIFF,
        '.tiff'    => MIME_IMAGE_TIFF,
        '.ras'     => MIME_IMAGE_X_CMU_RASTER,
        '.pnm'     => MIME_IMAGE_X_PORTABLE_ANYMAP,
        '.pbm'     => MIME_IMAGE_X_PORTABLE_BITMAP,
        '.pgm'     => MIME_IMAGE_X_PORTABLE_GRAYMAP,
        '.ppm'     => MIME_IMAGE_X_PORTABLE_PIXMAP,
        '.rgb'     => MIME_IMAGE_X_RGB,
        '.xbm'     => MIME_IMAGE_X_XBITMAP,
        '.xpm'     => MIME_IMAGE_X_XPIXMAP,
        '.xwd'     => MIME_IMAGE_X_XWINDOWDUMP,
        '.html'    => MIME_TEXT_HTML,
        '.htm'     => MIME_TEXT_HTML,
        '.asc'     => MIME_TEXT_PLAIN,
        '.txt'     => MIME_TEXT_PLAIN,
        '.ini'     => MIME_TEXT_PLAIN,
        '.conf'    => MIME_TEXT_PLAIN,
        '.css'     => MIME_TEXT_CSS,
        '.xml'     => MIME_TEXT_XML,
        '.xsl'     => MIME_TEXT_XML,
        '.rdf'     => MIME_TEXT_XML,
        '.rss'     => MIME_TEXT_XML,
        '.rtx'     => MIME_TEXT_RICHTEXT,
        '.tsv'     => MIME_TEXT_TAB_SEPARATED_VALUES,
        '.etx'     => MIME_TEXT_X_SETEXT,
        '.dl'      => MIME_VIDEO_DL,
        '.fli'     => MIME_VIDEO_FLI,
        '.gl'      => MIME_VIDEO_GL,
        '.mp2'     => MIME_VIDEO_MPEG,
        '.mpe'     => MIME_VIDEO_MPEG,
        '.mpeg'    => MIME_VIDEO_MPEG,
        '.mpg'     => MIME_VIDEO_MPEG,
        '.mov'     => MIME_VIDEO_QUICKTIME,
        '.qt'      => MIME_VIDEO_QUICKTIME,
        '.avi'     => MIME_VIDEO_X_MSVIDEO,
        '.movie'   => MIME_VIDEO_X_SGI_MOVIE,
        '.vrm'     => MIME_X_WORLD_X_VRML,      
        '.vrml'    => MIME_X_WORLD_X_VRML,      
        '.wrl'     => MIME_X_WORLD_X_VRML,
      );
      
      $parts= explode('.', strtolower($name));
      $i= sizeof($parts)- 1;
      $idx= '';
      
      while ($i > 0 && $idx= $idx.'.'.$parts[$i]) {
        if (isset($map[$idx])) return $map[$idx];
        $i--;
      }
      
      return $default;
    }
  }
?>
