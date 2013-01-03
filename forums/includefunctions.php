	// If nothing else helps, we assign the default
<meta http-equiv="Content-Type" content="<?php echo get_mime() ?>; charset=utf-8" />
	{
		exit;
	}
}


//
// This function returns the correct mime type to serve with XHTML
//
function get_mime()
{
	if (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/xhtml+xml') !== false)
		return 'application/xhtml+xml';
	// special check for the W3C validation service
	else if (isset($_SERVER['HTTP_USER_AGENT']) && strpos($_SERVER['HTTP_USER_AGENT'], 'W3C_Validator') !== false)
		return 'application/xhtml+xml';
	else
		return 'text/html';
