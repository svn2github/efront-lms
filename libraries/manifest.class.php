<?php
/**
 *
 */

/**
 * EfrontManifestException class
 *
 * This class extends Exception class and is used to issue errors regarding IMS manifest
 * @author Antonellis Panagiotis <antonellis@efront.gr>
 * @version 1.0
 */
class EfrontManifestException extends Exception
{
    const NO_ERROR          = 0;
    const INVALID_ID        = 301;
    const LESSON_NOT_EXISTS = 302;
}

/**
 * EfrontManifest class
 *
 * This class represents an IMS manifest file.
 * A manifest file always correspond to a single lesson
 * @author Antonellis Panagiotis <antonelli@efront.gr>
 * @version 1.0
 */
class EfrontManifest
{
    /**
     * The lesson id of the corresponding lesson.
     *
     * @since 1.0
     * @var array
     * @access protected
     */
    protected $lesson_id;

    /**
     * The units. 
     *
     * @since 1.0
     * @var array
     * @access protected
     */
    protected $units = array();

    /**
     * The projects.
     *
     * @since 1.0
     * @var array
     * @access protected
     */
    protected $projects = array();

    /**
     * The tests.
     *
     * @since 1.0
     * @var array
     * @access protected
     */
    protected $tests = array();
    
    
    /**
     * The questions. 
     *
     * @since 1.0
     * @var array
     * @access protected
     */
    protected $questions = array();

    protected $unit_resources = array();
     
    protected $question_resources = array();
    
    protected $test_resources = array();
    
    protected $project_resources = array();
    
    /**
     * Class constructor
     *
     * This function is used to instantiate the class. The instatiation is done
     * based on a lesson id. If an entry with this id is not found in the database,
     * an eF_ManifestException is thrown.
     * <br/>Example:
     * <code>
     * $manifest = new EfrontManifest(32);                     //32 is a lesson id
     * </code>
     *
     * @param int $lesson_id The lesson id
     * @since 1.0
     * @access public
     */
    function __construct($lesson_id) {
        if (!eF_checkParameter($lesson_id, 'id')) {
            throw new eF_ManifestException(_INVALIDID, eF_ManifestException :: INVALID_ID);
        }
        $lesson = eF_getTableData("lessons", "*", "id = $lesson_id");
        if (sizeof($lesson) == 0) {
            throw new eF_ManifestException(_LESSONDOESNOTEXIST, eF_GroupException :: LESSON_NOT_EXISTS);
        } else {
            $this -> lesson_id  = $lesson_id;
            $content = new EfrontContentTree($this->lesson_id);
            $cunit = $content->getCurrentNode();
            $this->addUnit(new EfrontUnit($cunit[0]));
            $lunits = $content->getNextNodes();
            for ($i = 0; $i < sizeof($lunits); $i++){
                $this->addUnit(new EfrontUnit($lunits[$i]));
            }   
        }
    }


    /**
     * Function addUnit()
     *
     * This function is used to add a unit to the manifest file
     * <br/>Example:
     * <code>
     * $manifest = new EfrontManifest(32);    //32 is a lesson id
     * $unit = new EfrontUnit(10);      //10 is a content id 
     * $manifest->addUnit($unit);
     * </code>
     *
     * @param EfrontUnit $unit The unit to be added
     * @since 1.0
     * @access public
     */
    public function addUnit($unit){
        $this->units[$unit->offsetGet('id')] = $unit;
    }
    
    
     /**
     * Function addQuestion()
     *
     * This function is used to add a question to the manifest file
     * <br/>Example:
     * <code>
     * $manifest = new EfrontManifest(32);    //32 is a lesson id
     * $question = new Question(10);      //10 is a question id 
     * $manifest->addQuestioon($question);
     * </code>
     *
     * @param mixed $question The question object or the question id to be added
     * @since 1.0
     * @access public
     */
    function addQuestion($question){
        if ($question instanceOf Question){
            $this->questions[$question->question['id']] = $question;
        }
        else{
            $this->questions[$question] = new QuestionFactory($question);
        }
    }
    
        /**
     * Function addTest()
     *
     * This function is used to add a test to the manifest file
     * <br/>Example:
     * <code>
     * $manifest = new EfrontManifest(32);    //32 is a lesson id
     * $test = new EfrontTest(10);      //10 is a test id 
     * $manifest->addTest($test);
     * </code>
     *
     * @param EfrontTest $test The test to be added
     * @since 1.0
     * @access public
     */
    function addTest($test){
        $tests[] = $test;
    }
    
    function addProject($project_id){
        $pdata = ef_getTableData("projects", "*", "id=$project_id");
        $this->projects[$project_id] = $pdata[0];
    }

    function addUnitResource($filename, $uid){
        $this->unit_resources[$uid][] = $filename;
    }
    
    function addQuestionResource($filename, $qid){
        $this->question_resources[$qid][] = $filename;
    }
    
    function addTestResource($filename, $tid){
        $this->test_resources[$tid][] = $filename;
    }

    function addProjectResource($filename, $pid){
        $this->project_resources[$pid][] = $filename;
    }
    /**
     * Function toXML()
     *
     * This function is used to create the XML representation of the manifest file
     * <br/>Example:
     * <code>
     * $manifest = new EfrontManifest(32);    //32 is a lesson id
     * $xml = $manifest->toXML();
     * </code>
     *
     * @return string. The xml string
     * @since 1.0
     * @access public
     */
    public function toXML(){
        $xml =  '<?xml version="1.0" encoding="ISO-8859-7"?>' . "\n";
        $xml .= '<manifest identifier="SingleCourseManifest" version="1.1"
                xmlns="http://www.imsproject.org/xsd/imscp_rootv1p1p2"
                xmlns:adlcp="http://www.adlnet.org/xsd/adlcp_rootv1p2"
                xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
                xsi:schemaLocation="http://www.imsproject.org/xsd/imscp_rootv1p1p2 imscp_rootv1p1p2.xsd
                http://www.imsglobal.org/xsd/imsmd_rootv1p2p1 imsmd_rootv1p2p1.xsd
                http://www.adlnet.org/xsd/adlcp_rootv1p2 adlcp_rootv1p2.xsd">' . "\n";
        $xml .= "\t" . '<organizations default="org1">' . "\n";
        $xml .= "\t\t<organization identifier=\"Org\" structure=\"hierarchical\"><title>default</title>" . "\n";
        
        //write the units
        foreach ($this->units as $id => $unit){
            if (!$unit->isTest()){
                $xml .= "\t\t" . '<item identifier="content'.$id.'" identifierref="c'.$id.'">'."\n";
                $xml .= "\t\t\t<title>".$unit->offsetGet('name')."</title>\n";
                $preq = $unit->getPrerequisite();
                if ($preq){
                    $xml .= "\t\t\t".'<adlcp:prerequisites type="aicc_script">content'.$preq.'</adlcp:prerequisites>'."\n";
                }
                $xml .= "\t\t</item>\n";
            }
        }

        //write the questions
        foreach ($this->questions as $id => $question){
            $xml .= "\t\t" . '<item identifier="question'.$id.'" identifierref="q'.$id.'">'."\n";
            $xml .= "\t\t\t<title>question ".$question->question['id']."</title>\n";
        }

        //write the tests
        foreach ($this->tests as $id => $test){
            $xml .= "\t\t" . '<item identifier="test'.$id.'" identifierref="t'.$id.'">'."\n";
            $xml .= "\t\t\t<title>".$test->getUnit()->offsetGet['name']."</title>\n";
        }
        
        //write the projects
        foreach ($this->projects as $id => $project){
            $xml .= "\t\t" . '<item identifier="project'.$id.'" identifierref="p'.$id.'">'."\n";
            $xml .= "\t\t\t<title>".$project['title']."</title>\n";
        }
        
        $xml .= "\t\t".'</organization>'."\n\t".'</organizations>' . "\n";
        
        
        $xml .= "\t".'<resources>'."\n";
        
        //write the unit resources
        foreach ($this->unit_resources as $id => $filename){
            $xml .= "\t\t". '<resource identifier="u'.$id.' type="webcontent" adlcp:scormtype="sco" href="'.$filename.'">'."\n";
            $xml .= "\t\t\t". '<metadata></metadata>' . "\n";
            $xml .= "\t\t\t". '<file href="'.$filename.'"/>' . "\n";
            $xml .= "\t\t\t". '<dependency identifierref="dep_SPECIAL"/>' . "\n";
            $xml .= "\t\t". '</resource>';
        }
        
        //write the test resources
        foreach ($this->test_resources as $id => $filename){
            $xml .= "\t\t". '<resource identifier="u'.$id.' type="webcontent" adlcp:scormtype="sco" href="'.$filename.'">'."\n";
            $xml .= "\t\t\t". '<metadata></metadata>' . "\n";
            $xml .= "\t\t\t". '<file href="'.$filename.'"/>' . "\n";
            $xml .= "\t\t\t". '<dependency identifierref="dep_SPECIAL"/>' . "\n";
            $xml .= "\t\t". '</resource>';
        }
        
        //write the questions resources
        foreach ($this->question_resources as $id => $filename){
            $xml .= "\t\t". '<resource identifier="u'.$id.' type="webcontent" adlcp:scormtype="sco" href="'.$filename.'">'."\n";
            $xml .= "\t\t\t". '<metadata></metadata>' . "\n";
            $xml .= "\t\t\t". '<file href="'.$filename.'"/>' . "\n";
            $xml .= "\t\t\t". '<dependency identifierref="dep_SPECIAL"/>' . "\n";
            $xml .= "\t\t". '</resource>';
        }
        
        //write the projects resources
        foreach ($this->project_resources as $id => $filename){
            $xml .= "\t\t". '<resource identifier="u'.$id.' type="webcontent" adlcp:scormtype="sco" href="'.$filename.'">'."\n";
            $xml .= "\t\t\t". '<metadata></metadata>' . "\n";
            $xml .= "\t\t\t". '<file href="'.$filename.'"/>' . "\n";
            $xml .= "\t\t\t". '<dependency identifierref="dep_SPECIAL"/>' . "\n";
            $xml .= "\t\t". '</resource>';
        }
        
        $xml .= "\t".'</resources>'."\n";
        
        $xml .= '</manifest>';
        return $xml;
    }
}
?>