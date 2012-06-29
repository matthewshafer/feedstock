<?php require_once("header.php"); ?>
			<form name="createPage" class="well" method="post" action="<?php printf("%s%s", $this->templateEngine->getAdminUrl(), '/index.php'); ?>">
				<div class="control-group">
					<label class="control-label"><h4>Page Title: (We create a URI based off this)</h4></label>
					<input name="pageTitle" type="text" class="span5" value="<?php echo $this->templateEngine->pageTitleId(); ?>">
				</div>
				<div class="control-group">
					<label class="control-label"><h4>Custom URI:</h4></label>
					<input name="pageUri" type="text" class="span5" value="<?php echo $this->templateEngine->pageUri(); ?>">
				</div>
				<div class="control-group">
					<label class="control-label"><h4>Post Body:</h4></label>
					<textarea rows="10" class="span5" name="postorpagedata"><?php echo $this->templateEngine->pageBodyId(); ?></textarea>
				</div>
				<div class="control-group">
					<label class="control-label"><h4>Corral:</h4></label>
					<input name="pageCorral" type="text" class="span3" value="<?php echo $this->templateEngine->getPageCorral(); ?>"><br>
				</div>
				<div class="control-group">
					<label class="control-label"><h4>Draft:</h4></label>
					<label class="radio inline">
						<input name="draft" type="radio" value="1" <?php echo $this->templateEngine->isDraft() == 1 ? "Checked" : ""; ?>>yes
					</label>
					<label class="radio inline">
						<input name="draft" type="radio" value="0" <?php echo $this->templateEngine->isDraft() == 0 ? "Checked" : ""; ?>>no
					</label>
				</div>
				<input name="type" type="hidden" value="pageAdd">
				<input name="id" type="hidden" value="<?php echo $this->templateEngine->pageId(); ?>">
				<br>
				<input name="submit" type="submit" value="Post" class="btn btn-primary">
			</form>
			</form>
<?php require_once("footer.php"); ?>