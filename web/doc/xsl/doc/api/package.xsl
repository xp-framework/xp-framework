<?xml version="1.0" encoding="UTF-8"?>
<!--
 ! Overview page
 !
 ! $Id$
 !-->
<xsl:stylesheet
 version="1.0"
 xmlns:exsl="http://exslt.org/common"
 xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
 xmlns:func="http://exslt.org/functions"
 xmlns:php="http://php.net/xsl"
 xmlns:str="http://exslt.org/strings"
 xmlns:xp="http://xp-framework.net/xsl"
 extension-element-prefixes="func str"
 exclude-result-prefixes="func php exsl xsl xp str"
>
  <xsl:include href="../layout.inc.xsl"/>

  <func:function name="func:first-sentence">
    <xsl:param name="comment"/>
    
    <func:result>
      <xsl:value-of select="exsl:node-set(str:tokenize($comment, '.&#10;'))"/>
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
            <xsl:value-of select="concat('?', string(exsl:node-set(str:tokenize(func:ltrim($type, '&amp;'), '[&amp;'))))"/>
          </xsl:attribute>
        </xsl:if>
        
        <xsl:value-of select="$type"/>
      </a>
    </func:result>
  </func:function>
  
  <xsl:template match="comment">
    <div class="apidoc">
      <xsl:apply-templates/>
    </div>
  </xsl:template>

  <xsl:template match="comment//*">
    <xsl:copy>
      <xsl:copy-of select="@*"/>
      <xsl:apply-templates/>
    </xsl:copy>
  </xsl:template>

  <xsl:template match="package">
    <xsl:variable name="package" select="concat(@name, '.')"/>
    <h1>
      <xsl:value-of select="@name"/>
    </h1>

    <h2>Purpose: <xsl:value-of select="purpose"/></h2>
    <xsl:apply-templates select="comment"/>
    
    <xsl:if test="count(see) &gt; 0">
      <h2>See also</h2>

      <ul>
        <xsl:for-each select="see">
          <li>
            <xsl:apply-templates select="."/>
          </li>
        </xsl:for-each>
      </ul>
    </xsl:if>
    

    <h2>Package contents</h2>
    <a name="__interfaces"/>
    <xsl:if test="count(class[@type = 'interface'])">
      <fieldset>
        <legend>Interface Summary</legend>
        <ul>
          <xsl:for-each select="class[@type = 'interface']">
            <li>
              <a href="{xp:link(concat('api/class?', $package, @name))}"><b><xsl:value-of select="concat($package, @name)"/></b></a>
            </li>
          </xsl:for-each>
        </ul>
      </fieldset>
    </xsl:if>

    <a name="__classes"/>
    <xsl:if test="count(class[@type = 'class'])">
      <fieldset class="summary">
        <legend>Class Summary</legend>
        <ul>
          <xsl:for-each select="class[@type = 'class']">
            <li>
              <a href="{xp:link(concat('api/class?', $package, @name))}"><b><xsl:value-of select="concat($package, @name)"/></b></a>
            </li>
          </xsl:for-each>
        </ul>
      </fieldset>
    </xsl:if>

    <a name="__exceptions"/>
    <xsl:if test="count(class[@type = 'exception'])">
      <fieldset class="summary">
        <legend>Exception Summary</legend>
        <ul>
          <xsl:for-each select="class[@type = 'exception']">
            <li>
              <a href="{xp:link(concat('api/class?', $package, @name))}"><b><xsl:value-of select="concat($package, @name)"/></b></a>
            </li>
          </xsl:for-each>
        </ul>
      </fieldset>
    </xsl:if>

    <a name="__errors"/>
    <xsl:if test="count(class[@type = 'error'])">
      <fieldset class="summary">
        <legend>Error Summary</legend>
        <ul>
          <xsl:for-each select="class[@type = 'error']">
            <li>
              <a href="{xp:link(concat('api/class?', $package, @name))}"><b><xsl:value-of select="concat($package, @name)"/></b></a>
            </li>
          </xsl:for-each>
        </ul>
      </fieldset>
    </xsl:if>
  </xsl:template>
  
  <xsl:template match="see[@scheme = 'xp']" mode="short">
    <a href="?{@href}"><xsl:copy-of select="func:cutstring(@href, 24)"/></a>
  </xsl:template>

  <xsl:template match="see[@scheme = 'php']" mode="short">
    <a href="http://php3.de/{@href}"><xsl:copy-of select="func:cutstring(@href, 24)"/></a>
  </xsl:template>

  <xsl:template match="see[@scheme = 'http']" mode="short">
    <a href="http://{@href}"><xsl:copy-of select="func:cutstring(@href, 24)"/></a>
  </xsl:template>

  <xsl:template match="see[@scheme = 'xp']">
    <a href="?{@href}"><xsl:value-of select="@href"/></a>
  </xsl:template>

  <xsl:template match="see[@scheme = 'php']">
    <a href="http://php3.de/{@href}"><xsl:value-of select="@href"/></a>
  </xsl:template>

  <xsl:template match="see[@scheme = 'http']">
    <a href="http://{@href}"><xsl:value-of select="@href"/></a>
  </xsl:template>

  <xsl:template name="html-head">
    <link rel="shortcut icon" href="/common/favicon.ico" />
  </xsl:template>
 
  <xsl:template name="content">
    <table id="main" cellpadding="0" cellspacing="10">
      <tr>
        <td id="content">
          <xsl:apply-templates select="/formresult/doc/package"/>
        </td>
        <td id="context">
          <!-- -->
        </td>
      </tr>
    </table>
  </xsl:template>
  
  <xsl:template name="html-title">
    <xsl:value-of select="/formresult/doc/package/@name"/> - XP Framework Documentation
  </xsl:template>
</xsl:stylesheet>
