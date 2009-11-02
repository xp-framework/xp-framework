<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  define('FPDF_VERSION',            '2.0');
  
  define('FPDF_LANDSCAPE',          'L');
  define('FPDF_PORTRAIT',           'P');

  // Unit constants
  define('FPDF_UNIT_PT',            'pt');
  define('FPDF_UNIT_MM',            'mm');
  define('FPDF_UNIT_CM',            'cm');
  define('FPDF_UNIT_INCH',          'in');
  
  // Format constants
  define('FPDF_FORMAT_A3',          'A3');
  define('FPDF_FORMAT_A4',          'A4');
  define('FPDF_FORMAT_A5',          'A5');
  define('FPDF_FORMAT_LETTER',      'LETTER');
  define('FPDF_FORMAT_LEGAL',       'LEGAL');
  
  // Zoom constants
  define('FPDF_ZOOM_FULLPAGE',      'fullpage');
  define('FPDF_ZOOM_FULLWIDTH',     'fullwidth');
  define('FPDF_ZOOM_REAL',          'real');
  define('FPDF_ZOOM_DEFAULT',       'default');

  // Layout constants
  define('FPDF_LAYOUT_SINGLE',      'single');
  define('FPDF_LAYOUT_CONTINUOUS',  'continuous');
  define('FPDF_LAYOUT_TWO',         'two');
  define('FPDF_LAYOUT_DEFAULT',     'default');

  // Border flags (bitfield)  
  define('FPDF_BORDER_NONE',        0x0000);
  define('FPDF_BORDER_LEFT',        0x0001);
  define('FPDF_BORDER_RIGHT',       0x0002);
  define('FPDF_BORDER_TOP',         0x0004);
  define('FPDF_BORDER_BOTTOM',      0x0008);
  define('FPDF_BORDER_FRAME',       FPDF_BORDER_LEFT | FPDF_BORDER_RIGHT | FPDF_BORDER_TOP | FPDF_BORDER_BOTTOM);
  
  // Alignment flags, mutually exclusive
  define('FPDF_ALIGN_LEFT',         0x0000);
  define('FPDF_ALIGN_RIGHT',        0x0001);
  define('FPDF_ALIGN_CENTER',       0x0002);
  define('FPDF_ALIGN_JUSTIFY',      0x0003);

  // Positioning flags, mutually exclusive  
  define('FPDF_POS_RIGHT',          0x0000);
  define('FPDF_POS_NEXT_LINE',      0x0001);
  define('FPDF_POS_BELOW',          0x0002);

  // Rectangle flags (bitfield)
  define('FPDF_RECT_DRAW',          0x0001);  
  define('FPDF_RECT_FILL',          0x0002);
  define('FPDF_RECT_FILL_DRAW',     FPDF_RECT_FILL | FPDF_RECT_DRAW);

  // Filling flags, mutually exclusive    
  define('FPDF_FILL_TRANSPARENT',   0x0000);
  define('FPDF_FILL_PAINTED',       0x0001);
  
  // Hook events
  define('PFDF_EVENT_ENDPAGE',      0x0000);
  
  uses(
    'org.fpdf.FPDFFont',
    'org.fpdf.LzwDecompressor',
    'lang.IllegalArgumentException',
    'lang.MethodNotImplementedException'
  );
  
  /**
   * PDF creator
   *
   * @purpose  Create PDFs
   * @see      http://fpdf.org/
   */
  class FPDF extends Object {
    public 
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
    
    protected
      $_hooks             = array();

    /**
     * Constructor
     *
     * @param   string orientation default FPDF_PORTRAIT
     * @param   string unit default FPDF_UNIT_MM
     * @param   string format default FPDF_FORMAT_A4
     */
    public function __construct(
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
      $this->setDisplayMode(FPDF_ZOOM_FULLWIDTH);
      
      // Set compression to TRUE if gzcompress() exists
      $this->setCompression(function_exists('gzcompress'));

      // Initialize hooks
      $this->_hooks[PFDF_EVENT_ENDPAGE]= array();
    }
    
    /**
     * Add a hook
     *
     * @param   string event one of the PFDF_EVENT_* constants
     * @param   org.fpdf.FPDFHook hook
     * @return  org.fpdf.FPDFHook the hook added
     */
    public function addHook($event, FPDFHook $hook) {
      if (!isset($this->_hooks[$event])) $this->_hooks[$event]= array();

      $this->_hooks[$event][]= $hook;
      return $hook;
    }

    /**
     * Load fonts
     *
     * @param   util.Properties prop
     */
    public function loadFonts($prop) {
      $section= $prop->getFirstSection();
      do {
        $f= new FPDFFont($section);
        $f->configure($prop);
        $this->addFont($f);
      } while ($section= $prop->getNextSection());
    }
    
    /**
     * Retrieve scale factor for a specified unit
     *
     * @param   string unit on of the FPDF_UNIT_* constants
     * @return  float
     */
    public function getScaleFactor($unit) {
      static $map= array(
        FPDF_UNIT_PT    => 1,
        FPDF_UNIT_MM    => 2.83464566929,   // 72 / 25.4,
        FPDF_UNIT_CM    => 28.3464566929,   // 72 / 2.54,
        FPDF_UNIT_INCH  => 72
      );

      return $map[$unit];
    }
    
    /**
     * Retrieve page dimensions for a specified format
     *
     * @param   string format on of the FPDF_FORMAT_* constants
     * @return  float[2]
     */
    public function getPageDimensions($format) {
      static $map= array(
        FPDF_FORMAT_A3          => array(841.89, 1190.55),
        FPDF_FORMAT_A4          => array(595.28, 841.89),
        FPDF_FORMAT_A5          => array(420.94, 595.28),
        FPDF_FORMAT_LETTER      => array(612, 792),
        FPDF_FORMAT_LEGAL       => array(612, 1008)
      );
      
      return $map[$format];
    }
   
    /**
     * Set page format
     *
     * @param   string format on of the FPDF_FORMAT_* constants
     */ 
    public function setPageFormat($format) {
      list($this->fwPt, $this->fhPt)= $this->getPageDimensions($format);
      $this->fw= round($this->fwPt / $this->k, 2);
      $this->fh= round($this->fhPt / $this->k, 2);  
    }
    
    /**
     * Set orientation
     *
     * @param   string orientation one of FPDF_PORTRAIT or FPDF_LANDSCAPE
     */
    public function setOrientation($orientation) {
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
     * @param   float left
     * @param   float top
     * @param   float right default -1 Default value is the left one. 
     */
    public function setMargins($left, $top, $right= -1) {
      $this->setLeftMargin($left);
      $this->setTopMargin($top);
      $this->setRightMargin((-1 == $right) ? $left : $right);
    }

    /**
     * Set left margin
     *
     * @param   float margin
     */
    public function setLeftMargin($margin) {
      $this->lMargin= $margin;
      if ($this->page > 0 and $this->x < $margin) $this->x= $margin;
    }

    /**
     * Set top margin
     *
     * @param   float margin
     */
    public function setTopMargin($margin) {
      $this->tMargin= $margin;
    }

    /**
     * Set right margin
     *
     * @param   float margin
     */
    public function setRightMargin($margin) {
      $this->rMargin= $margin;
    }

    /**
     * Set auto page break mode and triggering margin
     *
     * @param   int auto
     * @param   int margin default 0
     */
    public function setAutoPageBreak($auto, $margin= 0) {
      $this->AutoPageBreak= $auto;
      $this->bMargin= $margin;
      $this->PageBreakTrigger= $this->h- $margin;
    }

    /**
     * Set display mode in viewer
     *
     * @param   mixed zoom either one of the FPDF_ZOOM_* constants or a number indicating the zooming factor to use.
     * @param   string layout default FPDF_LAYOUT_CONTINUOUS
     */
    public function setDisplayMode($zoom, $layout= FPDF_LAYOUT_CONTINUOUS) {
      $this->ZoomMode= $zoom;
      $this->LayoutMode= $layout;
    }

    /**
     * Turns page compression on or off. Throws an exception in case 
     * compression was requested (set to TRUE) but not available.
     *
     * Compression is possible if PHP is compiled with zlib and the
     * function gzcompress() is available.
     *
     * @see     php://gzcompress
     * @param   bool compress
     * @throws  lang.MethodNotImplementedException
     */
    public function setCompression($compress) {
      if ($compress && !function_exists('gzcompress')) {
        throw new MethodNotImplementedException('Compression not available');
      }
      $this->compress= $compress;
    }

    /**
     * Sets title of document
     *
     * @param   string title
     */
    public function setTitle($title) {
      $this->info['title']= $title;
    }

    /**
     * Sets subject of document
     *
     * @param   string subject
     */
    public function setSubject($subject) {
      $this->info['subject']= $subject;
    }

    /**
     * Sets author of document
     *
     * @param   string author
     */
    public function setAuthor($author) {
      $this->info['author']= $author;
    }

    /**
     * Associates keywords with the document, generally in the following form:
     * <pre>
     *   'keyword1 keyword2 ...'
     * </pre>
     *
     * @param   string keywords
     */
    public function setKeywords($keywords) {
      $this->info['keywords']= $keywords;
    }

    /**
     * Defines the creator of the document. This is typically the name 
     * of the application that generates the PDF.
     *
     * @param   string creator
     */
    public function setCreator($creator) {
      $this->info['creator']= $creator;
    }

    /**
     * This method begins the generation of the PDF document. It is not 
     * necessary to call it explicitly because AddPage() does it 
     * automatically.
     *
     * Note: no page is created by this method.
     *
     */
    public function open() {
      $this->_begindoc();
    }

    /**
     * Terminates the PDF document. It is not necessary to call this 
     * method explicitly because getBuffer() does it automatically.
     *
     * If the document contains no page, addPage() is called to prevent 
     * from getting an invalid document.
     *
     */
    public function close() {
      if (0 == $this->page) $this->addPage();
      $this->_endpage();
      $this->_enddoc();
    }

    /**
     * Start a new page with an optional orientation, which, if 
     * omitted, defaults to orientation given to constructor
     *
     * @param   string orientation default NULL
     */
    public function addPage($orientation= NULL) {
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
     * @return  int
     */
    public function pageNumber() {
      return $this->page;
    }

    /**
     * Return a color specification
     *
     * @param   int r
     * @param   int g
     * @param   int b
     * @param   string[2] identifiers
     * @return  string
     */    
    protected function _colorspec($r, $g, $b, $identifiers) {

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
     * @param   int r
     * @param   int g default -1
     * @param   int b default -1
     */
    public function setDrawColor($r, $g= -1, $b =-1) {
      $this->DrawColor= $this->_colorspec($r, $g, $b, array('G', 'RG'));
      if ($this->page > 0) $this->_out($this->DrawColor);
    }

    /**
     * Set color for all filling operations
     *
     * @param   int r
     * @param   int g default -1
     * @param   int b default -1
     */
    public function setFillColor($r, $g= -1, $b =-1) {
      $this->FillColor= $this->_colorspec($r, $g, $b, array('g', 'rg'));
      $this->ColorFlag= ($this->FillColor != $this->TextColor);
      if ($this->page > 0) $this->_out($this->FillColor);
    }

    /**
     * Set color for text
     *
     * @param   int r
     * @param   int g default -1
     * @param   int b default -1
     */
    public function setTextColor($r, $g= -1, $b =-1) {
      $this->TextColor= $this->_colorspec($r, $g, $b, array('g', 'rg'));
      $this->ColorFlag= ($this->FillColor != $this->TextColor);
    }

    /**
     * Get width of a string in the current font
     *
     * @param   string s
     * @return  float
     */
    public function getStringWidth($s) {
      $s= (string)$s; // Explicitely cast to a string

      for ($i= 0, $l= strlen($s), $w= 0; $i < $l; $i++) {
        $w+= @$this->CurrentFont->cw[$s{$i}];
      }
      return $w * $this->FontSize / 1000;
    }

    /**
     * Set line width
     *
     * @param   int width
     */
    public function setLineWidth($width) {
      $this->LineWidth= $width;
      if ($this->page > 0) $this->_out($width.' w');
    }

    /**
     * Renders a line
     *
     * @param   int x1
     * @param   int y1
     * @param   int x2
     * @param   int y2
     * @return  string
     */
    protected function _renderline($x1, $y1, $x2, $y2) {
      return $x1.' -'.$y1.' m '.$x2.' -'.$y2.' l S';
    }

    /**
     * Draw a line
     *
     * @param   int x1
     * @param   int y1
     * @param   int x2
     * @param   int y2
     */
    public function drawLine($x1, $y1, $x2, $y2) {
      $this->_out($this->_renderline($x1, $y1, $x2, $y2));
    }

    /**
     * Renders a rectangle
     *
     * @param   int x
     * @param   int y
     * @param   int w width
     * @param   int h height
     * @param   int style bitfield of FPDF_RECT_* constants
     * @return  string
     */
    protected function _renderrect($x, $y, $w, $h, $style= NULL) {
      static $ops= array(
        FPDF_RECT_DRAW       => 'S',
        FPDF_RECT_FILL       => 'f',
        FPDF_RECT_FILL_DRAW  => 'B'
      );
      return $x.' -'.$y.' '.$w.' -'.$h.' re '.($style ? $ops[$style] : '');
    }

    /**
     * Draw a rectangle
     *
     * @param   int x
     * @param   int y
     * @param   int w width
     * @param   int h height
     * @param   int style default FPDF_RECT_DRAW bitfield of FPDF_RECT_* constants
     */
    public function drawRect($x, $y, $w, $h, $style= FPDF_RECT_DRAW) {
      $this->_out($this->_renderrect($x, $y, $w, $h, $style));
    }

    /**
     * Add a font to this PDF
     *
     * @param   &org.pdf.FPDFFont font
     */
    public function addFont($font) {
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
      $this->fonts[$font->family.$font->style]= $font;
    }
    
    /**
     * Retrieve a font by name and optionally style. Returns NULL if font 
     * wasn't found
     *
     * @param   string family
     * @param   string style default ''
     * @return  &org.pdf.FPDFFont
     */
    public function getFontByName($family, $style= '') {
      if (!isset($this->fonts[$idx= strtolower($family).strtoupper($style)])) {
        return NULL;
      }
      
      return $this->fonts[$idx];
    }
    
    /**
     * Get font
     *
     * @return  &org.pdf.FPDFFont
     */
    public function getFont() {
      return $this->CurrentFont;
    }

    /**
     * Set font
     *
     * @param   org.pdf.FPDFFont font
     * @param   float size default 0 Font size in points. 
     * @throws  lang.IllegalArgumentException
     * @return  bool TRUE if the font was changed
     */
    public function setFont(FPDFFont $font, $size= 0) {
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
      $this->CurrentFont= $font;
      
      if ($this->page > 0) $this->_out('BT /F'.$this->CurrentFont->index.' '.$this->FontSize.' Tf ET');
      return TRUE;
    }

    /**
     * Set font size in points
     *
     * @param   int size
     * @return  bool TRUE if the font size was changed
     */
    public function setFontSize($size) {
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
     * @return  int
     * @see     xp://org.fpdf.FPDF#setLink
     */
    public function addLink() {
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
     * @param   int link The link identifier returned by addLink().
     * @param   float y default 0 Ordinate of target position; -1 indicates the current position. The default value is 0 (top of page).
     * @param   int page default -1 Number of target page; -1 indicates the current page. This is the default value.
     */
    public function setLink($link, $y= 0, $page= -1) {
      if ($y == -1) $y= $this->y;
      if ($page == -1) $page= $this->page;

      $this->links[$link]= array($page, $this->hPt - $y * $this->k);
    }

    /**
     * Puts a link on a rectangular area of the page. Text or image 
     * links are generally put via cell(), write() or image(), but 
     * this method can be useful for instance to define a clickable 
     * area inside an image.
     *
     * @param   int x
     * @param   int y
     * @param   int w width
     * @param   int h height
     * @param   mixed link URL or identifier returned by addLink().
     */
    public function putLink($x, $y, $w, $h, $link) {
      $this->PageLinks[$this->page][]= array(
        $x * $this->k, 
        $this->hPt- $y * $this->k, 
        $w * $this->k, 
        $h * $this->k, 
        $link
      );
    }

    /**
     * Renders text
     *
     * @param   int x Abscissa of the origin.
     * @param   int y Ordinate of the origin.
     * @param   string text
     * @return  string
     */
    protected function _rendertext($x, $y, $text) {
      $text= $this->_escape($text);
      $s= 'BT '.$x.' -'.$y.' Td ('.$text.') Tj ET';
      if ($this->underline and $text != '') {
        $s.= ' '.$this->_renderunderline($x, $y, $text);
      }
      if ($this->ColorFlag) $s= 'q '.$this->TextColor.' '.$s.' Q';
      return $s;
    }

    /**
     * Prints a character string. The origin is on the left of the first 
     * charcter, on the baseline. This method allows to place a string 
     * precisely on the page, but it is usually easier to use cell(), 
     * MultiCell() or Write() which are the standard methods to print 
     * text.
     *
     * @param   int x Abscissa of the origin.
     * @param   int y Ordinate of the origin.
     * @param   string text
     */
    public function putText($x, $y, $text) {
      $this->_out($this->_rendertext($s));
    }

    /**
     * Accept automatic page break or not
     *
     * @return  bool
     */
    public function getAcceptPageBreak() {
      return $this->AutoPageBreak;
    }

    /**
     * Prints a cell (rectangular area) with optional borders, background 
     * color and character string. The upper-left corner of the cell 
     * corresponds to the current position. The text can be aligned or 
     * centered. After the call, the current position moves to the right 
     * or to the next line. It is possible to put a link on the text.
     *
     * If automatic page breaking is enabled and the cell goes beyond the 
     * limit, a page break is done before outputting.
     *
     * The parameter ln indicates where the current position should go 
     * after the call.
     *
     * @param   float w Cell width. If 0, the cell extends up to the right margin.
     * @param   float h default 0 Cell height. Default value: 0.
     * @param   string text default '' String to print.
     * @param   int border default FPDF_BORDER_NONE one of the FPDF_BORDER_* constants
     * @param   int ln default FPDF_POS_RIGHT one of the FPDF_POS_* constants
     * @param   int align default FPDF_ALIGN_LEFT one of the FPDF_ALIGN_* constants
     * @param   int fill default FPDF_FILL_TRANSPARENT one of the FPDF_FILL_* constants
     * @param   mixed link default '' URL or identifier returned by addLink().
     */
    public function writeCell(
      $w, 
      $h= 0, 
      $text= '', 
      $border= FPDF_BORDER_NONE, 
      $ln= FPDF_POS_RIGHT, 
      $align= FPDF_ALIGN_LEFT, 
      $fill= FPDF_FILL_TRANSPARENT, 
      $link= ''
    ) {
    
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
      if ($fill || $border & FPDF_BORDER_FRAME) {
        $s.= $this->_renderrect($this->x, $this->y, $w, $h);
        $s.= $fill ? ($border & FPDF_BORDER_FRAME ? 'B ' : 'f ') : 'S ';
      }

      // Specific border sides given      
      if (!($border & FPDF_BORDER_FRAME)) {
        if ($border & FPDF_BORDER_LEFT) {
          $s.= $this->_renderline($this->x, $this->y, $this->x, $this->y+ $h);
        }
        if ($border & FPDF_BORDER_TOP) {
          $s.= $this->_renderline($this->x, $this->y, $this->x+ $w, $this->y);
        }
        if ($border & FPDF_BORDER_RIGHT) {
          $s.= $this->_renderline($this->x+ $w, $this->y, $this->x+ $w, $this->y+ $h);
        }
        if ($border & FPDF_BORDER_BOTTOM) {
          $s.= $this->_renderline($this->x, $this->y+ $h, $this->x+ $w, $this->y+ $h);
        }
      }
      
      // Text
      if ($text != '') {
        switch ($align) {
          case FPDF_ALIGN_RIGHT: 
            $dx= $w- $this->cMargin- $this->getStringWidth($text);
            break;
          
          case FPDF_ALIGN_CENTER:
            $dx= ($w- $this->getStringWidth($text)) / 2;
            break;
          
          case FPDF_ALIGN_LEFT:
          default:
            $dx= $this->cMargin;
        }

        $s.= $this->_rendertext(
          $this->x+ $dx, 
          $this->y+ .5 * $h+ .3* $this->FontSize, 
          $text
        );
        
        // A link?
        if ($link) $this->putLink(
          $this->x+ $this->cMargin, 
          $this->y+ .5 * $h- .5 * $this->FontSize, 
          $this->getStringWidth($text), 
          $this->FontSize, 
          $link
        );
      }
      if ($s) $this->_out($s);

      // Store last cell height
      $this->lasth= $h;
      
      // Set position
      switch ($ln) {
        case FPDF_POS_RIGHT: $this->x+= $w; break;
        case FPDF_POS_NEXT_LINE: $this->x= $this->lMargin; // break missing intentionally
        case FPDF_POS_BELOW: $this->y+= $h;
      }
    }

    /**
     * This method allows printing text with line breaks. They can be 
     * automatic (as soon as the text reaches the right border of the 
     * cell) or explicit (via the \n character). As many cells as 
     * necessary are output, one below the other.
     *
     * Text can be aligned, centered or justified. The cell block can be 
     * framed and the background painted.
     *
     * @param   int w
     * @param   int h default 5
     * @param   string txt
     * @param   int border default FPDF_BORDER_NONE one of the FPDF_BORDER_* constants
     * @param   int align default FPDF_ALIGN_JUSTIFY one of the FPDF_ALIGN_* constants
     * @param   int fill default FPDF_FILL_TRANSPARENT one of the FPDF_FILL_* constants
     */
    public function writeCells(
      $w, 
      $h= 5, 
      $txt, 
      $border= FPDF_BORDER_NONE, 
      $align= FPDF_ALIGN_JUSTIFY, 
      $fill= FPDF_FILL_TRANSPARENT
    ) {
      if (!$w) $w= $this->w- $this->rMargin- $this->x;
      $wmax= ($w- 2 * $this->cMargin) * 1000 / $this->FontSize;
      $s= str_replace("\r", '', $txt);
      $nb= strlen($s);
      if ($nb > 0 and $s{$nb- 1} == "\n") $nb--;

      $b2= FPDF_BORDER_NONE;
      if ($border & FPDF_BORDER_LEFT) $b2 |= FPDF_BORDER_LEFT;
      if ($border & FPDF_BORDER_RIGHT) $b2 |= FPDF_BORDER_RIGHT;
      $b= ($border & FPDF_BORDER_TOP) ? $b2 | FPDF_BORDER_TOP : $b2;
      
      $sep= -1;
      $i= $j= $l= $ns= 0;
      $nl= 1;
      while ($i < $nb) {
        if ("\n" == $s{$i}) {           // Explicit line break
          if ($this->ws > 0) {
            $this->ws= 0;
            $this->_out('0 Tw');
          }
          $this->writeCell($w, $h,substr($s, $j, $i- $j), $b, FPDF_POS_BELOW, $align, $fill);
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
            $this->writeCell($w, $h,substr($s, $j, $i- $j), $b, FPDF_POS_BELOW, $align, $fill);
          } else {
            if ($align == FPDF_ALIGN_JUSTIFY) {
              $this->ws= ($ns > 1) ? round(($wmax- $ls) / 1000 * $this->FontSize / ($ns- 1), 3) : 0;
              $this->_out($this->ws.' Tw');
            }
            $this->writeCell($w, $h,substr($s, $j, $sep- $j), $b, FPDF_POS_BELOW, $align, $fill);
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
      if ($border & FPDF_BORDER_BOTTOM) $b |= FPDF_BORDER_BOTTOM;
      $this->writeCell($w, $h,substr($s, $j, $i), $b, FPDF_POS_BELOW, $align, $fill);
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
     * @param   float h Line height.
     * @param   string txt String to print.
     * @param   mixed link default '' URL or identifier returned by addLink().
     */
    public function writeText($h, $text, $link= '') {
      $w= $this->w- $this->rMargin- $this->x;
      $wmax= ($w- 2 * $this->cMargin) * 1000 / $this->FontSize;
      $s= str_replace("\r", '', $text);
      $nb= strlen($s);
      $sep= -1;
      $i= $j= $l= 0;
      $nl= 1;
      while ($i < $nb) {
        if ("\n" == $s{$i}) {           // Explicit line break
          $this->writeCell(
            $w, 
            $h,
            substr($s, $j, $i- $j), 
            FPDF_BORDER_NONE, 
            FPDF_POS_BELOW, 
            FPDF_ALIGN_LEFT, 
            FPDF_FILL_TRANSPARENT, 
            $link
          );
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
            $this->writeCell(
              $w, $h, 
              substr($s, $j, $i- $j), 
              FPDF_BORDER_NONE, 
              FPDF_POS_BELOW, 
              FPDF_ALIGN_LEFT, 
              FPDF_FILL_TRANSPARENT, 
              $link
            );
          } else {
            $this->writeCell(
              $w, $h, 
              substr($s, $j, $sep- $j), 
              FPDF_BORDER_NONE, 
              FPDF_POS_BELOW, 
              FPDF_ALIGN_LEFT, 
              FPDF_FILL_TRANSPARENT, 
              $link
            );
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
        $this->writeCell(
          $w, 
          $h, 
          substr($s, $j, $i), 
          FPDF_BORDER_NONE, 
          FPDF_POS_RIGHT, 
          FPDF_ALIGN_LEFT, 
          FPDF_FILL_TRANSPARENT, 
          $link
        );
      }
    }

    /**
     * Put an image on the page
     *
     * @param   string file
     * @param   int x
     * @param   int y
     * @param   int w default 0
     * @param   int h default 0
     * @param   string type default ''
     * @param   string link default ''
     * @throws  lang.IllegalArgumentException
     */
    public function putImage($file, $x, $y, $w= 0, $h= 0, $type= '', $link= '') {
      if (!isset($this->images[$file])) {

        // First use of image, get info
        if ($type == '') {
          if (FALSE === ($pos= strrpos($file, '.'))) {
            throw new IllegalArgumentException(
              'Image file has no extension and no type was specified: '.$file
            );
          }
          $type= substr($file, $pos+ 1);
        }
        
        switch (strtolower($type)) {
          case 'jpg': 
          case 'jpeg':
            $info= $this->_parsejpg($file);
            break;
          
          case 'gif':
            $info= $this->_parsegif($file);
            break;

          case 'png':
            $info= $this->_parsepng($file);
            break;
          
          default:
            throw new IllegalArgumentException(
              'Unsupported image file type: '.$type
            );
        }
        
        $info['n']= sizeof($this->images)+ 1;
        $this->images[$file]= $info;
      } else {
        $info= $this->images[$file];
      }
      
      // Automatic width or height calculus
      if (!$w && !$h) {
        $w= $info['w'] / $this->k;
        $h= $info['h'] / $this->k;
      }
      
      if (!$w) $w= $h * $info['w'] / $info['h'];
      if (!$h) $h= $w * $info['h'] / $info['w'];

      $this->_out('q '.$w.' 0 0 '.$h.' '.$x.' -'.($y+ $h).' cm /I'.$info['n'].' Do Q');
      if ($link) $this->putLink($x, $y, $w, $h, $link);
    }

    /**
     * Line feed; default value is last cell height
     *
     * @param   int h default -1
     */
    public function lineFeed($h= -1) {
      $this->x= $this->lMargin;
      $this->y+= ($h < 0) ? $this->lasth : $h;
    }

    /**
     * Get x position
     *
     * @return  int
     */
    public function getX() {
      return $this->x;
    }

    /**
     * Set x position. Negative values calculate the x position relative
     * to the width.
     *
     * @param   int x
     */
    public function setX($x) {
      $this->x= ($x < 0) ? $this->w+ $x : $x;
    }

    /**
     * Get y position
     *
     * @return  int
     */
    public function getY() {
      return $this->y;
    }

    /**
     * Set y position and reset x. Negative values calculate the y 
     * position relative to the height.
     *
     * @param   int y
     */
    public function setY($y) {
      $this->x= $this->lMargin;
      $this->y= ($y < 0) ? $this->h+ $y : $y;
    }

    /**
     * Set x and y positions
     *
     * @param   int x
     * @param   int y
     */
    public function setXY($x, $y) {
      $this->SetY($y);
      $this->SetX($x);
    }

    /**
     * Retrieve current buffer. The method first calls close() if 
     * necessary to terminate the document.
     *
     * @return  string
     */
    public function getBuffer() {
      if ($this->state < 3) $this->close();
      return $this->buffer;
    }

    /**
     * Start document
     *
     */
    protected function _begindoc() {
      $this->state= 1;
      $this->_out('%PDF-1.3');
    }

    /**
     * Terminate document
     *
     */
    protected function _enddoc() {
      $nb= $this->page;

      if ($this->DefOrientation == FPDF_PORTRAIT) {
        $wPt= $this->fwPt;
        $hPt= $this->fhPt;
      } else {
        $wPt= $this->fhPt;
        $hPt= $this->fwPt;
      }
      
      $filter= ($this->compress) ? '/Filter /FlateDecode ' : '';
      
      // Go through all pages, outputting them one at a time
      for ($n= 1; $n <= $nb; $n++) {
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
        
        // Sanity check
        if (!file_exists($file)) {
          throw new IOException('Font file "'.$file.'" not found');
        }
        
        $size= filesize($file);
        $this->_out('<</Length '.$size);
        if (substr($file,-2) == '.z') $this->_out('/Filter /FlateDecode');
        $this->_out('/Length1 '.$info['originalsize']);
        $this->_out('>>');
        $this->_out('stream');
        $f= fopen($file, 'rb');
        $this->_out(fread($f, $size));
        fclose($f);
        $this->_out('endstream');
        $this->_out('endobj');
      }

      // Font objects
      foreach (array_keys($this->fonts) as $key) {
        with ($font= $this->fonts[$key]); {
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
      foreach ($this->info as $key => $val) {
        $this->_out('/'.ucfirst($key).' ('.$this->_escape($this->info[$key]).')');
      }
      $this->_out('/CreationDate (D:'.date('YmdHis').')>>');
      $this->_out('endobj');
 
      // Catalog
      $this->_newobj();
      $this->_out('<</Type /Catalog');
      
      switch ($this->ZoomMode) {
        case FPDF_ZOOM_DEFAULT:
          // NOOP
          break;
          
        case FPDF_ZOOM_FULLPAGE: 
          $this->_out('/OpenAction [3 0 R /Fit]'); 
          break;

        case FPDF_ZOOM_FULLWIDTH: 
          $this->_out('/OpenAction [3 0 R /FitH null]'); 
          break;

        case FPDF_ZOOM_REAL: 
          $this->_out('/OpenAction [3 0 R /XYZ null null 1]'); 
          break;

        default:
          $this->_out('/OpenAction [3 0 R /XYZ null null '.($this->ZoomMode / 100).']');
      }
      
      switch ($this->LayoutMode) {
        case FPDF_LAYOUT_DEFAULT:
          // NOOP
          break;

        case FPDF_LAYOUT_SINGLE:
          $this->_out('/PageLayout /SinglePage');
          break;
        
        case FPDF_LAYOUT_CONTINUOUS:
          $this->_out('/PageLayout /OneColumn');
          break;
        
        case FPDF_LAYOUT_TWO:
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
     * @param   string orientation
     */
    protected function _beginpage($orientation) {
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
        if ($orientation == FPDF_PORTRAIT) {
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
     */
    protected function _endpage() {
      for ($i= 0, $s= sizeof($this->_hooks[PFDF_EVENT_ENDPAGE]); $i < $s; $i++) {
        $this->_hooks[PFDF_EVENT_ENDPAGE][$i]->onEndPage($this, $this->page);
      }
      $this->state= 1;
    }

    /**
     * Begin a new object
     *
     */
    protected function _newobj() {
      $this->n++;
      $this->offsets[$this->n]= strlen($this->buffer);
      $this->_out($this->n.' 0 obj');
    }

    /**
     * Underline text
     *
     * @param   int x
     * @param   int y
     * @param   string txt
     * @return  string
     */
    protected function _renderunderline($x, $y, $txt) {
      $up= $this->CurrentFont->up;
      $ut= $this->CurrentFont->ut;
      $w= $this->getStringWidth($txt)+ $this->ws * substr_count($txt, ' ');
      return $x.' -'.($y- $up / 1000 * $this->FontSize).' '.$w.' -'.($ut / 1000 * $this->FontSize).' re f';
    }

    /**
     * Extract info from a JPEG file
     *
     * @param   string file
     * @throws  lang.IllegalArgumentException in case image is not a JPEG file
     */
    protected function _parsejpg($file) {
      if (FALSE === ($a= getimagesize($file))) {
        throw new IllegalArgumentException(
          'Missing or incorrect image file: '.$file
        );
      }
      if ($a[2] != IMG_JPEG) {
        throw new IllegalArgumentException(
          'Not a JPEG file: '.$file
        );
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
     * Extract info from a GIF file
     *
     * @param   string file
     * @throws  lang.IllegalArgumentException in case the file is corrupt
     */
    protected function _parsegif($file) {
      $f= fopen($file, 'rb');
      if (!$f) {
        throw new IllegalArgumentException('Cannot open image file: '.$file);
      }
      
      // Check signature
      $version= fread($f, 6);
      if ($version != 'GIF87a' && $version != 'GIF89a') {
        throw new IllegalArgumentException('Not a GIF file: '.$file);
      }
      
      // File header
      $dim= unpack('vw/vh', fread($f, 4));

      $b= ord(fread($f, 1));
      $table= (bool)($b & 0x80);
      $colorres= ($b & 0x70) >> 4;
      $sorted= (bool)($b & 0x08);
      $tablesize= 2 << ($b & 0x07);

      $bgcolor= ord(fread($f, 1));
      $pixelratio= ord(fread($f, 1));
      
      // Read colortable
      if ($table) {
        $colortable= array();
        for ($i= 0; $i < $tablesize; $i++) {
          $rgb= fread($f, 3);
          $colortable[]= (ord($rgb{2}) << 16) + (ord($rgb{1}) << 8) + ord($rgb{0});
        }
      }
      
      // Images
      $trans= $user= $delay= FALSE;
      $ntrans= 0;
      $comment= NULL;
      while (TRUE) {
        $b= ord(fread($f, 1));
        if (0x21 == $b) {                 // Extension
          $e= ord(fread($f, 1));

          if (0xF9 == $e) {               // Extension: Graphic Control
            $c= ord(fread($f, 1));
            $disp= ($c & 0x1C) >> 2;
            $user= (bool)($c & 0x02);
            $trans= (bool)($c & 0x01);
            $delay= unpack('n', fread($f, 2));
            $ntrans= ord(fread($f, 1));
          } else if (0xFE == $e) {        // Extension: Comment
            $c= ord(fread($f, 1));
            $comment= fread($f, $c);
          } else if (0x01 == $e) {        // Extension: Plain text
            // noop
          } else if (0xFF == $e) {        // Extension: Application
            // noop
          }
        } else if (0x2C == $b) {          // Image
          $idim= unpack('vl/vt/vw/vh', fread($f, 8));

          $m= ord(fread($f, 1));
          $itable= (bool)($m & 0x80);
          $interlace= (bool)($m & 0x40);
          $isorted= (bool)($m & 0x20);
          $itablesize= 2 << ($m & 0x07);

          // Read colortable
          if ($itable) {
            $icolortable= array();
            for ($i= 0; $i < $itablesize; $i++) {
              $rgb= fread($f, 3);
              $icolortable[]= (ord($rgb{2}) << 16) + (ord($rgb{1}) << 8) + ord($rgb{0});
            }
          }
          
          // Decompress
          $data= create(new LzwDecompressor())->deCompress($f);
          break;
        } else if (0x3B == $b) {  // EOF
          break;
        }
      }

      fclose($f);

      if ($table) {
        $colors= $tablesize;
        for ($pal= '', $i= 0; $i < $colors; $i++) {
          $pal .=
            chr(($colortable[$i] & 0x000000FF)).        // R
            chr(($colortable[$i] & 0x0000FF00) >>  8).  // G
            chr(($colortable[$i] & 0x00FF0000) >> 16)   // B
          ;
        }
        $colspace= 'Indexed';
      } else if ($itable) {
        $colors= $itablesize;
        for ($pal= '', $i= 0; $i < $colors; $i++) {
          $pal .=
            chr(($icolortable[$i] & 0x000000FF)).        // R
            chr(($icolortable[$i] & 0x0000FF00) >>  8).  // G
            chr(($icolortable[$i] & 0x00FF0000) >> 16)   // B
          ;
        }
        $colspace= 'Indexed';
      } else {
        $colors= 0;
        $pal= '';
        $colspace= 'DeviceGray';
      }

      if ($colspace == 'Indexed' && empty($pal)) {
        throw new IllegalArgumentException('Missing palette in '.$file);
      }

      return array(
        'w'     => $dim['w'],
        'h'     => $dim['h'],
        'cs'    => $colspace,
        'f'     => 'FlateDecode',
        'bpc'   => 8,
        'pal'   => $pal,
        'trns'  => $trans && ($colors > 0) ? array($ntrans) : '',
        'data'  => gzcompress($data)
      );
    }

    /**
     * Extract info from a PNG file
     *
     * @param   string file
     * @throws  lang.IllegalArgumentException in case the file is corrupt
     */
    protected function _parsepng($file) {
      $f= fopen($file, 'rb');
      if (!$f) {
        throw new IllegalArgumentException('Cannot open image file: '.$file);
      }

      // Check signature
      if (fread($f, 8) != chr(137).'PNG'.chr(13).chr(10).chr(26).chr(10)) {
        throw new IllegalArgumentException('Not a PNG file: '.$file);
      }

      // Read header chunk
      fread($f, 4);
      if (fread($f, 4) != 'IHDR') {
        throw new IllegalArgumentException('Incorrect PNG file: '.$file);
      }
      
      $w= $this->_freadint($f);
      $h= $this->_freadint($f);
      $bpc= ord(fread($f, 1));
      if ($bpc > 8) {
        throw new IllegalArgumentException('16-bit depth not supported: '.$file);
      }

      // Figure out colorspace      
      switch ($ct= ord(fread($f, 1))) {
        case 0: $colspace= 'DeviceGray'; break;
        case 2: $colspace= 'DeviceRGB'; break;
        case 3: $colspace= 'Indexed'; break;
        default:
          throw new IllegalArgumentException('Alpha channel not supported: '.$file);
      }
      
      if (ord(fread($f, 1)) != 0) {
        throw new IllegalArgumentException('Unknown compression method: '.$file);
      }
      if (ord(fread($f, 1)) != 0) {
        throw new IllegalArgumentException('Unknown filter method: '.$file);
      }
      if (ord(fread($f, 1)) != 0) {
        throw new IllegalArgumentException('Interlacing not supported: '.$file);
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
        throw new IllegalArgumentException('Missing palette in '.$file);
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
     * @param   resource f
     * @return  int
     */
    protected function _freadint($f) {
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
     * @param   string s
     * @return  string
     */
    protected function _escape($s) {
      return str_replace(')','\\)',str_replace('(','\\(',str_replace('\\','\\\\', $s)));
    }

    /**
     * Output a string
     *
     * @param   string s
     */
    protected function _out($s) {
      if (2 == $this->state) {
        $this->pages[$this->page].= $s."\n"; 
      } else {
        $this->buffer.=$s."\n";
      }
    }
  }
?>
