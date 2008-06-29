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
  
  <xsl:template name="html-head">
    <link rel="shortcut icon" href="/common/favicon.ico" />
  </xsl:template>

  <xsl:template name="content">
    <div style="padding: 0; margin: 0; width: 100%; height: 200px; background: #1981c9 url(/image/header.png); color: white">
      <a href="http://xp-framework.net/downloads/releases/"><img align="right" src="/common/image/download.png" alt="Download" vspace="10" hspace="10" border="0"/></a>
      <p style="padding: 70px 170px 0 10px; margin: 0">
        <b>XP technology</b> is a rapid development environment. It will run anywhere PHP runs, 
        Windows, Linux, Unix, MacOS, on your desktop PC, on your enterprise application 
        cluster and on your hoster's webserver.
        <br/><br/>
        It's <b>BSD</b>-licensed, that means it's completely free to use, to copy, to extend, 
        to modify and even to redistribute.
      </p>
    </div>
    
    <table id="main" cellpadding="0" cellspacing="10"><tr>
      <td id="content" style="background: white url(/image/lemon.jpg) no-repeat bottom left">
        <table width="100%" class="columned"><tr>
          <td width="70%" valign="top" id="left">
            <h2>I would like to:</h2>
            <h3><a href="http://xp-forge.net/">Use software written in XP</a></h3>
            
            <!-- Ports -->
            <p align="justify">
              XP::Forge provides a growing number of ready-to-run applications and APIs such
              as Google Search, Flickr, Delicio.us, Simpy, Java Webstart and Microsoft Office
              automation written using XP technology.
              <em>If you want to use and/or contribute, <a href="http://xp-forge.net/">here</a>'s
              the place to do so!</em>
            </p>
            <br/><br clear="all"/>
            
            <!-- Framework -->
            <h3><a href="http://docs.xp-framework.net/">Use XP to create an application</a></h3>
            <p align="justify">
              See how the framework can help you complete common tasks, from database-driven 
              websites, command line utilities, unittests, standalone daemons, XML and image 
              processing, how it works in a heterogenous environment with its integration
              into the J2EE&#8482;-world via native application server support or web services 
              implementations, and how it can even create graphical interfaces.
              <em>Learn more about the XP Framework <a href="http://docs.xp-framework.net/">here</a>.</em>
            </p>
            <br/><br clear="all"/>

            <!-- Futurama -->
            <h3><a href="http://developer.xp-framework.net/">See what's next</a></h3>
            <p align="justify">
              XP Technology is constantly being extended and enhanced.
              <em>Be part of the evolution <a href="http://developer.xp-framework.net/">here</a>.</em>
            </p>
            <br/><br clear="all"/>
          </td>
          <td width="30%" valign="top">
            <h2>Technology blogs</h2>
            <xsl:for-each select="/formresult/blog/entry">
              <div title="{title}" style="width: 100%; height: 1.5em; margin-right: 24px; overflow: hidden">
                <a href="http://news.xp-framework.net/article/{@id}/{xp:dateformat(date, 'Y/m/d/')}/{@link}}">
                  <h3><xsl:value-of select="title"/></h3>
                </a>
              </div>
              <em>
                <xsl:text>[</xsl:text>
                <xsl:for-each select="category">
                  <xsl:value-of select="."/>
                  <xsl:if test="position() &lt; last()">, </xsl:if>
                </xsl:for-each> @
                <xsl:value-of select="xp:date(date)"/>
                <xsl:text>]</xsl:text>
              </em>
              <br/><br clear="all"/>
            </xsl:for-each>
          </td>
        </tr></table>
        <br clear="all"/>
                
        <!-- Move the pills down a bit -->
        <div style="height: 180px">&#160;</div>
      </td>
    </tr></table>
  
  </xsl:template>
  
  <xsl:template name="context">
  </xsl:template>
</xsl:stylesheet>
