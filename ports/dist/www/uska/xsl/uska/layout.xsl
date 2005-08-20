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
    <nav name="news" area="news"/>
    
    <!-- 
    <nav name="people" area="people">
      <nav name="members" area="people"/>
      <nav name="posters" area="people"/>
    </nav>
    -->
    
    <nav name="events" area="events">
      <nav name="events?training" area="events"/>
      <nav name="events?tournaments" area="events"/>
      <nav name="events?misc" area="events"/>
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
    <xsl:if test="/formresult/user/username != ''">
      Angemeldet als
      <xsl:value-of select="concat(/formresult/user/firstname, ' ', /formresult/user/lastname)"/>
    </xsl:if>
  </xsl:template>
  
  
  <xsl:template name="default_subnavigation">
    <xsl:param name="items"/>
    
    <div id="sub_nav_container">
      <ul id="sub_nav_list">
        <xsl:for-each select="exsl:node-set($items)/items/item">
          <li><a class="sub_nav_item" href="{@href}"><span class="sub_nav_item_text"><xsl:value-of select="."/></span></a></li>
        </xsl:for-each>
      </ul>
    </div>
  </xsl:template>
  
  <!--
   ! Template that matches on the root node
   !
   ! @purpose  Define the site layout
   !-->
  <xsl:template match="/">
    <html>
      <head>
        <title>United Schlund Karlsruhe eV.</title>
        <link rel="stylesheet" href="/styles/main.css"/>
        <link rel="stylesheet" href="/styles/common.css"/>
      </head>
      <body>
        <div id="container">
          <!-- Header -->
          <div id="header">
            <div id="text_caption"><xsl:value-of select="func:get_text(concat('pagecaption#', $__state, '-', $__page))"/></div>
            <div id="key_visual"></div>
          </div>

          <div id="main_nav_container">
            <ul id="main_nav_list">
              <xsl:for-each select="exsl:node-set($navigation)/nav">
                <xsl:variable name="class">main_nav_<xsl:if test="@area = $area">active_</xsl:if>item</xsl:variable>
                <li>
                  <a class="{$class}" href="{func:link(@name)}">
                    <span class="{$class}_text"><xsl:value-of select="func:get_text(concat('nav#', @name))"/></span>
                  </a>
                </li>
              </xsl:for-each>
            </ul>
          </div>

          <!-- Main pane -->
          <div id="main_container">
            <div id="left_column_container">
              <xsl:call-template name="context"/>
              <div id="sub_container1"></div>
            </div>

            <div id="sub_container2">
              <div id="content_container">
                <xsl:call-template name="content"/>
              </div>
            </div>
          </div>

          <!-- Footer -->
          <div id="footer"> 
            <div id="footer_text">
              (c) Copyright USKA 2005 | 
              <a href="http://xp-framework.net"><img src="/image/powered_by_xp.png" width="80" height="15" border="0" align="top"/></a>
            </div>
          </div>
        </div>
      </body>
    </html>
  </xsl:template>

</xsl:stylesheet>
