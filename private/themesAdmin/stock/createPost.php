<?php require_once("header.php"); ?>
			<form name="createPost" class="well" method="post" action="<?php printf("%s%s", $this->templateEngine->getAdminUrl(), '/index.php'); ?>">
				<div class="control-group">
					<label class="control-label"><h4>Post Title: (We create a URI based off this)</h4></label>
					<input name="postTitle" type="text" class="span5" value="<?php echo $this->templateEngine->postTitleId(); ?>">
				</div>
				<div class="control-group">
					<label class="control-label"><h4>Post Body:</h4></label>
					<textarea class="span5" rows="10" name="postorpagedata"><?php echo $this->templateEngine->postBodyId(); ?></textarea>
				</div>
				
				<div class="control-group">
					<label class="control-label"><h4>Categories:</h4></label>
					<div class="controls">
						<table class="table table-condensed">
							<thead>
								<th>Selected</th>
								<th>Name</th>
							</thead>
							<tbody>
				<?php foreach($this->templateEngine->getCategoryData() as $key)
				{
					echo '<tr>';

					if(isset($key["Checked"]) and $key["Checked"] == 1)
					{
						echo '<td><input name="postCategories[]" type="checkbox" value="' . $key["PrimaryKey"] . '" checked></td>' . '<td>' . $key["Name"] . '</td>';
					}
					else
					{
						echo '<td><input name="postCategories[]" type="checkbox" value="' . $key["PrimaryKey"] . '"></td>' . '<td>' . $key["Name"] . '</td>';
					}

					echo '</tr>';
				} 
				echo "\n";
				?>	
							</tbody>
						</table>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label"><h4>Tags: (separate with a ,)</h4></label>
					<input name="postTags" type="text" class="textInput" value="<?php echo $this->templateEngine->postTagsId(); ?>">
				</div>
				<div class="control-group">
					<label class="control-label"><h4>Draft:</h4></label>
					<div class="controls">
						<label class="radio inline">
							<input name="draft" type="radio" value="1" <?php echo $this->templateEngine->isDraft() == 1 ? "Checked" : ""; ?>> yes
						</label>
						<label class="radio inline">
							<input name="draft" type="radio" value="0" <?php echo $this->templateEngine->isDraft() == 0 ? "Checked" : ""; ?>> no
						</label>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label"><h4>Use current date:</h4></label>
					<div class="controls">
						<label class="radio inline">
							<input name="useCurrentDate" type="radio" value="1">yes
						</label>
						<label class="radio inline">
							<input name="useCurrentDate" type="radio" value="0" Checked>no
						</label>
					</div>
				</div>
				<input name="type" type="hidden" value="postAdd">
				<input name="id" type="hidden" value="<?php echo $this->templateEngine->postId(); ?>">
				<input name="submit" type="submit" value="Post" class="btn btn-primary">
			</form>
<?php require_once("footer.php"); ?>