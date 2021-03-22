<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet output="xml" version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns="http://www.w3.org/1999/xhtml">
    <xsl:import href="layout.xsl"/>
    <xsl:param name="css">
        <link href="css/install.css" rel="stylesheet" type="text/css"></link>
    </xsl:param>
    <xsl:template match="install" mode="body">
        <header>
            <img src="images/lazycat.png" alt="logo"/><h1>Lazy Framework installation</h1>
        </header>
        <article>
            <p>Welcome to Lazy Framework (LF) installation. Please setup your application configuration in `/cfg/Application.xml` before.</p>
            <p>You've installed site demo.</p>
            <a href="{@redirect}" class="button">Go to demo</a>
        </article>
    </xsl:template>
</xsl:stylesheet>
