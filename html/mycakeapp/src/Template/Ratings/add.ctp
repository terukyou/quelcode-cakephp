<?php

/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Rating $rating
 */
?>
<div class="ratings form large-9 medium-8 columns content">
    <?= $this->Form->create($rating) ?>
    <?php
    $this->Form->templates([
        'nestingLabel' => '{{hidden}}{{input}}<label{{attrs}}>{{text}}</label>',
    ]);
    ?>
    <fieldset>
        <legend><?= __('Add Rating') ?></legend>
        <p>○○さんはいかがでしたか</p>
        <p>評価</p>
        <?php
        echo $this->Form->radio('rating_scale', [
            ['text' => 1, 'value' => 1],
            ['text' => 2, 'value' => 2],
            ['text' => 3, 'value' => 3],
            ['text' => 4, 'value' => 4],
            ['text' => 5, 'value' => 5],
        ]);
        echo $this->Form->control('rating_comment', ['label' => 'コメント']);
        ?>
    </fieldset>
    <?= $this->Form->button(__('Submit')) ?>
    <?= $this->Form->end() ?>
</div>
