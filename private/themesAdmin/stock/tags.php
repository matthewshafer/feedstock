<?php require_once("header.php"); ?>
			<div class="well">
				<table class="table">
					<thead>
						<th>Tag Name</th>
						<th>Uri</th>
					</thead>
					<tbody>
			<?php
			foreach($this->templateEngine->getTagData() as $key)
			{
				//echo 'Tag: <a href="''" . $key['Name'] . ' ' . 'URI: ' . $key['URIName'] . '<br><br>';

				echo '<tr>';

				echo '<td>';
				echo '<a href="' . sprintf("%s/index.php/tags/%s", $this->templateEngine->getAdminUrl(), $key["URIName"]) . '">' . $key["Name"] . '</a>';
				echo '</td>';

				echo '<td>' . $key["URIName"] . '</td>';


				echo '</tr>';
			}
			?>

					</tbody>
				</table>

			</div>
<?php require_once("footer.php"); ?>