<?xml version="1.0" encoding="iso-8859-1" ?>
<xsl:stylesheet
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform" 
  version="1.0"
>

  <xsl:template match="packages|classdoc">
<xsl:if test="$mode = 'search'">
  <xsl:call-template name="search"/>
</xsl:if>
<html>
<head>
 <title>
   <xsl:choose>
     <xsl:when test="$mode = 'class'">
       XP::<xsl:value-of select="./@classname"/>
     </xsl:when>
     <xsl:when test="$mode = 'collection'">
       XP::<xsl:value-of select="./@prefix"/>
     </xsl:when>
     <xsl:when test="$mode = 'showsource'">
       <xsl:processing-instruction name="php">
         <![CDATA[
           echo 'XP:: Source of '.strip_tags ($_REQUEST['f']);
         ]]>
       </xsl:processing-instruction>
     </xsl:when>
     <xsl:when test="$mode = 'search'">
       XP::Searchresults
     </xsl:when>
     <xsl:otherwise>XP::Documentation</xsl:otherwise>
   </xsl:choose>
 </title>
 <link rel="stylesheet" href="/style.css" />
</head>

<xsl:comment>
  Params are:
  mode = <xsl:value-of select="$mode"/>
  package = <xsl:value-of select="$package"/>
  collection = <xsl:value-of select="$collection"/>
</xsl:comment>

<body
	topmargin="0" leftmargin="0"
	marginheight="0" marginwidth="0"
        bgcolor="#ffffff"
        text="#000000"
        link="#000033"
        alink="#0099ff"
        vlink="#000033"
><a name="TOP"></a>
<table border="0" cellspacing="0" cellpadding="0" height="48" width="100%">
  <tr bgcolor="#9eb6ff">
    <td align="left" rowspan="2">
       <a href="/"><img src="/image/xp-logo.gif" border="0" width="120" height="64" ALT="PHP-GTK"  vspace="0" hspace="0"/></a><br/>
    </td>
    <td align="right" valign="top" nowrap="nowrap">
      <font color="#ffffff">
        <b>
          <xsl:processing-instruction name="php">
            echo date ('l, F d, Y');
          </xsl:processing-instruction>
        </b>
        <br/>
      </font>
    </td>
  </tr>

  <tr bgcolor="#9eb6ff">
    <td align="right" valign="bottom" nowrap="nowrap">
      <a href="#" class="menuBlack">download</a> | <a href="/apidoc/" class="menuBlack">documentation</a> | <a href="#" class="menuBlack">faq</a> | <a href="/ports/" class="menuBlack">ports</a> | <a href="#" class="menuBlack">changelog</a> | <a href="#" class="menuBlack">resources</a> <br/>
      <img src="/image/spacer.gif" width="2" height="2" border="0" alt=""/><br/>
    </td>
  </tr>

  <tr bgcolor="#000033"><td colspan="2"><img src="/image/spacer.gif" width="1" height="1" border="0" alt=""/><br/></td></tr>

  <tr bgcolor="#3654a5"> <!-- #e0e8fc"> -->
    <form method="GET" action="/search.php">
      <td align="right" valign="top" colspan="2" nowrap="nowrap"><font color="#ffffff">
        <small><u>s</u>earch for</small>
<input class="small" type="text" name="keyword" value="&lt;?php echo isset($_REQUEST['keyword']) ?  $_REQUEST['keyword'] : ''; ?&gt;" size="30" accesskey="s"/>
<small>in the</small>
<select name="show" class="small">
<option value="apidoc">api docs</option>
<!--
<option value="php-gtk-dev-list">development mailing list</option>
<option value="php-gtk-doc-list">documentation mailing list</option>
<option value="manual">manual</option>
-->
</select>
<input type="image" src="/image/small_submit_white.gif" border="0" width="11" height="11" ALT="search"  align="bottom"/> <br/>
     </font></td>
    </form>
  </tr>

  <tr bgcolor="#000033"><td colspan="2"><img src="/image/spacer.gif" width="1" height="1" border="0" alt=""/><br/></td></tr>
</table>
<table cellpadding="0" cellspacing="0">
 <tr valign="top">
  <td bgcolor="#d6e1ff">
   <table width="170" cellpadding="4" cellspacing="0">
    <tr valign="top">
	  <td class="sidebar">
	    <a href="/">XP</a> stands for <b>X</b>ML <b>P</b>HP.<br/>
		XP is far more than that!
        <!-- This is the place to call the navigation -->    
      </td>
    </tr>
   </table>
  </td>
  <td bgcolor="#cccccc" background="/image/checkerboard.gif"><img src="/image/spacer.gif" width="1" height="1" border="0" alt=""/><br/></td>
  <td>
   <table width="600" cellpadding="10" cellspacing="0">
    <tr>
     <td valign="top">
        <!-- This is the "main" window -->
        <xsl:choose>
          <xsl:when test="$mode = 'apidoc-index'">
            <xsl:apply-templates select="package">
              <xsl:sort select="./@type"/>
            </xsl:apply-templates>
          </xsl:when>

          <xsl:when test="$mode = 'collection'">
            <!-- 
               When there exist same-name-collections in different 
               packages, we must use this and recode the makefile
               
               <xsl:apply-templates select="//package[@type = $package]//collection[@prefix = $collection]"/>
            -->
            <xsl:apply-templates select="//collection[@prefix = $collection]"/>
          </xsl:when>

          <xsl:when test="$mode = 'class'">
            <xsl:call-template name="classheader">
              <xsl:with-param name="classname" select="./@classname"/>
              <xsl:with-param name="collection" select="$collection"/>
            </xsl:call-template>
            
            <xsl:call-template name="class"/>   
          </xsl:when>
          
          <xsl:when test="$mode = 'showsource'">
            <xsl:call-template name="showsource"/>
          </xsl:when>
          
          <xsl:when test="$mode = 'search'">
            <xsl:call-template name="searchresults"/>
          </xsl:when>
          
          <xsl:otherwise>
            <xsl:call-template name="main"/>
          </xsl:otherwise>
        </xsl:choose>
     </td>
    </tr>
   </table>
  </td>
 </tr>
</table>

<table border="0" cellspacing="0" cellpadding="0" width="100%">
  <tr bgcolor="#000033"><td><img src="/image/spacer.gif" width="1" height="1" border="0" alt=""/><br/></td></tr>
  <tr bgcolor="#3654a5">
    <td align="right" valign="bottom"><a href="/credits.html" class="menuWhite">credits</a> <br/>
    </td>
  </tr>
  <tr bgcolor="#000033"><td><img src="/image/spacer.gif" width="1" height="1" border="0" alt=""/><br/></td></tr>
</table>

<table border="0" cellspacing="0" cellpadding="6" width="100%">
  <tr valign="top" bgcolor="#f0f0f0">
    <td><small>
      <a href="http://schlund.com/"><img src="/image/schlund.gif" border="0" align="left"/></a>      
	  <a href="/copyright.html">Copyright XP-Team, Schlund+Partner AG</a><br/>
      All rights reserved.<br/>
      </small>
    </td>
    <td align="right"><small>
	  This page was generated on: <xsl:value-of select="./@generated_at"/><br/>
      </small><br/>
    </td>
  </tr>
</table>

</body>
</html>
  </xsl:template>
  
  <xsl:template name="divider">
    <table border="0" cellpadding="0" cellspacing="0" width="100%">
      <tr bgcolor="#cccccc"><td colspan="1"><img src="/image/spacer.gif" height="1" border="0"/></td></tr>
    </table>
  </xsl:template>
  
  <xsl:template name="embedded-divider">
    <tr bgcolor="#cccccc"><td colspan="2"><img src="/image/spacer.gif" height="1" border="0"/></td></tr>
    <tr><td><br/></td><td><br/></td></tr>
  </xsl:template>
  
  <xsl:template name="frame">
    <xsl:param name="content"/>
    <xsl:param name="color">#000000</xsl:param>
    <xsl:param name="icolor">#FFFFFF</xsl:param>
    
    <table border="0" bgcolor="{$color}" cellspacing="1" cellpadding="0" width="100%">
      <tr><td><table border="0" bgcolor="{$icolor}" cellspacing="0" cellpadding="0" width="100%">
        <tr><td><xsl:copy-of select="$content"/></td></tr></table>
      </td></tr>
    </table>
  </xsl:template>
  
  <xsl:template match="xmp|pre|code/span/span|code/span/span/br">
    <xsl:copy>
      <xsl:copy-of select="@*"/>
      <xsl:apply-templates/>
    </xsl:copy>
  </xsl:template>
  
  <xsl:template match="code">
    <br/>
    <xsl:call-template name="frame">
      <xsl:with-param name="color" select="'#cccccc'"/>
      <xsl:with-param name="content">
        <code>
          <xsl:apply-templates/>
        </code>
      </xsl:with-param>
    </xsl:call-template>
  </xsl:template>
  
</xsl:stylesheet>
