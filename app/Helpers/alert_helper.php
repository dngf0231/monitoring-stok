<?php

if (!function_exists('flash_success')) {
    function flash_success($message)
    {
        session()->flash('alert', [
            'type' => 'success',
            'title' => 'Berhasil!',
            'text' => $message,
        ]);
    }
}

if (!function_exists('flash_error')) {
    function flash_error($message)
    {
        session()->flash('alert', [
            'type' => 'error',
            'title' => 'Perhatian!',
            'text' => $message,
        ]);
    }
}

if (!function_exists('flash_info')) {
    function flash_info($message)
    {
        session()->flash('alert', [
            'type' => 'info',
            'title' => 'Informasi!',
            'text' => $message,
        ]);
    }
}
