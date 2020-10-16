<h2>ミニオークション!</h2>
<h3>※出品されている商品</h3>
<table cellpadding="0" cellspacing="0">
<thead>
	<tr>
		<th class="main" scope="col"><?= $this->Paginator->sort('name') ?></th>
		<th scope="col"><?= $this->Paginator->sort('finished') ?></th>
		<th scope="col"><?= $this->Paginator->sort('endtime') ?></th>
		<th scope="col" class="actions"><?= __('Actions') ?></th>
	</tr>
</thead>
<tbody>
	<?php foreach ($auction as $biditem): ?>
	<tr>
		<td><?= h($biditem->name) ?></td>
		<td><?= h($biditem->finished ? 'Finished':'') ?></td>
		<td><?= h($biditem->endtime) ?></td>
		<td class="actions">
			<?= $this->Html->link(__('View'), ['action' => 'view', $biditem->id]) ?>
			<?php
			$bidinfo = $biditem->bidinfo;
			// (bidinfoテーブルに商品のデータがある)&&(ユーザーが出品者か落札者である)
			if ((!empty($bidinfo)) && (($biditem->user_id === $authuser['id']) || ($bidinfo->user_id === $authuser['id']))) : ?>
				<!-- 取引終了後のページのリンクを表示 -->
				<?php if (in_array($biditem->id, $endOfTransaction)) : ?>
					取引終了
				<?php else : ?>
					<?= $this->Html->link(__('Interact'), ['action' => 'interact', $biditem->id]) ?>
				<?php endif; ?>
			<?php endif; ?>
		</td>
	</tr>
	<?php endforeach; ?>
</tbody>
</table>

<div class="paginator">
	<ul class="pagination">
		<?= $this->Paginator->first('<< ' . __('first')) ?>
		<?= $this->Paginator->prev('< ' . __('previous')) ?>
		<?= $this->Paginator->numbers() ?>
		<?= $this->Paginator->next(__('next') . ' >') ?>
		<?= $this->Paginator->last(__('last') . ' >>') ?>
	</ul>
</div>
