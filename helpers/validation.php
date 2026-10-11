<?php
function positive_int(mixed $value): ?int {
    $number = filter_var($value, FILTER_VALIDATE_INT);
    return ($number !== false && $number > 0) ? $number : null;
}
function nonnegative_money(mixed $value): ?float {
    if (!is_numeric($value)) return null;
    $number = (float)$value;
    return ($number >= 0) ? $number : null;
}
