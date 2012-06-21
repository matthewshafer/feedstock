<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html" charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<!--<Link href="<?php printf("%s%s", $this->templateEngine->getAdminUrl(), '/css/style.css'); ?>" rel="stylesheet" type="text/css">-->
		<Link href="<?php printf("%s%s", $this->templateEngine->getAdminUrl(), '/css/bootstrap.min.css'); ?>" rel="stylesheet" type="text/css">
			<Link href="<?php printf("%s%s", $this->templateEngine->getAdminUrl(), '/css/extra.css'); ?>" rel="stylesheet" type="text/css">
		<Link href="<?php printf("%s%s", $this->templateEngine->getAdminUrl(), '/css/bootstrap-responsive.min.css'); ?>" rel="stylesheet" type="text/css">
		<title><?php echo $this->templateEngine->getHtmlTitle();  ?></title>
	</head>
	
	<body>
		<div class="navbar navbar-fixed-top">
			<div class="navbar-inner">
				<div class="container">
					<a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</a>

					<a href="<?php printf("%s%s", $this->templateEngine->getAdminUrl(), '/'); ?>" id="logo">Feedstock Admin</a>

					<div class="nav-collapse">
						<ul class="nav pull-right">
							<li class="dropdown">
								<a href="#" class="dropdown-toggle" data-toggle="dropdown">Posts<b class="caret"></b></a>
								<ul class="dropdown-menu">
									<a href="<?php printf("%s%s", $this->templateEngine->getAdminUrl(), '/index.php/posts'); ?>">View Posts</a>
									<a href="<?php printf("%s%s", $this->templateEngine->getAdminUrl(), '/index.php/post'); ?>">New Post</a>
								</ul>
							</li>

							<li class="dropdown">
								<a href="#" class="dropdown-toggle" data-toggle="dropdown">Pages<b class="caret"></b></a>
								<ul class="dropdown-menu">
									<a href="<?php printf("%s%s", $this->templateEngine->getAdminUrl(), '/index.php/pages'); ?>">View Pages</a>
									<a href="<?php printf("%s%s", $this->templateEngine->getAdminUrl(), '/index.php/page'); ?>">New Page</a>
								</ul>
							</li>

							<li class="dropdown">
								<a href="#" class="dropdown-toggle" data-toggle="dropdown">Snippets<b class="caret"></b></a>
								<ul class="dropdown-menu">
									<a href="<?php printf("%s%s", $this->templateEngine->getAdminUrl(), '/index.php/snippets/'); ?>">View Snippets</a>
									<a href="<?php printf("%s%s", $this->templateEngine->getAdminUrl(), '/index.php/snippet'); ?>">New Snippet</a>
								</ul>
							</li>

							<li><a href="<?php printf("%s%s", $this->templateEngine->getAdminUrl(), '/index.php/tags'); ?>">Tags</a></li>
							<li><a href="<?php printf("%s%s", $this->templateEngine->getAdminUrl(), '/index.php/categories'); ?>">Categories</a></li>
							<li><a href="<?php printf("%s%s", $this->templateEngine->getAdminUrl(), '/index.php/corral'); ?>">Corral</a></li>
						</ul>
					</div>

				</div>
			</div>
		</div>
	<div id="container">
	
		<div class="row">
			<div class="span6 offset3">