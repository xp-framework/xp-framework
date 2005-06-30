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
  <xsl:include href="../master.xsl"/>
  <xsl:include href="calendar.inc.xsl"/>
  
  <xsl:variable name="navigation">
    <nav name="news" area="news">
      <nav name="members" area="news"/>
      <nav name="posters" area="news"/>
    </nav>
    
    <nav name="events" area="events">
      <nav name="events/training" area="events"/>
      <nav name="events/tournaments" area="events"/>
      <nav name="events/misc" area="events"/>
    </nav>
    
    <nav name="organization" area="organization">
      <nav name="requests" area="organization"/>
      <nav name="profile" area="organization"/>
      <nav name="contact" area="organization"/>
      <nav name="blog" area="organization"/>
      <nav name="admin/createplayer" area="organization"/>
      <nav name="admin/createevent" area="organization"/>
    </nav>
    
    <nav name="about"/>
    <nav name="login"/>
  </xsl:variable>
  
  <xsl:variable name="area" select="substring-before(concat($__state, '/'), '/')"/>

  <xsl:template name="login-logout">
    <xsl:choose>
      <xsl:when test="/formresult/user/username != ''">
        LOGOUT<br/>
        <a href="{func:link(concat('player/edit?player_id=', /formresult/user/player_id))}">
          <xsl:value-of select="concat(/formresult/user/firstname, ' ', /formresult/user/lastname)"/>
        </a>
      </xsl:when>
      <xsl:otherwise>
        LOGIN
      </xsl:otherwise>
    </xsl:choose>
  </xsl:template>
  
  <!--
   ! Template that matches on the root node
   !
   ! @purpose  Define the site layout
   !-->
  <xsl:template match="/">
    <html>
      <head>
        <title>United Schlund | <xsl:value-of select="$__state"/> | <xsl:value-of select="$__page"/></title>
        <link rel="stylesheet" href="/styles/static.css"/>
      </head>
      <body>
        <form name="search" method="GET" action="/xml/{$__product}.{$__lang}/lookup">
        
          <!-- top navigation -->
          <table width="100%" border="0" cellspacing="0" cellpadding="2">
            <tr>
              <td colspan="9" align="right" valign="top" height="100">
                <img src="/image/logo.png" width="608" height="44" align="left"/>
                <xsl:call-template name="login-logout"/>
              </td>
            </tr>
            <tr>
              <xsl:for-each select="exsl:node-set($navigation)/nav">
                <xsl:variable name="class">nav<xsl:if test="@area = $area">active</xsl:if></xsl:variable>
                <td width="5%" class="{$class}" nowrap="nowrap">
                  <a class="{$class}" href="{func:link(@name)}">
                    <xsl:value-of select="func:get_text(concat('nav#', @name))"/>
                  </a>
                </td>
              </xsl:for-each>
              <td class="nav" colspan="9 - count(exsl:node-set($navigation)/nav)" width="100%">&#160;</td>
            </tr>
            <tr>
              <td class="navactive" colspan="9"/>
            </tr>
          </table>
        </form>

        <!-- main content -->
        <table width="100%" border="0" cellspacing="0" cellpadding="2">
          <tr>
            <td width="15%" valign="top" nowrap="nowrap">
              <xsl:call-template name="context"/>
              <br/>
              <img src="/image/blank.gif" width="180" height="1"/>
              <br/>
            </td>
            <td width="2%">&#160;</td>
            <td valign="top">
              <xsl:call-template name="content"/>
            </td>
          </tr>
        </table>
        <br/>

        <!-- footer -->
        <br/>
        <table width="100%" border="0" cellspacing="0" cellpadding="2" class="footer">
          <tr>
            <td valign="top"><small>&#169; 2005 Alex Kiesel</small></td>
            <td align="right" valign="top"><small>
              <a href="#credits">credits</a> |
              <a href="#feedback">feedback</a> |
              <a href="http://xp-framework.net"><img src="/image/powered_by_xp.png" width="80" height="15" border="0" align="top"/></a>
            </small></td>
          </tr>
        </table>
      </body>
    </html>
  </xsl:template>

</xsl:stylesheet>
