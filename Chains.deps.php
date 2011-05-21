<?php
// This file exists to ensure that base classes are preloaded before
// MonoBook.php is compiled, working around a bug in the APC opcode
// cache on PHP 5, where cached code can break if the include order
// changed on a subsequent page view.
// see http://lists.wikimedia.org/pipermail/wikitech-l/2006-January/021311.html

if ( ! defined( 'MEDIAWIKI' ) )
	die( 1 );

chdir(dirname($_SERVER["SCRIPT_FILENAME"]).'/skins/');
require_once( '../includes/SkinTemplate.php');
