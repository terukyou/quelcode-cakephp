<!-- 出品者 -->
<?php if ($user === 'seller') : ?>
    <!-- buyerinfoテーブルに値が入っていないとき -->
    <?php if ($status === 'form') : ?>
        <p>落札者が発送先の情報を入力するまでお待ちください</p>
    <?php endif; ?>
    <!-- 発送完了していないとき -->
    <?php if ($status === 'ship') : ?>
        <h4>発送先</h4>
        [名前] <?= $form['name'] . '<br>'; ?>
        [住所] <?= $form['home'] . '<br>'; ?>
        [電話] <?= $form['phone'] . '<br>'; ?>

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
    <?php if ($status === 'receive') : ?>
    <?php endif; ?>
<?php endif; ?>

<!-- 落札者 -->
<?php if ($user === 'buyer') {
    // buyerinfoテーブルに値が入っていないとき
    if ($status === 'form') {
        echo $this->Form->create($entity, [
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
    <?php }
    // 発送完了していないとき
    if ($status === 'ship') : ?>
        <p>落札者が発送先の情報を入力するまでお待ちください</p>
    <?php endif; ?>
    <?php if ($status === 'receive') : ?>
    <?php endif; ?>
<?php } ?>
