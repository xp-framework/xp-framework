<?xml version="1.0" encoding="UTF-8"?>
<!--
 ! Markup template for markup rendered by Doclet markup API
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

  <!-- 
   ! Transform <code> ... </code> to <pre class="code"> ... </pre>
   ! because of IE's lacking support for white-space: pre
   !-->
  <xsl:template match="code">
    <pre class="code">
      <xsl:apply-templates/>
    </pre>
  </xsl:template>

  <!--
   ! Links to documentation
   !-->
  <xsl:template match="link[@rel= 'doc']">
    <a href="http://docs.xp-framework.net/xml/doc?{@href}"><xsl:value-of select="."/></a>
  </xsl:template>

  <!--
   ! Links to api docs
   !-->
  <xsl:template match="link[@rel= 'package']">
    <a href="http://docs.xp-framework.net/xml/api/package?{@href}"><xsl:value-of select="."/></a>
  </xsl:template>

  <!--
   ! Links to api docs
   !-->
  <xsl:template match="link[@rel= 'class']">
    <a href="http://docs.xp-framework.net/xml/api/class?{@href}"><xsl:value-of select="."/></a>
  </xsl:template>

  <!--
   ! Links to a state in this site
   !-->
  <xsl:template match="link[@rel= 'state']">
    <a href="/xml/{@href}"><xsl:value-of select="."/></a>
  </xsl:template>

  <!--
   ! Links to an XP RFC
   !-->
  <xsl:template match="link[@rel= 'rfc']">
    <a href="http://developer.xp-framework.net/xml/rfc/view?{@href}"><xsl:value-of select="."/></a>
  </xsl:template>

  <!--
   ! Links to another topic
   !-->
  <xsl:template match="link[@rel= 'topic']">
    <a href="{xp:link(concat($__state, '?', @href))}"><xsl:value-of select="."/></a>
  </xsl:template>

  <!--
   ! Links to a PHP manual page
   !-->
  <xsl:template match="link[@rel= 'php']">
    <a href="http://de3.php.net/{@href}"><xsl:value-of select="."/></a>
  </xsl:template>

  <!--
   ! Links without caption
   !-->
  <xsl:template match="link[string(.) = '']">
    <a href="{@rel}://{@href}"><xsl:value-of select="concat(@rel, '://', @href)"/></a>
  </xsl:template>

  <!--
   ! Links
   !-->
  <xsl:template match="link">
    <a href="{@rel}://{@href}"><xsl:value-of select="."/></a>
  </xsl:template>

  <!--
   ! Summary
   !-->
  <xsl:template match="summary">
    <fieldset class="summary">
      <xsl:apply-templates/>
    </fieldset>
  </xsl:template>

</xsl:stylesheet>
