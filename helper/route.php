<?php

function base_url($path = '') {
    return '/estu/' . ltrim($path, '/');
}

function asset($path = '') {
    return base_url('assets/' . $path);
}

function url($path = '') {
    return base_url($path);
}