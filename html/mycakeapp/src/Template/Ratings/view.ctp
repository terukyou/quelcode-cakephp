<?php

/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Rating $rating
 */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('Edit Rating'), ['action' => 'edit', $rating->id]) ?> </li>
        <li><?= $this->Form->postLink(__('Delete Rating'), ['action' => 'delete', $rating->id], ['confirm' => __('Are you sure you want to delete # {0}?', $rating->id)]) ?> </li>
        <li><?= $this->Html->link(__('List Ratings'), ['action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Rating'), ['action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Biditems'), ['controller' => 'Biditems', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Biditem'), ['controller' => 'Biditems', 'action' => 'add']) ?> </li>
    </ul>
</nav>
<div class="ratings view large-9 medium-8 columns content">
    <h3><?= h($rating->id) ?></h3>
    <table class="vertical-table">
        <tr>
            <th scope="row"><?= __('Biditem') ?></th>
            <td><?= $rating->has('biditem') ? $this->Html->link($rating->biditem->name, ['controller' => 'Biditems', 'action' => 'view', $rating->biditem->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Rating Comment') ?></th>
            <td><?= h($rating->rating_comment) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Id') ?></th>
            <td><?= $this->Number->format($rating->id) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Appraisee Id') ?></th>
            <td><?= $this->Number->format($rating->appraisee_id) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Rating Scale') ?></th>
            <td><?= $this->Number->format($rating->rating_scale) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Reviewer Id') ?></th>
            <td><?= $this->Number->format($rating->reviewer_id) ?></td>
        </tr>
    </table>
</div>
