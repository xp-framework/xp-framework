<?xml version="1.0" encoding="iso-8859-1"?>
<!--
 ! Layout stylesheet
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
  <xsl:include href="master.xsl"/>
  
  <xsl:variable name="navigation">
    <nav target="static">Home</nav>
    <nav target="news">News</nav>
    <nav target="about">About</nav>
    <nav target="documentation">Documentation</nav>
    <nav target="resources">Resources</nav>
    <nav target="devel">Development</nav>
  </xsl:variable>
  
  <xsl:variable name="area" select="substring-before(concat($__state, '/'), '/')"/>

  <!--
   ! Template that matches on the root node
   !
   ! @purpose  Define the site layout
   !-->
  <xsl:template match="/">
    <html>
      <head>
        <title>XP Framework | <xsl:value-of select="$__state"/> | <xsl:value-of select="$__page"/></title>
        <link rel="stylesheet" href="/styles/static.css"/>
      </head>
      <body>
        <form name="search" method="GET" action="/xml/{$__product}.{$__lang}/lookup">
        
          <!-- top navigation -->
          <table width="100%" border="0" cellspacing="0" cellpadding="2">
            <tr>
              <td colspan="9"><img src="/image/planet-xp.gif" width="191" height="60"/></td>
            </tr>
            <tr>
              <xsl:for-each select="exsl:node-set($navigation)/nav">
                <xsl:variable name="class">nav<xsl:if test="@target = $area">active</xsl:if></xsl:variable>
                <td width="5%" class="{$class}">
                  <a class="{$class}" href="/xml/{$__product}.{$__lang}/{@target}">
                    <xsl:value-of select="."/>
                  </a>
                </td>
              </xsl:for-each>
              <td class="nav">&#160;</td>
              <td width="5%" class="nav" align="right">
                &#160;
                <!-- <input class="search" type="text" name="q" size="24"/> -->
              </td>
              <td width="1%" class="nav" align="right">
                &#160;
                <!-- <input type="image" src="/image/submit_search.gif" border="0" width="11" height="11" alt="search"/> -->
              </td>
            </tr>
            <tr>
              <td class="navactive" colspan="9"/>
            </tr>
          </table>
        </form>

        <!-- main content -->
        <table width="100%" border="0" cellspacing="0" cellpadding="2">
          <tr>
            <td valign="top">
              <xsl:call-template name="content"/>
            </td>
            <td width="2%">&#160;</td>
            <td width="15%" valign="top" nowrap="nowrap">
              <xsl:call-template name="context"/>
              <br/>
              <img src="/image/blank.gif" width="180" height="1"/>
              <br/>
            </td>
          </tr>
        </table>
        <br/>

        <!-- footer -->
        <br/>
        <table width="100%" border="0" cellspacing="0" cellpadding="2" class="footer">
          <tr>
            <td><small>&#169; 2001-2005 the XP team</small></td>
            <td align="right"><small>
              <a href="#credits">credits</a> |
              <a href="#feedback">feedback</a>
            </small></td>
          </tr>
        </table>
      </body>
    </html>
  </xsl:template>

</xsl:stylesheet>
