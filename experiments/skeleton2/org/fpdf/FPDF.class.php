<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('org.fpdf.FPDFFont');

  define('FPDF_VERSION',        1.5);
  
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
  
  
  
  /**
   * PDF creator
   *
   * @purpose  Create PDFs
   * @see      http://fpdf.org/
   */
  class FPDF extends Object {
    public
      $page               = 0,
      $n                  = 2,
      $offsets,
      $buffer             = '',
      $pages              = array(),
      $state              = 0,
      $compress,
      $DefOrientation,
      $CurOrientation,
      $OrientationChanges = array(),
      $fwPt,
      $fhPt,
      $fw,
      $fh,
      $wPt,
      $hPt,
      $k,
      $w,
      $h,
      $lMargin,
      $tMargin,
      $rMargin,
      $bMargin,
      $cMargin,
      $x,
      $y,
      $lasth,
      $LineWidth,
      $fonts              = array(),
      $FontFiles          = array(),
      $diffs              = array(),
      $images             = array(),
      $PageLinks,
      $links              = array(),
      $FontFamily         = '',
      $FontStyle          = '',
      $underline          = FALSE,
      $CurrentFont,
      $FontSizePt         = 12,
      $FontSize,
      $DrawColor          = '0 G',
      $FillColor          = '0 g',
      $TextColor          = '0 g',
      $ColorFlag          = FALSE,
      $ws                 = 0,
      $AutoPageBreak,
      $PageBreakTrigger,
      $InFooter           = FALSE,
      $ZoomMode,
      $LayoutMode,
      $title,
      $subject,
      $author,
      $keywords,
      $creator,
      $AliasNbPages;                  // alias for total number of pages

    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    public function loadFonts($prop) {
      $section= $prop->getFirstSection();
      do {
        $f= new FPDFFont($section);
        $f->configure($prop);
        self::addFont($f);
      } while ($section= $prop->getNextSection());
    }
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    public function getScaleFactor($unit) {
      if (!isset($this->_kmap)) $this->_kmap= array(
        FPDF_UNIT_PT    => 1,
        FPDF_UNIT_MM    => 72 / 25.4,
        FPDF_UNIT_CM    => 72 / 2.54,
        FPDF_UNIT_INCH  => 72
      );
      return $this->_kmap[$unit];
    }
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    public function getPageFormat($format) {
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
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */ 
    public function setPageFormat($format) {
      list ($this->fwPt, $this->fhPt)= self::getPageFormat($format);
      $this->fw= round($this->fwPt / $this->k, 2);
      $this->fh= round($this->fhPt / $this->k, 2);  
    }
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
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
     * Constructor
     *
     * @access  public
     * @param   string orientation default 'P' Seitenausrichtung ("P"ortrait / "L"andscape)
     * @param   string unit default 'mm' Einheit
     * @param   string format default 'A4' Seiten-Format
     */
    public function __construct(
      $orientation= FPDF_PORTRAIT, 
      $unit=        FPDF_UNIT_MM, 
      $format=      FPDF_FORMAT_A4
    ) {

      // Scale factor
      $this->k= self::getScaleFactor($unit);

      // Page format
      self::setPageFormat($format);
      
      // Page orientation
      self::setOrientation($orientation);
      
      // Page margins (1 cm)
      $margin= round(28.35/$this->k,2);
      self::setMargins($margin,$margin);
      
      // Interior cell margin (1 mm)
      $this->cMargin=$margin/10;
      
      // Line width (0.2 mm)
      $this->LineWidth=round(.567 / $this->k, 3);
      
      // Automatic page break
      self::setAutoPageBreak(true,2*$margin);
      
      // Full width display mode
      self::setDisplayMode('fullwidth');
      
      // Compression
      self::setCompression(true);
      
      
    }

    public function setMargins($left,$top,$right=-1)
    {
      //Set left, top and right margins
      $this->lMargin=$left;
      $this->tMargin=$top;
      if($right==-1)
          $right=$left;
      $this->rMargin=$right;
    }

    public function setLeftMargin($margin)
    {
      //Set left margin
      $this->lMargin=$margin;
      if($this->page>0 and $this->x<$margin)
          $this->x=$margin;
    }

    public function setTopMargin($margin)
    {
      //Set top margin
      $this->tMargin=$margin;
    }

    public function setRightMargin($margin)
    {
      //Set right margin
      $this->rMargin=$margin;
    }

    public function setAutoPageBreak($auto,$margin=0)
    {
      //Set auto page break mode and triggering margin
      $this->AutoPageBreak=$auto;
      $this->bMargin=$margin;
      $this->PageBreakTrigger=$this->h-$margin;
    }

    public function setDisplayMode($zoom,$layout='continuous')
    {
      //Set display mode in viewer
      if($zoom=='fullpage' or $zoom=='fullwidth' or $zoom=='real' or $zoom=='default' or !is_string($zoom))
          $this->ZoomMode=$zoom;
      elseif($zoom=='zoom')
          $this->ZoomMode=$layout;
      else
          throw (new Exception('Incorrect zoom display mode: '.$zoom));
      if($layout=='single' or $layout=='continuous' or $layout=='two' or $layout=='default')
          $this->LayoutMode=$layout;
      elseif($zoom!='zoom')
          throw (new Exception('Incorrect layout display mode: '.$layout));
    }

    public function setCompression($compress)
    {
      //Set page compression
      if(function_exists('gzcompress'))
          $this->compress=$compress;
      else
          $this->compress=false;
    }

    public function setTitle($title)
    {
      //Title of document
      $this->title=$title;
    }

    public function setSubject($subject)
    {
      //Subject of document
      $this->subject=$subject;
    }

    public function setAuthor($author)
    {
      //Author of document
      $this->author=$author;
    }

    public function setKeywords($keywords)
    {
      //Keywords of document
      $this->keywords=$keywords;
    }

    public function setCreator($creator)
    {
      //Creator of document
      $this->creator=$creator;
    }

    public function AliasNbPages($alias='{nb}')
    {
      //Define an alias for total number of pages
      $this->AliasNbPages=$alias;
    }

    public function open()
    {
      //Begin document
      self::_begindoc();
    }

    public function close()
    {
      //Terminate document
      if($this->page==0)
          self::AddPage();
      //Page footer
      $this->InFooter=true;
      self::Footer();
      $this->InFooter=false;
      //Close page
      self::_endpage();
      //Close document
      self::_enddoc();
    }

    public function addPage($orientation='')
    {
      //Start a new page
      $family=$this->FontFamily;
      $style=$this->FontStyle.($this->underline ? 'U' : '');
      $size=$this->FontSizePt;
      $lw=$this->LineWidth;
      $dc=$this->DrawColor;
      $fc=$this->FillColor;
      $tc=$this->TextColor;
      $cf=$this->ColorFlag;
      if($this->page>0)
      {
          //Page footer
          $this->InFooter=true;
          self::Footer();
          $this->InFooter=false;
          //Close page
          self::_endpage();
      }
      //Start new page
      self::_beginpage($orientation);
      //Set line cap style to square
      self::_out('2 J');
      //Set line width
      $this->LineWidth=$lw;
      self::_out($lw.' w');
      //Set font
      if($family)
          self::SetFont(self::getFontByName($family,$style),$size);
      //Set colors
      $this->DrawColor=$dc;
      if($dc!='0 G')
          self::_out($dc);
      $this->FillColor=$fc;
      if($fc!='0 g')
          self::_out($fc);
      $this->TextColor=$tc;
      $this->ColorFlag=$cf;
      //Page header
      self::Header();
      //Restore line width
      if($this->LineWidth!=$lw)
      {
          $this->LineWidth=$lw;
          self::_out($lw.' w');
      }
      //Restore font
      if($family)
          self::SetFont(self::getFontByName($family,$style),$size);
      //Restore colors
      if($this->DrawColor!=$dc)
      {
          $this->DrawColor=$dc;
          self::_out($dc);
      }
      if($this->FillColor!=$fc)
      {
          $this->FillColor=$fc;
          self::_out($fc);
      }
      $this->TextColor=$tc;
      $this->ColorFlag=$cf;
    }

    public function Header()
    {
      //To be implemented in your own inherited class
    }

    public function Footer()
    {
      //To be implemented in your own inherited class
    }

    public function PageNo()
    {
      //Get current page number
      return $this->page;
    }

    public function setDrawColor($r,$g=-1,$b=-1)
    {
      //Set color for all stroking operations
      if(($r==0 and $g==0 and $b==0) or $g==-1)
          $this->DrawColor=substr($r/255,0,5).' G';
      else
          $this->DrawColor=substr($r/255,0,5).' '.substr($g/255,0,5).' '.substr($b/255,0,5).' RG';
      if($this->page>0)
          self::_out($this->DrawColor);
    }

    public function setFillColor($r,$g=-1,$b=-1)
    {
      //Set color for all filling operations
      if(($r==0 and $g==0 and $b==0) or $g==-1)
          $this->FillColor=substr($r/255,0,5).' g';
      else
          $this->FillColor=substr($r/255,0,5).' '.substr($g/255,0,5).' '.substr($b/255,0,5).' rg';
      $this->ColorFlag=($this->FillColor!=$this->TextColor);
      if($this->page>0)
          self::_out($this->FillColor);
    }

    public function setTextColor($r,$g=-1,$b=-1)
    {
      //Set color for text
      if(($r==0 and $g==0 and $b==0) or $g==-1)
          $this->TextColor=substr($r/255,0,5).' g';
      else
          $this->TextColor=substr($r/255,0,5).' '.substr($g/255,0,5).' '.substr($b/255,0,5).' rg';
      $this->ColorFlag=($this->FillColor!=$this->TextColor);
    }

    public function getStringWidth($s)
    {
      //Get width of a string in the current font
      $s=(string)$s;
      $cw=&$this->CurrentFont->cw;
      $w=0;
      $l=strlen($s);
      for($i=0;$i<$l;$i++)
          $w+=$cw[$s{$i}];
      return $w*$this->FontSize/1000;
    }

    public function setLineWidth($width)
    {
      //Set line width
      $this->LineWidth=$width;
      if($this->page>0)
          self::_out($width.' w');
    }

    public function Line($x1,$y1,$x2,$y2)
    {
      //Draw a line
      self::_out($x1.' -'.$y1.' m '.$x2.' -'.$y2.' l S');
    }

    public function Rect($x,$y,$w,$h,$style='')
    {
      //Draw a rectangle
      if($style=='F')
          $op='f';
      elseif($style=='FD' or $style=='DF')
          $op='B';
      else
          $op='S';
      self::_out($x.' -'.$y.' '.$w.' -'.$h.' re '.$op);
    }

    /**
     * Dem Dokument eine Schriftart hinzufügen
     *
     * @access  public
     * @param   org.pdf.FPDFFont font Ein Font-Objekt
     */
    public function addFont(FPDFFont $font) {
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
     * Einen Font nach Familie und Stil zurückgeben
     *
     * @access  public
     * @param   string family Font-Familie
     * @param   string style default '' Font-Stil, bspw. "BI", "U", ...
     * @return  
     */
    public function getFontByName($family, $style= '') {
      if (!isset($this->fonts[$idx= strtolower($family).strtoupper($style)])) throw (new Exception(
        'font not in document [family='.$family.', style='.$style.']'
      ));
      
      return $this->fonts[$idx];
    }

    /**
     * Den aktuellen Font auswählen
     *
     * @access  
     * @param   
     * @return  
     */
    public function setFont($font, $size= 0) {
      if (NULL == $font) throw (new IllegalArgumentException('font is not a org.pdf.FPDFFont'));
      
      $this->underline= $font->isUnderline();
      if ($size == 0) $size= $this->FontSizePt;
      
      // Test if font is already selected
      if (
        ($this->FontFamily == $font->family) and 
        ($this->FontStyle  == $font->style) and 
        ($this->FontSizePt == $size)
      ) return;

      // Select it
      $this->FontFamily= $font->family;
      $this->FontStyle= $font->style;
      $this->FontSizePt= $size;
      $this->FontSize= round($size / $this->k, 2);
      $this->CurrentFont= $font;
      
      //var_dump($this->CurrentFont);
      if ($this->page > 0) self::_out('BT /F'.$this->CurrentFont->index.' '.$this->FontSize.' Tf ET');
    }

    public function setFontSize($size)
    {
      //Set font size in points
      if($this->FontSizePt==$size)
          return;
      $this->FontSizePt=$size;
      $this->FontSize=round($size/$this->k,2);
      if($this->page>0)
          self::_out('BT /F'.$this->CurrentFont->index.' '.$this->FontSize.' Tf ET');
    }

    public function AddLink()
    {
      //Create a new internal link
      $n=count($this->links)+1;
      $this->links[$n]=array(0,0);
      return $n;
    }

    public function setLink($link,$y=0,$page=-1)
    {
      //Set destination of internal link
      if($y==-1)
          $y=$this->y;
      if($page==-1)
          $page=$this->page;
      $this->links[$link]=array($page,$this->hPt-$y*$this->k);
    }

    public function Link($x,$y,$w,$h,$link)
    {
      //Put a link on the page
      $this->PageLinks[$this->page][]=array($x*$this->k,$this->hPt-$y*$this->k,$w*$this->k,$h*$this->k,$link);
    }

    public function Text($x,$y,$txt)
    {
      //Output a string
      $txt=str_replace(')','\\)',str_replace('(','\\(',str_replace('\\','\\\\',$txt)));
      $s='BT '.$x.' -'.$y.' Td ('.$txt.') Tj ET';
      if($this->underline and $txt!='')
          $s.=' '.self::_dounderline($x,$y,$txt);
      if($this->ColorFlag)
          $s='q '.$this->TextColor.' '.$s.' Q';
      self::_out($s);
    }

    public function AcceptPageBreak()
    {
      //Accept automatic page break or not
      return $this->AutoPageBreak;
    }

    public function Cell($w,$h=0,$txt='',$border=0,$ln=0,$align='',$fill=0,$link='')
    {
      //Output a cell
      if($this->y+$h>$this->PageBreakTrigger and !$this->InFooter and self::AcceptPageBreak())
      {
          $x=$this->x;
          $ws=$this->ws;
          if($ws>0)
          {
              $this->ws=0;
              self::_out('0 Tw');
          }
          self::AddPage($this->CurOrientation);
          $this->x=$x;
          if($ws>0)
          {
              $this->ws=$ws;
              self::_out($ws.' Tw');
          }
      }
      if($w==0)
          $w=$this->w-$this->rMargin-$this->x;
      $s='';
      if($fill==1 or $border==1)
      {
          $s.=$this->x.' -'.$this->y.' '.$w.' -'.$h.' re ';
          if($fill==1)
              $s.=($border==1) ? 'B ' : 'f ';
          else
              $s.='S ';
      }
      if(is_string($border))
      {
          $x=$this->x;
          $y=$this->y;
          if(is_int(strpos($border,'L')))
              $s.=$x.' -'.$y.' m '.$x.' -'.($y+$h).' l S ';
          if(is_int(strpos($border,'T')))
              $s.=$x.' -'.$y.' m '.($x+$w).' -'.$y.' l S ';
          if(is_int(strpos($border,'R')))
              $s.=($x+$w).' -'.$y.' m '.($x+$w).' -'.($y+$h).' l S ';
          if(is_int(strpos($border,'B')))
              $s.=$x.' -'.($y+$h).' m '.($x+$w).' -'.($y+$h).' l S ';
      }
      if($txt!='')
      {
          if($align=='R')
              $dx=$w-$this->cMargin-self::GetStringWidth($txt);
          elseif($align=='C')
              $dx=($w-self::GetStringWidth($txt))/2;
          else
              $dx=$this->cMargin;
          $txt=str_replace(')','\\)',str_replace('(','\\(',str_replace('\\','\\\\',$txt)));
          if($this->ColorFlag)
              $s.='q '.$this->TextColor.' ';
          $s.='BT '.($this->x+$dx).' -'.($this->y+.5*$h+.3*$this->FontSize).' Td ('.$txt.') Tj ET';
          if($this->underline)
              $s.=' '.self::_dounderline($this->x+$dx,$this->y+.5*$h+.3*$this->FontSize,$txt);
          if($this->ColorFlag)
              $s.=' Q';
          if($link)
              self::Link($this->x+$this->cMargin,$this->y+.5*$h-.5*$this->FontSize,self::GetStringWidth($txt),$this->FontSize,$link);
      }
      if($s)
          self::_out($s);
      $this->lasth=$h;
      if($ln>0)
      {
          //Go to next line
          $this->y+=$h;
          if($ln==1)
              $this->x=$this->lMargin;
      }
      else
          $this->x+=$w;
    }

    public function MultiCell($w,$h,$txt,$border=0,$align='J',$fill=0)
    {
      //Output text with automatic or explicit line breaks
      $cw= $this->CurrentFont->cw;
      if($w==0)
          $w=$this->w-$this->rMargin-$this->x;
      $wmax=($w-2*$this->cMargin)*1000/$this->FontSize;
      $s=str_replace("\r",'',$txt);
      $nb=strlen($s);
      if($nb>0 and $s[$nb-1]=="\n")
          $nb--;
      $b=0;
      if($border)
      {
          if($border==1)
          {
              $border='LTRB';
              $b='LRT';
              $b2='LR';
          }
          else
          {
              $b2='';
              if(is_int(strpos($border,'L')))
                  $b2.='L';
              if(is_int(strpos($border,'R')))
                  $b2.='R';
              $b=is_int(strpos($border,'T')) ? $b2.'T' : $b2;
          }
      }
      $sep=-1;
      $i=0;
      $j=0;
      $l=0;
      $ns=0;
      $nl=1;
      while($i<$nb)
      {
          //Get next character
          $c=$s[$i];
          if($c=="\n")
          {
              //Explicit line break
              if($this->ws>0)
              {
                  $this->ws=0;
                  self::_out('0 Tw');
              }
              self::Cell($w,$h,substr($s,$j,$i-$j),$b,2,$align,$fill);
              $i++;
              $sep=-1;
              $j=$i;
              $l=0;
              $ns=0;
              $nl++;
              if($border and $nl==2)
                  $b=$b2;
              continue;
          }
          if($c==' ')
          {
              $sep=$i;
              $ls=$l;
              $ns++;
          }
          $l+=$cw[ord($c)];
          if($l>$wmax)
          {
              //Automatic line break
              if($sep==-1)
              {
                  if($i==$j)
                      $i++;
                  if($this->ws>0)
                  {
                      $this->ws=0;
                      self::_out('0 Tw');
                  }
                  self::Cell($w,$h,substr($s,$j,$i-$j),$b,2,$align,$fill);
              }
              else
              {
                  if($align=='J')
                  {
                      $this->ws=($ns>1) ? round(($wmax-$ls)/1000*$this->FontSize/($ns-1),3) : 0;
                      self::_out($this->ws.' Tw');
                  }
                  self::Cell($w,$h,substr($s,$j,$sep-$j),$b,2,$align,$fill);
                  $i=$sep+1;
              }
              $sep=-1;
              $j=$i;
              $l=0;
              $ns=0;
              $nl++;
              if($border and $nl==2)
                  $b=$b2;
          }
          else
              $i++;
      }
      //Last chunk
      if($this->ws>0)
      {
          $this->ws=0;
          self::_out('0 Tw');
      }
      if($border and is_int(strpos($border,'B')))
          $b.='B';
      self::Cell($w,$h,substr($s,$j,$i),$b,2,$align,$fill);
      $this->x=$this->lMargin;
    }

    public function Write($h,$txt,$link='')
    {
      //Output text in flowing mode
      $cw=&$this->CurrentFont->cw;
      $w=$this->w-$this->rMargin-$this->x;
      $wmax=($w-2*$this->cMargin)*1000/$this->FontSize;
      $s=str_replace("\r",'',$txt);
      $nb=strlen($s);
      $sep=-1;
      $i=0;
      $j=0;
      $l=0;
      $nl=1;
      while($i<$nb)
      {
          //Get next character
          $c=$s{$i};
          if($c=="\n")
          {
              //Explicit line break
              self::Cell($w,$h,substr($s,$j,$i-$j),0,2,'',0,$link);
              $i++;
              $sep=-1;
              $j=$i;
              $l=0;
              if($nl==1)
              {
                  $this->x=$this->lMargin;
                  $w=$this->w-$this->rMargin-$this->x;
                  $wmax=($w-2*$this->cMargin)*1000/$this->FontSize;
              }
              $nl++;
              continue;
          }
          if($c==' ')
          {
              $sep=$i;
              $ls=$l;
          }
          $l+=$cw[$c];
          if($l>$wmax)
          {
              //Automatic line break
              if($sep==-1)
              {
                  if($this->x>$this->lMargin)
                  {
                      //Move to next line
                      $this->x=$this->lMargin;
                      $this->y+=$h;
                      $w=$this->w-$this->rMargin-$this->x;
                      $wmax=($w-2*$this->cMargin)*1000/$this->FontSize;
                      $i++;
                      $nl++;
                      continue;
                  }
                  if($i==$j)
                      $i++;
                  self::Cell($w,$h,substr($s,$j,$i-$j),0,2,'',0,$link);
              }
              else
              {
                  self::Cell($w,$h,substr($s,$j,$sep-$j),0,2,'',0,$link);
                  $i=$sep+1;
              }
              $sep=-1;
              $j=$i;
              $l=0;
              if($nl==1)
              {
                  $this->x=$this->lMargin;
                  $w=$this->w-$this->rMargin-$this->x;
                  $wmax=($w-2*$this->cMargin)*1000/$this->FontSize;
              }
              $nl++;
          }
          else
              $i++;
      }
      //Last chunk
      if($i!=$j)
      {
          $w=round($l/1000*$this->FontSize,2);
          self::Cell($w,$h,substr($s,$j,$i),0,0,'',0,$link);
      }
    }

    public function Image($file,$x,$y,$w,$h=0,$type='',$link='')
    {
      //Put an image on the page
      if(!isset($this->images[$file]))
      {
          //First use of image, get info
          if($type=='')
          {
              $pos=strrpos($file,'.');
              if(!$pos)
                  throw (new Exception('Image file has no extension and no type was specified: '.$file));
              $type=substr($file,$pos+1);
          }
          $type=strtolower($type);
          $mqr=get_magic_quotes_runtime();
          set_magic_quotes_runtime(0);
          if($type=='jpg' or $type=='jpeg')
              $info=self::_parsejpg($file);
          elseif($type=='png')
              $info=self::_parsepng($file);
          else
              throw (new Exception('Unsupported image file type: '.$type));
          set_magic_quotes_runtime($mqr);
          $info['n']=count($this->images)+1;
          $this->images[$file]=$info;
      }
      else
          $info=$this->images[$file];
      //Automatic width or height calculus
      if($w==0)
          $w=round($h*$info['w']/$info['h'],2);
      if($h==0)
          $h=round($w*$info['h']/$info['w'],2);
      self::_out('q '.$w.' 0 0 '.$h.' '.$x.' -'.($y+$h).' cm /I'.$info['n'].' Do Q');
      if($link)
          self::Link($x,$y,$w,$h,$link);
    }

    public function Ln($h='')
    {
      //Line feed; default value is last cell height
      $this->x=$this->lMargin;
      if(is_string($h))
          $this->y+=$this->lasth;
      else
          $this->y+=$h;
    }

    public function getX()
    {
      //Get x position
      return $this->x;
    }

    public function setX($x)
    {
      //Set x position
      if($x>=0)
          $this->x=$x;
      else
          $this->x=$this->w+$x;
    }

    public function getY()
    {
      //Get y position
      return $this->y;
    }

    public function setY($y)
    {
      //Set y position and reset x
      $this->x=$this->lMargin;
      if($y>=0)
          $this->y=$y;
      else
          $this->y=$this->h+$y;
    }

    public function setXY($x,$y)
    {
      //Set x and y positions
      self::SetY($y);
      self::SetX($x);
    }

    public function getBuffer() {
      if ($this->state < 3) self::Close();

      return $this->buffer;
    }

    protected function _begindoc()
    {
      //Start document
      $this->state=1;
      self::_out('%PDF-1.3');
    }

    protected function _enddoc()
    {
      //Terminate document
      $nb=$this->page;
      if(!empty($this->AliasNbPages))
      {
          //Replace number of pages
          for($n=1;$n<=$nb;$n++)
              $this->pages[$n]=str_replace($this->AliasNbPages,$nb,$this->pages[$n]);
      }
      if($this->DefOrientation=='P')
      {
          $wPt=$this->fwPt;
          $hPt=$this->fhPt;
      }
      else
      {
          $wPt=$this->fhPt;
          $hPt=$this->fwPt;
      }
      $filter=($this->compress) ? '/Filter /FlateDecode ' : '';
      for($n=1;$n<=$nb;$n++)
      {
          //Page
          self::_newobj();
          self::_out('<</Type /Page');
          self::_out('/Parent 1 0 R');
          if(isset($this->OrientationChanges[$n]))
              self::_out('/MediaBox [0 0 '.$hPt.' '.$wPt.']');
          self::_out('/Resources 2 0 R');
          if(isset($this->PageLinks[$n]))
          {
              $annots='/Annots [';
              foreach($this->PageLinks[$n] as $pl)
              {
                  $rect=round($pl[0],2).' '.round($pl[1],2).' '.round($pl[0]+$pl[2],2).' '.round($pl[1]-$pl[3],2);
                  $annots.='<</Type /Annot /Subtype /Link /Rect ['.$rect.'] /Border [0 0 0] ';
                  if(is_string($pl[4]))
                      $annots.='/A <</S /URI /URI ('.$pl[4].')>>>>';
                  else
                  {
                      $l=$this->links[$pl[4]];
                      $annots.='/Dest ['.(1+2*$l[0]).' 0 R /XYZ 0 '.$l[1].' null]>>';
                  }
              }
              self::_out($annots.']');
          }
          self::_out('/Contents '.($this->n+1).' 0 R>>');
          self::_out('endobj');
          //Page content
          $p=($this->compress) ? gzcompress($this->pages[$n]) : $this->pages[$n];
          self::_newobj();
          self::_out('<<'.$filter.'/Length '.strlen($p).'>>');
          self::_out('stream');
          self::_out($p.'endstream');
          self::_out('endobj');
      }
      //Fonts
      $nf=$this->n;
      foreach($this->diffs as $diff)
      {
          //Encodings
          self::_newobj();
          self::_out('<</Type /Encoding /BaseEncoding /WinAnsiEncoding /Differences ['.$diff.']>>');
          self::_out('endobj');
      }
      foreach($this->FontFiles as $file=>$info)
      {
          //Font file embedding
          self::_newobj();
          $this->FontFiles[$file]['n']=$this->n;
          if(defined('FPDF_FONTPATH'))
              $file=FPDF_FONTPATH.$file;
          $size=filesize($file);
          if(!$size)
              throw (new Exception('Font file not found'));
          self::_out('<</Length '.$size);
          if(substr($file,-2)=='.z')
              self::_out('/Filter /FlateDecode');
          self::_out('/Length1 '.$info['originalsize']);
          self::_out('>>');
          self::_out('stream');
          $f=fopen($file,'rb');
          self::_out(fread($f,$size));
          fclose($f);
          self::_out('endstream');
          self::_out('endobj');
      }
      foreach($this->fonts as $k=>$font)
      {
          //Font objects
          self::_newobj();
          $this->fonts[$k]->n= $this->n;
          $name=$font->fontname;
          self::_out('<</Type /Font');
          self::_out('/BaseFont /'.$name);
              
          if($font->type == '__CORE__')
          {
              //Standard font
              self::_out('/Subtype /Type1');
              if($name!='Symbol' and $name!='ZapfDingbats')
                  self::_out('/Encoding /WinAnsiEncoding');
          }
          else
          {
              //TrueType
              self::_out('/Subtype /TrueType');
              self::_out('/FirstChar 32');
              self::_out('/LastChar 255');
              self::_out('/Widths '.($this->n+1).' 0 R');
              self::_out('/FontDescriptor '.($this->n+2).' 0 R');
              if($font->enc)
              {
                  if(isset($font->diff))
                      self::_out('/Encoding '.($nf+$font->diff).' 0 R');
                  else
                      self::_out('/Encoding /WinAnsiEncoding');
              }
          }
          self::_out('>>');
          self::_out('endobj');
          if($font->type != '__CORE__')
          {
              //Widths
              self::_newobj();
              $cw=&$font->cw;
              $s='[';
              for($i=32;$i<=255;$i++)
                  $s.=$cw[$i].' ';
              self::_out($s.']');
              self::_out('endobj');
              //Descriptor
              self::_newobj();
              $s='<</Type /FontDescriptor /FontName /'.$name;
              foreach($font->desc as $k=>$v)
                  $s.=' /'.$k.' '.$v;
              $file=$font->file;
              if($file)
                  $s.=' /FontFile2 '.$this->FontFiles[$file]['n'].' 0 R';
              self::_out($s.'>>');
              self::_out('endobj');
          }
      }
      //Images
      $ni=$this->n;
      reset($this->images);
      while(list($file,$info)=each($this->images))
      {
          self::_newobj();
          self::_out('<</Type /XObject');
          self::_out('/Subtype /Image');
          self::_out('/Width '.$info['w']);
          self::_out('/Height '.$info['h']);
          if($info['cs']=='Indexed')
              self::_out('/ColorSpace [/Indexed /DeviceRGB '.(strlen($info['pal'])/3-1).' '.($this->n+1).' 0 R]');
          else
              self::_out('/ColorSpace /'.$info['cs']);
          self::_out('/BitsPerComponent '.$info['bpc']);
          self::_out('/Filter /'.$info['f']);
          if(isset($info['parms']))
              self::_out($info['parms']);
          if(isset($info['trns']) and is_array($info['trns']))
          {
              $trns='';
              for($i=0;$i<count($info['trns']);$i++)
                  $trns.=$info['trns'][$i].' '.$info['trns'][$i].' ';
              self::_out('/Mask ['.$trns.']');
          }
          self::_out('/Length '.strlen($info['data']).'>>');
          self::_out('stream');
          self::_out($info['data']);
          self::_out('endstream');
          self::_out('endobj');
          //Palette
          if($info['cs']=='Indexed')
          {
              self::_newobj();
              self::_out('<</Length '.strlen($info['pal']).'>>');
              self::_out('stream');
              self::_out($info['pal']);
              self::_out('endstream');
              self::_out('endobj');
          }
      }
      //Pages root
      $this->offsets[1]=strlen($this->buffer);
      self::_out('1 0 obj');
      self::_out('<</Type /Pages');
      $kids='/Kids [';
      for($i=0;$i<$this->page;$i++)
          $kids.=(3+2*$i).' 0 R ';
      self::_out($kids.']');
      self::_out('/Count '.$this->page);
      self::_out('/MediaBox [0 0 '.$wPt.' '.$hPt.']');
      self::_out('>>');
      self::_out('endobj');
      //Resources
      $this->offsets[2]=strlen($this->buffer);
      self::_out('2 0 obj');
      self::_out('<</ProcSet [/PDF /Text /ImageB /ImageC /ImageI]');
      self::_out('/Font <<');
      foreach($this->fonts as $font)
          self::_out('/F'.$font->index.' '.$font->n.' 0 R');
      self::_out('>>');
      if(count($this->images))
      {
          self::_out('/XObject <<');
          $nbpal=0;
          reset($this->images);
          while(list(,$info)=each($this->images))
          {
              self::_out('/I'.$info['n'].' '.($ni+$info['n']+$nbpal).' 0 R');
              if($info['cs']=='Indexed')
                  $nbpal++;
          }
          self::_out('>>');
      }
      self::_out('>>');
      self::_out('endobj');
      //Info
      self::_newobj();
      self::_out('<</Producer (FPDF '.FPDF_VERSION.')');
      if(!empty($this->title))
          self::_out('/Title ('.self::_escape($this->title).')');
      if(!empty($this->subject))
          self::_out('/Subject ('.self::_escape($this->subject).')');
      if(!empty($this->author))
          self::_out('/Author ('.self::_escape($this->author).')');
      if(!empty($this->keywords))
          self::_out('/Keywords ('.self::_escape($this->keywords).')');
      if(!empty($this->creator))
          self::_out('/Creator ('.self::_escape($this->creator).')');
      self::_out('/CreationDate (D:'.date('YmdHis').')>>');
      self::_out('endobj');
      //Catalog
      self::_newobj();
      self::_out('<</Type /Catalog');
      if($this->ZoomMode=='fullpage')
          self::_out('/OpenAction [3 0 R /Fit]');
      elseif($this->ZoomMode=='fullwidth')
          self::_out('/OpenAction [3 0 R /FitH null]');
      elseif($this->ZoomMode=='real')
          self::_out('/OpenAction [3 0 R /XYZ null null 1]');
      elseif(!is_string($this->ZoomMode))
          self::_out('/OpenAction [3 0 R /XYZ null null '.($this->ZoomMode/100).']');
      if($this->LayoutMode=='single')
          self::_out('/PageLayout /SinglePage');
      elseif($this->LayoutMode=='continuous')
          self::_out('/PageLayout /OneColumn');
      elseif($this->LayoutMode=='two')
          self::_out('/PageLayout /TwoColumnLeft');
      self::_out('/Pages 1 0 R>>');
      self::_out('endobj');
      //Cross-ref
      $o=strlen($this->buffer);
      self::_out('xref');
      self::_out('0 '.($this->n+1));
      self::_out('0000000000 65535 f ');
      for($i=1;$i<=$this->n;$i++)
          self::_out(sprintf('%010d 00000 n ',$this->offsets[$i]));
      //Trailer
      self::_out('trailer');
      self::_out('<</Size '.($this->n+1));
      self::_out('/Root '.$this->n.' 0 R');
      self::_out('/Info '.($this->n-1).' 0 R>>');
      self::_out('startxref');
      self::_out($o);
      self::_out('%%EOF');
      $this->state=3;
    }

    protected function _beginpage($orientation)
    {
      $this->page++;
      $this->pages[$this->page]='';
      $this->state=2;
      $this->x=$this->lMargin;
      $this->y=$this->tMargin;
      $this->lasth=0;
      $this->FontFamily='';
      //Page orientation
      if(!$orientation)
          $orientation=$this->DefOrientation;
      else
      {
          $orientation=strtoupper($orientation{0});
          if($orientation!=$this->DefOrientation)
              $this->OrientationChanges[$this->page]=true;
      }
      if($orientation!=$this->CurOrientation)
      {
          //Change orientation
          if($orientation=='P')
          {
              $this->wPt=$this->fwPt;
              $this->hPt=$this->fhPt;
              $this->w=$this->fw;
              $this->h=$this->fh;
          }
          else
          {
              $this->wPt=$this->fhPt;
              $this->hPt=$this->fwPt;
              $this->w=$this->fh;
              $this->h=$this->fw;
          }
          $this->PageBreakTrigger=$this->h-$this->bMargin;
          $this->CurOrientation=$orientation;
      }
      //Set transformation matrix
      self::_out(round($this->k,6).' 0 0 '.round($this->k,6).' 0 '.$this->hPt.' cm');
    }

    protected function _endpage()
    {
      //End of page contents
      $this->state=1;
    }

    protected function _newobj()
    {
      //Begin a new object
      $this->n++;
      $this->offsets[$this->n]=strlen($this->buffer);
      self::_out($this->n.' 0 obj');
    }

    protected function _dounderline($x,$y,$txt)
    {
      //Underline text
      $up=$this->CurrentFont->up;
      $ut=$this->CurrentFont->ut;
      $w=self::GetStringWidth($txt)+$this->ws*substr_count($txt,' ');
      return $x.' -'.($y-$up/1000*$this->FontSize).' '.$w.' -'.($ut/1000*$this->FontSize).' re f';
    }

    protected function _parsejpg($file)
    {
      //Extract info from a JPEG file
      $a=GetImageSize($file);
      if(!$a)
          throw (new Exception('Missing or incorrect image file: '.$file));
      if($a[2]!=2)
          throw (new Exception('Not a JPEG file: '.$file));
      if(!isset($a['channels']) or $a['channels']==3)
          $colspace='DeviceRGB';
      elseif($a['channels']==4)
          $colspace='DeviceCMYK';
      else
          $colspace='DeviceGray';
      $bpc=isset($a['bits']) ? $a['bits'] : 8;
      //Read whole file
      $f=fopen($file,'rb');
      $data=fread($f,filesize($file));
      fclose($f);
      return array('w'=>$a[0],'h'=>$a[1],'cs'=>$colspace,'bpc'=>$bpc,'f'=>'DCTDecode','data'=>$data);
    }

    protected function _parsepng($file)
    {
      //Extract info from a PNG file
      $f=fopen($file,'rb');
      if(!$f)
          throw (new Exception('Can\'t open image file: '.$file));
      //Check signature
      if(fread($f,8)!=chr(137).'PNG'.chr(13).chr(10).chr(26).chr(10))
          throw (new Exception('Not a PNG file: '.$file));
      //Read header chunk
      fread($f,4);
      if(fread($f,4)!='IHDR')
          throw (new Exception('Incorrect PNG file: '.$file));
      $w=self::_freadint($f);
      $h=self::_freadint($f);
      $bpc=ord(fread($f,1));
      if($bpc>8)
          throw (new Exception('16-bit depth not supported: '.$file));
      $ct=ord(fread($f,1));
      if($ct==0)
          $colspace='DeviceGray';
      elseif($ct==2)
          $colspace='DeviceRGB';
      elseif($ct==3)
          $colspace='Indexed';
      else
          throw (new Exception('Alpha channel not supported: '.$file));
      if(ord(fread($f,1))!=0)
          throw (new Exception('Unknown compression method: '.$file));
      if(ord(fread($f,1))!=0)
          throw (new Exception('Unknown filter method: '.$file));
      if(ord(fread($f,1))!=0)
          throw (new Exception('Interlacing not supported: '.$file));
      fread($f,4);
      $parms='/DecodeParms <</Predictor 15 /Colors '.($ct==2 ? 3 : 1).' /BitsPerComponent '.$bpc.' /Columns '.$w.'>>';
      //Scan chunks looking for palette, transparency and image data
      $pal='';
      $trns='';
      $data='';
      do
      {
          $n=self::_freadint($f);
          $type=fread($f,4);
          if($type=='PLTE')
          {
              //Read palette
              $pal=fread($f,$n);
              fread($f,4);
          }
          elseif($type=='tRNS')
          {
              //Read transparency info
              $t=fread($f,$n);
              if($ct==0)
                  $trns=array(substr($t,1,1));
              elseif($ct==2)
                  $trns=array(substr($t,1,1),substr($t,3,1),substr($t,5,1));
              else
              {
                  $pos=strpos($t,chr(0));
                  if(is_int($pos))
                      $trns=array($pos);
              }
              fread($f,4);
          }
          elseif($type=='IDAT')
          {
              //Read image data block
              $data.=fread($f,$n);
              fread($f,4);
          }
          elseif($type=='IEND')
              break;
          else
              fread($f,$n+4);
      }
      while($n);
      if($colspace=='Indexed' and empty($pal))
          throw (new Exception('Missing palette in '.$file));
      fclose($f);
      return array('w'=>$w,'h'=>$h,'cs'=>$colspace,'bpc'=>$bpc,'f'=>'FlateDecode','parms'=>$parms,'pal'=>$pal,'trns'=>$trns,'data'=>$data);
    }

    protected function _freadint($f)
    {
      //Read a 4-byte integer from file
      $i=ord(fread($f,1))<<24;
      $i+=ord(fread($f,1))<<16;
      $i+=ord(fread($f,1))<<8;
      $i+=ord(fread($f,1));
      return $i;
    }

    protected function _escape($s)
    {
      //Add \ before \, ( and )
      return str_replace(')','\\)',str_replace('(','\\(',str_replace('\\','\\\\',$s)));
    }

    protected function _out($s)
    {
      //var_dump($s);
      if($this->state==2)
          $this->pages[$this->page].=$s."\n";
      else
          $this->buffer.=$s."\n";
    }
  }
?>
