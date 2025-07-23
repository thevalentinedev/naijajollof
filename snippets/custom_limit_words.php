<?php
// Utility function to limit a string to a certain number of words.
function custom_limit_words($string, $limit) {
    $words = explode(' ', $string);
    return implode(' ', array_slice($words, 0, $limit));
} 