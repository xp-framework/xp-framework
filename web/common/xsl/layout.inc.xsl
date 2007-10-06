<?xml version="1.0" encoding="UTF-8"?>
<!--
 ! Overview page
 !
 ! $Id: master.xsl 4410 2004-12-18 18:19:28Z friebe $
 !-->
<xsl:stylesheet
 version="1.0"
 xmlns:exsl="http://exslt.org/common"
 xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
 xmlns:func="http://exslt.org/functions"
 xmlns:php="http://php.net/xsl"
 xmlns:xp="http://xp-framework.net/xsl"
 extension-element-prefixes="func"
 remote-result-prefixes="func php exsl xsl"
>
  <xsl:import href="overrideables.inc.xsl"/>
  <xsl:include href="master.xsl"/>
  <xsl:include href="date.inc.xsl"/>
  
  <xsl:template match="/">
    <html>
      <xsl:call-template name="generate-page-head"/>
      <xsl:call-template name="generate-page-body"/>
      <xsl:call-template name="generate-page-foot"/>
    </html>
  </xsl:template>
  
  <xsl:template name="generate-page-head">
    <head>
      <title><xsl:call-template name="html-title"/></title>
      <link rel="stylesheet" type="text/css" href="/style/style.css"/>
      <xsl:call-template name="html-head"/>
    </head>
  </xsl:template>
  
  <xsl:template name="generate-page-body">
    <xsl:call-template name="top-navigation"/>
    <xsl:call-template name="content"/>
  </xsl:template>
  
  <xsl:template name="generate-page-foot">
    <div id="footer">
      <a href="credits.html">Credits</a> |
      <a href="feedback.html">Feedback</a>
      
      <br/>
      
      © 2001-<xsl:value-of select="xp:dateformat(/formresult/@serial, 'Y')"/> the XP team
    </div>    
  </xsl:template>
</xsl:stylesheet>