<div id="hheader">

<table width="100%" border="0" cellspacing="0" cellpadding="0">
<tr>
<td id="homelinktd"><h1 id="homelink"><a href="/"><img src="<?php if($ms_user['valid']) {echo '/img/header/' . $ms_user['style_logo'] . '.png';} else {echo '/img/header/default.png';} ?>" height="28px" style="vertical-align: middle; border: none;" alt="Link to Mod Share" /> Mod Share</a></h1></td>
<td>&nbsp;</td>
<td align="center" class="ltd"><a href="/users/">Users</a></td>
<td align="center" class="ltd"><a href="/browse">Browse</a></td>
<td align="center" class="ltd"><a href="/upload">Upload</a></td>
<td align="center" class="ltd"><a href="/forums/">Forums</a></td>
<?php if ($ms_user['imgsrv']) { ?>
<td align="center" class="ltd"><a href="/imgsrv">Images</a></td>
<?php } ?>
<td align="center" class="ltd"><a href="/<?php if ($ms_user['valid']) { echo 'users/' . rawurlencode($ms_user['username']); } else { echo 'login'; } ?>"><?php if ($ms_user['valid']) { echo $ms_user['username']; } else { echo 'Log in'; } ?></a></td>
</tr>
</table>

</div>