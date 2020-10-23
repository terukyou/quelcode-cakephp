<h2>商品を出品する</h2>
<?= $this->Form->create($biditem, array('type' => 'file')) ?>
<fieldset>
	<legend>※商品名と終了日時を入力：</legend>
	<?php
		echo $this->Form->hidden('user_id', ['value' => $authuser['id']]);
		echo '<p><strong>USER: ' . $authuser['username'] . '</strong></p>';
		echo $this->Form->control('name');
		echo $this->Form->control('description', ['type' => 'textarea']);
		echo $this->Form->hidden('finished', ['value' => 0]);
		echo $this->Form->hidden('shipped', ['value' => 0]);
		echo $this->Form->control('endtime');
		echo $this->Form->control('image_name', ['type' => 'file']);
	?>
	<p class="error-message">
		<?php
		if (isset($fileError) && ($fileError === 'onemore')) {
			echo 'ファイルを選択してください';
		}
		?>
	</p>
	<?php echo $this->Form->error('fileType'); ?>
</fieldset>
<?= $this->Form->button(__('Submit')) ?>
<?= $this->Form->end() ?>

