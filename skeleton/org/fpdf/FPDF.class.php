<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  define('FPDF_VERSION',        1.6);
  
  define('FPDF_LANDSCAPE',      'L');
  define('FPDF_PORTRAIT',       'P');

  define('FPDF_UNIT_PT',        'pt');
  define('FPDF_UNIT_MM',        'mm');
  define('FPDF_UNIT_CM',        'cm');
  define('FPDF_UNIT_INCH',      'in');
  
  define('FPDF_FORMAT_A3',      'A3');
  define('FPDF_FORMAT_A4',      'A4');
  define('FPDF_FORMAT_A5',      'A5');
  define('FPDF_FORMAT_LETTER',  'LETTER');
  define('FPDF_FORMAT_LEGAL',   'LEGAL');
  
  uses('org.fpdf.FPDFFont');
  
  /**
   * PDF creator
   *
   * @purpose  Create PDFs
   * @see      http://fpdf.org/
   */
  class FPDF extends Object {
    var 
      $page               = 0,        // current page number
      $n                  = 2,        // current object number
      $offsets,                       // array of object offsets
      $buffer             = '',       // buffer holding in-memory PDF
      $pages              = array(),  // array containing pages
      $state              = 0,        // current document state
      $compress,                      // compression flag
      $DefOrientation,                // default orientation
      $CurOrientation,                // current orientation
      $OrientationChanges = array(),  // array indicating orientation changes
      $fwPt, $fhPt,                   // dimensions of page format in points
      $fw, $fh,                       // dimensions of page format in user unit
      $wPt, $hPt,                     // current dimensions of page in points
      $k,                             // scale factor (number of points in user unit)
      $w, $h,                         // current dimensions of page in user unit
      $lMargin,                       // left margin
      $tMargin,                       // top margin
      $rMargin,                       // right margin
      $bMargin,                       // page break margin
      $cMargin,                       // cell margin
      $x, $y,                         // current position in user unit for cell positionning
      $lasth,                         // height of last cell printed
      $LineWidth,                     // line width in user unit
      $fonts              = array(),  // array of used fonts
      $FontFiles          = array(),  // array of font files
      $diffs              = array(),  // array of encoding differences
      $images             = array(),  // array of used images
      $PageLinks,                     // array of links in pages
      $links              = array(),  // array of internal links
      $FontFamily         = '',       // current font family
      $FontStyle          = '',       // current font style
      $underline          = FALSE,    // underlining flag
      $CurrentFont,                   // current font info
      $FontSizePt         = 12,       // current font size in points
      $FontSize,                      // current font size in user unit
      $DrawColor          = '0 G',    // commands for drawing color
      $FillColor          = '0 g',    // commands for filling color
      $TextColor          = '0 g',    // commands for text color
      $ColorFlag          = FALSE,    // indicates whether fill and text colors are different
      $ws                 = 0,        // word spacing
      $AutoPageBreak,                 // automatic page breaking
      $PageBreakTrigger,              // threshold used to trigger page breaks
      $ZoomMode,                      // zoom display mode
      $LayoutMode,                    // layout display mode
      $info               = array();  // Information (creator, author, title, ...)

    /**
     * Constructor
     *
     * @access  public
     * @param   string orientation default FPDF_PORTRAIT
     * @param   string unit default FPDF_UNIT_MM
     * @param   string format default FPDF_FORMAT_A4
     */
    function __construct(
      $orientation= FPDF_PORTRAIT, 
      $unit= FPDF_UNIT_MM, 
      $format= FPDF_FORMAT_A4
    ) {

      // Scale factor
      $this->k= $this->getScaleFactor($unit);

      // Page format
      $this->setPageFormat($format);
      
      // Page orientation
      $this->setOrientation($orientation);
      
      // Page margins (1 cm)
      $margin= round(28.35 / $this->k, 2);
      $this->setMargins($margin, $margin);
      
      // Interior cell margin (1 mm)
      $this->cMargin= $margin / 10;
      
      // Line width (0.2 mm)
      $this->LineWidth= round(.567 / $this->k, 3);
      
      // Automatic page break
      $this->setAutoPageBreak(TRUE, 2 * $margin);
      
      // Full width display mode
      $this->setDisplayMode('fullwidth');
      
      // Compression
      $this->setCompression(TRUE);
    }

    /**
     * Load fonts
     *
     * @access  public
     * @param   &util.Properties prop
     */
    function loadFonts(&$prop) {
      $section= $prop->getFirstSection();
      do {
        $f= &new FPDFFont($section);
        $f->configure($prop);
        $this->addFont($f);
      } while ($section= $prop->getNextSection());
    }
    
    /**
     * Retrieve scale factor for a specified unit
     *
     * @access  public
     * @param   string unit on of the FPDF_UNIT_* constants
     * @return  float
     */
    function getScaleFactor($unit) {
      if (!isset($this->_kmap)) $this->_kmap= array(
        FPDF_UNIT_PT    => 1,
        FPDF_UNIT_MM    => 72 / 25.4,
        FPDF_UNIT_CM    => 72 / 2.54,
        FPDF_UNIT_INCH  => 72
      );
      return $this->_kmap[$unit];
    }
    
    /**
     * Retrieve page dimensions for a specified format
     *
     * @access  public
     * @param   string format on of the FPDF_FORMAT_* constants
     * @return  float[2]
     */
    function getPageDimensions($format) {
      if (!isset($this->_fmap)) $this->_fmap= array(
        FPDF_FORMAT_A3          => array(841.89, 1190.55),
        FPDF_FORMAT_A4          => array(595.28, 841.89),
        FPDF_FORMAT_A5          => array(420.94, 595.28),
        FPDF_FORMAT_LETTER      => array(612, 792),
        FPDF_FORMAT_LEGAL       => array(612, 1008)
      );
      
      return $this->_fmap[$format];
    }
   
    /**
     * Set page format
     *
     * @access  public
     * @param   string format on of the FPDF_FORMAT_* constants
     */ 
    function setPageFormat($format) {
      list($this->fwPt, $this->fhPt)= $this->getPageDimensions($format);
      $this->fw= round($this->fwPt / $this->k, 2);
      $this->fh= round($this->fhPt / $this->k, 2);  
    }
    
    /**
     * Set orientation
     *
     * @access  public
     * @param   string orientation one of FPDF_PORTRAIT or FPDF_LANDSCAPE
     */
    function setOrientation($orientation) {
      switch ($orientation) {
        case FPDF_PORTRAIT:
          $this->wPt= $this->fwPt;
          $this->hPt= $this->fhPt;
          break;
          
        case FPDF_LANDSCAPE:
          $this->wPt= $this->fhPt;
          $this->hPt= $this->fwPt;
          break;
      }

      $this->w= round($this->wPt / $this->k, 2);
      $this->h= round($this->hPt / $this->k, 2);
      $this->CurOrientation= $orientation;
      if (!isset($this->DefOrientation)) $this->DefOrientation= $this->CurOrientation;
    }

    /**
     * Set left, top and right margins
     *
     * @access  public
     * @param   int left
     * @param   int top
     * @param   int right default -1
     */
    function setMargins($left, $top, $right= -1) {
      $this->lMargin= $left;
      $this->tMargin= $top;
      $this->rMargin= (-1 == $right) ? $left : $right;
    }

    /**
     * Set left margin
     *
     * @access  public
     * @param   int margin
     */
    function setLeftMargin($margin) {
      $this->lMargin= $margin;
      if ($this->page > 0 and $this->x < $margin) $this->x= $margin;
    }

    /**
     * Set top margin
     *
     * @access  public
     * @param   int margin
     */
    function setTopMargin($margin) {
      $this->tMargin= $margin;
    }

    /**
     * Set right margin
     *
     * @access  public
     * @param   int margin
     */
    function setRightMargin($margin) {
      $this->rMargin= $margin;
    }

    /**
     * Set auto page break mode and triggering margin
     *
     * @access  public
     * @param   int auto
     * @param   int margin default 0
     */
    function setAutoPageBreak($auto, $margin= 0) {
      $this->AutoPageBreak= $auto;
      $this->bMargin= $margin;
      $this->PageBreakTrigger= $this->h- $margin;
    }

    /**
     * Set display mode in viewer
     *
     * @access  public
     * @param   string zoom
     * @param   string layout default continuous
     * @throws  lang.IllegalArgumentException
     */
    function setDisplayMode($zoom, $layout= 'continuous') {
      switch ($zoom) {
        case 'fullpage':
        case 'fullwidth':
        case 'real':
        case 'default':
        case NULL:
          $this->ZoomMode= $zoom;
          break;
        
        case 'zoom':
          $this->ZoomMode= $layout;
          return;
        
        default:
          return throw(new IllegalArgumentException('Incorrect zoom display mode: '.$zoom));
      }
      
      switch ($layout) {
        case 'single':
        case 'continuous':
        case 'two':
        case 'default':
          $this->LayoutMode=$layout;
          break;
        
        default:
          return throw(new IllegalArgumentException('Incorrect layout display mode: '.$layout));
      }
    }

    /**
     * Turns page compression on or off. Throws an exception in case 
     * compression was requested (set to TRUE) but not available.
     *
     * @access  public
     * @param   bool compress
     * @throws  lamg.MethodNotImplementedException
     */
    function setCompression($compress) {
      if ($compress && ! function_exists('gzcompress')) {
        return throw(new MethodNotImplementedException('Compression not available'));
      }
      $this->compress= $compress;
    }

    /**
     * Sets title of document
     *
     * @access  public
     * @param   string 
     */
    function setTitle($title) {
      $this->info['title']= $title;
    }

    /**
     * Sets subject of document
     *
     * @access  public
     * @param   string 
     */
    function setSubject($subject) {
      $this->info['subject']= $subject;
    }

    /**
     * Sets author of document
     *
     * @access  public
     * @param   string 
     */
    function setAuthor($author) {
      $this->info['author']= $author;
    }

    /**
     * Associates keywords with the document, generally in the following form:
     * <pre>
     *   'keyword1 keyword2 ...'
     * </pre>
     *
     * @access  public
     * @param   string 
     */
    function setKeywords($keywords) {
      $this->info['keywords']= $keywords;
    }

    /**
     * Defines the creator of the document. This is typically the name 
     * of the application that generates the PDF.
     *
     * @access  public
     * @param   string creator
     */
    function setCreator($creator) {
      $this->info['creator']= $creator;
    }

    /**
     * This method begins the generation of the PDF document. It is not 
     * necessary to call it explicitly because AddPage() does it 
     * automatically.
     *
     * Note: no page is created by this method.
     *
     * @access  public
     */
    function open() {
      $this->_begindoc();
    }

    /**
     * Terminates the PDF document. It is not necessary to call this 
     * method explicitly because getBuffer() does it automatically.
     *
     * If the document contains no page, addPage() is called to prevent 
     * from getting an invalid document.
     *
     * @access  public
     */
    function close() {
      if (0 == $this->page) $this->addPage();
      $this->_endpage();
      $this->_enddoc();
    }

    /**
     * Start a new page with an optional orientation, which, if 
     * omitted, defaults to orientation given to constructor
     *
     * @access  public
     * @param   string orientation default NULL
     */
    function addPage($orientation= NULL) {
      $family= $this->FontFamily;
      
      // Finalize previous page if needed
      if ($this->page > 0) $this->_endpage();

      // Start new page
      $this->_beginpage($orientation);

      // Set line cap style to square
      $this->_out('2 J');

      // Set line width
      $this->_out($this->LineWidth.' w');

      // Set font
      if ($family) {
        $style= $this->FontStyle.($this->underline ? 'U' : '');
        $this->setFont($this->getFontByName($family, $style), $this->FontSizePt);
      }

      // Set colors
      if ($this->DrawColor != '0 G') $this->_out($this->DrawColor);
      if ($this->FillColor != '0 g') $this->_out($this->FillColor);
    }

    /**
     * Get current page number
     *
     * @access  public
     * @return  int
     */
    function getPageNo() {
      return $this->page;
    }

    /**
     * Return a color specification
     *
     * @access  private
     * @param   int r
     * @param   int g
     * @param   int b
     * @param   string[2] identifiers
     * @return  string
     */    
    function _colorspec($r, $g, $b, $identifiers) {

      // Border case: Black or no green
      if (($r == 0 and $g == 0 and $b == 0) or $g == -1) {
        return substr($r / 255, 0, 5).' '.$identifiers[0];
      }
      return (
        substr($r / 255, 0, 5).' '.
        substr($g / 255, 0, 5).' '.
        substr($b / 255, 0, 5).' '.
        $identifiers[1]
      );
    }

    /**
     * Set color for all stroking operations
     *
     * @access  public
     * @param   int r
     * @param   int g default -1
     * @param   int b default -1
     */
    function setDrawColor($r, $g= -1, $b =-1) {
      $this->DrawColor= $this->_colorspec($r, $g, $b, array('G', 'RG'));
      if ($this->page > 0) $this->_out($this->DrawColor);
    }

    /**
     * Set color for all filling operations
     *
     * @access  public
     * @param   int r
     * @param   int g default -1
     * @param   int b default -1
     */
    function setFillColor($r, $g= -1, $b =-1) {
      $this->FillColor= $this->_colorspec($r, $g, $b, array('g', 'rg'));
      $this->ColorFlag= ($this->FillColor != $this->TextColor);
      if ($this->page > 0) $this->_out($this->FillColor);
    }

    /**
     * Set color for text
     *
     * @access  public
     * @param   int r
     * @param   int g default -1
     * @param   int b default -1
     */
    function setTextColor($r, $g= -1, $b =-1) {
      $this->TextColor= $this->_colorspec($r, $g, $b, array('g', 'rg'));
      $this->ColorFlag= ($this->FillColor != $this->TextColor);
    }

    /**
     * Get width of a string in the current font
     *
     * @access  public
     * @param   string s
     * @return  float
     */
    function getStringWidth($s) {
      $s= (string)$s; // Explicitely cast to a string

      for ($i= 0, $l= strlen($s), $w= 0; $i < $l; $i++) {
        $w+= $this->CurrentFont->cw[$s{$i}];
      }
      return $w * $this->FontSize / 1000;
    }

    /**
     * Set line width
     *
     * @access  public
     * @param   int width
     */
    function setLineWidth($width) {
      $this->LineWidth= $width;
      if ($this->page > 0) $this->_out($width.' w');
    }

    /**
     * Draw a line
     *
     * @access  public
     * @param   int x1
     * @param   int y1
     * @param   int x2
     * @param   int y2
     */
    function Line($x1, $y1, $x2, $y2) {
      $this->_out($x1.' -'.$y1.' m '.$x2.' -'.$y2.' l S');
    }

    /**
     * Draw a rectangle
     *
     * @access  public
     * @param   int x
     * @param   int y
     * @param   int w width
     * @param   int h height
     * @param   string $style default '' one of 'F', 'FD', 'DF' or ''
     */
    function Rect($x, $y, $w, $h, $style= '') {
      switch ($style) {
        case 'F': $op= 'f'; break;
        case 'FD':
        case 'DF': $op= 'B'; break;
        default: $op= 'S';
      }
      $this->_out($x.' -'.$y.' '.$w.' -'.$h.' re '.$op);
    }

    /**
     * Add a font to this PDF
     *
     * @access  public
     * @param   &org.pdf.FPDFFont font
     */
    function addFont(&$font) {
      if (isset($this->fonts[$font->family.$font->style])) return 1;
      
      // Diff?
      if (!empty($font->diff)) {
        if (FALSE === ($d= array_search($diff, $this->diffs))) $d= sizeof($this->diffs)+ 1;
        $this->diffs[$d]= $font->diff;
      }
      
      // Font-File?
      if (!empty($font->file)) {
        $this->FontFile[$font->file]= array('originalsize' => $font->originalsize);
      }
      
      $font->index= sizeof($this->fonts)+ 1;
      $this->fonts[$font->family.$font->style]= &$font;
    }
    
    /**
     * Retrieve a font by name and optionally style. Returns NULL if font 
     * wasn't found
     *
     * @access  public
     * @param   string family
     * @param   string style default ''
     * @return  &org.pdf.FPDFFont
     */
    function &getFontByName($family, $style= '') {
      if (!isset($this->fonts[$idx= strtolower($family).strtoupper($style)])) {
        return NULL;
      }
      
      return $this->fonts[$idx];
    }

    /**
     * Set font
     *
     * @access  public
     * @param   &org.pdf.FPDFFont font
     * @param   float size default 0 Font size in points. 
     * @throws  lang.IllegalArgumentException
     * @return  bool TRUE if the font was changed
     */
    function setFont(&$font, $size= 0) {
      if (!is_a($font, 'FPDFFont')) {
        return throw(new IllegalArgumentException('Font is not a org.pdf.FPDFFont'));
      }
      
      $this->underline= $font->isUnderline();
      if ($size == 0) $size= $this->FontSizePt;
      
      // Test if font is already selected
      if (
        ($this->FontFamily == $font->family) and 
        ($this->FontStyle  == $font->style) and 
        ($this->FontSizePt == $size)
      ) return FALSE;

      // Select it
      $this->FontFamily= $font->family;
      $this->FontStyle= $font->style;
      $this->FontSizePt= $size;
      $this->FontSize= round($size / $this->k, 2);
      $this->CurrentFont= &$font;
      
      if ($this->page > 0) $this->_out('BT /F'.$this->CurrentFont->index.' '.$this->FontSize.' Tf ET');
      return TRUE;
    }

    /**
     * Set font size in points
     *
     * @access  public
     * @param   int size
     * @return  bool TRUE if the font size was changed
     */
    function setFontSize($size) {
      if ($this->FontSizePt == $size) return FALSE;

      $this->FontSizePt= $size;
      $this->FontSize= round($size / $this->k, 2);
      if ($this->page > 0) $this->_out('BT /F'.$this->CurrentFont->index.' '.$this->FontSize.' Tf ET');
      return TRUE;
    }

    /**
     * Creates a new internal link and returns its identifier. An internal 
     * link is a clickable area which directs to another place within the 
     * document. 
     *
     * @access  public
     * @return  int
     * @see     xp://org.fpdf.FPDF#setLink
     */
    function addLink() {
      $n= sizeof($this->links)+ 1;
      $this->links[$n]= array(0, 0);
      return $n;
    }

    /**
     * Set destination of internal link
     *
     * Example: Set link to the top of page #1
     * <code>
     *   $pdf->setLink($pdf->addLink(), 0, 1);
     * </code>
     *
     * @access  public
     * @param   int link The link identifier returned by addLink().
     * @param   float y default 0 Ordinate of target position; -1 indicates the current position. The default value is 0 (top of page).
     * @param   int page default -1 Number of target page; -1 indicates the current page. This is the default value.
     */
    function setLink($link, $y= 0, $page= -1) {
      if ($y == -1) $y= $this->y;
      if ($page == -1) $page= $this->page;

      $this->links[$link]= array($page, $this->hPt - $y * $this->k);
    }

    /**
     * Put a link on the page
     *
     * @access  public
     * @param   int x
     * @param   int y
     * @param   int w width
     * @param   int h height
     * @param   int link
     */
    function Link($x, $y, $w, $h, $link) {
      $this->PageLinks[$this->page][]= array(
        $x * $this->k, 
        $this->hPt- $y * $this->k, 
        $w * $this->k, 
        $h * $this->k, 
        $link
      );
    }

    /**
     * Output a string
     *
     * @access  public
     * @param   int x
     * @param   int y
     * @param   string text
     */
    function Text($x, $y, $text) {
      $text= $this->_escape($text);
      
      $s= 'BT '.$x.' -'.$y.' Td ('.$text.') Tj ET';
      if ($this->underline and $text != '') $s.= ' '.$this->_dounderline($x, $y, $text);
      if ($this->ColorFlag) $s= 'q '.$this->TextColor.' '.$s.' Q';

      $this->_out($s);
    }

    /**
     * Accept automatic page break or not
     *
     * @access  public
     * @return  bool
     */
    function getAcceptPageBreak() {
      return $this->AutoPageBreak;
    }

    /**
     * Output a cell
     *
     * @access  public
     * @param   int w
     * @param   int h default 0
     * @param   string text default ''
     * @param   mixed border default FALSE
     * @param   int ln default  0
     * @param   string align default ''
     * @param   string fill default FALSE
     * @param   string link default ''
     */
    function Cell($w, $h= 0, $text= '', $border= FALSE, $ln= 0, $align= '', $fill= FALSE, $link= '') {
    
      // Check if we need to add a page break
      if ($this->y + $h > $this->PageBreakTrigger and $this->AutoPageBreak) {
        $x= $this->x;
        $ws= $this->ws;
        if ($ws > 0) {
          $this->ws= 0;
          $this->_out('0 Tw');
        }
        $this->addPage($this->CurOrientation);
        $this->x= $x;
        if ($ws > 0) {
          $this->ws= $ws;
          $this->_out($ws.' Tw');
        }
      }
      
      // Calculate width if not specified
      if ($w == 0) $w= $this->w- $this->rMargin- $this->x;

      // Fill / border
      $s= '';
      if ($fill || $border) {
        $s.= $this->x.' -'.$this->y.' '.$w.' -'.$h.' re ';
        $s.= $fill ? ($border ? 'B ' : 'f ') : 'S ';
      }
      
      // Border given a string containg L, T, R and/or B
      if (is_string($border)) {
        if (is_int(strpos($border, 'L'))) {     // Left border
          $s.= $this->x.' -'.$this->y.' m '.$this->x.' -'.($this->y+ $h).' l S ';
        }
        if (is_int(strpos($border, 'T'))) {     // Top border
          $s.= $this->x.' -'.$this->y.' m '.($this->x+ $w).' -'.$this->y.' l S ';
        }
        if (is_int(strpos($border, 'R'))) {     // Right border
          $s.= ($this->x+ $w).' -'.$this->y.' m '.($this->x+ $w).' -'.($this->y+ $h).' l S ';
        }
        if (is_int(strpos($border, 'B'))) {     // Bottom border
          $s.= $this->x.' -'.($this->y+ $h).' m '.($this->x+ $w).' -'.($this->y+ $h).' l S ';
        }
      }
      
      // Text
      if ($text != '') {
        switch ($align) {
          case 'R': 
            $dx= $w- $this->cMargin- $this->GetStringWidth($text);
            break;
          
          case 'C':
            $dx= ($w- $this->GetStringWidth($text)) / 2;
            break;
          
          default:
            $dx= $this->cMargin;
        }

        $text= $this->_escape($text);
        if ($this->ColorFlag) $s.='q '.$this->TextColor.' ';
        $s.= 'BT '.($this->x+ $dx).' -'.($this->y+ .5 * $h+ .3* $this->FontSize).' Td ('.$text.') Tj ET';
        if ($this->underline) {
          $s.=' '.$this->_dounderline($this->x+ $dx, $this->y+ .5 * $h+ .3 * $this->FontSize, $text);
        }
        if ($this->ColorFlag) $s.=' Q';
        
        // A link?
        if ($link) $this->Link(
          $this->x+ $this->cMargin, 
          $this->y+ .5 * $h- .5 * $this->FontSize, 
          $this->GetStringWidth($text), 
          $this->FontSize, 
          $link
        );
      }
      if ($s) $this->_out($s);

      // Store last cell height
      $this->lasth= $h;
      
      // Go to next line
      if ($ln > 0) {
        $this->y+= $h;
        if ($ln == 1) $this->x= $this->lMargin;
      } else {
        $this->x+= $w;
      }
    }

    /**
     * Output text with automatic or explicit line breaks
     *
     * @access  public
     * @param   int w
     * @param   int h
     * @param   string txt
     * @param   int border default 0
     * @param   string align default 'J'
     * @param   int fill default 0
     */
    function MultiCell($w, $h, $txt, $border= 0, $align= 'J', $fill= 0) {
      if (!$w) $w= $this->w- $this->rMargin- $this->x;
      $wmax= ($w- 2 * $this->cMargin) * 1000 / $this->FontSize;
      $s= str_replace("\r",'', $txt);
      $nb= strlen($s);
      if ($nb > 0 and $s{$nb- 1} == "\n") $nb--;

      $b= 0;
      if ($border) {
        if ($border == 1) {
          $border= 'LTRB';
          $b= 'LRT';
          $b2= 'LR';
        } else {
          $b2= '';
          if (is_int(strpos($border,'L'))) $b2.='L';
          if (is_int(strpos($border,'R'))) $b2.='R';
          $b= is_int(strpos($border,'T')) ? $b2.'T' : $b2;
        }
      }
      
      $sep= -1;
      $i= $j= $l= $ns= 0;
      $nl= 1;
      while ($i < $nb) {
        if ("\n" == $s{$i}) {           // Explicit line break
          if ($this->ws > 0) {
            $this->ws= 0;
            $this->_out('0 Tw');
          }
          $this->Cell($w, $h,substr($s, $j, $i- $j), $b, 2, $align, $fill);
          $i++;
          $sep= -1;
          $j= $i;
          $l= $ns= 0;
          $nl++;
          if ($border and $nl == 2) $b= $b2;
          continue;
        }
        
        if (' ' == $s{$i}) {            // Space
          $sep= $i;
          $ls= $l;
          $ns++;
        }
        
        $l+= $this->CurrentFont->cw[ord($s{$i})];
        if ($l > $wmax) {               // Automatic line break
          if ($sep == -1) {
            if ($i == $j) $i++;
            if ($this->ws > 0) {
              $this->ws= 0;
              $this->_out('0 Tw');
            }
            $this->Cell($w, $h,substr($s, $j, $i- $j), $b, 2, $align, $fill);
          } else {
            if ($align == 'J') {
              $this->ws= ($ns > 1) ? round(($wmax- $ls) / 1000 * $this->FontSize / ($ns- 1), 3) : 0;
              $this->_out($this->ws.' Tw');
            }
            $this->Cell($w, $h,substr($s, $j, $sep- $j), $b, 2, $align, $fill);
            $i= $sep+ 1;
          }
          $sep= -1;
          $j= $i;
          $l= 0;
          $ns= 0;
          $nl++;
          if ($border and $nl == 2) $b= $b2;
        } else {
          $i++;
        }
      }
       
      // Last chunk
      if ($this->ws > 0) {
        $this->ws= 0;
        $this->_out('0 Tw');
      }
      if ($border and is_int(strpos($border,'B'))) $b.='B';
      $this->Cell($w, $h,substr($s, $j, $i), $b, 2, $align, $fill);
      $this->x= $this->lMargin;
    }

    /**
     * Output text in flowing mode
     *
     * This method prints text from the current position. When the right 
     * margin is reached (or the \n character is met) a line break 
     * occurs and text continues from the left margin. Upon method 
     * exit, the current position is left just at the end of the text.
     *
     * It is possible to put a link on the text.
     *
     * @access  public
     * @param   float h Line height.
     * @param   string txt String to print.
     * @param   mixed link default '' URL or identifier returned by addLink().
     */
    function write($h, $text, $link= '') {
      $w= $this->w- $this->rMargin- $this->x;
      $wmax= ($w- 2 * $this->cMargin) * 1000 / $this->FontSize;
      $s= str_replace("\r", '', $text);
      $nb= strlen($s);
      $sep= -1;
      $i= $j= $l= 0;
      $nl= 1;
      while ($i < $nb) {
        if ("\n" == $s{$i}) {           // Explicit line break
          $this->Cell($w, $h,substr($s, $j, $i- $j), 0, 2, '', 0, $link);
          $i++;
          $sep= -1;
          $j= $i;
          $l= 0;
          if ($nl == 1) {
            $this->x= $this->lMargin;
            $w= $this->w- $this->rMargin- $this->x;
            $wmax= ($w- 2 * $this->cMargin) * 1000 / $this->FontSize;
          }
          $nl++;
          continue;
        }

        if (' ' == $s{$i}) {            // Space
          $sep= $i;
          $ls= $l;
        }

        $l+= $this->CurrentFont->cw[ord($s{$i})];
        if ($l > $wmax) {               // Automatic line break
          if ($sep == -1) {
            if ($this->x > $this->lMargin) {

              // Move to next line
              $this->x= $this->lMargin;
              $this->y+=$h;
              $w= $this->w- $this->rMargin- $this->x;
              $wmax= ($w- 2 * $this->cMargin) * 1000 / $this->FontSize;
              $i++;
              $nl++;
              continue;
            }
            
            if ($i == $j) $i++;
            $this->Cell($w, $h, substr($s, $j, $i- $j), 0, 2, '', 0, $link);
          } else {
            $this->Cell($w, $h, substr($s, $j, $sep- $j), 0, 2, '', 0, $link);
            $i= $sep+ 1;
          }
          $sep= -1;
          $j= $i;
          $l= 0;
          if ($nl == 1) {
            $this->x= $this->lMargin;
            $w= $this->w- $this->rMargin- $this->x;
            $wmax= ($w- 2 * $this->cMargin) * 1000 / $this->FontSize;
          }
          $nl++;
        } else {
          $i++;
        }
      }

      // Last chunk
      if ($i != $j) {
        $w= round($l / 1000 * $this->FontSize, 2);
        $this->Cell($w, $h, substr($s, $j, $i), 0, 0, '', 0, $link);
      }
    }

    /**
     * Put an image on the page
     *
     * @access  public
     * @param   string file
     * @param   int x
     * @param   int y
     * @param   int w
     * @param   int h default 0
     * @param   string type default ''
     * @param   string link default ''
     */
    function Image($file, $x, $y, $w, $h= 0, $type= '', $link= '') {
      if (!isset($this->images[$file])) {

        // First use of image, get info
        if ($type == '') {
          $pos= strrpos($file, '.');
          if (!$pos) {
            return throw(new Exception('Image file has no extension and no type was specified: '.$file));
          }
          $type= substr($file, $pos+ 1);
        }
        
        switch (strtolower($type)) {
          case 'jpg': 
          case 'jpeg':
            $info= $this->_parsejpg($file);
            break;
          
          case 'png':
            $info= $this->_parsepng($file);
            break;
          
          default:
            return throw(new Exception('Unsupported image file type: '.$type));
        }
        
        $info['n']= sizeof($this->images)+ 1;
        $this->images[$file]= $info;
      } else {
        $info= $this->images[$file];
      }
      
      // Automatic width or height calculus
      if (!$w) $w= round($h * $info['w'] / $info['h'], 2);
      if (!$h) $h= round($w * $info['h'] / $info['w'], 2);

      $this->_out('q '.$w.' 0 0 '.$h.' '.$x.' -'.($y+ $h).' cm /I'.$info['n'].' Do Q');
      if ($link) $this->Link($x, $y, $w, $h, $link);
    }

    /**
     * Line feed; default value is last cell height
     *
     * @access  public
     * @param   int h default -1
     */
    function Ln($h= -1) {
      $this->x= $this->lMargin;
      $this->y+= ($h < 0) ? $this->lasth : $h;
    }

    /**
     * Get x position
     *
     * @access  public
     * @return  int
     */
    function getX() {
      return $this->x;
    }

    /**
     * Set x position. Negative values calculate the x position relative
     * to the width.
     *
     * @access  public
     * @param   int x
     */
    function setX($x) {
      $this->x= ($x < 0) ? $this->w+ $x : $x;
    }

    /**
     * Get y position
     *
     * @access  public
     * @return  int
     */
    function getY() {
      return $this->y;
    }

    /**
     * Set y position and reset x. Negative values calculate the y position 
     * relative to the height.
     *
     * @access  public
     * @param   int y
     */
    function setY($y) {
      $this->x= $this->lMargin;
      $this->y= ($y < 0) ? $this->h+ $y : $y;
    }

    /**
     * Set x and y positions
     *
     * @access  public
     * @param   int x
     * @param   int y
     */
    function setXY($x, $y) {
      $this->SetY($y);
      $this->SetX($x);
    }

    /**
     * Retrieve current buffer
     *
     * @access  public
     * @return  string
     */
    function getBuffer() {
      if ($this->state < 3) $this->Close();
      return $this->buffer;
    }

    /**
     * Start document
     *
     * @access  public
     */
    function _begindoc() {
      $this->state= 1;
      $this->_out('%PDF-1.3');
    }

    /**
     * Terminate document
     *
     * @access  public
     */
    function _enddoc() {
      $nb= $this->page;

      if ($this->DefOrientation == 'P') {
        $wPt= $this->fwPt;
        $hPt= $this->fhPt;
      } else {
        $wPt= $this->fhPt;
        $hPt= $this->fwPt;
      }
      
      $filter= ($this->compress) ? '/Filter /FlateDecode ' : '';
      
      // Go through all pages, outputting them one at a time
      for($n= 1; $n <= $nb; $n++) {
        $this->_newobj();
        $this->_out('<</Type /Page');
        $this->_out('/Parent 1 0 R');
        if (isset($this->OrientationChanges[$n])) {
          $this->_out('/MediaBox [0 0 '.$hPt.' '.$wPt.']');
        }
        $this->_out('/Resources 2 0 R');
        
        // Page links
        if (isset($this->PageLinks[$n])) {
          $annots= '/Annots [';
          foreach ($this->PageLinks[$n] as $pl) {
            $rect= round($pl[0], 2).' '.round($pl[1], 2).' '.round($pl[0]+ $pl[2], 2).' '.round($pl[1]- $pl[3], 2);
            $annots.= '<</Type /Annot /Subtype /Link /Rect ['.$rect.'] /Border [0 0 0] ';
            if (is_string($pl[4])) {
              $annots.='/A <</S /URI /URI ('.$pl[4].')>>>>';
            } else {
              $l= $this->links[$pl[4]];
              $annots.='/Dest ['.(1+ 2* $l[0]).' 0 R /XYZ 0 '.$l[1].' null]>>';
            }
          }
          $this->_out($annots.']');
        }
        $this->_out('/Contents '.($this->n+1).' 0 R>>');
        $this->_out('endobj');

        // Page content
        $p= ($this->compress) ? gzcompress($this->pages[$n]) : $this->pages[$n];
        $this->_newobj();
        $this->_out('<<'.$filter.'/Length '.strlen($p).'>>');
        $this->_out('stream');
        $this->_out($p.'endstream');
        $this->_out('endobj');
      }

      // Font encodings
      $nf= $this->n;
      foreach ($this->diffs as $diff) {
        $this->_newobj();
        $this->_out('<</Type /Encoding /BaseEncoding /WinAnsiEncoding /Differences ['.$diff.']>>');
        $this->_out('endobj');
      }
      
      // Font file embedding
      foreach ($this->FontFiles as $file => $info) {
        $this->_newobj();
        $this->FontFiles[$file]['n']= $this->n;
        if (defined('FPDF_FONTPATH')) $file= FPDF_FONTPATH.$file;
        $size= filesize($file);
        if (!$size) return throw(new Exception('Font file not found'));
        
        $this->_out('<</Length '.$size);
        if (substr($file,-2) == '.z') $this->_out('/Filter /FlateDecode');
        $this->_out('/Length1 '.$info['originalsize']);
        $this->_out('>>');
        $this->_out('stream');
        $f= fopen($file,'rb');
        $this->_out(fread($f, $size));
        fclose($f);
        $this->_out('endstream');
        $this->_out('endobj');
      }

      // Font objects
      foreach (array_keys($this->fonts) as $key) {
        with ($font= &$this->fonts[$key]); {
          $this->_newobj();
          $font->n= $this->n;
          $this->_out('<</Type /Font');
          $this->_out('/BaseFont /'.$font->fontname);

          if ($font->type == '__CORE__') {        // Standard font
            $this->_out('/Subtype /Type1');
            if ($font->fontname != 'Symbol' and $font->fontname != 'ZapfDingbats') {
              $this->_out('/Encoding /WinAnsiEncoding');
            }
          } else {                                // TrueType
            $this->_out('/Subtype /TrueType');
            $this->_out('/FirstChar 32');
            $this->_out('/LastChar 255');
            $this->_out('/Widths '.($this->n+ 1).' 0 R');
            $this->_out('/FontDescriptor '.($this->n+ 2).' 0 R');
            if ($font->enc) {
              if (isset($font->diff)) {
                $this->_out('/Encoding '.($nf+ $font->diff).' 0 R');
              } else {
                $this->_out('/Encoding /WinAnsiEncoding');
              }
            }
          }
          $this->_out('>>');
          $this->_out('endobj');

          // Font widths
          if ($font->type != '__CORE__') {
            $this->_newobj();
            $s= '[';
            for ($i= 32; $i <= 255; $i++) $s.= $font->cw[$i].' ';
            $this->_out($s.']');
            $this->_out('endobj');

            // Descriptor
            $this->_newobj();
            $s= '<</Type /FontDescriptor /FontName /'.$font->fontname;
            foreach ($font->desc as $k => $v) $s.=' /'.$k.' '.$v;
            if ($font->file) $s.=' /FontFile2 '.$this->FontFiles[$font->file]['n'].' 0 R';
            $this->_out($s.'>>');
            $this->_out('endobj');
          }
        } 
      }

      // Images
      $ni= $this->n;
      foreach ($this->images as $file => $info) {
        $this->_newobj();
        $this->_out('<</Type /XObject');
        $this->_out('/Subtype /Image');
        $this->_out('/Width '.$info['w']);
        $this->_out('/Height '.$info['h']);
        if ($info['cs'] == 'Indexed') {
          $this->_out('/ColorSpace [/Indexed /DeviceRGB '.(strlen($info['pal']) / 3- 1).' '.($this->n+ 1).' 0 R]');
        } else {
          $this->_out('/ColorSpace /'.$info['cs']);
        }
        $this->_out('/BitsPerComponent '.$info['bpc']);
        $this->_out('/Filter /'.$info['f']);
        if (isset($info['parms'])) $this->_out($info['parms']);
        if (isset($info['trns']) and is_array($info['trns'])) {
          $trns= '';
          for ($i= 0; $i < sizeof($info['trns']); $i++) {
            $trns.= $info['trns'][$i].' '.$info['trns'][$i].' ';
          }
          $this->_out('/Mask ['.$trns.']');
        }
        $this->_out('/Length '.strlen($info['data']).'>>');
        $this->_out('stream');
        $this->_out($info['data']);
        $this->_out('endstream');
        $this->_out('endobj');

        // Palette
        if ($info['cs'] == 'Indexed') {
          $this->_newobj();
          $this->_out('<</Length '.strlen($info['pal']).'>>');
          $this->_out('stream');
          $this->_out($info['pal']);
          $this->_out('endstream');
          $this->_out('endobj');
        }
      }

      // Pages root
      $this->offsets[1]= strlen($this->buffer);
      $this->_out('1 0 obj');
      $this->_out('<</Type /Pages');
      $kids= '/Kids [';
      for ($i= 0; $i < $this->page; $i++) $kids.= (3+ 2* $i).' 0 R ';
      $this->_out($kids.']');
      $this->_out('/Count '.$this->page);
      $this->_out('/MediaBox [0 0 '.$wPt.' '.$hPt.']');
      $this->_out('>>');
      $this->_out('endobj');

      // Resources: Fonts
      $this->offsets[2]= strlen($this->buffer);
      $this->_out('2 0 obj');
      $this->_out('<</ProcSet [/PDF /Text /ImageB /ImageC /ImageI]');
      $this->_out('/Font <<');
      foreach (array_keys($this->fonts) as $key) {
        $this->_out('/F'.$this->fonts[$key]->index.' '.$this->fonts[$key]->n.' 0 R');
      }
      $this->_out('>>');
      
      // Resources: Images
      if (!empty($this->images)) {
        $this->_out('/XObject <<');
        $nbpal= 0;
        
        foreach (array_keys($this->images) as $key) {
          $this->_out('/I'.$this->images[$key]['n'].' '.($ni+ $this->images[$key]['n']+ $nbpal).' 0 R');
          if ($this->images[$key]['cs'] == 'Indexed') $nbpal++;
        }
        $this->_out('>>');
      }
      $this->_out('>>');
      $this->_out('endobj');

      // Info
      $this->_newobj();
      $this->_out('<</Producer (FPDF '.FPDF_VERSION.')');
      if (!empty($this->info['title'])) $this->_out('/Title ('.$this->_escape($this->info['title']).')');
      if (!empty($this->info['subject'])) $this->_out('/Subject ('.$this->_escape($this->info['subject']).')');
      if (!empty($this->info['author'])) $this->_out('/Author ('.$this->_escape($this->info['author']).')');
      if (!empty($this->info['keywords'])) $this->_out('/Keywords ('.$this->_escape($this->info['keywords']).')');
      if (!empty($this->info['creator'])) $this->_out('/Creator ('.$this->_escape($this->info['creator']).')');
      $this->_out('/CreationDate (D:'.date('YmdHis').')>>');
      $this->_out('endobj');
 
      // Catalog
      $this->_newobj();
      $this->_out('<</Type /Catalog');
      
      switch ($this->ZoomMode) {
        case 'fullpage': 
          $this->_out('/OpenAction [3 0 R /Fit]'); 
          break;

        case 'fullwidth': 
          $this->_out('/OpenAction [3 0 R /FitH null]'); 
          break;

        case 'real': 
          $this->_out('/OpenAction [3 0 R /XYZ null null 1]'); 
          break;

        default:
          $this->_out('/OpenAction [3 0 R /XYZ null null '.($this->ZoomMode/100).']');
      }
      
      switch ($this->LayoutMode) {
        case 'single':
          $this->_out('/PageLayout /SinglePage');
          break;
        
        case 'continuous':
          $this->_out('/PageLayout /OneColumn');
          break;
        
        case 'two':
          $this->_out('/PageLayout /TwoColumnLeft');
          break;
      }
      $this->_out('/Pages 1 0 R>>');
      $this->_out('endobj');
 
      // Cross-ref
      $o= strlen($this->buffer);
      $this->_out('xref');
      $this->_out('0 '.($this->n+ 1));
      $this->_out('0000000000 65535 f ');

      for ($i= 1; $i <= $this->n; $i++) {
        $this->_out(sprintf('%010d 00000 n ', $this->offsets[$i]));
      }

      // Trailer
      $this->_out('trailer');
      $this->_out('<</Size '.($this->n+ 1));
      $this->_out('/Root '.$this->n.' 0 R');
      $this->_out('/Info '.($this->n- 1).' 0 R>>');
      $this->_out('startxref');
      $this->_out($o);
      $this->_out('%%EOF');
      $this->state= 3;
    }

    /**
     * Begin a page
     *
     * @access  public
     * @param   string orientation
     */
    function _beginpage($orientation) {
      $this->page++;
      $this->pages[$this->page]='';
      $this->state= 2;
      $this->x= $this->lMargin;
      $this->y= $this->tMargin;
      $this->lasth= 0;
      $this->FontFamily= '';

      // Page orientation
      if (!$orientation) {
        $orientation= $this->DefOrientation; 
      } else {
        $orientation= strtoupper($orientation{0});
        if ($orientation != $this->DefOrientation) {
          $this->OrientationChanges[$this->page]= TRUE;
        }
      }

      // Change orientation
      if ($orientation != $this->CurOrientation) {
        if ($orientation == 'P') {
          $this->wPt= $this->fwPt;
          $this->hPt= $this->fhPt;
          $this->w= $this->fw;
          $this->h= $this->fh;
        } else {
          $this->wPt= $this->fhPt;
          $this->hPt= $this->fwPt;
          $this->w= $this->fh;
          $this->h= $this->fw;
        }
        $this->PageBreakTrigger= $this->h- $this->bMargin;
        $this->CurOrientation= $orientation;
      }

      // Set transformation matrix
      $this->_out(round($this->k, 6).' 0 0 '.round($this->k, 6).' 0 '.$this->hPt.' cm');
    }

    /**
     * End of page contents
     *
     * @access  private
     */
    function _endpage() {
      $this->state=1;
    }

    /**
     * Begin a new object
     *
     * @access  private
     */
    function _newobj() {
      $this->n++;
      $this->offsets[$this->n]= strlen($this->buffer);
      $this->_out($this->n.' 0 obj');
    }

    /**
     * Underline text
     *
     * @access  private
     * @param   int x
     * @param   int y
     * @param   string txt
     * @return  string
     */
    function _dounderline($x, $y, $txt) {
      $up= $this->CurrentFont->up;
      $ut= $this->CurrentFont->ut;
      $w= $this->GetStringWidth($txt)+ $this->ws* substr_count($txt, ' ');
      return $x.' -'.($y- $up / 1000 * $this->FontSize).' '.$w.' -'.($ut / 1000 * $this->FontSize).' re f';
    }

    /**
     * Extract info from a JPEG file
     *
     * @access  public
     * @param   string file
     */
    function _parsejpg($file) {
      $a= getimagesize($file);
      if (!$a) {
        return throw(new Exception('Missing or incorrect image file: '.$file));
      }
      if ($a[2] != 2) {
        return throw(new Exception('Not a JPEG file: '.$file));
      }
      
      // Figure out colorspace
      if (!isset($a['channels']) or $a['channels'] == 3) {
        $colspace= 'DeviceRGB';
      } elseif ($a['channels'] == 4) {
        $colspace= 'DeviceCMYK';
      } else {
        $colspace= 'DeviceGray';
      }
      $bpc= isset($a['bits']) ? $a['bits'] : 8;

      // Read whole file
      $f= fopen($file, 'rb');
      $data= fread($f, filesize($file));
      fclose($f);

      return array(
        'w'     => $a[0],
        'h'     => $a[1],
        'cs'    => $colspace,
        'bpc'   => $bpc,
        'f'     => 'DCTDecode',
        'data'  => $data
      );
    }

    /**
     * Extract info from a PNG file
     *
     * @access  public
     * @param   string file
     */
    function _parsepng($file) {
      $f= fopen($file, 'rb');
      if (!$f) {
        return throw(new Exception('Cannot open image file: '.$file));
      }

      // Check signature
      if (fread($f,8) != chr(137).'PNG'.chr(13).chr(10).chr(26).chr(10)) {
        return throw(new Exception('Not a PNG file: '.$file));
      }

      // Read header chunk
      fread($f, 4);
      if (fread($f, 4) != 'IHDR') {
        return throw(new Exception('Incorrect PNG file: '.$file));
      }
      
      $w= $this->_freadint($f);
      $h= $this->_freadint($f);
      $bpc= ord(fread($f, 1));
      if ($bpc > 8) {
        return throw(new Exception('16-bit depth not supported: '.$file));
      }

      // Figure out colorspace      
      switch ($ct= ord(fread($f, 1))) {
        case 0: $colspace= 'DeviceGray'; break;
        case 2: $colspace= 'DeviceRGB'; break;
        case 3: $colspace= 'Indexed'; break;
        default:
          return throw(new Exception('Alpha channel not supported: '.$file));
      }
      
      if (ord(fread($f, 1)) != 0) {
        return throw(new Exception('Unknown compression method: '.$file));
      }
      if (ord(fread($f, 1)) != 0) {
        return throw(new Exception('Unknown filter method: '.$file));
      }
      if (ord(fread($f, 1)) != 0) {
        return throw(new Exception('Interlacing not supported: '.$file));
      }

      fread($f, 4);
      $parms= '/DecodeParms <</Predictor 15 /Colors '.($ct == 2 ? 3 : 1).' /BitsPerComponent '.$bpc.' /Columns '.$w.'>>';

      // Scan chunks looking for palette, transparency and image data
      $pal= '';
      $trns= '';
      $data= '';
      do {
        $n= $this->_freadint($f);
        switch ($type= fread($f, 4)) {
          case 'PLTE':                    // Read palette
            $pal= fread($f, $n);
            fread($f, 4);
            break;

          case 'tRNS':                    // Read transparency info
            $t= fread($f, $n);
            if ($ct == 0) {
              $trns= array(substr($t, 1, 1));
            } elseif ($ct == 2) {
              $trns= array(substr($t, 1, 1), substr($t, 3, 1), substr($t, 5, 1));
            } else {
              $pos= strpos($t, "\0");
              if (is_int($pos)) $trns= array($pos);
            }
            fread($f, 4);
            break;

          case 'IDAT':                    // Read image data block
            $data.= fread($f, $n);
            fread($f, 4);
            break;

          case 'IEND':
            break 2;

          default:
            fread($f, $n+ 4);
        }
      } while ($n);

      if ($colspace == 'Indexed' and empty($pal)) {
        return throw(new Exception('Missing palette in '.$file));
      }
      
      fclose($f);
      return array(
        'w'     => $w,
        'h'     => $h,
        'cs'    => $colspace,
        'bpc'   => $bpc,
        'f'     => 'FlateDecode',
        'parms' => $parms,
        'pal'   => $pal,
        'trns'  => $trns,
        'data'  => $data
      );
    }

    /**
     * Read a 4-byte integer from file
     *
     * @access  private
     * @param   resource f
     * @return  int
     */
    function _freadint($f) {
      return (
        (ord(fread($f, 1)) << 24) + 
        (ord(fread($f, 1)) << 16) + 
        (ord(fread($f, 1)) << 8) + 
        (ord(fread($f, 1)))
      );
    }

    /**
     * Escape a string (add \ before \, ( and ))
     *
     * @access  private
     * @param   string s
     * @return  string
     */
    function _escape($s) {
      return str_replace(')','\\)',str_replace('(','\\(',str_replace('\\','\\\\', $s)));
    }

    /**
     * Output a string
     *
     * @access  private
     * @param   string s
     */
    function _out($s) {
      if (2 == $this->state) {
        $this->pages[$this->page].= $s."\n"; 
      } else {
        $this->buffer.=$s."\n";
      }
    }
  }
?>
