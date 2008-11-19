<?php

/**
 * Abstract class for creating and manipulating information
 *
 */
abstract class EfrontInformation
{

    /**
     * Metadata array.
     * 
     * This array holds the actual metadata, while the keys are the Dublin Core atributes.
     *
     * @var array
     * @since 3.5.0
     * @access public
     */
    public $metadataArray;
    
    /**
     * Metadata attributes
     * 
     * This array holds the metadata attributes along with their equivalent textual representation
     *
     * @var array
     * @since 3.5.0
     * @access public
     */
    public $metadataAttributes;

    /**
     * Instantiate class
     * 
     * This function is used to instantiate the class object. If $metadata is specified
     * then this array is used to populate the information fields, as long as the array
     * keys match the ones built in the object
     * <br/>Example:
     * <code>
     * $md = array('title' => 'A title', 'creator' => 'John Doe');
     * $metadata = new DublinCoreMetadata($md);			//Instantiate Dublin Core (DC) metadata representation
     * </code> 
     *
     * @param array $metadata The metadata/information to instantiate object with
     * @since 3.5.0
     * @access public
     */
    function __construct($metadata = false) {
        if ($metadata) {
            foreach ($this -> metadataAttributes as $attribute => $label) {
                if (isset($metadata[$attribute]) && $metadata[$attribute]) {
                    $this -> metadataArray[$attribute] = $metadata[$attribute];
                }
            }
        }
    }
    
    /**
     * Update information
     *
     * This function is used to update the information, based on the $attributes array.
     * This is an array of attribute/value pairs, where attributes are part of the information 
     * type (for example Dublin Core attributes).
     * <br/>Example:
     * <code>
     * $metadata = new DublinCoreMetadata($md);					//Instantiate Dublin Core (DC) metadata representation 
     * $metadata -> update(array('title' => 'new title'));		//Update a DC field  
     * $xml = $metadata -> toXML();								//Return the XML representation of the changed metadata
     * </code>
     *   
     * @param array $attributes The new attribute/value pairs
     * @since 3.5.0
     * @access public
     */
    public function update($attributes) {
        foreach ($attributes as $node => $value) {
            if (in_array($node, array_keys($this -> metadataAttributes))) {
                $this -> metadataArray[$node] = $value;
            }
        }
    }
        
    /**
     * Convert information to XML
     * 
     * This function is used to convert the current information (or metadata) attribute/value pairs
     * to the equivalent XML representation
     * <br/>Example:
     * <code>
     * $metadata = new DublinCoreMetadata($md);					//Instantiate Dublin Core (DC) metadata representation
     * $metadata -> update(array('title' => 'new title'));		//Update a DC field  
     * $xml = $metadata -> toXML();								//Return the XML representation of the changed metadata
     * </code>
     *
     * @return string The xml representation of the information
     * @since 3.5.0
     * @access public
     */
    public function toXML() {
        foreach ($this -> metadataArray as $attribute => $value) {
            $dc[] = "<$attribute>$value</$attribute>";
        }
        $xml = '<metadata>'.implode("\n", $dc).'</metadata>';
        
        return $xml;
    }
        
    /**
     * Convert XML to information
     * 
     * This function is used to convert an XML representation to inner information (or metadata) 
     * attribute/value pairs
     * 
     * <br/>Example:
     * <code>
     * $metadata = new DublinCoreMetadata();					//Instantiate Dublin Core (DC) metadata representation
     * $metadata -> fromXML($xml);								//Load metadata XML 
     * $metadata -> update(array('title' => 'new title'));		//Update a DC field  
     * $xml = $metadata -> toXML();								//Return the XML representation of the changed metadata
     * </code>
     *
     * @param string $metadataXML The xml information
     * @since 3.5.0
     * @access public
     */
    public function fromXML($xml) {
        $sxi = new SimpleXMLIterator($xml);
        foreach ($sxi as $node => $value) {
            if (in_array($node, array_keys($this -> metadataAttributes))) {
                $this -> metadataArray[$node] = (string)$value;
            }
        }
    }
        
    /**
     * Create a form for information manipulation
     * 
     * This function sets up required fields for manipulating information
     * <br/>Example:
     * <code>
     * $form 	 = new HTML_QuickForm("info_form", "post", basename($_SERVER['PHP_SELF']), null, null, true);
     * $metadata = new DublinCoreMetadata($md);					//Instantiate Dublin Core (DC) metadata representation
     * $metadata -> toHTMLQuickForm($form);						//Populate form fields
     * </code>
     *
     * @param HTML_QuickForm $form The form to add fields to
     * @since 3.5.0
     * @access public
     */
    public function toHTMLQuickForm(& $form) {
        foreach ($this -> metadataAttributes as $attribute => $label) {
            $form -> addElement('textarea', $attribute, $label, 'class = "inputText" style = "display:none;width:80%" id = "'.$attribute.'"');
            $form -> setDefaults(array($attribute => $this -> metadataArray[$attribute]));
        }
    }
    
    /**
     * Print HTML code for information
     * 
     * This function prints HTML code suitable for viewing and editing information.
     * <br/>Example:
     * <code>
     * $form 	 = new HTML_QuickForm("info_form", "post", basename($_SERVER['PHP_SELF']), null, null, true);
     * $metadata = new DublinCoreMetadata($md);					//Instantiate Dublin Core (DC) metadata representation
     * $metadata -> toHTML($form);								//Populate form fields 
     * </code>
     * The function calls toHTMLQuickForm() in order to build the form fields and then creates
     * the corresponding HTML. Since the editing makes extensive use of JavaScript, the $printJS
     * parameter is provided, so that this Javascript code may be optionally excluded. This way,
     * if we want to have many information instances in one page, only the last one should print 
     * the Javascript code.
     * <br/>Example:
     * <code>
     * $form 	 = new HTML_QuickForm("info_form", "post", basename($_SERVER['PHP_SELF']), null, null, true);
     * $metadata = new LearningObjectInformation($info);		//Instantiate Learning Object (LO) information representation
     * $metadata -> toHTML($form, false);						//Populate form fields but don't print JS 
     * $metadata = new DublinCoreMetadata($md);					//Instantiate Dublin Core (DC) metadata representation
     * $metadata -> toHTML($form);								//Populate form fields and print JS
     * </code>
     *
     * @param HTML_QuickForm $form The form to populate fields into
     * @param boolean $printJS Whether to print the accompanying Javascript functions
     * @param boolean $showTools Whether to show add/edit/delete handles  
     * @return string The HTML code
     * @since 3.5.0
     * @access public
     */
    public function toHTML(& $form, $printJS = true, $showTools = true) {
        $this -> toHTMLQuickForm($form);                                           //Assign proper elements to the form 
	    $renderer =& new HTML_QuickForm_Renderer_ArraySmarty($foo);                //Get a smarty renderer, only because it reforms the form in a very convenient way for printing html
        
	    $form          -> accept($renderer);                                       //Render the form
	    $formArray      = $renderer -> toArray();                                  //Get the rendered form fields
	    
	    $html = '
                <table style = "width:100%">';
	    foreach ($this -> metadataAttributes as $attribute => $label) {
	        $html .= '
	    			<tr><td class = "labelCell">'.$label.':&nbsp;</td>  	  
		            	<td class = "elementCell" style = "white-space:normal">
	                    	'.$formArray[$attribute]['html'];
	        if ($this -> metadataArray[$attribute]) {
	            $html .= '
                			<span style = "white-space:normal">'.$this -> metadataArray[$attribute].'</span>&nbsp;';
	            if ($showTools) {
	                $html .= '
	                		<img src = "images/16x16/edit.png"   id = "edit_'.$attribute.'"   alt = "'._EDIT.'"   title = "'._EDIT.'"   border = "0" onclick = "toggleFields(\''.$attribute.'\')" style = "vertical-align:middle;cursor:pointer;"/>
            				<img src = "images/16x16/check2.png" id = "submit_'.$attribute.'" alt = "'._SAVE.'"   title = "'._SAVE.'"   border = "0" onclick = "submitField(\''.$attribute.'\');" style = "vertical-align:middle;cursor:pointer;display:none;"/>
            				<img src = "images/16x16/delete.png" id = "delete_'.$attribute.'" alt = "'._DELETE.'" title = "'._DELETE.'" border = "0" onclick = "deleteField(\''.$attribute.'\')" 	style = "vertical-align:middle;cursor:pointer;"/>';
	            }	            
	        } else {
	            $html .= '
                			<span class = "emptyCategory inactiveElement">'._NOENTRYFOUNDFORTHEFIELD.' &quot;'.$label.'&quot;</span>&nbsp;';
	            if ($showTools) {
	                $html .= '
                			<img src = "images/16x16/add2.png"   id = "edit_'.$attribute.'"    alt = "'._ADD.'" title = "'._ADD.'" border = "0" onclick = "toggleFields(\''.$attribute.'\');" 	   style = "vertical-align:middle"/>
            				<img src = "images/16x16/check2.png" id = "submit_'.$attribute.'"    alt = "'._SAVE.'" title = "'._SAVE.'" border = "0" onclick = "submitField(\''.$attribute.'\');" style = "vertical-align:middle;cursor:pointer;display:none;"/>
            				<img src = "images/16x16/delete.png" id = "delete_'.$attribute.'" alt = "'._DELETE.'" title = "'._DELETE.'" border = "0" onclick = "deleteField(\''.$attribute.'\')" style = "vertical-align:middle;cursor:pointer;display:none;"/>';
	            }	            
	        }
	        $html .= '
					        <img src = "images/16x16/delete2.png" id = "cancel_'.$attribute.'" alt = "'._CANCEL.'" title = "'._CANCEL.'" border = "0" onclick = "toggleFields(\''.$attribute.'\')" style = "vertical-align:middle;cursor:pointer;display:none"/>
						</td></tr>';		                                					                                				  
	    }
	    $html .= '
                </table>';
	    
	    if ($printJS) {
	        $html .= "
				<script>
            	function deleteField(key) {
            		if (confirm('"._IRREVERSIBLEACTIONAREYOUSURE."')) {
            			$(key).value = '';
            			submitField(key, 'delete');
            		}
            	}
				
            	function toggleFields(key) {
            		$(key).next().toggle();
            		$(key).toggle();
            		$('edit_'+key).toggle();
            		$('submit_'+key).toggle();
            		$('delete_'+key).hide();
            		$('cancel_'+key).toggle();
				}
				
				function submitField(key, action) {
					action == 'delete' ? el = $('delete_'+key) : el = $('cancel_'+key);
                    var url =  '".$_SERVER['REQUEST_URI']."&postAjaxRequest=1&dc='+key+'&value='+encodeURIComponent($(key).value);

                    var img = new Element(\"img\", {'src': 'images/others/progress1.gif'});                            
                    img.clonePosition(el);
                    img.setStyle({'position': 'absolute', 'left': parseInt(img.style.left) + 22 + 'px'});	//16px is the image size, and we add a 6px-wide space
                    el.parentNode.appendChild(img);
                        new Ajax.Request(url, {
                                method:'get',
                                asynchronous:true,
                                onFailure: function (transport) {
                                	img.hide();
                                    img.clonePosition(el);
                                    img.setStyle({'position': 'absolute', 'left': parseInt(img.style.left) + 22 + 'px'});	//16px is the image size, and we add a 6px-wide space
                                    img.setAttribute('src', 'images/16x16/delete2.png');
                                    img.setAttribute('title', transport.responseText);
                                    new Effect.Appear(img.identify());
                                    window.setTimeout('Effect.Fade(\"'+img.identify()+'\")', 10000);
	    						},
                                onSuccess: function (transport) {
                                    img.hide();
                                    if (action == 'delete') {
                                    	$(key).next().update('"._NOENTRYFOUNDFOR." "._FIELD."').addClassName('inactiveElement').addClassName('emptyCategory');
                                    	$('delete_'+key).hide();
                                    	$('edit_'+key).src = 'images/16x16/add2.png';                                                        
                                        el = $('edit_'+key);
                                    } else {
                                        toggleFields(key);
                                    	$(key).next().update(transport.responseText).removeClassName('inactiveElement').removeClassName('emptyCategory');
                                    	$('delete_'+key).show();
                                    	$('edit_'+key).src = 'images/16x16/edit.png';
                                        el = $('delete_'+key);
                                    }
                                    img.clonePosition(el);
                                    img.setStyle({'position': 'absolute', 'left': parseInt(img.style.left) + 22 + 'px'});	//16px is the image size, and we add a 6px-wide space
                                    img.setAttribute('src', 'images/16x16/check.png');
                                    new Effect.Appear(img.identify());
                                    window.setTimeout('Effect.Fade(\"'+img.identify()+'\")', 2500);
                                }
                        });
                }
            	</script>";
	    }
	    
	    return $html;
    }
}

/**
 * Class for manipulating Dublin Core metadata
 *
 */
class DublinCoreMetadata extends EfrontInformation
{
    /**
     * Dublin Core attributes for XML metadata
     * 
     * This array holds the Dublin Core attributes as keys and their equivalent textual 
     * representations as values.
     *
     * @var array
     * @since 3.5.0
     * @access public
     * @static
     */
    public static $dublinCoreAttributes = array('title'       => _DCTITLE,
            								    'creator' 	  => _DCCREATOR, 
            								    'subject' 	  => _DCSUBJECT, 
            								    'description' => _DCDESCRIPTION, 
            								    'publisher'   => _DCPUBLISHER, 
            								    'contributor' => _DCCONTRIBUTOR, 
            								    'date' 		  => _DCDATE,
            								    'type' 		  => _DCTYPE, 
            								    'format' 	  => _DCFORMAT, 
            								    'identifier'  => _DCIDENTIFIER, 
            								    'source' 	  => _DCSOURCE, 
            								    'language' 	  => _DCLANGUAGE, 
            								    'relation' 	  => _DCRELATION, 
            								    'coverage' 	  => _DCCOVERAGE, 
            								    'rights' 	  => _DCRIGHTS);
        
    /**
     * Instantiate class
     * 
     * This is the class constructor for Dublin Core metadata. If the $metadata array
     * is specified, then the metadata are initialized to these values.
     * <br/>Example:
     * <code>
     * $dc = new DublinCoreMetadata();									//Instantiate raw DC metadata
     * $dc = new DublinCoreMetadata(array('title' => 'a title'));		//Instantiate DC metadata using array of values
     * </code>
     * 
     * @param array $metadata An optional metadata array
     * @since 3.5.0
     * @access public
     */
    function __construct($metadata = false) {
        $this -> metadataAttributes = self :: $dublinCoreAttributes;
        parent :: __construct($metadata);
    }

	/**
     * Convert information to XML
     * 
     * This function is used to convert the current information (or metadata) attribute/value pairs
     * to the equivalent XML representation
     * <br/>Example:
     * <code>
     * $metadata = new DublinCoreMetadata();					//Instantiate Dublin Core (DC) metadata representation
     * $metadata -> fromXML($xml);								//Load metadata XML 
     * $metadata -> update(array('title' => 'new title'));		//Update a DC field  
     * $xml = $metadata -> toXML();								//Return the XML representation of the changed metadata
     * </code>
     *
     * @param string $prefix Whether to prepend a prefix to the metadata attribute names, such as 'dc:'
     * @return string The xml representation of the information
     * @since 3.5.0
     * @access public
     */
    public function toXML($prefix = '') {
        foreach ($this -> metadataArray as $attribute => $value) {
            $dc[] = "<".$prefix.$attribute.">$value</".$prefix.$attribute.">";
        }
        $xml = '<metadata>'.implode("\n", $dc).'</metadata>';
        
        return $xml;
    }
    
    /**
     * Convert XML to information
     * 
     * This function is used to convert an XML representation to inner information (or metadata) 
     * attribute/value pairs
     * 
     * <br/>Example:
     * <code>
     * $metadata = new DublinCoreMetadata();					//Instantiate Dublin Core (DC) metadata representation
     * $metadata -> fromXML($xml);								//Load metadata XML 
     * $metadata -> update(array('title' => 'new title'));		//Update a DC field  
     * $xml = $metadata -> toXML();								//Return the XML representation of the changed metadata
     * </code>
     *
     * @param string $metadataXML The xml information
     * @since 3.5.0
     * @access public
     */
    public function fromXML($xml) {
        $sxi = new SimpleXMLIterator($xml);
        foreach ($sxi as $node => $value) {
            if (in_array($node, array_keys($this -> metadataAttributes))) {
                $this -> metadataArray[$node] = (string)$value;
            }
        }
    }
    
}

/**
 * Class for manipulating Learning Object information
 *
 */
class LearningObjectInformation extends EfrontInformation
{
    /**
     * Learning object information
     * 
     * This array holds attributes that describe a learning object. Keys are the attributes and values are
     * their corresponding text.
     *
     * @var array
     * @since 3.5.0
     * @access public 
     * @static
     */
    public static $learningObjectAttributes = array('general_description' => _GENERALDESCRIPTION,
                                                    'objectives'          => _OBJECTIVES,
                                                    'assessment'          => _ASSESSMENT,
                                                    'lesson_topics'       => _TOPICS,
                                                    'resources'           => _RESOURCES,
                                                    'other_info'          => _OTHERINFO);
    
        
    /**
     * Instantiate class
     * 
     * This is the class constructor for Learning Object (LO) information. If the $metadata array
     * is specified, then the metadata/information are initialized to these values.
     * <br/>Example:
     * <code>
     * $dc = new LearningObjectInformation();									//Instantiate raw LO metadata
     * $dc = new LearningObjectInformation(array('title' => 'a title'));		//Instantiate LO metadata using array of values
     * </code>
     * 
     * @param array $metadata An optional metadata/information array
     * @since 3.5.0
     * @access public
     */
    function __construct($metadata) {
        $this -> metadataAttributes = self :: $learningObjectAttributes;
        parent :: __construct($metadata);
    }

	/**
     * Convert information to XML
     * 
     * This function is used to convert the current information (or metadata) attribute/value pairs
     * to the equivalent XML representation
     * <br/>Example:
     * <code>
     * $metadata = new LearningObjectInformation();				//Instantiate Dublin Core (DC) metadata representation
     * $metadata -> fromXML($xml);								//Load metadata XML 
     * $metadata -> update(array('title' => 'new title'));		//Update a DC field  
     * $xml = $metadata -> toXML();								//Return the XML representation of the changed metadata
     * </code>
     *
     * @return string The xml representation of the information
     * @since 3.5.0
     * @access public
     */
    public function toXML() {
        foreach ($this -> metadataArray as $attribute => $value) {
            $dc[] = "<$attribute>$value</$attribute>";
        }
        $xml = '<metadata>'.implode("\n", $dc).'</metadata>';
        
        return $xml;
    }

	/**
     * Convert XML to information
     * 
     * This function is used to convert an XML representation to inner information (or metadata) 
     * attribute/value pairs
     * 
     * <br/>Example:
     * <code>
     * $metadata = new DublinCoreMetadata();					//Instantiate Dublin Core (DC) metadata representation
     * $metadata -> fromXML($xml);								//Load metadata XML 
     * $metadata -> update(array('title' => 'new title'));		//Update a DC field  
     * $xml = $metadata -> toXML();								//Return the XML representation of the changed metadata
     * </code>
     *
     * @param string $metadataXML The xml information
     * @since 3.5.0
     * @access public
     */
    public function fromXML($xml) {
        $sxi = new SimpleXMLIterator($xml);
        foreach ($sxi as $node => $value) {
            if (in_array($node, array_keys($this -> metadataAttributes))) {
                $this -> metadataArray[$node] = (string)$value;
            }
        }
    }
    
}



?>