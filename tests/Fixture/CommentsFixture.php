<?php
declare(strict_types=1);

namespace Kareylo\Comments\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

class CommentsFixture extends TestFixture
{
    /**
     * records property
     *
     * @var array
     * @access public
     */
    public array $records = [
        [
            'content' => 'Lorem Ipsum',
            'ref' => 'Posts',
            'ref_id' => 2,
            'ip' => '::1',
            'parent_id' => null,
            'user_id' => 1,
            'created' => '2017-03-22 12:00:00',
            'modified' => '2017-03-22 12:00:00',
        ],
        [
            'content' => 'Lorem Ipsum',
            'ref' => 'Posts',
            'ref_id' => 2,
            'ip' => '::1',
            'parent_id' => 1,
            'user_id' => 2,
            'created' => '2017-03-22 12:05:00',
            'modified' => '2017-03-22 12:05:00',
        ],
        [
            'content' => 'Lorem Ipsum',
            'ref' => 'Posts',
            'ref_id' => 2,
            'ip' => '::1',
            'parent_id' => 1,
            'user_id' => 3,
            'created' => '2017-03-22 12:07:00',
            'modified' => '2017-03-22 12:07:00',
        ],
        [
            'content' => 'Lorem Ipsum',
            'ref' => 'Posts',
            'ref_id' => 2,
            'ip' => '::1',
            'parent_id' => 3,
            'user_id' => 4,
            'created' => '2017-03-22 12:07:00',
            'modified' => '2017-03-22 12:07:00',
        ],
        [
            'content' => 'Lorem Ipsum',
            'ref' => 'Posts',
            'ref_id' => 2,
            'ip' => '::1',
            'parent_id' => 2,
            'user_id' => 3,
            'created' => '2017-03-22 17:07:00',
            'modified' => '2017-03-22 17:07:00',
        ],
    ];
}
