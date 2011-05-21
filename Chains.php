<?php
/**
 * Chains - Branch of MonoBook which has many usability improvements and
 * somewhat cleaner code.
 *
 * @todo document
 * @file
 * @ingroup Skins
 */

if( !defined( 'MEDIAWIKI' ) ) {
	die( -1 );
}

include_once('Vector.php');

/**
 * SkinTemplate class for Chains skin
 * @ingroup Skins
 */
class SkinChains extends SkinVector {

	/* Functions */
	var $skinname = 'chains', $stylename = 'chains',
		$template = 'ChainsTemplate', $useHeadElement = true;

	/**
	 * Initializes output page and sets up skin-specific parameters
	 * @param $out OutputPage object to initialize
	 */
	public function initPage( OutputPage $out ) {
		global $wgLocalStylePath, $wgRequest;

		parent::initPage( $out );
		
	}

	/**
	 * Load skin and user CSS files in the correct order
	 * fixes bug 22916
	 * @param $out OutputPage object
	 */
	function setupSkinUserCss( OutputPage $out ){
		SkinTemplate::setupSkinUserCss( $out );
		#$out->addModuleStyles( 'skins.vector' );
		$out->addStyle($this->skinname.'/960.reset.css', 'screen' );
		$out->addStyle($this->skinname.'/960.grid.css', 'screen' );
		$out->addStyle($this->skinname.'/960.layout.css', 'screen' );
		$out->addStyle($this->skinname.'/960.text.css', 'screen' );
		$out->addStyle($this->skinname.'/screen.content.css', 'screen' );
		$out->addStyle($this->skinname.'/960.nav.css', 'screen' );
		$out->addStyle($this->skinname.'/screen.layout.css', 'screen' );
		$out->addStyle($this->skinname.'/Miso-fontfacekit/stylesheet.css', 'screen' );
		$out->addStyle($this->skinname.'/mobile.css', '(max-width:700px)' );
		$out->addStyle($this->skinname.'/large.css', '(min-width:1100px)' );
	}

}

/**
 * QuickTemplate class for Chains skin
 * @ingroup Skins
 */
class ChainsTemplate extends QuickTemplate {

	/* Members */

	/**
	 * @var Cached skin object
	 */
	var $skin;

	/* Functions */

	/**
	 * Outputs the entire contents of the XHTML page
	 */
	public function execute() {
		global $wgRequest, $wgLang;

		$this->skin = $this->data['skin'];
		$action = $wgRequest->getText( 'action' );

		// Build additional attributes for navigation urls
		$nav = $this->skin->buildNavigationUrls();
		foreach ( $nav as $section => $links ) {
			foreach ( $links as $key => $link ) {
				$xmlID = $key;
				if ( isset( $link['context'] ) && $link['context'] == 'subject' ) {
					$xmlID = 'ca-nstab-' . $xmlID;
				} else if ( isset( $link['context'] ) && $link['context'] == 'talk' ) {
					$xmlID = 'ca-talk';
				} else {
					$xmlID = 'ca-' . $xmlID;
				}
				$nav[$section][$key]['attributes'] =
					' id="' . Sanitizer::escapeId( $xmlID ) . '"';
				if ( $nav[$section][$key]['class'] ) {
					$nav[$section][$key]['attributes'] .=
						' class="' . htmlspecialchars( $link['class'] ) . '"';
					unset( $nav[$section][$key]['class'] );
				}
				// We don't want to give the watch tab an accesskey if the page
				// is being edited, because that conflicts with the accesskey on
				// the watch checkbox.  We also don't want to give the edit tab
				// an accesskey, because that's fairly superfluous and conflicts
				// with an accesskey (Ctrl-E) often used for editing in Safari.
				if (
					in_array( $action, array( 'edit', 'submit' ) ) &&
					in_array( $key, array( 'edit', 'watch', 'unwatch' ) )
				) {
					$nav[$section][$key]['key'] =
						$this->skin->tooltip( $xmlID );
				} else {
					$nav[$section][$key]['key'] =
						$this->skin->tooltipAndAccesskey( $xmlID );
				}
			}
		}
		$this->data['namespace_urls'] = $nav['namespaces'];
		$this->data['view_urls'] = $nav['views'];
		$this->data['action_urls'] = $nav['actions'];
		$this->data['variant_urls'] = $nav['variants'];
		// Build additional attributes for personal_urls
		foreach ( $this->data['personal_urls'] as $key => $item) {
			$this->data['personal_urls'][$key]['attributes'] =
				' id="' . Sanitizer::escapeId( "pt-$key" ) . '"';
			if ( isset( $item['active'] ) && $item['active'] ) {
				$this->data['personal_urls'][$key]['attributes'] .=
					' class="active"';
			}
			$this->data['personal_urls'][$key]['key'] =
				$this->skin->tooltipAndAccesskey('pt-'.$key);
		}

		// Generate additional footer links
		$footerlinks = $this->data["footerlinks"];
		
		// Reduce footer links down to only those which are being used
		$validFooterLinks = array();
		foreach( $footerlinks as $category => $links ) {
			$validFooterLinks[$category] = array();
			foreach( $links as $link ) {
				if( isset( $this->data[$link] ) && $this->data[$link] ) {
					$validFooterLinks[$category][] = $link;
				}
			}
		}
		
		// Generate additional footer icons
		$footericons = $this->data["footericons"];
		// Unset any icons which don't have an image
		foreach ( $footericons as $footerIconsKey => &$footerIconsBlock ) {
			foreach ( $footerIconsBlock as $footerIconKey => $footerIcon ) {
				if ( !is_string($footerIcon) && !isset($footerIcon["src"]) ) {
					unset($footerIconsBlock[$footerIconKey]);
				}
			}
		}
		// Redo removal of any empty blocks
		foreach ( $footericons as $footerIconsKey => &$footerIconsBlock ) {
			if ( count($footerIconsBlock) <= 0 ) {
				unset($footericons[$footerIconsKey]);
			}
		}
		
		// Reverse horizontally rendered navigation elements
		if ( $wgLang->isRTL() ) {
			$this->data['view_urls'] =
				array_reverse( $this->data['view_urls'] );
			$this->data['namespace_urls'] =
				array_reverse( $this->data['namespace_urls'] );
			$this->data['personal_urls'] =
				array_reverse( $this->data['personal_urls'] );
		}
		// Output HTML Page
		$this->html( 'headelement' );
?>
	<div id="mw-page-base" class="noprint"></div>

	<!-- content -->
	<div id="content"<?php $this->html('specialpageattributes') ?> class="">
		<div class="container_16 marginmodifier">
				
			<a id="top"></a>
			<div id="mw-js-message" class="full_16" style="display:none;"<?php $this->html('userlangattributes') ?>></div>
			<?php if ( $this->data['sitenotice'] ): ?>
			<!-- sitenotice -->
			<div id="siteNotice" class="full_16"><?php $this->html( 'sitenotice' ) ?></div>
			<!-- /sitenotice -->
			<?php endif; ?>
			<!-- firstHeading -->
			
				<div id="firstHeading" class="firstHeading full_16"><h1>
					<span id="title">
						<!--<a href="<?$this->data['namespace_urls']?>">--><?=$this->html( 'title' );?><!--</a>-->
					</span>
					<span class="rhs">
						<ul class="nav main">
							<?php
							$this->renderNavigation( array( 'VIEWS' ) );
							$this->renderNavigation( array( 'ACTIONS') ); 
							?>
							<li><span>
							<? $this->renderNavigation( array( 'SEARCH' ) ); ?>
							</span></li>
						</ul>
					</span>
				</h1></div>
			<!-- /firstHeading -->
			<div class="clear"></div>

			<!-- bodyContent -->
			<div id="bodyContent">
				<!-- tagline -->
				<div id="siteSub" class="full_16"><?php $this->msg( 'tagline' ) ?></div>
				<!-- /tagline -->
				<!-- subtitle -->
				<!--<div id="contentSub"<?php $this->html('userlangattributes') ?> class="full_16"><?php $this->html( 'subtitle' ) ?></div>-->
				<!-- /subtitle -->
				<?php if ( $this->data['undelete'] ): ?>
				<!-- undelete -->
				<div id="contentSub2" class="full_16"><?php $this->html( 'undelete' ) ?></div>
				<!-- /undelete -->
				<?php endif; ?>
				<?php if($this->data['newtalk'] ): ?>
				<!-- newtalk -->
				<div class="usermessage" class="full_16"><?php $this->html( 'newtalk' )  ?></div>
				<!-- /newtalk -->
				<?php endif; ?>
				<?php if ( $this->data['showjumplinks'] ): ?>
				<!-- jumpto -->
				<div id="jump-to-nav" class="full_16">
					<?php $this->msg( 'jumpto' ) ?> <a href="#mw-head"><?php $this->msg( 'jumptonavigation' ) ?></a>,
					<a href="#p-search"><?php $this->msg( 'jumptosearch' ) ?></a>
				</div>
				<!-- /jumpto -->
				<?php endif; ?>
				<!-- bodytext -->
				<div class="full_16">
					<?php $this->html( 'bodytext' ) ?>
					<!-- /bodytext -->
					<?php if ( $this->data['catlinks'] ): ?>
					<!-- catlinks -->
					<?php $this->html( 'catlinks' ); ?>
					<!-- /catlinks -->
					<?php endif; ?>
					<?php if ( $this->data['dataAfterContent'] ): ?>
					<!-- dataAfterContent -->
					<?php $this->html( 'dataAfterContent' ); ?>
					<!-- /dataAfterContent -->
					<?php endif; ?>
				</div>
				<div class="visualClear"></div>
			</div>
			<!-- /bodyContent -->

	</div>
</div>
<!-- /content -->


<!--					-->


<!-- header -->
<div class="position-top">
	<div class="container_16 chains_container">
			
		<div class="full_8" id="chains-head-logo">
			<!-- panel -->
			<div id="mw-panel" class="noprint"  style="top: 0; left: 0;">
				<!-- logo -->
					<div id="p-logo"><a style="" href="<?=htmlspecialchars( $this->data['nav_urls']['mainpage']['href'] ) ?>" <?=$this->skin->tooltipAndAccesskey( 'p-logo' ) ?>>Wikichains</a></div>
				<!-- /logo -->
			</div>
			<!-- /panel -->
		</div>

		<div class="full_8" id="chains-head-nav">

			<ul class="nav rhs">
					<?php $this->renderNavigation( array( 'PERSONAL' ) ); ?>
			</ul>

			<ul class="nav rhs">
			<?
			global $wgArticlePath;
			ob_start();
			$this->msg('topnav', false);
			$topnav = ob_get_clean();
			foreach (explode("\n",trim($topnav)) as $navitem):
				if(empty($navitem)) continue;
				list($article,$key,$image) = explode("|",trim($navitem));
				?>
					<li><a href="<?=str_replace( "$1", urlencode( $article ), $wgArticlePath );?>" <?=$this->skin->tooltipAndAccesskey( $key )?> class="icononly sprite-<?=$image?>"><?=$article?></a></li>
			<?
			endforeach;
			?>
			</ul>

		</div>
		<div class="full_16" id="chains-head-shade"></div>
	</div>
</div>
<!-- /header -->





<!-- footer -->
<div id="footer"<?php $this->html('userlangattributes') ?> class="container_16 chains_container">

	<div class="grid_4">
	<?
	$this->renderPortals( $this->data['sidebar'] ); 
	?>
	</div>

	<div class="grid_4">
	<?
	$portals['TOOLBOX'] = True;
	$portals['LANGUAGES'] = True;
	$this->renderPortals( $portals ); 
	?>
	</div>

	<div class="grid_8">
	<?

	foreach( $validFooterLinks as $category => $links ):
		if ( count( $links ) > 0 ):
		?>
		<ul id="footer-<?= $category ?>">
		<?
		foreach( $links as $link ):
			if( isset( $this->data[$link] ) && $this->data[$link] ):
				?>
				<li id="footer-<?= $category ?>-<?= $link ?>"><? $this->html( $link ) ?></li>
				<?
				endif;
			endforeach;
			?>
			</ul>
			<?
		endif;
	endforeach; 

	
	if ( count( $footericons ) > 0 ):
		?>
		<ul id="footer-icons" class="noprint">
		<?
		foreach ( $footericons as $blockName => $footerIcons ): 
			?>
			<li id="footer-<?= htmlspecialchars($blockName); ?>ico">
			<?
			foreach ( $footerIcons as $icon ):
				echo $this->skin->makeFooterIcon( $icon );
			endforeach;
			?>
			</li>
			<?
		endforeach;
		?>
		</ul>
		<?
	endif; 

	?>
	</div>
</div>


<!-- /footer -->
		<?php $this->html( 'bottomscripts' ); /* JS call to runBodyOnloadHook */ ?>
		<!-- fixalpha -->
		<script type="<?php $this->text('jsmimetype') ?>"> if ( window.isMSIE55 ) fixalpha(); </script>
		<!-- /fixalpha -->
		<?php $this->html( 'reporttime' ) ?>
		<?php if ( $this->data['debug'] ): ?>
		<!-- Debug output: <?php $this->text( 'debug' ); ?> -->
		<?php endif; ?>
	</body>
</html>
<?php
	}







	// CHAINS: NO CHANGES HERE, JUST OVERRIDING OVERRIDES
	// (impossible to not print portals?!)
	//

	/**
	 * Render a series of portals
	 */
	private function renderPortals( $portals ) {
		// Force the rendering of the following portals
		# don't be silly!
		#if ( !isset( $portals['SEARCH'] ) ) $portals['SEARCH'] = true;
		#if ( !isset( $portals['TOOLBOX'] ) ) $portals['TOOLBOX'] = true;
		#if ( !isset( $portals['LANGUAGES'] ) ) $portals['LANGUAGES'] = true;
		// Render portals
		foreach ( $portals as $name => $content ) {
			echo "\n<!-- {$name} -->\n";
			switch( $name ) {
				case 'SEARCH':
					break;
				case 'TOOLBOX':
?>
<div class="portal" id="p-tb">
	<h5<?php $this->html('userlangattributes') ?>><?php $this->msg( 'toolbox' ) ?></h5>
	<div class="body">
		<ul>
		<?php if( $this->data['notspecialpage'] ): ?>
			<li id="t-whatlinkshere"><a href="<?php echo htmlspecialchars( $this->data['nav_urls']['whatlinkshere']['href'] ) ?>"<?php echo $this->skin->tooltipAndAccesskey( 't-whatlinkshere' ) ?>><?php $this->msg( 'whatlinkshere' ) ?></a></li>
			<?php if( $this->data['nav_urls']['recentchangeslinked'] ): ?>
			<li id="t-recentchangeslinked"><a href="<?php echo htmlspecialchars( $this->data['nav_urls']['recentchangeslinked']['href'] ) ?>"<?php echo $this->skin->tooltipAndAccesskey( 't-recentchangeslinked' ) ?>><?php $this->msg( 'recentchangeslinked-toolbox' ) ?></a></li>
			<?php endif; ?>
		<?php endif; ?>
		<?php if( isset( $this->data['nav_urls']['trackbacklink'] ) ): ?>
		<li id="t-trackbacklink"><a href="<?php echo htmlspecialchars( $this->data['nav_urls']['trackbacklink']['href'] ) ?>"<?php echo $this->skin->tooltipAndAccesskey( 't-trackbacklink' ) ?>><?php $this->msg( 'trackbacklink' ) ?></a></li>
		<?php endif; ?>
		<?php if( $this->data['feeds']): ?>
		<li id="feedlinks">
			<?php foreach( $this->data['feeds'] as $key => $feed ): ?>
			<a id="<?php echo Sanitizer::escapeId( "feed-$key" ) ?>" href="<?php echo htmlspecialchars( $feed['href'] ) ?>" rel="alternate" type="application/<?php echo $key ?>+xml" class="feedlink"<?php echo $this->skin->tooltipAndAccesskey( 'feed-' . $key ) ?>><?php echo htmlspecialchars( $feed['text'] ) ?></a>
			<?php endforeach; ?>
		</li>
		<?php endif; ?>
		<?php foreach( array( 'contributions', 'log', 'blockip', 'emailuser', 'upload', 'specialpages' ) as $special ): ?>
			<?php if( $this->data['nav_urls'][$special]): ?>
			<li id="t-<?php echo $special ?>"><a href="<?php echo htmlspecialchars( $this->data['nav_urls'][$special]['href'] ) ?>"<?php echo $this->skin->tooltipAndAccesskey( 't-' . $special ) ?>><?php $this->msg( $special ) ?></a></li>
			<?php endif; ?>
		<?php endforeach; ?>
		<?php if( !empty( $this->data['nav_urls']['print']['href'] ) ): ?>
		<li id="t-print"><a href="<?php echo htmlspecialchars( $this->data['nav_urls']['print']['href'] ) ?>" rel="alternate"<?php echo $this->skin->tooltipAndAccesskey( 't-print' ) ?>><?php $this->msg( 'printableversion' ) ?></a></li>
		<?php endif; ?>
		<?php if (  !empty(  $this->data['nav_urls']['permalink']['href'] ) ): ?>
		<li id="t-permalink"><a href="<?php echo htmlspecialchars( $this->data['nav_urls']['permalink']['href'] ) ?>"<?php echo $this->skin->tooltipAndAccesskey( 't-permalink' ) ?>><?php $this->msg( 'permalink' ) ?></a></li>
		<?php elseif ( $this->data['nav_urls']['permalink']['href'] === '' ): ?>
		<li id="t-ispermalink"<?php echo $this->skin->tooltip( 't-ispermalink' ) ?>><?php $this->msg( 'permalink' ) ?></li>
		<?php endif; ?>
		<?php wfRunHooks( 'SkinTemplateToolboxEnd', array( &$this ) ); ?>
		</ul>
	</div>
</div>
<?php
					break;
				case 'LANGUAGES':
					if ( $this->data['language_urls'] ) {
?>
<div class="portal" id="p-lang">
	<h5<?php $this->html('userlangattributes') ?>><?php $this->msg( 'otherlanguages' ) ?></h5>
	<div class="body">
		<ul>
		<?php foreach ( $this->data['language_urls'] as $langlink ): ?>
			<li class="<?php echo htmlspecialchars(  $langlink['class'] ) ?>"><a href="<?php echo htmlspecialchars( $langlink['href'] ) ?>" title="<?php echo htmlspecialchars( $langlink['title'] ) ?>"><?php echo $langlink['text'] ?></a></li>
		<?php endforeach; ?>
		</ul>
	</div>
</div>
<?php
					}
					break;
				default:
?>
<div class="portal" id='<?php echo Sanitizer::escapeId( "p-$name" ) ?>'<?php echo $this->skin->tooltip( 'p-' . $name ) ?>>
	<h5<?php $this->html('userlangattributes') ?>><?php $out = wfMsg( $name ); if ( wfEmptyMsg( $name, $out ) ) echo htmlspecialchars( $name ); else echo htmlspecialchars( $out ); ?></h5>
	<div class="body">
		<?php if ( is_array( $content ) ): ?>
		<ul>
		<?php foreach( $content as $val ): ?>
			<li id="<?php echo Sanitizer::escapeId( $val['id'] ) ?>"<?php if ( $val['active'] ): ?> class="active" <?php endif; ?>><a href="<?php echo htmlspecialchars( $val['href'] ) ?>"<?php echo $this->skin->tooltipAndAccesskey( $val['id'] ) ?>><?php echo htmlspecialchars( $val['text'] ) ?></a></li>
		<?php endforeach; ?>
		</ul>
		<?php else: ?>
		<?php echo $content; /* Allow raw HTML block to be defined by extensions */ ?>
		<?php endif; ?>
	</div>
</div>
<?php
				break;
			}
			echo "\n<!-- /{$name} -->\n";
		}
	}

	/**
	 * Render one or more navigations elements by name, automatically reveresed
	 * when UI is in RTL mode
	 */
	private function renderNavigation( $elements ) {
		global $wgVectorUseSimpleSearch, $wgVectorShowVariantName, $wgUser;

		// If only one element was given, wrap it in an array, allowing more
		// flexible arguments
		if ( !is_array( $elements ) ) {
			$elements = array( $elements );
		// If there's a series of elements, reverse them when in RTL mode
		} else if ( wfUILang()->isRTL() ) {
			$elements = array_reverse( $elements );
		}
		// Render elements
		foreach ( $elements as $name => $element ) {
			echo "\n<!-- {$name} -->\n";
			$casedata = '';
			switch ( $element ) {
				case 'NAMESPACES':
//<li id="p-namespaces" class="chainsTabs<?php if ( count( $this->data['namespace_urls'] ) == 0 ) echo ' emptyPortlet'; ?/>">
//	<a href="#"><?php $this->msg('namespaces') ?/></a>
//	<ul<?php $this->html('userlangattributes') ?/>>
?>
		<?php foreach ($this->data['namespace_urls'] as $link ): ?>
			<li <?php echo $link['attributes'] ?>><span><a href="<?php echo htmlspecialchars( $link['href'] ) ?>" <?php echo $link['key'] ?>><?php echo htmlspecialchars( $link['text'] ) ?></a></span></li>
		<?php endforeach; ?>
<?php
//	</ul>
//</li>
				break;
				case 'VARIANTS':
					if ( count( $this->data['variant_urls'] ) == 0 ) continue;
?>
<li id="p-variants" class="chainsMenu<?php if ( count( $this->data['variant_urls'] ) == 0 ) echo ' emptyPortlet'; ?>">
	<?php if ( $wgVectorShowVariantName ): ?>
		<a href="#">
		<?php foreach ( $this->data['variant_urls'] as $link ): ?>
			<?php if ( stripos( $link['attributes'], 'selected' ) !== false ): ?>
				<?php echo htmlspecialchars( $link['text'] ) ?>
			<?php endif; ?>
		<?php endforeach; ?>
		</a>
	<?php endif; ?>
</li>

<li><a href="#"><?php $this->msg('variants') ?></a>
	<ul<?php $this->html('userlangattributes') ?>>
		<?php foreach ( $this->data['variant_urls'] as $link ): ?>
			<li<?php echo $link['attributes'] ?>><a href="<?php echo htmlspecialchars( $link['href'] ) ?>" <?php echo $link['key'] ?>><?php echo htmlspecialchars( $link['text'] ) ?></a></li>
		<?php endforeach; ?>
	</ul>
</li>
<?php
					break;
				case 'VIEWS':
					$casedata = $this->data['view_urls'];

				case 'PERSONAL':
					if ( empty( $casedata ) ) $casedata = $this->data['personal_urls'] ;

				case 'ACTIONS':
					if ( empty( $casedata) ) $casedata = $this->data['action_urls'];

					if ( count( $casedata ) == 0 ) continue;

					foreach ( $casedata as $id => $link ): 
						if( empty( $link['text'] ) ) continue;
						?>
						<li<?=$link['attributes'] ?>><span><a href="<?=htmlspecialchars( $link['href'] ) ?>" <?=$link['key'] ?> class="icononly sprite-<?=$id?>"><?=htmlspecialchars( $link['text'] ) ?></a></span></li>
						<? 
					endforeach;
					break;


				case 'SEARCH':
//<div id="p-search">
?>
	<!--<h5<?php $this->html('userlangattributes') ?>><label for="searchInput"><?php $this->msg( 'search' ) ?></label></h5>-->
	<form action="<?php $this->text( 'wgScript' ) ?>" id="searchform">
		<input type='hidden' name="title" value="<?php $this->text( 'searchtitle' ) ?>"/>
		<?php if ( $wgVectorUseSimpleSearch && $wgUser->getOption( 'vector-simplesearch' ) ): ?>
		<div id="simpleSearch">
			<?php if ( $this->data['rtl'] ): ?>
			<button id="searchButton" type='submit' name='button' <?php echo $this->skin->tooltipAndAccesskey( 'search-fulltext' ); ?>><img src="<?php echo $this->skin->getSkinStylePath('images/search-rtl.png'); ?>" alt="<?php $this->msg( 'searchbutton' ) ?>" /></button>
			<?php endif; ?>
			<input id="searchInput" name="search" type="text" <?php echo $this->skin->tooltipAndAccesskey( 'search' ); ?> <?php if( isset( $this->data['search'] ) ): ?> value="<?php $this->text( 'search' ) ?>"<?php endif; ?> />
			<?php if ( !$this->data['rtl'] ): ?>
			<button id="searchButton" type='submit' name='button' <?php echo $this->skin->tooltipAndAccesskey( 'search-fulltext' ); ?>><img src="<?php echo $this->skin->getSkinStylePath('images/search-ltr.png'); ?>" alt="<?php $this->msg( 'searchbutton' ) ?>" /></button>
			<?php endif; ?>
		</div>
		<?php else: ?>
		<input id="searchInput" name="search" type="text" <?php echo $this->skin->tooltipAndAccesskey( 'search' ); ?> <?php if( isset( $this->data['search'] ) ): ?> value="<?php $this->text( 'search' ) ?>"<?php endif; ?> />
		<input type='submit' name="go" class="searchButton" id="searchGoButton"	value="<?php $this->msg( 'searcharticle' ) ?>"<?php echo $this->skin->tooltipAndAccesskey( 'search-go' ); ?> />
		<input type="submit" name="fulltext" class="searchButton" id="mw-searchButton" value="<?php $this->msg( 'searchbutton' ) ?>"<?php echo $this->skin->tooltipAndAccesskey( 'search-fulltext' ); ?> />
		<?php endif; ?>
	</form>
<?php

				break;
			}
			echo "\n<!-- /{$name} -->\n";
		}
	}
}
