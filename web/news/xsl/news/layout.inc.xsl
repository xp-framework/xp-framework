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
  <xsl:include href="../../../common/xsl/layout.inc.xsl"/>

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
        <!-- FIXME: Do not hardcode domain names -->
        <li id="select"><a href="#">&#160;</a></li>
        <li><a href="http://planet-xp.net/">Home</a></li>
        <li id="active"><a href="http://news.xp-framework.net/">News</a></li>
        <!--  <li><a href="docs.html">Documentation</a></li> -->
        <li><a href="http://xp-framework.net/download/">Download</a></li>
        <!--  <li><a href="dev.html">Developers</a></li> -->
      </ul>
      <!-- For Mozilla to calculate height correctly -->
      &#160;
    </div>
  </xsl:template>
  
  <xsl:template match="pager">
    <div style="text-align: center;">
      <a href="{xp:link(concat($__state, '?', /formresult/current-category/@id, ',', @prev))}">&lt;&lt;&lt;</a>
      |
      <a href="{xp:link(concat($__state, '?', /formresult/current-category/@id, ',', @next))}">&gt;&gt;&gt;</a>
    </div>
  </xsl:template>
</xsl:stylesheet>