<?php

class hbar_value
{
	function hbar_value( $left, $right )
	{
		$this->left = $left;
		$this->right = $right;
	}
	
	function set_colour( $colour )
	{
		$this->colour = $colour;	
	}
}

class hbar
{
	function hbar( $colour )
	{
		$this->type      = "hbar";
		$this->values    = array();
		$this->set_colour( $colour );
	}
	
	function append_value( $v )
	{
		$this->values[] = $v;		
	}
	
	function set_colour( $colour )
	{
		$this->colour = $colour;	
	}
	
	function set_key( $text, $size )
	{
		$this->text = $text;
		$tmp = 'font-size';
		$this->$tmp = $size;
	}
}

