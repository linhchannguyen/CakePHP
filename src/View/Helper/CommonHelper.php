<?php

if (!function_exists('randomStringUnique')) {
    function randomStringUnique($length)
    {
        if ($length%2 === 0) {
            return bin2hex(random_bytes($length/2));
        }

        $key = '';
        $keys = array_merge(range(0, 9), range('a', 'z'));

        for ($i = 0; $i < $length; $i++) {
            $key .= $keys[array_rand($keys)];
        }
        return $key;
    }
}
