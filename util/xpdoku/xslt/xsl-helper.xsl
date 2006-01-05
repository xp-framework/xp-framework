<?xml version="1.0" encoding="iso-8859-1" ?>
<xsl:stylesheet
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform" 
  version="1.0"
>
  <xsl:param name="area" select="'home'"/>
  <xsl:variable name="areas">
    <area name="home" color="#d3d3d3" logo="logo-home"/>
    <area name="documentation" color="#9eb6fe" logo="logo"/>
    <area name="about" color="#add3b0" logo="logo-green"/>
    <area name="resources" color="#f7c794" logo="logo-orange"/>
    <area name="devel" color="#ffe97f" logo="logo-yellow"/>
  </xsl:variable>
  
  <xsl:template match="packages|classdoc|document">
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
         <xsl:when test="name() = 'document'">
           <xsl:value-of select="/document/@title"/>
         </xsl:when>
         <xsl:otherwise>XP::Documentation</xsl:otherwise>
       </xsl:choose>
     </title>
     <link rel="stylesheet" href="/style.css"/>
     <link rel="alternate"  type="application/rss+xml" title="RSS" href="http://xp-framework.net/news.rdf.xml"/>
     <meta name="revisit-after" content="7 days"/>
     <meta name="description" content="XP is an object-oriented PHP framework"/>
     <meta name="keywords" content="PHP,PHP5,OOP,ZE2,object-oriented,framework,class collection"/>
     <meta name="author" content="The XP team"/>
     <meta name="copyright" content="2002-2006"/>
    </head>

    <body
      topmargin="0" 
      leftmargin="0"
      marginheight="0" 
      marginwidth="0"
      bgcolor="#ffffff"
      text="#000000"
      link="#000033"
      alink="#0099ff"
      vlink="#000033"
    >
    <a name="TOP"/>
    <table border="0" cellspacing="0" cellpadding="0" width="100%">
      <tr bgcolor="{$areas/area[@name= $area]/@color}">
        <td align="left" colspan="6">
           <a href="/">
             <img border="0" width="120" height="64" vspace="0" hspace="0" src="/image/xp-{$areas/area[@name= $area]/@logo}.gif"/>
           </a>
        </td>
      </tr>
      <tr bgcolor="#000033">
        <td colspan="6">
          <img src="/image/spacer.gif" width="1" height="1" border="0" alt=""/><br/>
        </td>
      </tr>

      <tr bgcolor="#666666">
        <form method="GET" action="/search.php">
          <td bgcolor="#fafafa" width="2%" nowrap="nowrap" align="center">
            <img src="/image/spacer.gif" width="100" height="1" border="0" alt=""/><br/>
            <b>&#160; <a href="/" class="menuBlack">home</a> &#160;</b>
          </td>
          <td bgcolor="#3654a5" width="2%" nowrap="nowrap" align="center">
            <img src="/image/spacer.gif" width="100" height="1" border="0" alt=""/><br/>
            <b>&#160; <a href="/apidoc/" class="menuWhite">documentation</a> &#160;</b>
          </td>
          <td bgcolor="#62996a" width="2%" nowrap="nowrap" align="center">
            <img src="/image/spacer.gif" width="100" height="1" border="0" alt=""/><br/>
            <b>&#160; <a href="/content/about.html" class="menuWhite">about</a>  &#160;</b>
          </td>
          <td bgcolor="#d58120" width="2%" nowrap="nowrap" align="center">
            <img src="/image/spacer.gif" width="100" height="1" border="0" alt=""/><br/>
            <b>&#160; <a href="/resources/index.html" class="menuWhite">resources</a>  &#160;</b>
          </td>
          <td bgcolor="#ffde41" width="2%" nowrap="nowrap" align="center">
            <img src="/image/spacer.gif" width="100" height="1" border="0" alt=""/><br/>
            <b>&#160; <a href="/devel/index.html" class="menuBlack">development</a>  &#160;</b>
          </td>
          <td class="searchbar" align="right" valign="top" nowrap="nowrap">
            <font color="white">
              <small><u>s</u>earch for</small>
              <input class="small" type="text" name="keyword" value="" size="30" accesskey="s"/>
              <!--
              <small>in the</small>
              <select name="show" class="small">
                <option value="apidoc">api docs</option>
              </select>
              -->
              <input type="image" src="/image/small_submit_white.gif" border="0" width="11" height="11" ALT="search"  align="bottom"/>
              <br/>
            </font>
          </td>
        </form>
      </tr>
      
      <tr bgcolor="#000033">
        <td colspan="6">
          <img src="/image/spacer.gif" width="1" height="1" border="0" alt=""/><br/>
        </td>
      </tr>
    </table>
    <table cellpadding="0" cellspacing="0">
      <tr valign="top">
        <td bgcolor="#fafafa">
          <table width="170" cellpadding="4" cellspacing="0">
            <tr valign="top">
	          <td class="sidebar">
                <xsl:call-template name="navigation"/>
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
                       When there are same-name-collections in different 
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
                    <xsl:apply-templates select="main"/>
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
      <tr bgcolor="{$areas/area[@name= $area]/@color}">
        <td align="right" valign="bottom">
          <xsl:if test="./@generated_at != ''">
            <small>
	          This page was generated on: <xsl:value-of select="./@generated_at"/> |
            </small>
          </xsl:if>
          <a href="/credits.html" class="menuBlack">credits</a>
          <br/>
        </td>
      </tr>
      <tr bgcolor="#000033"><td><img src="/image/spacer.gif" width="1" height="1" border="0" alt=""/><br/></td></tr>
    </table>

    <table border="0" cellspacing="0" cellpadding="6" width="100%">
      <tr valign="top" bgcolor="#f0f0f0">
        <td><small>
          <a href="http://schlund.com/"><img title="Hosted by Schlund+Partner" alt="Schlund+Partner logo" src="/image/schlund.gif" width="110" height="20" border="0" align="left"/></a>      
	      <!--<a href="/copyright.html">-->Copyright 2001-2005 XP-Team<!--</a>--><br/>
          All rights reserved.<br/>
          </small>
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

  <xsl:template name="nav-divider">
    <xsl:param name="caption"/>
    <xsl:param name="colorcode" select="'home'"/>
    <xsl:param name="link" select="''"/>
    <table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td bgcolor="{$areas/area[@name= $colorcode]/@color}" style="border-bottom: 1px solid #666666">
          <b>
            <a>
              <xsl:if test="$link">
                <xsl:attribute name="href"><xsl:value-of select="$link"/></xsl:attribute>
              </xsl:if>
              <xsl:apply-templates select="$caption"/>
            </a>
          </b>
        </td>
      </tr>
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
    <xsl:param name="margin">0</xsl:param>
    
    <table border="0" bgcolor="{$color}" cellspacing="1" cellpadding="0" width="100%">
      <tr><td><table border="0" bgcolor="{$icolor}" cellspacing="0" cellpadding="{$margin}" width="100%">
        <tr><td><xsl:copy-of select="$content"/></td></tr></table>
      </td></tr>
    </table>
  </xsl:template>
  
  <xsl:template match="frame">
    <xsl:call-template name="frame">
      <xsl:with-param name="content"><xsl:copy-of select="."/></xsl:with-param>
      <xsl:with-param name="color">#cccccc</xsl:with-param>
    </xsl:call-template>
  </xsl:template>
  
  <xsl:template match="quote|ul|ul/li|pre|code/span/span|code/span/span/br">
    <xsl:copy>
      <xsl:copy-of select="@*"/>
      <xsl:apply-templates/>
    </xsl:copy>
  </xsl:template>
  
  <xsl:template match="code">
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
