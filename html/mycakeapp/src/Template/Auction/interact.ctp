<!-- 出品者 -->
<?php if ($user === 'seller') : ?>
    <!-- buyerinfoテーブルに値が入っていないとき -->
    <?php if ($status === 'form') : ?>
        <p>落札者が発送先の情報を入力するまでお待ちください</p>
    <?php endif; ?>
    <!-- 発送完了していないとき -->
    <?php if ($status === 'ship') : ?>
        <h4>発送先</h4>
        [名前] <?php echo h($form['name']) . '<br>'; ?>
        [住所] <?php echo h($form['home']) . '<br>'; ?>
        [電話] <?php echo h($form['phone']) . '<br>'; ?>

        <?php echo $this->Form->create(null, [
            'type' => 'post',
            'url' => [
                'controller' => 'Auction',
                'action' => 'ship', $id
            ]
        ]);
        ?>
        <?= $this->Form->submit('発送完了ボタン'); ?>
        <?= $this->Form->end(); ?>

    <?php endif; ?>
    <!-- 受け取り完了していない時 -->
    <?php if ($status === 'receive') : ?>
        <p>落札者が受け取り完了ボタンを押すまでお待ちください</p>
    <?php endif; ?>
<?php endif; ?>

<!-- 落札者 -->
<?php if ($user === 'buyer') : ?>
    <!-- buyerinfoテーブルに値が入っていないとき -->
    <?php if ($status === 'form') : ?>
        <?= $this->Form->create($entity, [
            'type' => 'post',
            'url' => [
                'controller' => 'Auction',
                'action' => 'form', $id
            ]
        ]);
        ?>
        <?= $this->Form->input('Form.name'); ?>
        <?= $this->Form->input('Form.home'); ?>
        <?= $this->Form->input('Form.phone'); ?>
        <?= $this->Form->submit('送信'); ?>
        <?= $this->Form->end(); ?>
    <?php endif; ?>
    <!--  発送完了していないとき -->
    <?php if ($status === 'ship') : ?>
        <p>出品者が発送完了ボタンを押すまでお待ちください</p>
    <?php endif; ?>
    <!-- 受け取り完了していない時 -->
    <?php if ($status === 'receive') : ?>
        <?php echo $this->Form->create(null, [
            'type' => 'post',
            'url' => [
                'controller' => 'Auction',
                'action' => 'receive', $id
            ]
        ]);
        ?>
        <?= $this->Form->submit('受け取り完了ボタン'); ?>
        <?= $this->Form->end(); ?>
    <?php endif; ?>
<?php endif; ?>
