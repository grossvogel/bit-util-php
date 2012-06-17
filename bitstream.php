<?php
require_once 'bitarray.php';

/**
*	a mechanism for writing a stream if bits into a string, which can then be easily transmistted or stored
*/
class BitStreamWriter
{
	private $data = "";
	private $workingByte = null;
	private $cursor = 0;
	
	/**
	*	initialize be creating our working byte / buffer
	*/
	public function __construct ()
	{
		$this->workingByte = new BitArray (8);
	}
	
	/**
	 * 	convert a string of zeroes and ones into a binary representation
	 */
	public function writeString ($bitStr)
	{
		for ($i = 0; $i < strlen ($bitStr); $i++)
		{
			$this->writeBit ((bool) $bitStr[$i]);
		}
	}
	
	/**
	 * 	write a bit. 1 if $bit is true
	 */
	public function writeBit ($bit)
	{
		$this->workingByte[$this->cursor] = $bit;
		$this->cursor++;
		if ($this->cursor > 7)
		{
			$this->data .= $this->workingByte->getData ();
			$this->workingByte = new BitArray(8);
			$this->cursor = 0;
		}
	}
	
	/**
	 * 	when the data is accessed, make sure to include any data
	 * 	byte in $this->workingByte
	 */
	public function getData ()
	{
		$data = $this->data;
		if ($this->cursor > 0)
		{
			$data .= $this->workingByte->getData ();
		}
		return $data;
	}
}

/**
*	turns string data into a stream of bits
*/
class BitStreamReader
{
	private $dataArray = null;
	private $cursor = 0;
	
	/**
	*	To initialize, provide string data containing the bits
	*/
	public function __construct ($data)
	{
		$this->dataArray = BitArray::load ($data);
	}
	
	/**
	*	read one bit at a time from the buffer, returning null for EOF
	*/
	public function readBit ()
	{
		if ($this->isEOF ())
		{
			return null;
		}
		else
		{
			$bit = $this->dataArray[$this->cursor];
			$this->cursor++;
			return $bit;
		}
	}
	
	/**
	*	whether we've reached the end of our data
	*/
	public function isEOF ()
	{
		return !$this->dataArray->offsetExists ($this->cursor);
	}
}
