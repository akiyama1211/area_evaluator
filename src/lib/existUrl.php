<?php
function existUrl(string $url) {
    $response = @file_get_contents($url, false, NULL, 0, 1);
    if ($response !== false) {
        return true;
    } else {
        return false;
    }
}
