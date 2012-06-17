<?php
require 'bitstream.php';

/**
*	Test the bit array using the binary data
*	10000000 11111111 110
*/
$array = new BitArray (19);
for ($i = 0; $i < 19; $i++)
{
	assert ($array[$i] == 0);
	if ($i == 0 || ($i > 7 && $i < 18))
	{
		$array[$i] = 1;
	}
	else
	{
		$array[$i] = 0;
	}
}

for ($i = 0; $i < 19; $i++)
{
	if ($i == 0 || ($i > 7 && $i < 18))
	{
		assert ($array[$i] == 1);
	}
	else
	{
		assert ($array[$i] == 0);
	}
}

//	double-check the internal representation 
$vals = unpack ("C*", $array->getData ());
assert ($vals[1] == 1);
assert ($vals[2] == 255);
assert ($vals[3] == 3);


//	exercise the BitStreamReader and BitStreamWriter a bit
$writer = new BitStreamWriter ();
foreach ($array as $bit)
{
	$writer->writeBit ($bit);
}

$reader = new BitStreamReader ($writer->getData ());
foreach ($array as $bit)
{
	assert ($bit == $reader->readBit ());
}

