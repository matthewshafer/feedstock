<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
		<Link href="<?php printf("%s%s", $this->templateEngine->getAdminUrl(), '/css/style.css'); ?>" rel="stylesheet" type="text/css">
		<title><?php echo $this->templateEngine->getHtmlTitle();  ?></title>
	</head>
	
	<body>
	<div id="page">
	
	<div id="head">
		 <?php echo $this->templateEngine->siteNameLink(); ?>
		 <br>
		&nbsp&nbsp <?php echo $this->templateEngine->siteDescription();  ?>
	</div>
	
	<div id="menu">
			<ul id="tabnav">
			<li class="tab1"><a href="<?php printf("%s%s", $this->templateEngine->getAdminUrl(), '/'); ?>./">Index</a></li>
			<li class="tab2"><a href="<?php printf("%s%s", $this->templateEngine->getAdminUrl(), '/index.php/posts'); ?>">Posts</a></li>
			<li class="tab3"><a href="<?php printf("%s%s", $this->templateEngine->getAdminUrl(), '/index.php/pages'); ?>">Pages</a></li>
			<li class="tab4"><a href="<?php printf("%s%s", $this->templateEngine->getAdminUrl(), '/index.php/post'); ?>">New Post</a></li>
			<li class="tab5"><a href="<?php printf("%s%s", $this->templateEngine->getAdminUrl(), '/index.php/page'); ?>">New Page</a></li>
			<li class="tab6"><a href="<?php printf("%s%s", $this->templateEngine->getAdminUrl(), '/index.php/tags'); ?>">Tags</a></li>
			<li class="tab7"><a href="<?php printf("%s%s", $this->templateEngine->getAdminUrl(), '/index.php/categories'); ?>">Categories</a></li>
			<li class="tab8"><a href="<?php printf("%s%s", $this->templateEngine->getAdminUrl(), '/index.php/corral'); ?>">Corral</a></li>
			<li class="tab9"><a href="<?php printf("%s%s", $this->templateEngine->getAdminUrl(), '/index.php/snippets/'); ?>">Snippets</a></li>
			<li class="tab10"><a href="<?php printf("%s%s", $this->templateEngine->getAdminUrl(), '/index.php/snippet'); ?>">New Snippet</a></li>
		</ul>
	</div>
	
	<div id="content">