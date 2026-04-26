<?php

return [
    'max_weight_g' => 3000,
    'brackets' => [
        [
            'name' => 'standard',
            'max' => 1000,
            'cost' => 0,
        ],
        [
            'name' => 'heavy',
            'max' => 2000,
            'cost' => 5,
        ],
        [
            'name' => 'oversized',
            'max' => 9999,
            'cost' => 12,
        ],
    ],
];
