<?xml version="1.0" encoding="iso-8859-1"?>
<!--
 ! Stylesheet for resources page
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
  <xsl:include href="../layout.xsl"/>
  
  <!--
   ! Template for context navigation
   !
   ! @see      ../../layout.xsl
   ! @purpose  Context navigation
   !-->
  <xsl:template name="context">

    <!-- see also -->
    <h4 class="context">See also</h4>
    <ul class="context">
      <li>
        <em>cvsweb</em>:<br/>
        <a href="http://cvs.xp-framework.net/" target="_cvs">Browse CVS repository<img hspace="2" src="/image/arrow.gif" width="11" height="11" border="0"/></a>
      </li>
    </ul>

    <!-- rfcs -->
    <h4 class="context">Current RFCs</h4>
    <ul class="context">
      <li>
        <em>0007 (draft)</em>:<br/>
        <a href="#rfcs/0007">Hotswap technology</a>
      </li>
      <li>
        <em>0006 (draft)</em>:<br/>
        <a href="#rfcs/0006">Serialization functionality</a>
      </li>
      <li>
        <em>0005 (draft)</em>:<br/>
        <a href="#rfcs/0006">Ability to define classes</a>
      </li>
      <li>
        <em>0004 (draft)</em>:<br/>
        <a href="#rfcs/0005">Unify/extend class loading API</a>
      </li>
    </ul>

  </xsl:template>

  <!--
   ! Template for content
   !
   ! @see      ../../layout.xsl
   ! @purpose  Define main content
   !-->
  <xsl:template name="content">
    <h1>development</h1>

    <table width="100%" border="0" cellspacing="0" cellpadding="2" class="intro">
      <tr>
        <td width="1%">
          <img src="/image/tip.gif" width="69" height="52"/>
        </td>
        <td>
          <ul class="intro">
            <li>Increasing <a href="#tips/performance">performace</a> in common situations</li>
            <li>Basic <a href="#tips/performance">type security</a> in a dynamically typed language</li>
            <li>Refactoring code to use <a href="#tips/performance">native methods</a> whenever possible</li>
          </ul>
        </td>
      </tr>
    </table>

    <h3>
      Coding standards
    </h3>
    <p>
      The 12th paradigm of Extreme Programming:<br/>
      <quote>
        Coding Standards: Everyone codes to the same standards. 
        Ideally, you shouldn't be able to tell by looking at it who 
        on the team has touched a specific piece of code.
      </quote>
      <br/>
      Check out <a href="#coding">the framework's coding standards</a>.
    </p>

    <h3>
      Documentation
    </h3>
    <p>
      Documentation is an essential part of the XP framework. Well documented
      code is more time consuming to write in the first place, but will save
      others a lot of time!<br/>
      See the <a href="#documentation">documentation standards and howto</a>
      for more information.
    </p>

    <h3>
      Release maintainance
    </h3>
    <p>
      What can CVS do to help with stability and release maintenance? Well, 
      we've got some answers <a href="#cvs">here</a>.
    </p>

    <h3>
      Participating in development
    </h3>
    <p>
      Want to join the xp team? Well, there are some <a href="#becomedeveloper">prerequisites...</a>
    </p>
  </xsl:template>
  
</xsl:stylesheet>
