<?php
/**
 * A Zenphoto plugin for keyboard navigation using the left/right arrows for gallery/album pages, single image pages, 
 * news index/categories and single news articles pages. 
 * On single image pages the up arrow key also takes back to the album.
 *
 * Usage:
 * Place the plugin file within /plugins and enable it
 * Set the option for which pages you wish to enable the keyboard navigation
 * The plugin attaches to your theme automatically
 *
 * Note: This only works correctly with themes following the standard theme structure. 
 *
 * Inspired by Laurent Marineau's plugin http://www.zenphoto.org/news/keyboard-plugin though no adaption.
 * 
 * @author Malte Müller (acrylian) <info@maltem.de>
 * @copyright 2014 Malte Müller
 * @license GPL v3 or later
 * @package plugins
 * @subpackage media
 */
$plugin_is_filter = 5 | THEME_PLUGIN;
$plugin_description = gettext("Keyboard navigation for gallery/albums, images and Zenpage news item pages for the left/right arrows using jQuery.");
$plugin_author = "Malte Müller (acrylian)";
$plugin_version = '1.0';
$option_interface = 'keyboardNavExtended';

zp_register_filter('theme_body_close','keyboardNavExtended::keyBoardNavJS');

class keyboardNavExtended {

	function __construct() {
		setOptionDefault('keyboardnav-extended_gallerypages', 1);
		setOptionDefault('keyboardnav-extended_singleimage', 1);
		setOptionDefault('keyboardnav-extended_zenpagecategories', 1);
		setOptionDefault('keyboardnav-extended_zenpagearticle', 1);
	}
	
	function getOptionsSupported() {
		return array(
			gettext('Gallery pages') => array(
				'key' => 'keyboardnav-extended_albumpages', 'type' => OPTION_TYPE_CHECKBOX,
				'order'=>5,
				'desc' => gettext('Enable the keyboard navigation for the Gallery (index.php, gallery.php and album.php).')),
				gettext('Single image pages') => array(
				'key' => 'keyboardnav-extended_singleimage', 'type' => OPTION_TYPE_CHECKBOX,
				'order'=>5,
				'desc' => gettext('Enable the keyboard navigation the single image page (image.php).')),
				gettext('Zenpage categories') => array(
				'key' => 'keyboardnav-extended_zenpagecategories', 'type' => OPTION_TYPE_CHECKBOX,
				'order'=>5,
				'desc' => gettext('Enable the keyboard navigation for the Zenpage category listings.')),
				gettext('Zenpage single articles') => array(
				'key' => 'keyboardnav-extended_zenpagearticle', 'type' => OPTION_TYPE_CHECKBOX,
				'order'=>5,
				'desc' => gettext('Enable the keyboard navigation for the Zenpage single articles previous and next article.'))
		);
	}
	
	static function keyBoardNavJS() {
    global $_zp_gallery_page, $_zp_current_album;
    $prevurl = false;
    $nexturl = false;
    switch ($_zp_gallery_page) {
      case 'index.php':
      case 'gallery.php':
      case 'album.php':
        if (getOption('keyboardnav-extended_gallerypages')) {
          if (hasPrevPage()) {
            $prevurl = getPrevPageURL();
          }
          if (hasNextPage()) {
            $nexturl = getNextPageURL();
          }
        }
        break;
      case 'image.php':
        if (getOption('keyboardnav-extended_singleimage')) {
          if (hasPrevImage()) {
            $prevurl = getPrevImageURL();
          }
          if (hasNextImage()) {
            $nexturl = getNextImageURL();
          }
        }
        break;
      case 'news.php':
        if (is_NewsArticle()) {
          if (getOption('keyboardnav-extended_zenpagearticle')) {
            //return false if not available so no if check needed
            $prevurl = getPrevNewsURL();
            $nexturl = getNextNewsURL();
          }
        } else {
          if (getOption('keyboardnav-extended_zenpagecategories')) {
            $prevurl = getPrevNewsPageURL();
            $nexturl = getNextNewsPageURL();
          }
        }
        break;
    }
    if ($prevurl || $nexturl) {
          ?>
          			<script>
          				$(document).ready(function(){
          					$(document.documentElement).keydown(function(event) {
          						// handle cursor keys
          						if (event.keyCode === 37) {
          <?php if ($prevurl) { ?>
            								document.location.href = '<?php echo htmlspecialchars($prevurl); ?>';
          <?php } ?>
          						} else if (event.keyCode === 39) {
          <?php if ($nexturl) { ?>
            								document.location.href = '<?php echo htmlspecialchars($nexturl); ?>';
          <?php } ?>
          						}
          <?php if($_zp_gallery_page == 'image.php' && getOption('keyboardnav-extended_singleimage')) { ?>
              if (event.keyCode === 38) {
                document.location.href = '<?php echo htmlspecialchars($_zp_current_album->getLink()); ?>';
              }
          <?php } ?>
          					});
          				});
          	    </script>
          <?php
        }
    }
  } //class
