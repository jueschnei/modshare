	// Remove empty tags
	while (($new_text = strip_empty_bbcode($text)) !== false)
	{
		if ($new_text != $text)
		{
			$text = $new_text;
			if ($new_text == '')
			{
				$errors[] = $lang_post['Empty after strip'];
				return '';
			}
		}
		else
			break;
	}

	// Tags not allowed
	$tags_forbidden = array();
	// Disallow URL tags
	if ($pun_user['g_post_links'] != '1')
		$tags_forbidden[] = 'url';

		// Is the tag forbidden?
		if (in_array($current_tag, $tags_forbidden))
		{
			if (isset($lang_common['BBCode error tag '.$current_tag.' not allowed']))
				$errors[] = sprintf($lang_common['BBCode error tag '.$current_tag.' not allowed']);
			else
				$errors[] = sprintf($lang_common['BBCode error tag not allowed'], $current_tag);

			return false;
		}

	if ($bbcode === false && url_valid($full_url) === false)
		$bbcode = true;

