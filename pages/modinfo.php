<?php $page_title = 'About Mods - Mod Share' ?>
<h2>About Mods</h2>
<p>Mod Share revolves around the wonderful creativity that is expressed through the numerous modifications of Scratch. But what is a mod?</p>
<p>Sure, Scratch is a great program that is easy to learn and has great versatility. But for some people that block palette is too limited, or those features just aren't enough. That's why people started taking the program into their own hands and created a bunch of mods.</p>
<p>Mods keep the easy, understandable interface of Scratch and add their own little touch. Some may add just a few extra blocks, some may go as far as including whole new features. Mod Share selected a few of the best mods to upload to this site.</p>
<p>If you're interested in making a mod, you should learn Squeak (the language that Scratch is written in) and start reading some documentation. Make sure you contact us if you finish it up and share it with the community!</p>
<h3>Our Selected Mods</h3>
<p>
<ul>
<?php foreach($modlist as $key=>$val) {
	if($key != 'scratch' && $key != 'other') {
		echo '<li>' . $val['name'] . '</li>';
	}
} ?>
</ul>
</p>
