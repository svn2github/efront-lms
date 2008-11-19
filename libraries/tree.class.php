<?php

/**
 * Efront tree exceptions
 *
 */
class EfrontTreeException extends Exception
{
    const NODE_NOT_EXISTS    = 1001;    
}

/**
 * Tree class
 *
 */
abstract class EfrontTree
{
    /**
     * The tree object.
     *
     * @since 3.5.0
     * @var RecursiveArrayIterator
     * @access public
     */
    public $tree;

    /**
     * Get the first tree node
     * 
     * This function returns the array corresponding to the first node in the tree.
     * <br/>Example:
     * <code>
     * $content = new EfrontContentTree(4);					//Create the content tree for lesson with id 4
     * $firstNode = $content -> getFirstNode();				//$firstNode now holds the array of the tree's first node
     * </code>
     * 
     * @param ArrayIterator $iterator An specific iterator to use, instead of the default one
     * @return array The first node array
     * @since 3.5.0
     * @access public
     */
    public function getFirstNode($iterator = false) {
       if (!$iterator) {
           $iterator = new EfrontNodeFilterIterator(new RecursiveIteratorIterator(new RecursiveArrayIterator($this -> tree), RecursiveIteratorIterator :: SELF_FIRST));    //Create a new iterator, so that the internal iterator pointer is not reset
       }
       $iterator -> rewind();                            //Initialize iterator
        
       return $iterator -> current();
    }	

    /**
     * Get the last tree node
     * 
     * This function returns the array corresponding to the last node in the tree.
     * It does not alter the inner tree pointer
     * <br/>Example:
     * <code>
     * $content = new EfrontContentTree(4);					//Create the content tree for lesson with id 4
     * $lastNode = $content -> getLastNode();				//$lastNode now holds the array of the content tree's last node
     * </code>
     * 
     * @param ArrayIterator $iterator An specific iterator to use, instead of the default one
     * @return array The last node array
     * @since 3.5.0
     * @access public
     */
    public function getLastNode($iterator = false) {
       if (!$iterator) {
           $iterator = new EfrontNodeFilterIterator(new RecursiveIteratorIterator(new RecursiveArrayIterator($this -> tree), RecursiveIteratorIterator :: SELF_FIRST));
       }
       foreach ($iterator as $lastNode);    //We create new iterators (in order to leave unchanged the internal tree pointer) and advance them to the end of the tree
       return $lastNode;
    }
    
    /**
     * Get the next node in the tree
     * 
     * The function returns the specified node's next node
     * <br/>Example:
     * <code>
     * $content = new EfrontContentTree(4);				//Create the content tree for lesson with id 4
     * $node = $content -> getFirstNode();				//$node now holds the array of the content tree's first node
     * $node = $content -> getNextNode(32);				//$node now holds the array of node's 32 next node 
     * </code>
     * if the specified node is the last one, false is returned
     * 
     * @param int $queryNode A node id, to get its next node
     * @param ArrayIterator $iterator An specific iterator to use, instead of the default one
     * @return array The next node array
     * @since 3.5.0
     * @access public
     */
    public function getNextNode($queryNode, $iterator = false) {
        $queryNode instanceof ArrayObject ? $nodeId = $queryNode['id'] : $nodeId = $queryNode;
        if (!$iterator) {
            $iterator = new EfrontNodeFilterIterator(new RecursiveIteratorIterator(new RecursiveArrayIterator($this -> tree), RecursiveIteratorIterator :: SELF_FIRST));    //Create iterators for the tree
        }
        $iterator -> rewind();                                                                 //Initialize iterator
        while ($iterator -> valid() && $iterator -> key() != $nodeId) {                        //Advance iterator until we reach the designated node
            $iterator -> next();
        }
         
        if ($iterator -> valid()) {                                                            //If we found the designated node, advance the iterator once more to get its next node
            $iterator -> next();
        } else {
            throw new EfrontTreeException(_NODEDOESNOTEXIST.': '.$nodeId, EfrontTreeException :: NODE_NOT_EXISTS);
        }
        
        if ($iterator -> valid()) {                                                            //If there is a next node, get it
            $nextNode = $iterator -> current();
            if (!$queryNode) {
                $this -> currentNodeId = $nextNode['id'];                                      //If a $queryNode was not specified, we must advance the internal pointer, so assign the current node pointer to the next node
            }

            return $nextNode;
        } else {
            return false;
        }
    }

    /**
     * Get the previous node in the tree
     * 
     * The function returns the specified node's previous node
     * <br/>Example:
     * <code>
     * $content = new EfrontContentTree(4);				//Create the content tree for lesson with id 4
     * $node = $content -> getFirstNode();				//$node now holds the array of the content tree's first node
     * $node = $content -> getNextNode();				//$node now holds the array of the content tree's second node 
     * $node = $content -> getPreviousNode(32);			//$node now holds the array of node's 32 previous node
     * $node = $content -> getPreviousNode();			//$node now holds the array of the content tree's first node 
     * </code>
     * If the specified node is the last one, false is returned
     * 
     * @param int $queryNode A node id to get its previous node 
     * @param ArrayIterator $iterator An specific iterator to use, instead of the default one
     * @return array The previous node array
     * @since 3.5.0
     * @access public
     */
    public function getPreviousNode($queryNode, $iterator = false) {
        $queryNode instanceof ArrayObject ? $nodeId = $queryNode['id'] : $nodeId = $queryNode;
                
        if (!$iterator) {
            $iterator = new EfrontNodeFilterIterator(new RecursiveIteratorIterator(new RecursiveArrayIterator($this -> tree), RecursiveIteratorIterator :: SELF_FIRST));    //Create iterators for the tree
        }
        $iterator -> rewind();                                                                 //Initialize iterator
        while ($iterator -> valid() && $iterator -> key() != $nodeId) {                        //Advance iterator until we reach the designated node
            $previousNode = $iterator -> current();
            $iterator -> next();
        }
         
        if ($iterator -> valid()) {                                                            //If we found the designated node, $previousNode now holds the previous node
            if (!isset($previousNode)) {                                                       //The designated node was apparently the first one, so return false
                return false;
            } else {
	            if (!$queryNode) {
	                $this -> currentNodeId = $previousNode['id'];                                  //If a $queryNode was not specified, we must set the internal pointer to the previous node we just found
	            }
	            //$previousNode = $this -> filterOutChildren(new RecursiveArrayIterator($previousNode));     //Cut off node's children
	            return $previousNode;
            }
        } else {
            throw new EfrontTreeException(_NODEDOESNOTEXIST.': '.$nodeId, EfrontTreeException :: NODE_NOT_EXISTS);
        }
    }

    /**
     * Seeks designated node
     * 
     * This function seeks the node with the designatd id
     * in the content tree and returns its properties
     * <br/>Example:
     * <code>
     * $content = new EfrontContentTree(4);				//Create the content tree for lesson with id 4
     * $node = $content -> seekNode(43);				//Returns node with id 43
     * </code>
     * 
     * @param int $queryNode The node id
     * @return array The node array
     * @since 3.5.0
     * @access public
     */
    public function seekNode($queryNode) {
        $iterator = new EfrontNodeFilterIterator(new RecursiveIteratorIterator(new RecursiveArrayIterator($this -> tree), RecursiveIteratorIterator :: SELF_FIRST));    //Create iterators for the tree
        $iterator -> rewind();                                                                 //Initialize iterator
        while ($iterator -> valid() && $iterator -> key() != $queryNode) {                        //Advance iterator until we reach the designated node
            $iterator -> next();
        }
         
        if ($iterator -> valid()) {
            return $iterator -> current();
        } else {
            throw new EfrontTreeException(_NODEDOESNOTEXIST.': '.$queryNode, EfrontTreeException :: NODE_NOT_EXISTS);
        }
    }

    /**
     * Get node children
     * 
     * This function returns the tree branch holdning the designated node
     * and its children
     * <br/>Example:
     * <code>
     * $content = new EfrontContentTree(4);		//Create the content tree for lesson with id 4
     * $content -> getChildren(54);				//Get node's 54 children
     * </code>
     * The function uses RecursiveArrayIterator :: getChildren() to get the branch
     * 
     * @param mixed $node Either the node id or an EfrontNode object
     * @return RecursiveArrayIterator The tree branch
     * @since 3.5.0
     * @access public
     * @see RecursiveArrayIterator :: getChildren()
     */   
    public function getNodeChildren($node) {
        $node instanceof ArrayObject ? $nodeId = $node['id'] : $nodeId = $node;
        
        $iterator = new EfrontNodeFilterIterator(new RecursiveIteratorIterator(new RecursiveArrayIterator($this -> tree), RecursiveIteratorIterator :: SELF_FIRST));    //Create iterators for the tree
        $iterator -> rewind();                                                                 //Initialize iterator
        while ($iterator -> valid() && $iterator -> key() != $nodeId) {                        //Advance iterator until we reach the designated node
            $iterator -> next();
        }
         
        if ($iterator -> valid()) {                                                            //If we found the designated node
            return $iterator -> getChildren();
        } else {
            throw new EfrontTreeException(_NODEDOESNOTEXIST.': '.$nodeId, EfrontTreeException :: NODE_NOT_EXISTS);
        }        
    }

    /**
     * Get node ancestors
     * 
     * This function is used to get the node ancestors
     * <br/>Example:
     * <code>
     * </code>
     * 
     * @param mixed $node Either the node id or an EfrontNode object
     * @return array An array of array obhects, ancestors of the 
     */
    public function getNodeAncestors($node) {
        $node instanceof ArrayObject ? $nodeId = $node['id'] : $nodeId = $node;
        
        $iterator = new EfrontNodeFilterIterator(new RecursiveIteratorIterator($this -> tree, RecursiveIteratorIterator :: SELF_FIRST));    //Get an iterator for the current tree. This iterator returns only whole node arrays and not node members separately (such as id, timestamp etc)
        $iterator -> rewind();                                                //Initialize iterator

        while ($iterator -> valid() && $iterator -> key() != $nodeId) {        //Forward iterator index until you reach the designated element, which has an index equal to the node id that will be removed
		    $iterator -> next();
		}
		
		$parents[] = $this -> filterOutChildren($iterator -> current());

		for ($i = $iterator -> getDepth(); $i > 0; $i--) {                   //Climb up the iterators and keep ancestor ids
		    $parents[] = $this -> filterOutChildren($iterator -> getSubIterator($i));        //Get the corresponding nodes
		}
		
		return $parents;
    }

    /**
     * Filter out children
     * 
     * This function removes the children from the designated tree node
     * 
     * @param array $branch The tree branch
     * @return array The node without the children
     * @since 3.5.0
     * @access protected
     */
    protected function filterOutChildren($branch) {
        foreach (new EfrontAttributesOnlyFilterIterator(new RecursiveArrayIterator($branch)) as $key => $value) {        //Keep only associative array keys and drop numerical, which hold the children
            $node[$key] = $value;
        }
        return $node;
    }
    
    /**
     * Return a flat representation of the tree
     * 
     * This function is used to return a serial flat representation of the tree.
     * The nodes are presented ased on the proper succession, using depth-first
     * search. 
     * <br/>Example:
     * $content = new EfrontContentTree(4);					//Create the content tree for lesson with id 4
     * $array = $content -> getFlatTree();					//$array now is an array of nodes 
     * <code>
     * 
     * @return $array The flat tree representation
     * @since 3.5.0
     * @access public
     */
    public function getFlatTree() {
       $flatTree = array();
       foreach (new EfrontNodeFilterIterator(new RecursiveIteratorIterator(new RecursiveArrayIterator($this -> tree), RecursiveIteratorIterator :: SELF_FIRST)) as $key => $value) {    //We create new iterators (in order to leave unchanged the internal tree pointer) and advance them to the end of the tree
           $flatTree[] = $this -> filterOutChildren($value);
       }
       return $flatTree;
               
    }
    
    /**
     * Insert node to the tree
     *
     * @param mixed $node
     * @param mixed $parentNode
     * @param mixed $previousNode
     * @since 3.5.0
     * @access public
     * @abstract 
     */
	abstract public function insertNode($node, $parentNode = false, $previousNode = false);

	/**
	 * Remove node from tree
	 *
	 * @param mixed $node
     * @since 3.5.0
     * @access public
     * @abstract 
	 */
	abstract public function removeNode($node);    
    
	/**
	 * Reset/initialize tree
	 * 
     * @since 3.5.0
     * @access public
     * @abstract 
	 */
	abstract function reset();

    
}


/**
 * Filter out subarrays and numerical indices
 */
class EfrontAttributesOnlyFilterIterator extends FilterIterator
{
    /**
     * Filter out subarrays and numerical indices
     *
     * @return boolean
     */
    function accept() {
        return !is_numeric($this -> key()) && !is_array($this -> current());
    }    
}

/**
 * 
 */
class EfrontAttributeFilterIterator extends FilterIterator
{
	/**
	 * Filter mode
	 *
	 * @var mixed filter mode
	 */
    protected $mode;
    
    /**
     * Constructor
     *
     * @param unknown_type $it
     * @param unknown_type $mode
     */
    function __construct($it, $mode = false) {
        parent::__construct($it); 
        is_array($mode) ? $this -> mode = $mode : $this -> mode = array($mode);
    }
    
    /**
     * Keep only specified attributes
     *
     * @return unknown
     */
    function accept() {
        return in_array($this -> key(), $this -> mode);
    }    
}


/**
 * 
 */
class EfrontNodeFilterIterator extends FilterIterator
{
    protected $mode;
    protected $evaluate;
    
    /**
     * $evaluate sets if the mdoe will be evaluated to true or false
     */
    function __construct($it, $mode = false, $evaluate = true) {
        parent::__construct($it); 
        $this -> mode     = $mode;
        $this -> evaluate = $evaluate;
    }

    function accept() {
        if ($this -> mode) {
            $accepted = true;
            $current  = $this -> current();
            foreach ($this -> mode as $key => $value) {
                if (!isset($current[$key]) || $current[$key] != $value) {
                    $this -> evaluate ? $accepted = false : $accepted = true;
                }
            }
            return $this -> current() instanceof ArrayObject & $accepted;
        } else {
            return $this -> current() instanceof ArrayObject;
        }
    }
}



?>