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
    <table id="main" cellpadding="0" cellspacing="10"><tr>
      <td id="content" style="background: white url(/image/apple.jpg) no-repeat bottom left">
        <h1>Developer Zone</h1>
        <p>
          This site is dedicated to the development of the XP framework.
        </p>
        <br clear="all"/>

        <!-- Featured items -->
        <table width="100%" class="columned"><tr>
          <td width="70%" valign="top" id="left">
            <h2>For developers and contributors:</h2>
            
            <!-- SVN -->
            <h3><a href="{xp:link('static?source/checkout')}">Use the source</a></h3>
            <p align="justify">
              The framework's sourcecode is maintained in a subversion (SVN) repository.
              <em>Follow the <a href="{xp:link('static?source/checkout')}">checkout instructions</a> 
              to get the framework's sourecode.</em>
            </p>
            <br/><br clear="all"/>

            <!-- RFCs -->
            <h3><a href="{xp:link('rfc')}">Transparency</a></h3>
            <p align="justify">
              One of the major deficiencies in the development of many projects is that there 
              is no roadmap or strategy available other than in the developers' heads. The
              XP team publishes its decisions by documenting change requests in form of RFCs.
              <em>You can find the RFC overview <a href="{xp:link('rfc')}">here</a>.</em>
            </p>
            <br/><br clear="all"/>

            <!-- Releases
            <h3><a href="{xp:link('releases')}">Releases</a></h3>
            <p align="justify">
              ...
              <em>The <a href="{xp:link('releases')}">releases</a>.</em>
            </p>
            <br/><br clear="all"/>
             -->

            <!-- Unittests
            <h3><a href="{xp:link('unittests')}">Tested</a></h3>
            <p align="justify">
              ...
              <em>See the <a href="{xp:link('unittests')}">unittests</a> page!</em>
            </p>
            <br/><br clear="all"/>
            -->
             
            <!-- Bugs -->
            <h3><a href="http://bugs.xp-framework.net/">Bugs</a></h3>
            <p align="justify">
              Although we test our APIs thoroughly, we make mistakes: we are people, after all!
              <em>You can use our <a href="http://bugs.xp-framework.net/">bug tracker</a> to report bugs.</em>
            </p>
            <br/><br clear="all"/>

            <!-- Mailings lists -->
            <h3><a href="{xp:link('static?mailinglists')}">Mailing lists</a></h3>
            <p align="justify">
              Like many other development teams, the XP core team employs mailinglists 
              to discuss and announce changes and keep track of anything regarding our code.
              <em>To subscribe, see <a href="{xp:link('static?mailinglists')}">here</a>.</em>
            </p>
            <br/><br clear="all"/>

            <!-- Coding standards -->
            <h3><a href="{xp:link('static?cs')}">Coding standards</a></h3>
            <p align="justify">
              Code conventions are important to programmers for a number of reasons. 
              <em>Read about the <a href="{xp:link('static?cs')}">what the XP team sticks to</a>.</em>
            </p>
            <br/><br clear="all"/>
          </td>
          <td width="30%" valign="top">
            <h2>Utilities</h2>
            <a href="#"><h3>The XP / IDE integration project</h3></a>
            <em>[NEdit, Eclipse]</em>
            <br/><br clear="all"/>

            <a href="#"><h3>Compiler generator</h3></a>
            <em>[phpJay]</em>
            <br/><br clear="all"/>
            
            <h2>xp::forge</h2>
            <a href="#http://experiments.xp-forge.net/xml/arena"><h3>Experiments: Arena</h3></a>
            <em>[Core technologies]</em>
            <br/><br clear="all"/>

          </td>
        </tr></table>
        <br clear="all"/>

        <!-- Move the apple down a bit -->
        <div style="height: 240px">&#160;</div>
      </td>
    </tr></table>
  </xsl:template>

  <xsl:template name="html-title">
    XP Framework Developer Zone
  </xsl:template>
  
</xsl:stylesheet>
