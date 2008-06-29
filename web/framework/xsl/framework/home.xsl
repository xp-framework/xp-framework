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
    <div style="padding: 0; margin: 0; width: 100%; height: 200px; background: #015c2c url(/image/header.png); color: white">
      <p style="padding: 70px 10px 0 10px; margin: 0">
        The <acronym title="Extension framework for PHP">XP framework</acronym> offers 
        consistent, multi-purpose, object oriented, production-tested collection of 
        classes, APIs for app server connectivity, web services, dynamic web sites,
        date access and manipulation, logging, collections, I/O, databases, O/R
        mapping, XML, unittesting, and much more.
        <br/><br/>
      </p>
    </div>
    <table id="main" cellpadding="0" cellspacing="10">
      <tr>
        <td id="content">
        <table width="100%" class="columned"><tr>
          <td width="70%" valign="top" id="left">
            <h2>Featured:</h2>
            
            <!-- Framework documentation -->
            <h3><a href="http://docs.xp-framework.net/">Core concepts</a></h3>
            <p align="justify">
              The XP framework, while running on PHP, offers possibilities more commonly known from 
              Java or C#: Unified class loading, XAR archives for classes and resources, its own
              reflection API and a consistent type system, generics, annotations, type-safe 
              enumerations and more.
              <em>Learn more about the XP Framework <a href="http://docs.xp-framework.net/">here</a>.</em>
            </p>
            <br/><br clear="all"/>

            <!-- O/R mapper -->
            <h3><a href="http://docs.xp-framework.net/xml/doc?topics/objectpersistence">O/R-mapping API</a></h3>
            <p align="justify">
              The XP framework offers an API to object persistence via the DataSet and Peer classes. 
              These classes implement a variation of the "Row Data Gateway" pattern and the "Table Data Gateway".
              <em>Based on the framework's <a href="http://docs.xp-framework.net/xml/doc?topics/databases">rdbms API</a>,
              the O/R mapper makes it easy to access databases without thinking about SQL!</em>
            </p>
            <br/><br clear="all"/>

            <!-- Remote -->
            <h3><a href="#http://docs.xp-framework.net/xml/doc?topics/remote">Remoting</a></h3>
            <p align="justify">
              Crons written in Perl, websites using what is called <acronym title="Linux Apache MySQL PHP">LAMP</acronym>, 
              Java middleware based on JBoss, a Wiki in Python, GUIs written in Delphi or C#, Statistics using Ruby on Rails, 
              and so on. Sound familiar?
              While having one language to fit them all would surely be an ideal to follow, it is seldomly 
              realistic. <em>The XP framework's remoting API integrates application servers in PHP, C# and Java
              with clients in various languages, in a lightning-fast, transaction-safe and stable fashion.</em>
            </p>
            <br/><br clear="all"/>

            <!-- Unittests
            <h3><a href="#http://docs.xp-framework.net/xml/doc?topics/unittest">Unittesting</a></h3>
            <p align="justify">
              One never has enough unittests!
            </p>
            <br/><br clear="all"/>
            -->
          </td>
          <td width="30%" valign="top">
            <h2>See also</h2>
            <h3><a href="http://docs.xp-framework.net/xml/api">API documentation</a></h3>
            <em>[Generated]</em>
            <br/><br clear="all"/>

            <h3><a href="http://developer.xp-framework.net/xml/rfci">RFCs</a></h3>
            <em>[Request For Comments]</em>
            <br/><br clear="all"/>
            
            <h3><a href="http://docs.xp-framework.net/xml/doc?setup/framework">Installation</a></h3>
            <em>[Setting up the framework]</em>
            <br/><br clear="all"/>

            <h3><a href="http://developer.xp-framework.net/xml/static?cs">Coding standards</a></h3>
            <em>[SQL, PHP]</em>
            <br/><br clear="all"/>
          </td>
        </tr></table>
        <br clear="all"/>
        </td>
      </tr>
    </table>
  </xsl:template>

  <xsl:template name="html-title">
    XP Framework
  </xsl:template>
  
</xsl:stylesheet>
