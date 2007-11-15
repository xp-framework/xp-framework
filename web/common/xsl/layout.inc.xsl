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
 exclude-result-prefixes="func php exsl xsl xp"
>
  <xsl:import href="overrideables.inc.xsl"/>
  <xsl:include href="master.xsl"/>
  <xsl:include href="date.inc.xsl"/>
    
  <xsl:variable name="sitemap" select="document('sitemap.xml')"/>
  
  <xsl:template match="/">
    <html>
      <xsl:call-template name="generate-page-head"/>
      <xsl:call-template name="generate-page-body"/>
      <xsl:call-template name="generate-page-foot"/>
    </html>
  </xsl:template>
  
  <xsl:template name="generate-tracking-code">
    <xsl:variable name="tracking-code"><xsl:call-template name="tracking-code"/></xsl:variable>
    <xsl:if test="$tracking-code != ''">
      <script src="http://www.google-analytics.com/urchin.js" type="text/javascript">
      </script>
      <script type="text/javascript">
      _uacct = "<xsl:value-of select="$tracking-code"/>";
      urchinTracker();
      </script>
    </xsl:if>
  </xsl:template>
  
  <xsl:template name="generate-page-head">
    <head>
      <title><xsl:call-template name="html-title"/></title>
      <link rel="stylesheet" type="text/css" href="/style/style.css"/>
      <xsl:call-template name="html-head"/>
    </head>
  </xsl:template>
  
  <xsl:template name="generate-page-body">
    <body>
      <xsl:call-template name="top-navigation"/>
      <xsl:call-template name="content"/>
      <xsl:call-template name="generate-tracking-code"/>
    </body>
  </xsl:template>
  
  <xsl:template name="generate-page-foot">
    <div id="footer">
      <a href="#">Credits</a> |
      <a href="#">Feedback</a>
      
      <br/>
      
      © 2001-<xsl:value-of select="xp:dateformat(/formresult/@serial, 'Y')"/> the XP team
    </div>
  </xsl:template>
  
  <xsl:template name="top-navigation">
    <div id="search">
      <!-- FIXME: Create search -->
      <form action="#">
        <label for="query"><u>S</u>earch XP website for </label>
        <input name="query" accesskey="s" type="text">&#160;</input>
      </form>
    </div>
    <div id="top">&#160;
    </div>
    <div id="menu">
      <ul>
        <li id="select"><a href="#">&#160;</a></li>
        <xsl:for-each select="exsl:node-set($sitemap)/root/item">
          <li>
            <xsl:if test="@name = exsl:node-set($navigation)/area/@name"><xsl:attribute name="id">active</xsl:attribute></xsl:if>
           </li>
           <a href="{@href}"><xsl:value-of select="."/></a>
        </xsl:for-each>
      </ul>
      <!-- For Mozilla to calculate height correctly -->
      &#160;
    </div>
  </xsl:template>
</xsl:stylesheet>