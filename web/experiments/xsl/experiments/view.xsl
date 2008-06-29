<?xml version="1.0" encoding="UTF-8"?>
<!--
 ! View page
 !
 ! $Id$
 !-->
<xsl:stylesheet
 version="1.0"
 xmlns:exsl="http://exslt.org/common"
 xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
 xmlns:func="http://exslt.org/functions"
 xmlns:php="http://php.net/xsl"
 xmlns:xp="http://xp-framework.net/xsl"
 extension-element-prefixes="func"
 exclude-result-prefixes="func php exsl xsl xp"
>

  <xsl:include href="layout.inc.xsl"/>
  
  <xsl:template match="element[@mime= 'text/plain' or @mime= 'text/css' or @mime= 'text/html' or @mime= 'text/xml']">
    <pre id="view">
      <xsl:value-of select="php:function('XSLCallback::invoke', 'view', 'contents')"/>
    </pre>
    <!-- IE fix -->
    <xsl:comment>[if lt IE 8]&gt;
      &lt;script language="JavaScript"&gt;
        document.getElementById('view').style.width= document.body.offsetWidth - 320;
      &lt;/script&gt;
    &lt;![endif]</xsl:comment>
  </xsl:template>

  <xsl:template match="element[@mime= 'application/x-sh' or starts-with(name, 'Makefile')]">
    <pre id="view">
      <xsl:value-of select="php:function('XSLCallback::invoke', 'view', 'contents')"/>
    </pre>
    <!-- IE fix -->
    <xsl:comment>[if lt IE 8]&gt;
      &lt;script language="JavaScript"&gt;
        document.getElementById('view').style.width= document.body.offsetWidth - 320;
      &lt;/script&gt;
    &lt;![endif]</xsl:comment>
  </xsl:template>

  <xsl:template match="element[starts-with(name, 'README') or starts-with(name, 'TODO') or starts-with(name, 'ChangeLog')]">
    <xsl:apply-templates select="php:function('XSLCallback::invoke', 'view', 'markup')/markup"/>
  </xsl:template>

  <xsl:template match="element[@mime= 'application/x-perl' or @mime= 'application/x-php' or @mime= 'application/x-javascript' or @mime= 'application/x-java' or @mime= 'application/x-cpp' or @mime= 'application/x-csharp']">
    <pre id="view" class="code">
      <xsl:copy-of select="php:function('XSLCallback::invoke', 'view', 'highlight', substring-after(@mime, 'x-'))"/>
    </pre>
    <!-- IE fix -->
    <xsl:comment>[if lt IE 8]&gt;
      &lt;script language="JavaScript"&gt;
        document.getElementById('view').style.width= document.body.offsetWidth - 320;
      &lt;/script&gt;
    &lt;![endif]</xsl:comment>
  </xsl:template>

  <xsl:template match="element[@mime= 'application/x-c' or @mime= 'application/x-h']">
    <pre id="view" class="code">
      <xsl:copy-of select="php:function('XSLCallback::invoke', 'view', 'highlight', 'c')"/>
    </pre>
    <!-- IE fix -->
    <xsl:comment>[if lt IE 8]&gt;
      &lt;script language="JavaScript"&gt;
        document.getElementById('view').style.width= document.body.offsetWidth - 320;
      &lt;/script&gt;
    &lt;![endif]</xsl:comment>
  </xsl:template>

  <xsl:template match="element">
    <fieldset class="warning">
      <p>
        <b>This file's filetype cannot be viewed inline.</b><br/>
        To view this file, you need to download it.
        <br/><br/>
      </p>
    </fieldset>
  </xsl:template>

  <xsl:template match="element[@mime= 'image/gif' or @mime= 'image/png' or @mine = 'image/jpeg']">
    <div style="border: 1px solid #cccccc; padding: 10px; background-color: #efefef;">
      <img src="/pipe/?{$__query}" style="background-color: white;"/>
    </div>
  </xsl:template>
  
  <xsl:template name="content">
    <table id="main" cellpadding="0" cellspacing="10">
      <tr>
        <td id="content">
        
          <!-- Breadcrumb -->
          <div id="breadcrumb">
            <a href="{xp:link('home')}">Experiments</a> &#xbb;
            <xsl:call-template name="hierarchy">
              <xsl:with-param name="path" select="concat(/formresult/element/@path, ',')"/>
            </xsl:call-template> &#xbb;
            <a href="{xp:link(concat('view?', $__query))}">
              <xsl:value-of select="/formresult/element/name"/>
            </a>
          </div>
          <br/>
          
          <h2>Viewing <xsl:value-of select="/formresult/element/@mime"/></h2>
          <br/>
          <xsl:apply-templates select="/formresult/element"/>
        </td>
        <td id="context">
          <h3>
            <img align="right" src="/common/image/save.png" border="0"/>
            Download
          </h3>
          <p>
            You can download the file 
            &#xab;<xsl:value-of select="/formresult/element/name"/>&#xbb;
            by using 
            <a href="/pipe/?{$__query}:application/octet-stream">this link</a>.
          </p>
        </td>
      </tr>
    </table>
  </xsl:template>

  <xsl:template name="html-title">
    XP Forge Experiments
  </xsl:template>
  
</xsl:stylesheet>
