<?php

if (!function_exists('getColorHex')) {

    function getColorHex($color)
    {
        $colorMap = [
            // Màu cơ bản
            'Đen' => '#000000',
            'Trắng' => '#FFFFFF',
            'Xám' => '#808080',
            'Xám nhạt' => '#D3D3D3',
            'Xám đậm' => '#505050',
            'Xám bạc' => '#C0C0C0',

            // Màu vàng
            'Vàng' => '#FFD700',
            'Vàng chanh' => '#FFF700',
            'Vàng đất' => '#E1AD01',
            'Vàng nhạt' => '#FFFFE0',
            'Vàng kim' => '#FFD700',
            'Vàng cát' => '#F4A460',

            // Màu đỏ
            'Đỏ' => '#FF0000',
            'Đỏ đô' => '#800000',
            'Đỏ cam' => '#FF4500',
            'Đỏ cherry' => '#DE3163',
            'Đỏ bordeaux' => '#722F37',
            'Đỏ nhạt' => '#FFB6C1',
            'Đỏ thẫm' => '#8B0000',
            'Đỏ ruby' => '#E0115F',

            // Màu hồng
            'Hồng' => '#FFC0CB',
            'Hồng pastel' => '#FFD1DC',
            'Hồng đậm' => '#FF1493',
            'Hồng nhạt' => '#FFCCCB',
            'Hồng phấn' => '#DDA0DD',
            'Hồng sen' => '#F8BBD9',

            // Màu cam
            'Cam' => '#FFA500',
            'Cam đất' => '#D2691E',
            'Cam nhạt' => '#FFDAB9',
            'Cam đậm' => '#FF8C00',
            'Cam san hô' => '#FF7F50',

            // Màu tím
            'Tím' => '#800080',
            'Tím pastel' => '#D8BFD8',
            'Tím đậm' => '#4B0082',
            'Tím nhạt' => '#E6E6FA',
            'Tím violet' => '#8A2BE2',
            'Tím lavender' => '#E6E6FA',
            'Tím orchid' => '#DA70D6',

            // Màu nâu
            'Be' => '#F5F5DC',
            'Kem' => '#FFFDD0',
            'Nâu' => '#8B4513',
            'Nâu nhạt' => '#A0522D',
            'Nâu đất' => '#964B00',
            'Nâu chocolate' => '#D2691E',
            'Nâu cà phê' => '#6F4E37',
            'Nâu gỗ' => '#DEB887',
            'Nâu caramel' => '#AF6E4D',

            // Màu xanh dương
            'Xanh' => '#007BFF',
            'Xanh da trời' => '#87CEEB',
            'Xanh dương' => '#1E90FF',
            'Xanh navy' => '#001F54',
            'Xanh cobalt' => '#0047AB',
            'Xanh đậm' => '#003366',
            'Xanh nhạt' => '#ADD8E6',
            'Xanh royal' => '#4169E1',
            'Xanh steel' => '#4682B4',
            'Xanh midnight' => '#191970',

            // Màu xanh lá
            'Xanh lá' => '#28a745',
            'Xanh lá đậm' => '#006400',
            'Xanh lá nhạt' => '#90EE90',
            'Xanh rêu' => '#4B5320',
            'Xanh oliu' => '#808000',
            'Xanh cỏ' => '#7CFC00',
            'Xanh forest' => '#228B22',
            'Xanh lime' => '#32CD32',
            'Xanh emerald' => '#50C878',

            // Màu xanh lục bảo và biến thể
            'Xanh mint' => '#98FF98',
            'Xanh ngọc' => '#00CED1',
            'Xanh pastel' => '#B2F9F1',
            'Xanh Nâu' => '#8FBC8F',
            'Xanh turquoise' => '#40E0D0',
            'Xanh aqua' => '#00FFFF',
            'Xanh teal' => '#008080',
            'Xanh cyan' => '#00FFFF',

            // Màu khác
            'Bạc' => '#C0C0C0',
            'Vàng gold' => '#FFD700',
            'Đồng' => '#B87333',
            'Hồng gold' => '#E8B4CB',
            'Xám than' => '#36454F',
            'Ivory' => '#FFFFF0',
            'Wheat' => '#F5DEB3',
            'Khaki' => '#F0E68C',
            'Salmon' => '#FA8072',
            'Peach' => '#FFCBA4',
        ];

        $color = trim($color);
        foreach ($colorMap as $key => $value) {
            if (mb_strtolower($key, 'UTF-8') === mb_strtolower($color, 'UTF-8')) {
                return $value;
            }
        }
        return '#CCCCCC';
    }
}
