<?php
/**
 * Static functions called by various outside hooks, as well as by
 * extension.json.
 *
 * @author Yaron Koren
 * @file
 * @ingroup TinyMCE
 */

class TinyMCEHooks {

	public static function registerExtension() {
		if ( defined( 'TINYMCE_VERSION' ) ) {
			// Do not load this extension more than once.
			return 1;
		}

		define( 'TINYMCE_VERSION', '0.1' );

		$GLOBALS['wgTinyMCEIP'] = dirname( __DIR__ ) . '/../';
	}

	static function setGlobalJSVariables( &$vars ) {
		/**
		 * Compiles a list of tags that must be passed by the editor.
		 * @global Language $wgLang
		 * @global OutputPage $wgOut
		 * @param Parser $oParser MediaWiki parser object.
		 * @return bool Allow other hooked methods to be executed. Always true.
		 */

		global $wgParser;

		$extensionTags = $wgParser->getTags(); 
		$specialTags = '';
		foreach ( $extensionTags as $tagName ) {
			if ( ( $tagName == 'pre' ) || ($tagName == 'nowiki') ) {
				continue;
			}
			$specialTags .= $tagName . '|';
		}

		$defaultTags = array(
			"includeonly", "onlyinclude", "noinclude" //Definitively MediaWiki core
		);

		$tinyMCETagList = $specialTags . implode( '|', $defaultTags );

		$vars['wgTinyMCETagList'] = $tinyMCETagList;

		$context = new RequestContext();
		$vars['wgTinyMCELanguage'] = $context->getLanguage()->getCode();

		return true;
	}

	/**
	 * Is there a less hacky way to do this, like stopping the toolbar
	 * creation before it starts?
	 */
	public static function removeDefaultToolbar( &$toolbar ) {
		$toolbar = null;
		return true;
	}

	public static function addToEditPage( EditPage &$editPage, OutputPage &$output ) {
		$output->addModules( 'ext.tinymce' );
		return true;
	}

}