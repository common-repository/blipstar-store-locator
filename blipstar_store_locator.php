<?php
/*
Plugin Name: Blipstar Store Locator
Plugin URI: http://www.blipstar.com/blipstarplus/wordpress_store_locator_plugin.php
Description: Adds a store locator to your Wordpress site. Allows you to easily embed your own store locator (created using Blipstar).
Version: 1.3
License: GPL2
*/

error_reporting(E_ERROR);

if (!function_exists( 'blipstar_embed_shortcode' ) ) :

	function blipstar_embed_shortcode($atts, $content = null) {
	  
	  $blipstaruid=$atts["id"];
	  $blipstarmode=$atts["mode"];
	  
	  if(is_numeric($blipstaruid)===false) {
	   echo "<div class='error'>The ID must be a number</div>";
	   return false;
	  }else{
	   $furl="http://www.blipstar.com/blipstarplus/viewer/getdimensions.php?uid=".$blipstaruid;
	   if(ini_get('allow_url_fopen'))
	   {
	    $wh=file_get_contents($furl);
	   }else{
	    if(function_exists("curl_init")===false)
        {
         echo "<div class='error'>cURL needs to be enabled - please contact your network admin</div>";
		 return false;
        }else{
	     $wh=getFileViaCurl($furl);
		} 
	   }
	   
	   if($wh===false) {
	    echo "<div class='error'>Could not load store locator (when getting size)</div>";
	    return false;
	   }else{
	    $whe=explode(",",$wh);
	    if(count($whe)!=2) {
	   	 echo "<div class='error'>Could not load store locator (when reading in width and height)</div>";
	     return false;
	    }
	    if(is_numeric($whe[0])===false || is_numeric($whe[1])===false) {
	     echo "<div class='error'>Could not load store locator (when setting width and height)</div>";
	     return false;
	    }
	    $width=round($whe[0]);
	    $height=round($whe[1]);
	   }	  
	  
	  }
	  $defaults = array(
			'src' => 'http://www.blipstar.com/blipstarplus/viewer/blipstar.php?uid='.$blipstaruid,
			'width' => $width,
			'height' => $height,
			'scrolling' => 'no',
			'frameborder' => '0',
			'allowTransparency' => 'true'
	  );

	  foreach ($defaults as $default => $value) { // add defaults
			if (!@array_key_exists($default, $atts)) { // hide warning with "@" when no params at all
				$atts[$default] = $value;
			}
	  }
	  
	  if($blipstarmode=="all")
	  {
	   $atts["src"]="http://www.blipstar.com/blipstarplus/viewer/map.php?uid=".$blipstaruid."&type=all";
	  }
	  
	  if($_SERVER['HTTPS']!="")
	  {
	   $atts["src"]=str_replace("http://www.blipstar.com/blipstarplus/","https://secure298.hostgator.com/~blipstar/blipstarsecure/",$atts["src"]);
	  }

      $html = "\n".'<!-- Blipstar Store Locator plugin -->'."\n";
	  $html .= '<iframe';
      foreach ($atts as $attr => $value) {
			if( $attr != 'same_height_as' ){ // remove some attributes
				if( $value != '' ) { // adding all attributes
					$html .= ' ' . $attr . '="' . $value . '"';
				} else { // adding empty attributes
					$html .= ' ' . $attr;
				}
			}
	  }
	  $html .= '></iframe>';
	  return $html;
	}
	add_shortcode('blipstar', 'blipstar_embed_shortcode');
	
endif;


function blipstar_plugin_meta( $links, $file ) {
	if ( strpos( $file, 'blipstar_store_locator.php' ) !== false ) {
		$links = array_merge( $links, array( '<a href="http://www.blipstar.com/blipstarplus/contact.php" title="Need help?">' . __('Support') . '</a>' ) );
	}
	return $links;
}
add_filter( 'plugin_row_meta', 'blipstar_plugin_meta', 10, 2 );

function blipstar_admin_notice() {
echo "<div class='updated'><br/><b>Blipstar store locator plugin</b> - <a href='http://www.blipstar.com/blipstarplus/wordpress_store_locator_plugin_instructions.php' target='blipstar'>instructions</a><br/><br/></div>";	
}

function getFileViaCurl($inrequest)
{

// Establish a cURL handle.
$ch = curl_init($inrequest);
 
// Set our options
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
 
// Execute the request
$output = curl_exec($ch);
 
// Close the cURL session.
curl_close($ch);

return $output;

}

add_action('admin_notices', 'blipstar_admin_notice');

?>
