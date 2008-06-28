<?xml version="1.0" encoding="UTF-8"?>
<!--
 ! Overview page
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

  <xsl:template name="content">
    <div style="padding: 0; margin: 0; width: 100%; height: 200px; background: #710303 url(/image/header.png); color: white">
      <p style="padding: 70px 10px 0 10px; margin: 0">
        This site is dedicated to the development of the XP framework.
        <br/><br/>
      </p>
    </div>
    <table id="main" cellpadding="0" cellspacing="10"><tr>
      <td id="content" style="background: white url(/image/strawberries.jpg) no-repeat bottom left">
        <h1>xp::forge</h1>
        <p>
          Software written using the XP framework.
        </p>
        <br clear="all"/>

        <!-- Featured items -->
        <table width="100%" class="columned"><tr>
          <td width="70%" valign="top" id="left">
            <h2>Featured projects:</h2>
            
            <!-- Dialog -->
            <h3><a href="#{xp:link('projects/dialog')}">Dialog</a></h3>
            <p align="justify">
              <b>Dialog</b> is a photo blog software written in XP. It features offline resizing and 
              sharpening of the selected images to save bandwidth and web server CPU time, it's 
              <acronym title="via CSS">skinnable</acronym>, doesn't require a database and is easy to
              set up.
              <em>See it in action at the core developers' homepages (<a href="http://dialog.friebes.info">Timm</a>,
              <a href="http://dialog.kiesel.name">Alex</a>) or check out the <a href="#">project page</a>.</em>
            </p>
            <br/><br clear="all"/>

            <!-- LuceneD -->
            <h3><a href="#{xp:link('projects/calc')}">Lucene Daemon</a></h3>
            <p align="justify">
              Wraps the Apache Lucene search engine in a TCP/IP daemon so it can function as a search service.
            </p>
            <br/><br clear="all"/>
            
            <!-- Calc -->
            <h3><a href="#{xp:link('projects/calc')}">Calculator CLI</a></h3>
            <p align="justify">
              A command line calculator.
            </p>
            <br/><br clear="all"/>

          </td>
          <td width="30%" valign="top">
            <h2>Experiments</h2>
            <a href="#"><h3>Namespaces</h3></a>
            <em>[PHP5.3]</em>
            <br/><br clear="all"/>

          </td>
        </tr></table>
        <br clear="all"/>

        <!-- Move the strawberries down a bit -->
        <div style="height: 240px">&#160;</div>
      </td>
    </tr></table>
  </xsl:template>

  <xsl:template name="html-title">
    XP Forge
  </xsl:template>
  
</xsl:stylesheet>
