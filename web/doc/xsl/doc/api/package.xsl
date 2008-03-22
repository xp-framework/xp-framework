<?xml version="1.0" encoding="iso-8859-1"?>
<xsl:stylesheet
 version="1.0"
 xmlns:exsl="http://exslt.org/common"
 xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
 xmlns:str="http://exslt.org/strings"
 xmlns:func="http://exslt.org/functions"
 extension-element-prefixes="exsl func"
>

  <xsl:output method="html" encoding="iso-8859-1"/>

  <func:function name="func:first-sentence">
    <xsl:param name="comment"/>
    
    <func:result>
      <xsl:value-of select="exsl:node-set(str:tokenize($comment, '.&#10;'))"/>
    </func:result>
  </func:function>
  
  <func:function name="func:ltrim">
    <xsl:param name="text"/>
    <xsl:param name="chars"/>
    
    <func:result>
      <xsl:choose>
        <xsl:when test="contains(substring($text, 1, 1), $chars)">
          <xsl:value-of select="func:ltrim(substring($text, 2, string-length($text)), $chars)"/>
        </xsl:when>
        <xsl:otherwise>
          <xsl:value-of select="$text"/>
        </xsl:otherwise>
      </xsl:choose>
    </func:result>
  </func:function>
  
  <func:function name="func:cutstring">
    <xsl:param name="text"/>
    <xsl:param name="maxlength"/>
    
    <func:result>
      <xsl:choose>
        <xsl:when test="string-length($text) &lt;= $maxlength">
          <xsl:value-of select="$text"/>
        </xsl:when>
        <xsl:otherwise>
          <span title="{$text}">
            <xsl:value-of select="substring($text, 1, $maxlength)"/>
            <b>[...]</b>
          </span>
        </xsl:otherwise>
      </xsl:choose>
    </func:result>
  </func:function>
  
  <func:function name="func:typelink">
    <xsl:param name="type"/>
    
    <func:result>
      <a>
        <xsl:if test="contains($type, '.')">
          <xsl:attribute name="href">
            <xsl:value-of select="concat('?class:', string(exsl:node-set(str:tokenize(func:ltrim($type, '&amp;'), '[&amp;'))))"/>
          </xsl:attribute>
        </xsl:if>
        
        <xsl:value-of select="$type"/>
      </a>
    </func:result>
  </func:function>

  <xsl:template match="comment//*">
    <xsl:copy>
      <xsl:copy-of select="@*"/>
      <xsl:apply-templates/>
    </xsl:copy>
  </xsl:template>

  <xsl:template match="package">
    <xsl:variable name="package" select="concat(@name, '.')"/>
    <style type="text/css">
      h2 { margin-top: 30px; }
      h3 { margin-top: 20px; }
      h4 { font: bold 13px "Trebuchet MS", "Arial", sans-serif; margin-top: 0px; }
      hr { border: 0; background-color: #cccccc; height: 1px; }
      fieldset {
        margin-top: 20px;
        border: 1px solid #3165c5;
      }
      legend {
        font: bold 13px "Trebuchet MS", "Arial", sans-serif;
        color: #3165c5;
      }
      #content ul {
        list-style-type: square;
        list-style-image: url(image/li.gif);
        line-height: 18px;
      }
      code {
        display: block;
        white-space: pre;
      }
      a.class {
        background: url(image/arrow.png);
        background-position: right center; 
        background-repeat: no-repeat;
        padding-right: 20px;
      }
    </style>
    <h1>
      <xsl:value-of select="@name"/>
    </h1>

    <h2>Purpose: <xsl:value-of select="purpose"/></h2>
    <div class="apidoc">
      <xsl:apply-templates select="comment"/>
    </div>

    <h2>Package contents</h2>

    <a name="__interfaces"/>
    <xsl:if test="count(class[@type = 'interface'])">
      <fieldset>
        <legend>Interface Summary</legend>
        <ul>
          <xsl:for-each select="class[@type = 'interface']">
            <li>
              <a href="?class:{$package}{@name}"><b><xsl:value-of select="concat($package, @name)"/></b></a>
            </li>
          </xsl:for-each>
        </ul>
      </fieldset>
    </xsl:if>

    <a name="__classes"/>
    <xsl:if test="count(class[@type = 'class'])">
      <fieldset>
        <legend>Class Summary</legend>
        <ul>
          <xsl:for-each select="class[@type = 'class']">
            <li>
              <a href="?class:{$package}{@name}"><b><xsl:value-of select="concat($package, @name)"/></b></a>
            </li>
          </xsl:for-each>
        </ul>
      </fieldset>
    </xsl:if>

    <a name="__exceptions"/>
    <xsl:if test="count(class[@type = 'exception'])">
      <fieldset>
        <legend>Exception Summary</legend>
        <ul>
          <xsl:for-each select="class[@type = 'exception']">
            <li>
              <a href="?class:{$package}{@name}"><b><xsl:value-of select="concat($package, @name)"/></b></a>
            </li>
          </xsl:for-each>
        </ul>
      </fieldset>
    </xsl:if>

    <a name="__errors"/>
    <xsl:if test="count(class[@type = 'error'])">
      <fieldset>
        <legend>Error Summary</legend>
        <ul>
          <xsl:for-each select="class[@type = 'error']">
            <li>
              <a href="?class:{$package}{@name}"><b><xsl:value-of select="concat($package, @name)"/></b></a>
            </li>
          </xsl:for-each>
        </ul>
      </fieldset>
    </xsl:if>
  </xsl:template>
  
  <xsl:template match="see[@scheme = 'xp']" mode="short">
    <a href="?class:{@href}"><xsl:copy-of select="func:cutstring(@href, 24)"/></a>
  </xsl:template>

  <xsl:template match="see[@scheme = 'php']" mode="short">
    <a href="http://php3.de/{@href}"><xsl:copy-of select="func:cutstring(@href, 24)"/></a>
  </xsl:template>

  <xsl:template match="see[@scheme = 'http']" mode="short">
    <a href="http://{@href}"><xsl:copy-of select="func:cutstring(@href, 24)"/></a>
  </xsl:template>

  <xsl:template match="see[@scheme = 'xp']">
    <a href="?class:{@href}"><xsl:value-of select="@href"/></a>
  </xsl:template>

  <xsl:template match="see[@scheme = 'php']">
    <a href="http://php3.de/{@href}"><xsl:value-of select="@href"/></a>
  </xsl:template>

  <xsl:template match="see[@scheme = 'http']">
    <a href="http://{@href}"><xsl:value-of select="@href"/></a>
  </xsl:template>

  <xsl:template match="/">
    <html><head><link rel="stylesheet" href="style.css"/></head><body>
    <div id="search">
      <form action="/search">
        <label for="query"><u>S</u>earch XP website for </label>
        <input name="query" accesskey="s" type="text"></input>
      </form>
    </div>
    <div id="top">&#160;
    </div>
    <div id="menu">
      <ul>
        <li><a href="home.html">Home</a></li>
        <li><a href="news.html">News</a></li>
        <li id="active"><a href="?">Documentation</a></li>
        <li><a href="download.html">Download</a></li>
        <li><a href="dev.html">Developers</a></li>
      </ul>
      <!-- For Mozilla to calculate height correctly -->
      &#160;
    </div>
    <table id="main" cellpadding="0" cellspacing="10"><tr>
      <td id="content">

        <xsl:apply-templates select="doc"/>
        
      </td>
      <td id="context">
        <h3>Jump to</h3>
        <a href="#__constants">Interfaces</a><br/>
        <a href="#__fields">Classes</a><br/>
        <a href="#__methods">Exceptions</a><br/>
        <a href="#__methods">Errors</a><br/>

        <xsl:if test="count(doc/package/see) &gt; 0">
          <h3>See also</h3>
          
          <xsl:for-each select="doc/package/see">
            <xsl:apply-templates select="." mode="short"/>
            <br/>
          </xsl:for-each>
        </xsl:if>
      </td>
    </tr></table>
    <div id="footer">
      <a href="credits.html">Credits</a> |
      <a href="feedback.html">Feedback</a>
      
      <br/>
      
      (c) 2001-2007 the XP team
    </div>
    </body></html>
  </xsl:template>
</xsl:stylesheet>
