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
  <xsl:include href="../../../common/xsl/layout.inc.xsl"/>

  <xsl:template name="top-navigation">
    <div id="search">
      <form action="/search">
        <label for="query"><u>S</u>earch XP website for </label>
        <input name="query" accesskey="s" type="text"/>
      </form>
    </div>
    <div id="top">&#160;
    </div>
    <div id="menu">
      <ul>
        <li id="select"><a href="#">&#160;</a></li>
        <li><a href="home.html">Home</a></li>
        <li id="active"><a href="#">News</a></li>
        <li><a href="docs.html">Documentation</a></li>
        <li><a href="download.html">Download</a></li>
        <li><a href="dev.html">Developers</a></li>
      </ul>
      <!-- For Mozilla to calculate height correctly -->
      &#160;
    </div>
  </xsl:template>
</xsl:stylesheet>