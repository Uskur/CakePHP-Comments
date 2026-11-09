<?php
declare(strict_types=1);

namespace Kareylo\Comments\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

class PostsFixture extends TestFixture
{
    /**
     * records property
     *
     * @var array
     * @access public
     */
    public array $records = [
        [
            'id' => 1,
            'title' => 'First Article',
            'content' => 'Lorem ipsum dolor sit amet',
            'comments_count' => 0,
            'user_id' => 1,
        ],
        [
            'id' => 2,
            'title' => 'Second Article',
            'content' => 'Ipsum lorem dolor sit amet',
            'comments_count' => 5,
            'user_id' => 1,
        ],
    ];
}
