<?xml version="1.0" encoding="iso-8859-1"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
  <xsl:output method="text" omit-xml-declaration="yes"/>
  
  <xsl:variable name="lcletters">abcdefghijklmnopqrstuvwxyz</xsl:variable>
  <xsl:variable name="ucletters">ABCDEFGHIJKLMNOPQRSTUVWXYZ</xsl:variable>
  
  <xsl:template name="prettyname">
    <xsl:param name="string"/>
	
	<xsl:value-of select="concat(
	  translate(substring($string, 1, 1), $lcletters, $ucletters),
	  translate(substring($string, 2), $ucletters, $lcletters)
	)"/>
  </xsl:template>	
  
  <xsl:template match="/">
    <xsl:text>&lt;?php</xsl:text>
      <xsl:apply-templates/>
	<xsl:text>?></xsl:text>
  </xsl:template>
  
  <xsl:template match="table">
    <xsl:variable name="classname">
	  <xsl:call-template name="prettyname">
		<xsl:with-param name="string" select="@name"/>
	  </xsl:call-template>
	</xsl:variable>
    <xsl:text>
/*
  Auto-generated class for </xsl:text><xsl:value-of select="@name"/><xsl:text>

  (</xsl:text><xsl:value-of select="concat(
     ../@created_at, 
	 ' by ', 
	 ../@created_by
   )"/><xsl:text>)
*/

$GLOBALS['g_project']->implements("Session::Saveable");

class </xsl:text><xsl:value-of select="$classname"/><xsl:text> extends Saveable {&#10;</xsl:text>
	
	<!-- Attributes -->
	<xsl:for-each select="attribute">
	  <xsl:value-of select="concat('    var $', @name)"/>
	  <xsl:choose>
	    <xsl:when test="@typename= 'int'">= 0</xsl:when>
		<xsl:when test="@typename= 'string'">= ''</xsl:when>
		<xsl:when test="@typename= 'float'">= 0.0</xsl:when>
		<xsl:when test="@typename= 'bool'">= false</xsl:when>
		<xsl:when test="@typename= 'util.Date'">= null</xsl:when>
      </xsl:choose>
	  <xsl:text>;&#10;</xsl:text>
	</xsl:for-each>
	<xsl:text>    
    var $Debug= 0;        // ob debug
    var $_projectkey;&#10;</xsl:text>
	
	<!-- Create constructor -->
	<xsl:text>
    function </xsl:text><xsl:value-of select="$classname"/><xsl:text> ($initdata) {
        global $g_project;
        $this->Debug=(isset($GLOBALS["g_</xsl:text><xsl:value-of select="$classname"/><xsl:text>_debug"]) or
                isset($GLOBALS["g_ALL_debug"]) or
                isset($initdata['debug']) or
                $this->Debug?1:0);
				
        // TBD

        Saveable::Saveable($initdata);
        $this->_logline_text('new </xsl:text><xsl:value-of select="$classname"/><xsl:text>',$initdata);
        return true;
    }
	</xsl:text>
	
	<!-- Create getters and setters -->
    <xsl:for-each select="attribute">
      <xsl:text>
    function get</xsl:text>
	  <xsl:call-template name="prettyname">
		<xsl:with-param name="string" select="@name"/>
	  </xsl:call-template>
      <xsl:text> () {
        return $this-></xsl:text><xsl:value-of select="@name"/><xsl:text>;
    }
      </xsl:text>
	  
      <xsl:text>
    function set</xsl:text>
	  <xsl:call-template name="prettyname">
		<xsl:with-param name="string" select="@name"/>
	  </xsl:call-template>
      <xsl:text> ($</xsl:text><xsl:value-of select="@name"/><xsl:text>) {
        $this-></xsl:text><xsl:value-of select="concat(@name, '= $', @name)"/><xsl:text>;
    }
      </xsl:text>
	</xsl:for-each>

	<!-- Create update() method -->
	<xsl:text>
    function update() {
        global $g_project;    	
        $query='UPDATE </xsl:text><xsl:value-of select="@name"/><xsl:text> 
                SET&#10;</xsl:text>
    <xsl:for-each select="attribute[@identity = 'false']">
	  <xsl:text>                      </xsl:text>
	  <xsl:value-of select="concat(@name, ' = ')"/>
	  <xsl:choose>
		<xsl:when test="@typename= 'int' or @typename= 'float' or @typename= 'bool'">'.$this-><xsl:value-of select="@name"/>.'</xsl:when>
		<xsl:otherwise>"'.$this-><xsl:value-of select="@name"/>.'"</xsl:otherwise>
      </xsl:choose>
	  <xsl:if test="position() != last()">,&#10;</xsl:if>
	</xsl:for-each>

    <xsl:text>
                WHERE </xsl:text>
    <xsl:for-each select="attribute[@identity= 'true']">
	  <xsl:value-of select="concat(@name, ' = ')"/>
	  <xsl:choose>
		<xsl:when test="@typename= 'int' or @typename= 'float' or @typename= 'bool'">'.$this-><xsl:value-of select="@name"/><xsl:if test="position() != last()">.'</xsl:if></xsl:when>
		<xsl:otherwise>"'.$this-><xsl:value-of select="@name"/>.'"<xsl:if test="position() = last()">'</xsl:if></xsl:otherwise>
      </xsl:choose>
	  <xsl:if test="position() != last()"> and </xsl:if>
	</xsl:for-each>
	<xsl:text>;
	
	// TBD: Insert database member name here!
	return $g_project->***->update($query);
    }
	</xsl:text>
	
    <!-- Create method to get this from database by PK,U -->
	<xsl:text>
    function getFromDB() {
        global $g_project;
        $query='SELECT </xsl:text>
    <xsl:for-each select="attribute">
	  <xsl:value-of select="@name"/>
	  <xsl:if test="position() != last()">,&#10;                       </xsl:if>
	</xsl:for-each>
    <xsl:text>
                 FROM  </xsl:text><xsl:value-of select="@name"/><xsl:text>
                 WHERE </xsl:text><xsl:value-of select="index[@primary= 'true' and @unique= 'true']/key/text()"/>
	<xsl:text> = '.$this-></xsl:text><xsl:value-of select="index[@primary= 'true' and @unique= 'true']/key/text()"/><xsl:text>;
	
        // TBD: Insert database member name here!
        $result=$g_project->***->select($query);

        if (!$result) 
           return false;
	&#10;</xsl:text>
	<xsl:for-each select="attribute">
	  <xsl:value-of select="concat('        $this->', @name, '=$result[0][')"/>
	  <xsl:text>'</xsl:text><xsl:value-of select="@name"/><xsl:text>'];&#10;</xsl:text>
	</xsl:for-each>
	<xsl:text>
        return true;
    }
	</xsl:text>
	
    <!-- Create method to write this to database -->
	<xsl:text>
    function newToDB() {
        global $g_project;
        $query='INSERT INTO </xsl:text><xsl:value-of select="@name"/><xsl:text>
                    (</xsl:text>
    <xsl:for-each select="attribute">
	  <xsl:value-of select="@name"/>
	  <xsl:if test="position() != last()">,</xsl:if>
	</xsl:for-each>
    <xsl:text>)
                VALUES
              (</xsl:text>
    <xsl:for-each select="attribute[@identity = 'false']">
	  <xsl:choose>
		<xsl:when test="@typename= 'int' or @typename= 'float' or @typename= 'bool'">'.$this-><xsl:value-of select="@name"/>.'</xsl:when>
		<xsl:otherwise>"'.$this-><xsl:value-of select="@name"/>.'"</xsl:otherwise>
      </xsl:choose>
	  <xsl:if test="position() != last()">,&#10;               </xsl:if>
	</xsl:for-each>			  
    <xsl:text>)';

        // TBD: Insert database member name here!
        $this-></xsl:text><xsl:value-of select="index[@primary= 'true' and @unique= 'true']/key/text()"/><xsl:text>=$g_project->***->insert($query);

        $this->_logline_text('</xsl:text><xsl:value-of select="$classname"/><xsl:text>::newToDB',$query);
        $this->_logline_text('</xsl:text><xsl:value-of select="$classname"/><xsl:text>::newToDB returning',$this-></xsl:text>
		<xsl:value-of select="index[@primary= 'true' and @unique= 'true']/key/text()"/>
		<xsl:text>);

        return $this-></xsl:text><xsl:value-of select="index[@primary= 'true' and @unique= 'true']/key/text()"/><xsl:text>;
    }
	</xsl:text>

	<xsl:text>
    // Jede Klasse muss diese Funktion haben, mit der sie sich selbst zerstoeren kann
    function Destroy () {
        Saveable::Destroy();
        //unset($this); 
    }
	</xsl:text>
	
    <!-- Closing curly brace -->	
    <xsl:text>&#10;}&#10;</xsl:text>
  </xsl:template>
  
</xsl:stylesheet>
