<?php

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Rating Entity
 *
 * @property int $id
 * @property int $biditem_id
 * @property int $appraisee_id
 * @property int $rating_scale
 * @property string $rating_comment
 * @property int $reviewer_id
 *
 * @property \App\Model\Entity\Biditem $biditem
 * @property \App\Model\Entity\User $user
 */
class Rating extends Entity
{
    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array
     */
    protected $_accessible = [
        'biditem_id' => true,
        'appraisee_id' => true,
        'rating_scale' => true,
        'rating_comment' => true,
        'reviewer_id' => true,
    ];
}
