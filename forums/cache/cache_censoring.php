<?php

define('PUN_CENSOR_LOADED', 1);

$search_for = array (
  0 => '%(?<=[^\\p{L}\\p{N}])(damn[\\p{L}\\p{N}]*?)(?=[^\\p{L}\\p{N}])%iu',
  1 => '%(?<=[^\\p{L}\\p{N}])(fuck[\\p{L}\\p{N}]*?)(?=[^\\p{L}\\p{N}])%iu',
  2 => '%(?<=[^\\p{L}\\p{N}])(shit[\\p{L}\\p{N}]*?)(?=[^\\p{L}\\p{N}])%iu',
  3 => '%(?<=[^\\p{L}\\p{N}])(crap[\\p{L}\\p{N}]*?)(?=[^\\p{L}\\p{N}])%iu',
  4 => '%(?<=[^\\p{L}\\p{N}])(cunt[\\p{L}\\p{N}]*?)(?=[^\\p{L}\\p{N}])%iu',
  5 => '%(?<=[^\\p{L}\\p{N}])(ass)(?=[^\\p{L}\\p{N}])%iu',
);

$replace_with = array (
  0 => '[censored]',
  1 => '[censored]',
  2 => '[censored]',
  3 => '[censored]',
  4 => '[censored]',
  5 => '[censored]',
);

?>