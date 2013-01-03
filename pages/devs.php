<?php
$page_title = 'Developers - Mod Share';
?>
<h2>Mod Share Developer Page</h2>
<h3>Site API</h3>
<p>This site employs a very useful API that can be used to get information about projects, users, and more. Details are available at <a href="/api"><?php echo $_SERVER['HTTP_HOST']; ?>/api</a>.</p>
<h3>Site source code</h3>
<p>If you are experienced in PHP, you can access the source code for this website on <a href="https://www.assembla.com/code/mod-share-iv/git/nodes">Assembla</a>.</p>
<h3>Contribute code</h3>
<p>If you find a glitch in this site or feel that a feature is missing and have the code to fix it, you are welcome to submit it to us. To do so, you may do one of the following things:</p>
<ul>
	<li>Submit a <a href="https://www.assembla.com/code/mod-share-iv/git/merge_requests#open">merge request</a></li>
	<li><a href="/help">Contact us</a> and send a DIFF or PATCH file for any files you have changed (preferred)</li>
</ul>
<h3>Adding uploading functionality to your mod</h3>
<p>If you want to implement functionality to directly upload from your mod, you can import the following Squeak class definitions:</p>
<ul>
	<li><a href="/data/changesets/uploading/dialog.st">Dialog code</a></li>
	<li><a href="/data/changesets/uploading/frame.st">ScratchFrameMorph code</a></li>
	<li><a href="/data/changesets/uploading/httpsocket.st">HTTPSocket code</a>
</ul>
<p>Alternatively, you can submit a POST request to <code><?php echo $_SERVER['HTTP_HOST']; ?>/upload</code> with the following parameters:</p>
<table border="0">
	<tr>
		<th>Parameter name</th>
		<th>Contents</th>
	</tr>
	<tr>
		<td>title</td>
		<td>The title of the project</td>
	</tr>
	<tr>
		<td>description</td>
		<td>The description of the project</td>
	</tr>
	<tr>
		<td>mod</td>
		<td>The mod code of the project (e.g. insanity12 or bingo2)</td>
	</tr>
	<tr>
		<td>license</td>
		<td>The license of the project (pd, ms, or cc)</td>
	</tr>
	<tr>
		<td>project</td>
		<td>The file contents of the project</td>
	</tr>
	<tr>
		<td>un</td>
		<td>The username of the person uploading</td>
	</tr>
	<tr>
		<td>pwd</td>
		<td>The password of the person uploading</td>
	</tr>
</table>