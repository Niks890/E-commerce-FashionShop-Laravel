<?php

if (!function_exists('getColorHex')) {

    function getColorHex($color)
    {
        $colorMap = [
            'Đen' => '#000000',
            'Vàng' => '#FFD700',
            'Trắng' => '#FFFFFF',
            'Xanh' => '#007BFF',
            'Xanh lá' => '#28a745',
            'Đỏ' => '#FF0000',
            'Hồng' => '#FFC0CB',
            'Cam' => '#FFA500',
            'Xanh Nâu' => '#8FBC8F',

        ];
        return $colorMap[$color] ?? '#CCCCCC';
    }
}
