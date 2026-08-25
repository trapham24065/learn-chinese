<?php
$url = "https://raw.githubusercontent.com/gigacore/hsk-dictionaries/master/HSK%20Official%20With%20Definitions%202012%20L1%20to%20L6.csv";
$c = @file_get_contents($url);
if ($c) {
    echo "Success! Length: " . strlen($c) . "\n";
} else {
    echo "Failed\n";
}
