<?php

function rfbp_make_clickable($text, $target)
{
    return preg_replace('@(?<![.*">])\b(?:(?:https?|ftp|file)://|[a-z]\.)[-A-Z0-9+&#/%=~_|$?!:,.]*[A-Z0-9+&#/%=~_|$]@i', '<a href="\0" target="'.$target.'">\0</a>', $text);
}

function rfbp_time_ago($timestamp) 
{
	$diff = time() - (int) $timestamp;

		if ($diff == 0) 
			return 'just now';

		$intervals = array
		(
			1                   => array('year',    31556926),
			$diff < 31556926    => array('month',   2628000),
			$diff < 2629744     => array('week',    604800),
			$diff < 604800      => array('day',     86400),
			$diff < 86400       => array('hour',    3600),
			$diff < 3600        => array('minute',  60),
			$diff < 60          => array('second',  1)
			);

		$value = floor($diff/$intervals[1][1]);
		return $value.' '.$intervals[1][0].($value > 1 ? 's' : '').' ago';
}