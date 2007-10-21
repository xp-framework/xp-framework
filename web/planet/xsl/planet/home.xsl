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

  <xsl:include href="layout.inc.xsl"/>
  
  <xsl:template name="html-head">
    <link rel="shortcut icon" href="/common/favicon.ico" />
  </xsl:template>
  
  <xsl:template name="content">
    <div style="padding: 0; margin: 0; width: 100%; height: 200px; background: #1981c9 url(/common/image/header.png); color: white">
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
          <td width="75%" valign="top" id="left">
            <h2>I would like to:</h2>
            <h3><a href="#">Use software written in XP</a></h3>
            
            <!-- Ports -->
            <p align="justify">
              XP::Forge provides a growing number of ready-to-run applications and APIs such
              as Google Search, Flickr, Delicio.us, Simpy, Java Webstart and Microsoft Office
              automation written using XP technology.
              <em>If you want to use and/or contribute, <a href="#">here</a>'s
              the place to do so!</em>
            </p>
            <br/><br clear="all"/>
            
            <!-- Framework -->
            <h3><a href="#">Use XP to create an application</a></h3>
            <p align="justify">
              See how the framework can help you complete common tasks, from database-driven 
              websites, command line utilities, unittests, standalone daemons, XML and image 
              processing, how it works in a heterogenous environment with its integration
              into the J2EE&#8482;-world via native application server support or web services 
              implementations, and how it can even create graphical interfaces.
              <em>Learn more about the XP Framework <a href="#">here</a>.</em>
            </p>
            <br/><br clear="all"/>

            <!-- Futurama -->
            <h3><a href="#">See what's next</a></h3>
            <p align="justify">
              XP Technology is constantly being extended and enhanced.
              <em>Be part of the evolution <a href="#">here</a>.</em>
            </p>
            <br/><br clear="all"/>
          </td>
          <td width="25%" valign="top">
            <h2>Showcases</h2>
            <h3><a href="#">Web 2.0 Showcase</a></h3>
            <em>[Prototype.js, JSON, and XP]</em>
            <br/><br clear="all"/>
            
            <br/>
            <h2>Technology blogs</h2>
            <h3><a href="#">Dialog: IPTC support</a></h3>
            <em>[Ports News, 2006-07-01]</em>
            <br/><br clear="all"/>

            <h3><a href="#">newinstance</a></h3>
            <em>[Core News, 2006-06-14]</em>
            <br/><br clear="all"/>

            <h3><a href="#">Collection framework</a></h3>
            <em>[Framework News, 2006-06-12]</em>
            <br/><br clear="all"/>

            <h3><a href="#" title="So You Wanna Write A Unittest?">So You Wanna Write...</a></h3>
            <em>[Unittesting, 2006-05-20]</em>
            <br/><br clear="all"/>
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