<?php

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Buyerinfo Entity
 *
 * @property int $biditem_id
 * @property int $user_id
 * @property string $name
 * @property string $home
 * @property string $phone
 * @property bool $received
 *
 * @property \App\Model\Entity\User $user
 */
class Buyerinfo extends Entity
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
        'user_id' => true,
        'name' => true,
        'home' => true,
        'phone' => true,
        'received' => true,
        'user' => true,
    ];
}
