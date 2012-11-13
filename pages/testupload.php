<form action="http://scratch.mit.edu/services/upload" method="post" enctype="multipart/form-data">
	<table border="0">
		<tr>
			<td>Username</td>
			<td><input type="text" name="username" /></td>
		</tr>
		<tr>
			<td>Password</td>
			<td><input type="password" name="password" /></td>
		</tr>
		<input type="hidden" name="scratchkey" value="ch4ng3me" />
		<tr>
			<td>Project description</td>
			<td><textarea name="description" rows="10" cols="30"></textarea></td>
		</tr>
		<input type="hidden" name="project_history" value="<?php echo date('Y-m-d H:i:s') . "\t" . 'share' . "\t" . 'jvvg'; ?>" />
		<input type="hidden" name="tags" value="none" />
		<input type="hidden" name="version" value="1.4 of 39-Jun-09" />
		<input type="hidden" name="version-date" value="2009-6-30" />
		<input type="hidden" name="numberOfSprites" value="0" />
		<input type="hidden" name="totalScripts" value="0" />
		<input type="hidden" name="allScripts" value="" />
		<input type="hidden" name="hasScratchBoardSensorBlocks" value="false" />
		<input type="hidden" name="hasMotorBlocks" value="false" />
		<tr>
			<td>Project file</td>
			<td><input type="file" name="binary_file" /></td>
		</tr>
		<tr>
			<td>Thumbnail</td>
			<td><input type="file" name="thumbnail_image" /></td>
		</tr>
		<tr>
			<td>Preview</td>
			<td><input type="file" name="preview_image" /></td>
		</tr>
	</table>
	<input type="submit" name="Let&apos;s do this!" />
</form>