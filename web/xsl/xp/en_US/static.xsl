<?xml version="1.0" encoding="iso-8859-1"?>
<!--
 ! Stylesheet for home page
 !
 ! $Id$
 !-->
<xsl:stylesheet
 version="1.0"
 xmlns:exsl="http://exslt.org/common"
 xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
 xmlns:func="http://exslt.org/functions"
 extension-element-prefixes="func"
>
  <xsl:include href="../layout.xsl"/>
  <xsl:include href="../news.inc.xsl"/>
  
  <xsl:variable name="shortcuts">
    <shortcut href="about/topic?introduction" icon="introduction">Introduction</shortcut>
    <shortcut href="resources" icon="download">Download</shortcut>
    <shortcut href="about/examples" icon="examples">Examples</shortcut>
  </xsl:variable>
  
  <!--
   ! Template for context navigation
   !
   ! @see      ../../layout.xsl
   ! @purpose  Context navigation
   !-->
  <xsl:template name="context">

    <!-- news -->
    <h4 class="context">Newsflash</h4>
    <ul class="context">
      <xsl:for-each select="/formresult/news/items/item">
        <li>
          <em><xsl:value-of select="func:datetime(created_at)"/></em>:<br/>
          <a href="news/view?{news_id}">
            <xsl:value-of select="caption"/>
          </a>
        </li>
      </xsl:for-each>
    </ul>

    <!-- cvs -->
    <h4 class="context">CVS activity</h4>
    <ul class="context">
      <li>
        <em>2003-12-11 17:08</em>:<br/>
        <a href="#apidoc/classes/ch/ecma/StliConnection">StliConnection</a> (friebe)
      </li>
      <li>
        <em>2003-12-11 17:08</em>:<br/>
        <a href="#apidoc/classes/ch/ecma/StliConnection">TelephonyAddress</a> (friebe)
      </li>
      <li>
        <em>2003-09-27 15:30:00</em>:<br/>
        <a href="#apidoc/classes/com/sun/webstart/JnlpDocument">JnlpDocument</a> (friebe)
      </li>
    </ul>

    <!-- release -->
    <h4 class="context">Current release</h4>
    <ul class="context">
      <li>
        <em>2003-10-26</em>:<br/>
        <a href="#release/2003-10-26">Download</a> | <a href="#changelog/2003-10-26">Changelog</a>
      </li>
    </ul>
  </xsl:template>

  <!--
   ! Template for content
   !
   ! @see      ../../layout.xsl
   ! @purpose  Define main content
   !-->
  <xsl:template name="content">
    <h1>use::xp</h1>

    <table cellpadding="10">
      <tr>
        <xsl:for-each select="exsl:node-set($shortcuts)/shortcut">
          <td align="middle" valign="top">
            <a href="{@href}" class="nav">
              <img border="0" src="/image/icons/{@icon}.png"/><br/>
              <xsl:value-of select="."/>
            </a>
          </td>
        </xsl:for-each>
      </tr>
    </table>
    
    <xsl:for-each select="/formresult/entries/entry">
      <h3>
        <a href="news/view?{@id}">
          <xsl:value-of select="substring-after(title, ': ')"/>
        </a>
      </h3>
      <p>
        <xsl:apply-templates select="body"/>
        <xsl:if test="extended_length &gt; 0">
          &#160; ... <a href="news/view?{@id}" title="View extended entry"><b>(more)</b></a>
        </xsl:if>
        <br/>
      </p>
    </xsl:for-each>
  </xsl:template>
  
</xsl:stylesheet>
