<?php
declare(strict_types=1);

return [
    'comments' => [
        'columns' => [
            'private' => ['type' => 'boolean', 'null' => false, 'default' => false],
            'id' => ['type' => 'integer'],
            'content' => ['type' => 'text', 'null' => false],
            'ref' => ['type' => 'text', 'null' => false, 'length' => 255],
            'ref_id' => ['type' => 'integer', 'null' => false, 'length' => 11],
            'ip' => ['type' => 'string', 'null' => false, 'length' => 255],
            'parent_id' => ['type' => 'integer', 'null' => true, 'length' => 11],
            'user_id' => ['type' => 'integer', 'null' => false, 'length' => 11],
            'created' => ['type' => 'datetime', 'null' => true, 'default' => null],
            'modified' => ['type' => 'datetime', 'null' => true, 'default' => null],
        ],
        'constraints' => ['primary' => ['type' => 'primary', 'columns' => ['id']]],
    ],
    'users' => [
        'columns' => [
            'id' => ['type' => 'integer'],
            'username' => ['type' => 'string', 'null' => false],
            'passwd' => ['type' => 'string', 'null' => true, 'default' => null, 'length' => 128],
            'created' => ['type' => 'datetime', 'null' => true, 'default' => null],
            'modified' => ['type' => 'datetime', 'null' => true, 'default' => null],
        ],
        'constraints' => ['primary' => ['type' => 'primary', 'columns' => ['id']]],
    ],
    'posts' => [
        'columns' => [
            'id' => ['type' => 'integer'],
            'title' => ['type' => 'string', 'null' => false],
            'content' => ['type' => 'text', 'null' => false],
            'comments_count' => ['type' => 'integer', 'null' => false, 'default' => 0, 'length' => 10],
            'user_id' => ['type' => 'integer', 'null' => false, 'length' => 11],
        ],
        'constraints' => ['primary' => ['type' => 'primary', 'columns' => ['id']]],
    ],
    'articles' => [
        'columns' => [
            'id' => ['type' => 'integer'],
            'title' => ['type' => 'string', 'null' => false],
            'content' => ['type' => 'text', 'null' => false],
            'user_id' => ['type' => 'integer', 'null' => false, 'length' => 11],
        ],
        'constraints' => ['primary' => ['type' => 'primary', 'columns' => ['id']]],
    ],
    'attachments' => [
        'columns' => [
            'id' => ['type' => 'integer'],
            'user_id' => ['type' => 'integer'],
        ],
        'constraints' => ['primary' => ['type' => 'primary', 'columns' => ['id']]],
    ],
];
