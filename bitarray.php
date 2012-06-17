<?php
/**
 * 	a simple, fixed-size bit array, backed internally by a string
 */
class BitArray implements ArrayAccess
{
	private $data = '';
	private $size = 0;
	
	/**
	 * 	create a new bit array of the given size
	 */
	public function __construct ($size)
	{
		$this->size = $size;
		$this->init ();
	}
	
	/**
	*	create a new BitArray from the bytes in the supplied string
	*/
	public static function load ($data)
	{
		$array = new BitArray (strlen($data) * 8);
		$array->data = $data;
		return $array;
	}
	
	/**
	*	retrieve the internal string representation of the binary data
	*/
	public function getData ()
	{
		return $this->data;
	}
	
	/**
	 * 	whether the given offset exists: ArrayAccess
	 */
	public function offsetExists ($offset)
	{
		if (!is_int ($offset))
		{
			return false;
		}
		return ($offset >= 0 && $offset < $this->size);
	}
	
	/**
	 * 	get the value at the given offset: ArrayAccess
	 */
	public function offsetGet ($offset)
	{
		list ($index, $bit) = $this->getPosition ($offset);
		return ($this->getNumericValueAt($index) & (1 << $bit)) ? 1 : 0;
	}
	
	/**
	 * 	set the value at the given offset: ArrayAccess
	 */
	public function offsetSet ($offset, $value)
	{
		$Xor = false;
		if (!$value)
		{
			if ($this->offsetGet ($offset))
			{
				//	later, we'll flip that bit using XOR
				$Xor = true;
			}
			else
			{
				//	it's already 0. we're done
				return;
			}
		}
		list ($index, $bit) = $this->getPosition ($offset);
		$byte = $this->getNumericValueAt ($index);
		$byte = ($Xor) 
			? $byte ^ (1 << $bit)
			: $byte | (1 << $bit);
		$this->data[$index] = pack ("C*", $byte);		
	}
	
	/**
	 * 	unset given offset: ArrayAccess
	 * 	NOTE: This deviates a little from the true ArrayAccess meaning,
	 *	b/c we have a value (0 or 1) at every bit no matter what. unsetting sets to 0
	 */
	public function offsetUnset ($offset)
	{
		$this->offsetSet ($offset, 0);
	}
	
	/**
	 * 	get the one-byte character that contains the given offset
	 */
	private function getPosition ($offset)
	{
		$quotient = (int) ($offset / 8);
		$bit = $offset % 8;
		return array ($quotient, $bit);
	}
	
	/**
	 * 	get the current numeric value of a byte in our string
	 */
	private function getNumericValueAt ($index)
	{
		$bytes = unpack ("C*", $this->data[$index]);
		return $bytes[1];
	}
	
	/**
	 * 	initialize the array with 0 for all bits
	 */
	private function init ()
	{
		$this->data = str_repeat ("\0", ceil ($this->size / 8));
	}
}

