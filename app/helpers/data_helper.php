<?php

function formatDateTime($datetime)
{
    if (!$datetime) {
        return '';
    }

    return date('d M Y • H:i', strtotime($datetime));
}

function formatTime($datetime)
{
    if (!$datetime) {
        return '';
    }

    return date('H:i', strtotime($datetime));
}
