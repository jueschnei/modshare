<?php

if (!defined('PUN')) exit;
define('PUN_QJ_LOADED', 1);
$forum_id = isset($forum_id) ? $forum_id : 0;

?>				<form id="qjump" method="get" action="viewforum.php">
					<div><label><span><?php echo $lang_common['Jump to'] ?><br /></span>
					<select name="id" onchange="window.location=('viewforum.php?id='+this.options[this.selectedIndex].value)">
						<optgroup label="Information">
							<option value="1"<?php echo ($forum_id == 1) ? ' selected="selected"' : '' ?>>Announcements</option>
							<option value="6"<?php echo ($forum_id == 6) ? ' selected="selected"' : '' ?>>Help and FAQ</option>
							<option value="7"<?php echo ($forum_id == 7) ? ' selected="selected"' : '' ?>>Suggestions</option>
							<option value="17"<?php echo ($forum_id == 17) ? ' selected="selected"' : '' ?>>Bugs and glitches</option>
							<option value="14"<?php echo ($forum_id == 14) ? ' selected="selected"' : '' ?>>Site surveys</option>
						</optgroup>
						<optgroup label="Mods and Projects">
							<option value="2"<?php echo ($forum_id == 2) ? ' selected="selected"' : '' ?>>Show and Tell</option>
							<option value="10"<?php echo ($forum_id == 10) ? ' selected="selected"' : '' ?>>Your mods</option>
							<option value="19"<?php echo ($forum_id == 19) ? ' selected="selected"' : '' ?>>Scripting</option>
						</optgroup>
						<optgroup label="Advanced">
							<option value="4"<?php echo ($forum_id == 4) ? ' selected="selected"' : '' ?>>Advanced Topics</option>
							<option value="8"<?php echo ($forum_id == 8) ? ' selected="selected"' : '' ?>>Suggest a mod</option>
						</optgroup>
						<optgroup label="Other">
							<option value="5"<?php echo ($forum_id == 5) ? ' selected="selected"' : '' ?>>Miscellaneous</option>
							<option value="16"<?php echo ($forum_id == 16) ? ' selected="selected"' : '' ?>>Miscellaneous polls</option>
						</optgroup>
					</select>
					<input type="submit" value="<?php echo $lang_common['Go'] ?>" accesskey="g" />
					</label></div>
				</form>
