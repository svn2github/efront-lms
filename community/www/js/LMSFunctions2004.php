<?php


session_cache_limiter('none');          //Initialize session
session_start();

$path = "../../libraries/";                //Define default path

/** The configuration file.*/
require_once $path."configuration.php";

$path = "../../www/";                //Define default path

/*
require_once $path."sequencing_classes.php";
require_once $path."sequencing.php";
 */

/*These lines read SCO data for this student and pass them to the javascript code through the LMSToSCOValues variable*/

$SCOState = 'var SCOState = new Array();';

$result = eF_getTableData("scorm_data_2004", "*", "users_LOGIN = '".$_SESSION['s_login']."' AND content_ID = '".$_GET['view_unit']."'");

$entry_info = $result[0]['entry'];

if ($entry_info == 'resume') {	
    sizeof($result) ? $LMSToSCOValues = $result[0] : $LMSToSCOValues = array();
} else {
    $LMSToSCOValues['total_time'] = '0000:00:00';
}

/*These lines read global SCO data and pass them to the javascript through the $SCOValues variable.*/
$result = eF_getTableData("scorm_data_2004", "*", "users_LOGIN is null AND content_ID = '".$_GET['view_unit']."'");
sizeof($result) ? $SCOValues = $result[0] : $SCOValues = array();


foreach ($SCOValues as $key => $value)
{

	if(is_null($value))
		$SCOState .= "SCOState['$key'] = null;";//echo "alert('LMS User Set: SCOState[$key] = $value');";
	else		
		$SCOState .= "SCOState['$key'] = '".addslashes($value)."';";//echo "alert('LMS User Set: SCOState[$key] = $value');";

}

$result = eF_getTableData("scorm_sequencing_limit_conditions", "*", "content_ID=".$_GET['view_unit']);	

if($result[0]['attempt_absolute_duration_limit'])
{
	$SCOState .= "SCOState['maxtimeallowed'] = '".$result[0]['attempt_absolute_duration_limit']."';";//echo "alert('LMS Set: SCOState[$key] = $value');";
	//echo "alert('test:". $result[0]['min_progress_measure'] ."');";
}


foreach ($LMSToSCOValues as $key => $value)
{
    //Score must be set to zero each time the student visits the SCO; The professor has other ways to see t
    if ($key == 'score') {
        $value = 0;
	}
	if(is_null($value))
		$SCOState .= "SCOState['$key'] = null;";//echo "alert('LMS User Set: SCOState[$key] = $value');";
	else		
		$SCOState .= "SCOState['$key'] = '".addslashes($value)."';";//echo "alert('LMS User Set: SCOState[$key] = $value');";
}


$SCOState .= "SCOState['progress_measure'] = null;";
$SCOState .= "SCOState['completion_status'] = null;";
$SCOState .= "SCOState['success_status'] = null;";
$SCOState .= "SCOState['score_scaled'] = null;";
$SCOState .= "SCOState['completion_threshold'] = null;";





$currentContent = new EfrontContentTree($_SESSION['s_lessons_ID']);
$scoBranch = array();
$scoBranch[$_SESSION['organization']] = $currentContent->tree->offsetGet($_SESSION['organization']);
$scoContent = new EfrontContentTreeSCORM($scoBranch, $_GET['view_unit']);

error_reporting(E_ERROR);

/*
//Caching causes some problems with data interchange
ob_start ("ob_gzhandler");
header("Content-type: text/javascript; charset: UTF-8");
header("Cache-Control: must-revalidate");
$offset = 60 * 60 ;
$ExpStr = "Expires: " .
gmdate("D, d M Y H:i:s",
time() + $offset) . " GMT";
header($ExpStr);
 */


echo $SCOState;

?>


//for (x in SCOState) {
//  alert('x: '+x+' SCOState: '+SCOState[x]);
//}




/*LMS Initializations. Among many operations, we attach LMS functions to the API adapter and initialize data model (cmi).*/
var _DEBUG = 0;
var _TEMP  = '';
var _TEMP2  = '';

var d = new Date()
session_start_time = d.getTime();


myInitError();
myCurrentState     = -1;
commitArray        = new Array();
/*
 * An API Implementation’s object name changed from APBKMEI to API_1484_11
*/
window.API_1484_11 = new API();
cmi                = new myCmi();
adl				   = new myADL();
//adm				   = new ActivityDataModel();


/**
* This function is the API adapter. To this are attached all LMS functions, which are defined below,
* so that the content may access them.
*/
function API()
{
		this.version = '1.0';


/*
 * SCORM 2004: Method calls have changed
*/

this.Initialize     = LMSInitialize;
this.Terminate      = LMSFinish;
this.GetValue       = LMSGetValue;
this.SetValue       = LMSSetValue;
this.Commit         = LMSCommit;
this.GetLastError   = LMSGetLastError;
this.GetErrorString = LMSGetErrorString;
this.GetDiagnostic  = LMSGetDiagnostic;



}



function form_id(mystring)
{
	return mystring.replace(/^\s*|\s*$/,"");
}


function evaluateSuccessStatus(flag) {

	var scaledPassingScore;
	var scoreScaled;
	var successStatus;
	var value;

	scaledPassingScore	= cmi.scaled_passing_score.value;

	if(flag) {
		successStatus		= cmi.success_status.value;
		scoreScaled			= cmi.score.scaled.value;
	} else {
		successStatus		= SCOState['success_status'];
		scoreScaled			= SCOState['score_scaled'];
	}
		
	if (scaledPassingScore == null && scoreScaled == null && successStatus == null)
		value = '';						
	else if (scaledPassingScore == null && scoreScaled == null && successStatus != null)
		value = successStatus;
	else if (scaledPassingScore == null && scoreScaled != null && successStatus != null)
		value = successStatus;	
	else if (scaledPassingScore == null && scoreScaled != null && successStatus == null)
		value = '';
	else if (scaledPassingScore != null && scoreScaled == null && successStatus == null)
		value = 'unknown';
	else if (scaledPassingScore != null && scoreScaled == null && successStatus != null)
		value = 'unknown';
	else if (parseFloat(scaledPassingScore) > parseFloat(scoreScaled) && successStatus != null)
		value = 'failed';
	else if (parseFloat(scaledPassingScore) <= parseFloat(scoreScaled) && successStatus != null)			
		value = 'passed';			
	else if (parseFloat(scaledPassingScore) > parseFloat(scoreScaled) && successStatus == null)
		value = 'failed';
	else if (parseFloat(scaledPassingScore) <= parseFloat(scoreScaled) && successStatus == null)
		value = 'passed';

	return value;
}

function evaluateCompletionStatus(flag) {

	var completionThreshold;
	var progressMeasure;
	var completionStatus;
	var value;

	completionThreshold	= cmi.completion_threshold.value;

	if(flag) {
		completionStatus	= cmi.completion_status.value;
		progressMeasure		= cmi.progress_measure.value;
	} else {
		completionStatus		= SCOState['completion_status'];
		progressMeasure			= SCOState['progress_measure'];
	}
		
	if (completionThreshold == null && progressMeasure == null && completionStatus == null)
		value = '';						
	else if (completionThreshold == null && progressMeasure == null && completionStatus != null)
		value = completionStatus;
	else if (completionThreshold == null && progressMeasure != null && completionStatus != null)
		value = completionStatus;	
	else if (completionThreshold == null && progressMeasure != null && completionStatus == null)
		value = '';
	else if (completionThreshold != null && progressMeasure == null && completionStatus == null)
		value = 'unknown';
	else if (completionThreshold != null && progressMeasure == null && completionStatus != null)
		value = 'unknown';
	else if (parseFloat(completionThreshold) > parseFloat(progressMeasure) && completionStatus != null)
		value = 'incomplete';
	else if (parseFloat(completionThreshold) <= parseFloat(progressMeasure) && completionStatus != null)			
		value = 'completed';			
	else if (parseFloat(completionThreshold) > parseFloat(progressMeasure) && completionStatus == null)
		value = 'incomplete';
	else if (parseFloat(completionThreshold) <= parseFloat(progressMeasure) && completionStatus == null)
		value = 'completed';

	return value;
}


















/*
########################## LMS Functions ################################

These functions implement the SCORM protocol. However, they use corresponding oxiliary 
functions to the the real job. They all initially call myInitError(), which
sets the LMS error code to 0. This is done because each new call to an LMS
function needs to set a new error code, which remains 0 unless an error occurs.

#########################################################################
*/

/**
* Content initialization.
*/
function LMSInitialize(parameter)
{
    myInitError();

    try {
		return_value  = myInitialize(parameter);
		<?php
//		debug();

			$setObjectives = '';
			

			$result = eF_getTableData("scorm_sequencing_completion_threshold", "*", "content_ID=".$_GET['view_unit']);
			if($result[0]['completed_by_measure'] == 'true')
				$setObjectives .= "cmi.completion_threshold.value = '".$result[0]['min_progress_measure']."';";


			//Initialize objectives
			if($entry_info == 'resume')
				$objectives = $scoContent->objectives->get_objectives($_GET['view_unit']);
			else
				$objectives = $scoContent->objectives->get_objectives($_GET['view_unit']);

			$cnt=0;


			foreach ($objectives as $key => $value) {

				$objective_info = $scoContent->objectives->get_objective_info($value['objective_ID'], $value['content_ID'], true, true, false);
				$objective = $scoContent->objectives->get_objective($value['objective_ID'], $value['content_ID']);

				if($objective['is_primary'] == 1) {
					if($objective_info['raw_score']!='')
						$setObjectives .= "eval('cmi.score.raw.value =\'".$objective_info['raw_score'] ."\'');\n"; 
					if($objective_info['min_score']!='')
						$setObjectives .= "eval('cmi.score.min.value =\'".$objective_info['min_score'] ."\'');\n"; 
					if($objective_info['max_score']!='')				
						$setObjectives .= "eval('cmi.score.max.value =\'".$objective_info['max_score'] ."\'');\n"; 
				
					if($objective_info['objective_progress_status']=='false')							
						$setObjectives .= "eval('cmi.success_status.value = \'unknown\'');\n";
					else if($objective_info['objective_progress_status']=='true' && $objective_info['objective_satisfied_status']=='false')
						$setObjectives .= "eval('cmi.success_status.value = \'failed\'');\n";
					else if($objective_info['objective_progress_status']=='true' && $objective_info['objective_satisfied_status']=='true')
						$setObjectives .= "eval('cmi.success_status.value = \'passed\'');\n";


				if($objective_info['objective_measure_status'] == 'true' && $objective_info['objective_normalized_measure']!='')
					$setObjectives .= "eval('cmi.score.scaled.value = \'".$objective_info['objective_normalized_measure']."\'');\n";

				if($objective['satisfied_by_measure'] == 'true')
					$setObjectives .= "cmi.scaled_passing_score.value = '".$objective['min_normalized_measure']."';";


				if($objective_info['attempt_progress_status'] == 'false')				
						$setObjectives .= "eval('cmi.completion_status.value = \'unknown\'');\n";
				else if($objective_info['attempt_progress_status'] == 'true' && $objective_info['attempt_completion_status'] == 'false') {
					if($objective_info['not_attempted'])
						$setObjectives .= "eval('cmi.completion_status.value = \'not attempted\'');\n";
					else
						$setObjectives .= "eval('cmi.completion_status.value = \'incomplete\'');\n";
				 } else if($objective_info['attempt_progress_status'] == 'true' && $objective_info['attempt_completion_status'] == 'true')
					 $setObjectives .= "eval('cmi.completion_status.value = \'completed\'');\n";

				if($objective_info['reported_progress_measure'] == '1' && $objective_info['attempt_completion_amount_status'] == 'true')				
					$setObjectives .= "eval('cmi.progress_measure.value =\'".$objective_info['attempt_completion_amount']."\'');\n";
			
				}

				//REQ_72.3.3 For each objective defined (<imsss:primaryObjective> or <imsss:objective>) that includes an objectiveID attribute...				
				if($value['objective_ID'] == '') {
					continue;
				}

				$setObjectives .= "eval('cmi.objectives[".$cnt."] = new objectivesObject()');\n";
				$setObjectives .= "eval('cmi.objectives[".$cnt."].id.value =\'".$value['objective_ID']."\'');\n";

			
				if($objective_info['raw_score']!='')
					$setObjectives .= "eval(LMSSetValue('cmi.objectives." .$cnt. ".score.raw', '".$objective_info['raw_score'] ."'));\n"; 
				if($objective_info['min_score']!='')
					$setObjectives .= "eval(LMSSetValue('cmi.objectives." .$cnt. ".score.min', '".$objective_info['min_score'] ."'));\n";
				if($objective_info['max_score']!='')				
					$setObjectives .= "eval(LMSSetValue('cmi.objectives." .$cnt. ".score.max', '".$objective_info['max_score'] ."'));\n";

				if($objective_info['attempt_progress_status'] == 'false')				
						$setObjectives .= "eval('cmi.objectives[".$cnt."].completion_status.value = \'unknown\'');\n";
				else if($objective_info['attempt_progress_status'] == 'true' && $objective_info['attempt_completion_status'] == 'false') {
					if($objective_info['not_attempted'])
						$setObjectives .= "eval('cmi.objectives[".$cnt."].completion_status.value = \'not attempted\'');\n";
					else
						$setObjectives .= "eval('cmi.objectives[".$cnt."].completion_status.value = \'incomplete\'');\n";
				 } else if($objective_info['attempt_progress_status'] == 'true' && $objective_info['attempt_completion_status'] == 'true')
						$setObjectives .= "eval('cmi.objectives[".$cnt."].completion_status.value = \'completed\'');\n";

				

				if($objective_info['attempt_completion_amount_status'] == 'true')				
					$setObjectives .= "eval(LMSSetValue('cmi.objectives.". $cnt.".progress_measure', '".$objective_info['attempt_completion_amount'] ."'));\n";
			
				if($objective_info['objective_measure_status'] == 'true' && $objective_info['objective_normalized_measure']!='')
					$setObjectives .= "eval(LMSSetValue('cmi.objectives.". $cnt.".score.scaled', '".$objective_info['objective_normalized_measure'] ."'));\n";


				if($objective_info['objective_progress_status']=='false')							
					$setObjectives .= "eval(LMSSetValue('cmi.objectives.". $cnt.".success_status', '"."unknown" ."'));\n";
				else if($objective_info['objective_progress_status']=='true' && $objective_info['objective_satisfied_status']=='false')
					$setObjectives .= "eval(LMSSetValue('cmi.objectives.". $cnt.".success_status', '"."failed" ."'));\n";
				else if($objective_info['objective_progress_status']=='true' && $objective_info['objective_satisfied_status']=='true')
					$setObjectives .= "eval(LMSSetValue('cmi.objectives.". $cnt.".success_status', '"."passed" ."'));\n";





				/*
				if($objective['satisfied_by_measure'] == 'true' && $objective_info['objective_measure_status'] != 'false') {
					if($objective_info['objective_normalized_measure'] >= $objective['min_normalized_measure'])
						$setObjectives .= "eval(LMSSetValue('cmi.objectives.". $cnt.".success_status', 'passed'));\n";
					else
						$setObjectives .= "eval(LMSSetValue('cmi.objectives.". $cnt.".success_status', 'failed'));\n";
				}*/ 


				if($objective_info['description']!='')				
					$setObjectives .= "eval(LMSSetValue('cmi.objectives." .$cnt. ".description', '".$objective_info['description'] ."'));\n"; 

						
				$cnt++;				
			}
			echo $setObjectives;
			
			//Initialize adl data
			$maps = $scoContent->maps->getMaps($_GET['view_unit']);
		
			$cnt=0;
			foreach ($maps as $key => $value) {
				
				$setMaps .= "eval('adl.data[" . $cnt . "] = new dataObject()');\n";
				$setMaps .= "eval('adl.data[" . $cnt . "].id.value =\'".$value['target_ID']."\'');\n";
				$setMaps .= "eval('adl.data[" . $cnt . "].store.write =\'".$value['write_shared_data']."\'');\n";
				$setMaps .= "eval('adl.data[" . $cnt . "].store.read =\'".$value['read_shared_data']."\'');\n";

				$map_info = $scoContent->maps->getMapInfo($value['target_ID']);

				$setMaps .= "eval('adl.data[" . $cnt . "].store.value =\'".$map_info['store']."\'');\n";
				$cnt++;
			}
			echo $setMaps;

			//Initialize comments from learner
			$res = eF_getTableData("scorm_sequencing_comments_from_learner", "*", "content_ID = '".$_GET['view_unit'] ."' AND users_LOGIN = '".$_SESSION['s_login']."'"); 
			if(!empty($res)) {
				$commentsFromLearner = json_decode($res[0]['data'], true);

				$cnt=0;
				foreach ($commentsFromLearner as $key => $value) {
					$setCommentsFromLearner .= "eval('cmi.comments_from_learner[" . $cnt . "] = new comments_from_learnerObject()');\n";
					$setCommentsFromLearner .= "eval('cmi.comments_from_learner[" . $cnt . "].comment.value =\'".$value['comment']['value']."\'');\n";
					$setCommentsFromLearner .= "eval('cmi.comments_from_learner[" . $cnt . "].location.value =\'".$value['location']['value']."\'');\n";
					$setCommentsFromLearner .= "eval('cmi.comments_from_learner[" . $cnt . "].timestamp.value =\'".$value['timestamp']['value']."\'');\n";

					$cnt++;
				}
				echo $setCommentsFromLearner;
			}


			//Initialize learner preferences
			$res = eF_getTableData("scorm_sequencing_learner_preferences", "*", "content_ID = '".$_GET['view_unit'] ."' AND users_LOGIN = '".$_SESSION['s_login']."'"); 
			if(!empty($res)) {
				$learnerPreferences = json_decode($res[0]['data'], true);

				$setLearnerPreferences .= "eval('cmi.learner_preference.audio_level.value =\'".$learnerPreferences['audio_level']['value']."\'');\n";
				$setLearnerPreferences .= "eval('cmi.learner_preference.language.value =\'".$learnerPreferences['language']['value']."\'');\n";
				$setLearnerPreferences .= "eval('cmi.learner_preference.delivery_speed.value =\'".$learnerPreferences['delivery_speed']['value']."\'');\n";
				$setLearnerPreferences .= "eval('cmi.learner_preference.audio_captioning.value =\'".$learnerPreferences['audio_captioning']['value']."\'');\n";
				
				echo $setLearnerPreferences;
			}

			//Initialize interactions
			$res = eF_getTableData("scorm_sequencing_interactions", "*", "content_ID = '".$_GET['view_unit'] ."' AND users_LOGIN = '".$_SESSION['s_login']."'");
			if(!empty($res)) {
				$interactions = json_decode($res[0]['data'], true);

				$cnt=0;
				foreach ($interactions as $key => $value) {
					$setInteractions .= "eval('cmi.interactions[" . $cnt . "] = new interactionsObject()');\n";
					$setInteractions .= "eval('cmi.interactions[" . $cnt . "].id.value =\'".$value['id']['value']."\'');\n";
					$setInteractions .= "eval('cmi.interactions[" . $cnt . "].type.value =\'".$value['type']['value']."\'');\n";
					$setInteractions .= "eval('cmi.interactions[" . $cnt . "].timestamp.value =\'".$value['timestamp']['value']."\'');\n";
					$setInteractions .= "eval('cmi.interactions[" . $cnt . "].weighting.value =\'".$value['weighting']['value']."\'');\n";
					$setInteractions .= "eval('cmi.interactions[" . $cnt . "].result.value =\'".$value['result']['value']."\'');\n";
					$setInteractions .= "eval('cmi.interactions[" . $cnt . "].latency.value =\'".$value['latency']['value']."\'');\n";
					$setInteractions .= "eval('cmi.interactions[" . $cnt . "].learner_response.value =\'".$value['learner_response']['value']."\'');\n";
					

					if(!is_null($value['description']['value']))
						$setInteractions .= "eval('cmi.interactions[" . $cnt . "].description.value =\'".rawurlencode($value['description']['value'])."\'');\n";


					$cnt1 = 0;
					foreach($value['objectives'] as $key1 => $value1) {
						$setInteractions .= "eval('cmi.interactions[" . $cnt . "].objectives[" . $cnt1 . "]  = new objectivesObject()');\n";
						$setInteractions .= "eval('cmi.interactions[" . $cnt . "].objectives[" . $cnt1 . "].id.value =\'".$value1['id']['value']."\'');\n";
						$cnt1++;
					}

					$cnt1 = 0;
					foreach($value['correct_responses'] as $key1 => $value1) {
						$setInteractions .= "eval('cmi.interactions[" . $cnt . "].correct_responses[" . $cnt1 . "]  = new correct_responsesObject()');\n";
						$setInteractions .= "eval('cmi.interactions[" . $cnt . "].correct_responses[" . $cnt1 . "].pattern.value =\'".$value1['pattern']['value']."\'');\n";
						$cnt1++;
					}
									
					$cnt++;
				}
				echo $setInteractions;
			}




?>


    } catch(e) {
        myErrorHandler(e);
    } finally {
        if (_DEBUG) alert("Function: LMSInitialize \nArgument: '"+parameter+"' \nReturnes: '"+return_value+"'");
		//alert('LMSInitialize Error code: '+myErrorNumber);

        return return_value;
    }
}

/**
* Content finalization.
*/
function LMSFinish(parameter)
{
    myInitError();

    try {
        return_value  = myFinish(parameter);
    } catch(e) {
        myErrorHandler(e);
    } finally {
        if (_DEBUG) alert("Function: LMSFinish \nArgument: '"+parameter+"' \nReturnes: '"+return_value+"'");
        //alert('LMSFinish Error code: '+myErrorNumber);
        return return_value;
    }
}

/**
* Get paramater value.
*/
function LMSGetValue(parameter)
{

    myInitError();

		return_value  = myGetValues(parameter, value);


    if (_DEBUG) alert("Function: LMSGetValue \nArgument: '"+parameter+"' \nReturns: '"+return_value+"'");
    //alert('LMSGetValue Error code: '+myErrorNumber);
    return return_value;
}

/**
* Set paramater value.
*/
function LMSSetValue(parameter, value)
{
	//WriteToFile("SetValues: " + parameter + " " + value);

    //if (parameter == 'cmi.interactions.2.latency' && value = '03:01:39:52') _DEBUG = 1;
    myInitError();

		//alert(parameter + " " + value);
	
		return_value  = mySetValues(parameter, value);


    if (_DEBUG) alert("Function: LMSSetValue \nArgument: '"+parameter+"' \nSet Value: '"+value+"' \nReturns: '"+return_value+"'");
	//alert('LMSSetValue Error code: '+myErrorNumber);
	//
    return return_value;
}

/**
* Orders LMS to store all content parameters
*/
function LMSCommit(parameter)
{
    myInitError();
    return_value = myCommit(parameter);

    if (_DEBUG) alert("Commit! Parameter: "+parameter+" Returnes value: "+return_value);
    //alert('LMSCommit Error code: '+myErrorNumber);
    return return_value;
}

/**
* Returns last error code
*/
function LMSGetLastError()
{
    if (_DEBUG) alert("GetLastError: "+myErrorNumber);
    return myErrorNumber;
}

/**
* Returns the errorNumber error description
*/
function LMSGetErrorString(errorNumber)
{
    if (_DEBUG) alert("GetErrorString");
    return myGetErrorString(errorNumber);
}

/**
* Returns an comprehensive description od the errorNumber error.
*/
function LMSGetDiagnostic(errorNumber)
{
    if (_DEBUG) alert("LMSGetDiagnostic with errorNumber: "+errorNumber);

    if (errorNumber == "") {
        return myErrorDiagnostic;
    } else {
        return myGetDiagnostic(errorNumber);
    }
}


/*
########################## my* Function ##############################

These functions implement the actual functionality of the corresponding LMS
functions. Initially, each function checks the LMS state. Possible states are
"Not started", "Started", "Finished", each with different valid function calls.

#########################################################################
*/

/**
* LMS initialization. Sets the LMS state (myCurrentState) to 0 (Started).
* The only valid parameter is "". Returns true/false.
*/
function myInitialize(parameter)
{
    return_value = "false";

    if (parameter != '')
    {
        throw new myError ('201', 'Non-Empty parameter');
    }

    if (myCurrentState != -1 && myCurrentState != 1)
    {
        throw new myError('103', 'Already Initialized');
    }
    else
    {
        myCurrentState = 0;
        return_value  = "true";
    }

    return return_value;
}

/**
* LMS finalization. It sets the LMS state to 1 (Finished).
* The only valid parameter is "". It returns true/false
*/
function myFinish(parameter)
{
    //alert(myCurrentState);
    return_value = "false";

    if (parameter != '')                    
	{
        throw new myError ('201', 'Non-Empty parameter');
    }

    if (myCurrentState != 0 && myCurrentState != 1)
	{
        throw new myError('301', 'LMS not initialized');
    }
    else                                   
	{

        /*1. Set cmi.core.lesson_status
        if (!(SCOState['completion_status']))
        {
            if (SCOState['masteryscore'])
            {
                if (cmi.score.raw.get() < SCOState['masteryscore'])
                {
                    SCOState['completion_status'] = 'failed';
                    cmi.completion_status.set('failed');
                }
                else
				{
                    SCOState['completion_status'] = 'passed';
                    cmi.completion_status.set('passed');
                }
            }
            else
            {
                SCOState['completion_status'] = 'unknown';
                cmi.completion_status.set('unknown');
            }
        }
*/
		/*2. Set cmi.core.entry.*/


        var exit = SCOState['scorm_exit'];
        switch (exit)
        {
            case 'time-out':
                SCOState['entry'] = '';
                break;
            case 'suspend':
				SCOState['entry'] = 'resume';
                break;
            case 'logout':
                SCOState['entry'] = '';
                break;
            case '':
                SCOState['entry'] = 'ab-initio';
                break;
            default:
                SCOState['entry'] = '';
                break;
		}

		if(SCOState['navigation'] == 'suspendAll')
		{
			SCOState['entry'] = 'resume';
		}
/*
		if(SCOState['scorm_exit']!='suspend')
		{
			SCOState['suspend_data']='';
		}
*/

		SCOState['completion_status']	= evaluateCompletionStatus();
		SCOState['success_status']		= evaluateSuccessStatus();

        /*3, Set cmi.core.total_time*/
        if (SCOState['session_time'] && SCOState['session_time'] != '' )
		{
            //SCOState['total_time'] = SCOState['session_time'];          //ΠΡΟΣΟΧΗ: Αυτή η τιμή δεν αντιστοιχεί πλέον στο total_time. Απλώς βολεύει να την πάρουμε εδώ. μόλις λοιπόν κληθεί η LMSFinish() (και ανεξάρτητα από το αν έχει κληθεί προηγουμένως η LMSCommit()), παίρνουμε το τρέχον session time Και το βάζουμε σε αυτήν τη μεταβλητή. Στη συνέχεια, αυτή στέλνετε στο LMSCommitpage.php, το οποίο τη χειρίζεται ανάλογα
		}
		else
		{
			var d = new Date();
			var sessionTime = d.getTime() - session_start_time; // in miliseconds

			var	hours = Math.floor(sessionTime/3600000);
			session_time = sessionTime%3600000;
			var minutes = Math.floor(sessionTime/60000);
			session_time = sessionTime%60000;
			var seconds =  (sessionTime/1000).toFixed(2);

			var str = hours +':'+ minutes +':'+ seconds;
			SCOState['session_time'] = str;
			SCOState['total_time'] = SCOState['session_time'];
		}


		myCommit('finish');
        myCurrentState = 1;
        return_value  = "true";
	}

	if (adl.nav.request.get() != '') {
                    switch (adl.nav.request.get()) {

					case 'choice':
                        break;
                        case 'exit':
                        break;
                        case 'exitAll':
                        break;
                        case 'abandon':
                        break;
                        case 'abandonAll':
                        break;
                    }
	
	}

    return return_value;
}




/**
* Returns the "property" value, or false if an error occurs
*/
function myGetValues(property)
{

	//WriteToFile("GetValues: " + property);

    var return_value = "";
    try {
	
	if (!checkState()) {
		throw new myError('122');                               
	}

	if(property=="") {
		throw new myError('301');                               
	}

//	alert('get');	



	property = checkParameter(property, 0, '');//;alert("PROPERTY: "+property);
	if(property == 'requestValid') {
		return_value = 'unknown';	
	} else {
		eval('return_value = ' + property + '.get()');
	}

    } catch (e) {
        myErrorHandler(e);
	} finally {
        return return_value;
    }
}

/**
* Sets "property" value to "value". Returns true/false
*/
function mySetValues(property, value)   //throw new myError(0);
{

    var return_value = "false";
	try {

	if (!checkState()) {
		throw new myError('132');                               
	}

	if(property == "")
	{
		throw new myError('351');                               		
	}
	
	property = checkParameter(property, 1, value);
	//alert('property: '+property+' value: '+ value); //alert('return_value = ' + property + '.set('+value+')');
	

	var str_split = property.split(".");


	if(str_split[3]=='id')
	{
		eval('return_value = ' + property + '.set1(value)');
	}
	else
	{
		eval('return_value = ' + property + '.set(value)');
	}


    } catch (e) {
        myErrorHandler(e);
    } finally {
        return return_value;
    }
}

/**
* Orders LMS to persist all parameter values
* Only valid argument is "". It returns true/false.
*/
function myCommit(parameter)
{
    var return_value = "false";
	
    if (parameter != '' && parameter != 'finish')                                   //'finish' parameter indicates that myCommit was called from myFinish, so it may persist total_time as well
    {
        myErrorHandler(new myError('201', 'Non-Empty parameter'));
        return return_value;
}
    else
    {
		try {
			
			if(!checkState())
			{
				throw new myError('142', 'Commit Before Initialization');
			}
			/*Are we going to store any data;*/


			/** SCORM 2004
 			* cmi.core flattened to cmi
			*/ 
            SCOState['credit'] = cmi.credit.get();

            commitArray = SCOState;

            commitParameters = '';
            /*commitArray holds the variables that need to be commited. These become a series of GET parameters, which are communicated to the LMSCommitPage.php page*/
			//alert('Commit');
            for (mykey in commitArray)
            {
				if ((mykey != 'total_time' || parameter == 'finish') && document.getElementById(mykey)) {
					if (commitArray[mykey]==null || (typeof commitArray[mykey]) == 'undefined') {
						document.getElementById(mykey).disabled = true;							
					} else {		
						document.getElementById(mykey).value = commitArray[mykey];
					}
                }                
			}

			//If commit was issued from finish we must handle the possible navigation request from the content
			if(parameter == 'finish') {
				document.getElementById('finish').value = 'true';
			}

			var objectives = Object.toJSON(cmi.objectives);
			document.getElementById('objectives').value = objectives;	

			var commentsFromLMS = Object.toJSON(cmi.comments_from_lms);
			document.getElementById('comments_from_lms').value = commentsFromLMS;

			var commentsFromLearner = Object.toJSON(cmi.comments_from_learner);
			document.getElementById('comments_from_learner').value = commentsFromLearner;

			var interactions = Object.toJSON(cmi.interactions);
			document.getElementById('interactions').value = interactions;		

			var shared_data = Object.toJSON(adl.data);
			document.getElementById('shared_data').value = shared_data;

			var learnerPreferences = Object.toJSON(cmi.learner_preference);
			document.getElementById('learner_preferences').value = learnerPreferences;

            <?php
                if ( $_GET['view_unit'] ) {
                    echo "document.getElementById('content_ID').value = ".$_GET['view_unit'].";";
                }
			?>
			$('scorm_form').request({onSuccess:handleCommit});
            //document.scorm_form.submit();
			
			return_value = "true";
        } catch (e) {
            myErrorHandler(e);
        } finally {
            return return_value;
        }
    }
}

function handleCommit(transport) {
	if (!(w = findFrame(top, 'mainframe'))) {
		w = window;
	}

	w.updateProgress(transport.responseText.evalJSON(true));

	if (transport.responseText.evalJSON(true)[4]) {
		w.location = transport.responseText.evalJSON(true)[4];
	}
}


/**
* REturns the message that corresponds to errrorNumber.
*/
function myGetErrorString(errorNumber)
{
    var errorStrings    = new Array();
    errorStrings['0']   = 'No Error';
    errorStrings['101'] = 'General exception'; 
    errorStrings['201'] = 'Invalid argument error';
    errorStrings['202'] = 'Element cannot have children';
    errorStrings['203'] = 'Element not an array - cannot have count';
    errorStrings['301'] = 'Not initialized';
    errorStrings['401'] = 'Not implemented error';
    errorStrings['402'] = 'Invalid set value, element is a keyword';
   
	//errorStrings['404'] = 'Element is write only';
	//errorStrings['405'] = 'Incorrect Data Type';


	/** SCORM 2004: 405-> 406, 301->403, 403->404, 
	*/
	errorStrings['403'] = 'Data Model Element Value Not Initialized';
	errorStrings['404'] = 'Data Model Element Is Read Only';
	errorStrings['405'] = 'Data Model Element Is Write Only';
	errorStrings['406'] = 'Data Model Element Type Mismatch';
	errorStrings['407'] = 'Data Model Element Value Out Of Range';


    if (errorNumber == '') {                                                    //No arguments were given, so no error is reported (this is basically to comply with the case where the function is called before LMSInitialize())
        return '';
    } else if ((typeof errorStrings[errorNumber]) == 'undefined') {             //If the error code is not valid, do nothing really
        return '';
    } else {
        return errorStrings[errorNumber];
    }

}

/**
* Returns a custom message that corresponds to errorNumber, or the message that corresponds to the current error, if the argument is "".
*/
function myGetDiagnostic(errorNumber)
{
	var errorDiagnostic    = new Array();

    errorDiagnostic['001']   = 'Succesful operation. There were no errors';


    errorDiagnostic['0']   = 'Succesful operation. There were no errors';
    errorDiagnostic['101'] = 'A general fault occured - General exception';
    errorDiagnostic['201'] = 'You cannot set such value - Invalid argument error';
    errorDiagnostic['202'] = 'This element cannot have children - Element cannot have children';
    errorDiagnostic['203'] = 'This element is not an array - Element not an array - cannot have count';
    errorDiagnostic['301'] = 'System has to be initialized - Not initialized';
    errorDiagnostic['401'] = 'This property is not implemented - Not implemented error';
    errorDiagnostic['402'] = 'You cannot set a value to a keyword - Invalid set value, element is a keyword';
    errorDiagnostic['403'] = 'You can only read this element\'s value - Element is read only';
    errorDiagnostic['404'] = 'You can only write this element\'s value - Element is write only';
    errorDiagnostic['405'] = 'You cannot set this element to this value - Incorrect Data Type';

	/** SCORM 2004: 405-> 405
	*/
	errorDiagnostic['406'] = 'You cannot set this element to this value - Data Model Element Type Mismatch';
	errorDiagnostic['407'] = 'You cannot set this element to this value - Data Model Element Value Out Of Range';

    if (errorNumber == "") errorNumber = myErrorNumber;

    return errorDiagnostic[errorNumber];
}


/*
######################## Supplementary functions #########################

These functions implement supplementary operations needed by the LMS. They 
are mainly associated to error handling.

#########################################################################
*/

/**
* Checks the LMS state. If it is other than 0, it means it is not initialized, so an error is fired.
*/
function checkState()
{
    if (myCurrentState != 0)
    {
		//throw new myError('301');
		return false;
	}
	else
	{
		return true;
	}
}




function checkDependancy1 (property)
{

	var str_split = property.split(".");


	str = str_split[0]+'.'+str_split[1]+'['+str_split[2]+']';

	if(str_split[3]!='id' && str_split[3]!='comment' && eval('typeof ' + str)=='undefined')
	{
		throw new myError('408');		
	}
	else if(str_split[3]=='learner_response' && eval('typeof ' + str + '.type')=='undefined')
	{
		throw new myError('408');				
	}


}

function checkDependancy2 (property)
{

	str_split = property.split(".");


	str = str_split[0]+'.'+str_split[1]+'['+str_split[2]+']' + '.' + str_split[3] + '[' + str_split[4] + ']';

	if(str_split[5]=='pattern' && str_split[4]>0)
	{
		//throw new myError('3511111111');		
	}



}







/**
* Checks id the specified parameter is implemented
*/

function checkParameter(property, caller, value)
{


if(property == 'cmi.interactions.1.correct_responses.1.pattern')
{
	//alert(property + " " + value + " " + cmi.interactions.length);


}

  /*The code below is used to handle strings of the form "cmi.interactions.1.objectives.0.id" */
    str = property;
    str_split = property.split(".");


	if(str_split[3] == 'choice' || str_split[3] == 'jump')
	{
		return "requestValid";
		
		temp = str_split[4].split(new RegExp("[{,=,}]"));
		property = property.replace(new RegExp("\{.*$"), temp[2].replace(/-|\./, ""));
		//alert(property);
		str = property;
		str_split = property.split(".");
	}

    if (!isNaN(parseInt(str_split[2])))
	{
	
        k = 3;      //k is used to discriminate cmi.interactions.1.objectives.0.id from cmi.interactions.1.id
        str = str_split[0]+'.'+str_split[1]+'['+str_split[2]+']';
        /*If the objext is already defined, do not define it again*/

        if (eval('typeof '+str_split[0]+'.'+str_split[1]) == 'undefined')
        {
            throw new myError('201');
        }
        _TEMP = str_split[2];                                                       //This is used to signify globally the current index

        if (!(eval(str)))
        {
            var current_length = eval(str_split[0]+'.'+str_split[1]+'.length');     //Check if the siginified index is sequential (e.g. if the last array index is 3, the designated array index must be at most 4)
			
			if(caller==1)
			{
				if (str_split[2] > current_length)
            	{
                	throw new myError('351');
				}
				else if((str_split[3] == 'id' || str_split[3] == 'comment') && eval('typeof ' + str)=='undefined')
				{
                	eval(str+'= new '+str_split[1]+'Object()');
				}
				else if (eval('typeof ' + str)=='undefined')
				{
					if(str_split[0] == 'adl')
	                	throw new myError('351');
						
                	throw new myError('408');
				}
			
			
				if(str_split[1]=='objectives' || str_split[1]=='interactions')
				{
					for (var z=0; z < current_length;  z++)
					{
						if (eval(str_split[0]+'.'+ str_split[1] + '[' + z + '].id.get()')==value)
						{
                			throw new myError('351');
						}
					}
				}	

			}
			else
			{
				if (str_split[2] >= current_length)
				{
                	throw new myError('301');					
				}

			}	
		}
			
        if (!isNaN(parseInt(str_split[4])))
		{

		if(property == "adl.nav.request_valid.choice.{target=activity_2}")
		{
			//alert("bainei sto deutero");
		}


            if (eval('typeof '+str+'.'+str_split[3]) == 'undefined')
            {
                throw new myError('201');
            }
            _TEMP2 = str_split[4];                                                  //This is used to signify globally the current index

			var current_length = eval(str+'.'+str_split[3]+'.length');              //Check if the siginified index is sequential (e.g. if the last array index is 3, the designated array index must be at most 4)

			var temp =  str+'.'+str_split[3]+'.length';

            str+='.'+str_split[3]+'['+str_split[4]+']';

			if (caller==1)
			{
				if (str_split[4] > current_length)
				{
                	throw new myError('351');

                	//throw new myError('351 ' + str_split[4] + '>' + current_length + ' ' + temp);
				}

				if(str_split[5]=='id' && current_length>1 )
				{
					for (var m=0; m < current_length;  m++)
					{
						if (eval(str_split[0]+'.'+str_split[1]+'['+str_split[2]+']'+'.'+str_split[3]+'[' + m + '].id.get()')==value)
						{
                			throw new myError('351');
						}
					}
				}
				if(str_split[5]=='pattern' && current_length>0 )
				{
					if(_TEMP2>0 &&  (eval('cmi.interactions[' + _TEMP + '].type.getValue()')=='true-false' || eval('cmi.interactions[' + _TEMP + '].type.getValue()')=='numeric'))
					{
						throw new myError('351');
					}

					if(eval('cmi.interactions[' + _TEMP + '].type.getValue()')=='choice' || eval('cmi.interactions[' + _TEMP + '].type.getValue()')=='sequencing')
					{
						for (var m=0; m < current_length;  m++)
						{
							if (eval(str_split[0]+'.'+str_split[1]+'['+str_split[2]+']'+'.'+str_split[3]+'[' + m + '].pattern.get()')==value)
							{
								throw new myError('351');
							}
						}
					}
				}
				if(eval('typeof ' + str)=='undefined')
				{
					checkDependancy2(property);
					
                	eval(str+'= new '+str_split[3]+'Object()');
				}
			}
			else
			{
				if (str_split[4] >= current_length)
				{
                	throw new myError('301');					
				}

			}	

		k = 5;

		}

        for (var i = k; i < str_split.length; i++)
        {
            str += '.'+str_split[i];
        }

        property = str;
    }

    str_split    = property.split(".");
    var temp_str = str_split[0];
    for (var i = 1; i < str_split.length; i++) {
        //alert('temp_str: '+temp_str);
			if (eval('typeof ' + temp_str) == 'undefined') {
            throw new myError('201');
        }
        temp_str = temp_str + '.' + str_split[i];
    }

	/*Check if the parameter exists*/

    if (property == null || eval('typeof '+property) == 'undefined')
    {
        var last_element = str_split.pop();                         //Take the last element in the array
        if (last_element == '_children') {
            throw new myError('202');
        } else if (last_element == '_count') {
            throw new myError('203');
        } else {
            throw new myError('2015');                               
        }
    }

    //alert("checkParameter returned: "+property);


    return property;

}




var langCodes = {'aa':'', 'ab':'', 'ae':'', 'af':'', 'ak':'', 'am':'', 'an':'', 'ar':'', 'as':'', 'av':'', 'ay':'', 'az':'',
                          'ba':'', 'be':'', 'bg':'', 'bh':'', 'bi':'', 'bm':'', 'bn':'', 'bo':'', 'br':'', 'bs':'',
                          'ca':'', 'ce':'', 'ch':'', 'co':'', 'cr':'', 'cs':'', 'cu':'', 'cv':'', 'cy':'',
                          'da':'', 'de':'', 'dv':'', 'dz':'', 'ee':'', 'el':'', 'en':'', 'eo':'', 'es':'', 'et':'', 'eu':'',
                          'fa':'', 'ff':'', 'fi':'', 'fj':'', 'fo':'', 'fr':'', 'fy':'', 'ga':'', 'gd':'', 'gl':'', 'gn':'', 'gu':'', 'gv':'',
                          'ha':'', 'he':'', 'hi':'', 'ho':'', 'hr':'', 'ht':'', 'hu':'', 'hy':'', 'hz':'',
                          'ia':'', 'id':'', 'ie':'', 'ig':'', 'ii':'', 'ik':'', 'io':'', 'is':'', 'it':'', 'iu':'',
                          'ja':'', 'jv':'', 'ka':'', 'kg':'', 'ki':'', 'kj':'', 'kk':'', 'kl':'', 'km':'', 'kn':'', 'ko':'', 'kr':'', 'ks':'', 'ku':'', 'kv':'', 'kw':'', 'ky':'',
                          'la':'', 'lb':'', 'lg':'', 'li':'', 'ln':'', 'lo':'', 'lt':'', 'lu':'', 'lv':'',
                          'mg':'', 'mh':'', 'mi':'', 'mk':'', 'ml':'', 'mn':'', 'mo':'', 'mr':'', 'ms':'', 'mt':'', 'my':'',
                          'na':'', 'nb':'', 'nd':'', 'ne':'', 'ng':'', 'nl':'', 'nn':'', 'no':'', 'nr':'', 'nv':'', 'ny':'',
                          'oc':'', 'oj':'', 'om':'', 'or':'', 'os':'', 'pa':'', 'pi':'', 'pl':'', 'ps':'', 'pt':'',
                          'qu':'', 'rm':'', 'rn':'', 'ro':'', 'ru':'', 'rw':'',
                          'sa':'', 'sc':'', 'sd':'', 'se':'', 'sg':'', 'sh':'', 'si':'', 'sk':'', 'sl':'', 'sm':'', 'sn':'', 'so':'', 'sq':'', 'sr':'', 'ss':'', 'st':'', 'su':'', 'sv':'', 'sw':'',
                          'ta':'', 'te':'', 'tg':'', 'th':'', 'ti':'', 'tk':'', 'tl':'', 'tn':'', 'to':'', 'tr':'', 'ts':'', 'tt':'', 'tw':'', 'ty':'',
                          'ug':'', 'uk':'', 'ur':'', 'uz':'', 've':'', 'vi':'', 'vo':'',
                          'wa':'', 'wo':'', 'xh':'', 'yi':'', 'yo':'', 'za':'', 'zh':'', 'zu':'',
                          'aar':'', 'abk':'', 'ave':'', 'afr':'', 'aka':'', 'amh':'', 'arg':'', 'ara':'', 'asm':'', 'ava':'', 'aym':'', 'aze':'',
                          'bak':'', 'bel':'', 'bul':'', 'bih':'', 'bis':'', 'bam':'', 'ben':'', 'tib':'', 'bod':'', 'bre':'', 'bos':'',
                          'cat':'', 'che':'', 'cha':'', 'cos':'', 'cre':'', 'cze':'', 'ces':'', 'chu':'', 'chv':'', 'wel':'', 'cym':'',
                          'dan':'', 'ger':'', 'deu':'', 'div':'', 'dzo':'', 'ewe':'', 'gre':'', 'ell':'', 'eng':'', 'epo':'', 'spa':'', 'est':'', 'baq':'', 'eus':'', 'per':'',
                          'fas':'', 'ful':'', 'fin':'', 'fij':'', 'fao':'', 'fre':'', 'fra':'', 'fry':'', 'gle':'', 'gla':'', 'glg':'', 'grn':'', 'guj':'', 'glv':'',
                          'hau':'', 'heb':'', 'hin':'', 'hmo':'', 'hrv':'', 'hat':'', 'hun':'', 'arm':'', 'hye':'', 'her':'',
                          'ina':'', 'ind':'', 'ile':'', 'ibo':'', 'iii':'', 'ipk':'', 'ido':'', 'ice':'', 'isl':'', 'ita':'', 'iku':'',
                          'jpn':'', 'jav':'', 'geo':'', 'kat':'', 'kon':'', 'kik':'', 'kua':'', 'kaz':'', 'kal':'', 'khm':'', 'kan':'', 'kor':'', 'kau':'', 'kas':'', 'kur':'', 'kom':'', 'cor':'', 'kir':'',
                          'lat':'', 'ltz':'', 'lug':'', 'lim':'', 'lin':'', 'lao':'', 'lit':'', 'lub':'', 'lav':'',
                          'mlg':'', 'mah':'', 'mao':'', 'mri':'', 'mac':'', 'mkd':'', 'mal':'', 'mon':'', 'mol':'', 'mar':'', 'may':'', 'msa':'', 'mlt':'', 'bur':'', 'mya':'',
                          'nau':'', 'nob':'', 'nde':'', 'nep':'', 'ndo':'', 'dut':'', 'nld':'', 'nno':'', 'nor':'', 'nbl':'', 'nav':'', 'nya':'',
                          'oci':'', 'oji':'', 'orm':'', 'ori':'', 'oss':'', 'pan':'', 'pli':'', 'pol':'', 'pus':'', 'por':'', 'que':'',
                          'roh':'', 'run':'', 'rum':'', 'ron':'', 'rus':'', 'kin':'', 'san':'', 'srd':'', 'snd':'', 'sme':'', 'sag':'', 'slo':'', 'sin':'', 'slk':'', 'slv':'', 'smo':'', 'sna':'', 'som':'', 'alb':'', 'sqi':'', 'srp':'', 'ssw':'', 'sot':'', 'sun':'', 'swe':'', 'swa':'',
                          'tam':'', 'tel':'', 'tgk':'', 'tha':'', 'tir':'', 'tuk':'', 'tgl':'', 'tsn':'', 'ton':'', 'tur':'', 'tso':'', 'tat':'', 'twi':'', 'tah':'',
						  'uig':'', 'ukr':'', 'urd':'', 'uzb':'', 'ven':'', 'vie':'', 'vol':'', 'wln':'', 'wol':'', 'xho':'', 'yid':'', 'yor':'', 'zha':'', 'chi':'', 'zho':'', 'zul':''};



function check_multiple_choice(parameter)
{
	var r = new Array();

	var mySplitResult = parameter.split("[,]");

	if(mySplitResult=='')
	{
		return true;
	}

	for(var i = 0, n = mySplitResult.length; i < n; i++)
	{
		if(!checkDataType(mySplitResult[i], 'CMIIdentifier'))	
		{
			return false;
		}
		
		for(var x = 0, y = r.length; x < y; x++)
		{
			if(r[x]==mySplitResult[i])
			{
				match=false;
				return match;
			}
		}
		r[r.length] = mySplitResult[i];
	}
	return true;
}

function check_sequencing(parameter)
{

	var r = new Array();

	var mySplitResult = parameter.split("[,]");

	for(var i = 0, n = mySplitResult.length; i < n; i++)
	{
		if(!checkDataType(mySplitResult[i], 'CMIIdentifier'))	
		{
			return false;
		}
	}
	return true;
}


function check_localized_string(parameter) {


	var	matches = parameter.match('^(\{(lang)=([^\}]?)\})');

	

	if(matches!=null && matches[3]=='')
	{
		return false;
	}

	return true;

}


function check_fill_in(parameter)
{	
	var r = new Array();
	var mySplitResult = parameter.split("[,]");

	for(var i = 0, n = mySplitResult.length; i < n; i++)
	{
		if(i==0)
		{
			langPresent = false;

			while (matches = mySplitResult[0].match('^(\{(lang|case_matters|order_matters)=([^\}]+))'))
			{
				switch(matches[2])
				{
					case 'case_matters':
						if(langPresent)
							return false;
						if(!checkDataType(matches[3], 'CMIBoolean'))
							return false;
						break;
					case 'order_matters':
						if(langPresent)
							return false;
						if(!checkDataType(matches[3], 'CMIBoolean'))
							return false;
						break;
					case 'lang':
						langPresent = true;
						langCode = matches[3].split('-');
						if(langCodes[langCode[0].toLowerCase()] == undefined)
						{	
							return false;
						}
						break;
					default:
						break;
				}
				mySplitResult[0] = mySplitResult[0].substr(matches[1].length);
			}
		}
		else
		{
			matches = mySplitResult[i].match('^\{(lang)=([^\}\-]+)[^\}]*\}');
	
			if(matches!=null && matches[2] != undefined && langCodes[matches[2].toLowerCase()] == undefined)
			{
				return false;
			}
		}
	}
	return true;
}

function check_performance(parameter)
{
	var r = new Array();
	var mySplitResult = parameter.split("[,]");

	for(var i = 0, n = mySplitResult.length; i < n; i++)
	{
		var matches = mySplitResult[i].match('^(\{(order_matters)=([^\}]+)\})');
		if (matches!= null && matches[2] == 'order_matters')
		{
			if(!checkDataType(matches[3], 'CMIBoolean'))
				return false;
		
			mySplitResult[i] = mySplitResult[i].substr(matches[1].length);
		}



	


		var match_record =/^[a-z0-9]*\[\.\][a-z0-9]*?$/i.test(mySplitResult[i]);

		if(!match_record)	
		{
			return false;
		}
					
		var steps = mySplitResult[i].split("[.]");

		if(steps.length<2)
		{
			return false;
		}

		var	match_steps = steps[1].match('^([a-z0-9]*).:.([a-z0-9]*)');

		if(match_steps!=null)
		{
			if(match_steps[1]!='' && !checkDataType(match_steps[1], 'real'))
				return false;
			if(match_steps[2]!='' && !checkDataType(match_steps[2], 'real'))
				return false;
		}
	}
	match=true;
	return match;
}					

function check_numeric(parameter)
{
		var limits = parameter.split("[:]");

		if(limits.length==2)
		{
			if(!checkDataType(limits[0], 'real'))
				return false;
			if(!checkDataType(limits[1], 'real'))
				return false;
			if(parseFloat(limits[0])>parseFloat(limits[1]))
				return false;
		}
		else if(!checkDataType(parameter, 'real'))
		{
			return false;
		}

		match=true;
		return match;
}

/**
* This function checks parameters against regular expressions to verify their compliance with the data model.
* Parameter is the string to check. data_model is the model to check the parameter against, i.e. CMIDecimal.
* type is an argument used only in cases where the data model can be one of a number of different enumerations.
*/
function checkDataType(parameter, data_model, type)
{
    switch (data_model) {
        case 'CMIBlank':
            return match = /^$/.test(parameter);                        //empty string
            break;

        case 'CMIBoolean':
            return match = /^(true|false)$/.test(parameter);                //boolean, true or false
            break;

        case 'CMIDecimal':
            return match = /^-?\d+(\.\d+)?$/i.test(parameter);          //positive or negative number that may be decimal
			break;

		case 'real':
			return match = /^-?([0-9]{1,5})(\.[0-9]+)?$/i.test(parameter);          //positive or negative number that may be decimal
			break;

        case 'CMIFeedback':
            switch (type) {
                case 'true-false':
                    return match = /^(true|false)$/.test(parameter);                   // Can be one of: "0", "1", "t", "f" 
                    break;
				case 'choice':
					match=check_multiple_choice(parameter);
					return match;
                    break;
                case 'fill-in':
					match=check_fill_in(parameter);
					return match;
                    break;
				case 'long-fill-in':
					match=check_fill_in(parameter);
					return match;
                    break;
                case 'numeric':
					match=check_numeric(parameter);
					return match;
					break;
                case 'likert':
		            return match = /^[a-zA-Z0-9()+,.:=@;$_!*'-/]+$/i.test(parameter);                              //A string with no white spaces
                    break;
                case 'matching':
                    return match = /^[a-z0-9]+\[\.\][a-z0-9]+(\[,\][a-z0-9]+\[\.\][a-z0-9]+)*?$/i.test(parameter);             //Pair of identifiers, i.e. 2.s,4.2
                    break;
                case 'performance':
					match=check_performance(parameter);
					return match;
					break;
                case 'sequencing':
					match=check_sequencing(parameter);
					return match;
					break;
				 case 'other':
					return match = /^(\S{1,20000})?$/i.test(parameter);                    //A string with no white spaces
                    break;
                default:
                    return false;
                    break;
            }
            break;

        case 'CMIIdentifier':
            return match = /^[a-zA-Z0-9()+,.:=@;$_!*'-/]+$/i.test(parameter);                              //A string with no white spaces
			break;

		case 'urn':
			return match = /^urn:[a-zA-Z0-9][a-zA-Z0-9-]{1,31}:([a-zA-Z0-9()+,.:=@;$_!*'-/])+/i.test(parameter);

        case 'CMIInteger':
            return match = (/^[0-9]{1,5}$/i.test(parameter) && parameter <=65536);              //Integer less than or equal to 65536
            break;

        case 'CMISInteger':
            return match = (/^(\-|\+)?[0-9]{1,5}$/i.test(parameter) && parameter <= 32768 && parameter >= -32768);      //Signed integer ranging from -32768 to +32768
            break;

        case 'CMIString255':
            return match = /^.{0,255}$/i.test(parameter);               //Any character string with length at most 255
            break;

        case 'CMIString4096':
            return match = /^.{0,4096}$/i.test(parameter);              //Any character string with length at most 4096
			break;

    	case 'CMIString64000':
            return match = /^.{0,64000}$/i.test(parameter);              //Any character string with length at most 64000
			break;

		case 'characterstring':
			return match = /^.{0,1000}$/i.test(parameter);              //Any character string with length at most 1000
			break;


				/** SCORM 2004 CMITime -> time
 				*/
		case 'time':
			return match =/^(19[7-9]{1}[0-9]{1}|20[0-2]{1}[0-9]{1}|203[0-8]{1})((-(0[1-9]{1}|1[0-2]{1}))((-(0[1-9]{1}|[1-2]{1}[0-9]{1}|3[0-1]{1}))(T([0-1]{1}[0-9]{1}|2[0-3]{1})((:[0-5]{1}[0-9]{1})((:[0-5]{1}[0-9]{1})((\.[0-9]{1,2})((Z|([+|-]([0-1]{1}[0-9]{1}|2[0-3]{1})))(:[0-5]{1}[0-9]{1})?)?)?)?)?)?)?)?$/i.test(parameter);             //A point in a 24-hour clock, with an optional 1 or 2 digit decimal part in seconds
            break;
        case 'CMITimespan':
            return match = /^\d{2,4}:\d\d:\d\d(.\d{1,2})?$/i.test(parameter);           //A timespan, in the form HHHH:MM:SS.SS
			break;

		/**
		* SCORM 2004: new element 
		*/
		case 'timeinterval':
			return match = /^P(\d+Y)?(\d+M)?(\d+D)?(T(((\d+H)(\d+M)?(\d+(\.\d{1,2})?S)?)|((\d+M)(\d+(\.\d{1,2})?S)?)|((\d+(\.\d{1,2})?S))))?$/i.test(parameter); //A timespan, in the form [yY][mM][dD][T[hH][nM][s[.s]S]]
			break; 

		/**
		* SCORM 2004: new element replaces CMISInteger
		*/
        case 'integer':
            return match = (/^(\-|\+)?[0-9]{1,5}$/i.test(parameter) && parameter <= 32768 && parameter >= -32768);      //Signed integer ranging from -32768 to +32768
            break;


		/** 
 		* SCORM:2004 CMIVocabulary changed to state
		*/

		case 'state':
            switch (type) {
                case 'Mode':
                    return match = /^(normal|review|browse)$/.test(parameter);
                    break;
                case 'SuccessStatus':
                    return match = /^(passed|failed|unknown)$/.test(parameter);
					break;
                case 'CompletionStatus':
                    return match = /^(completed|incomplete|not attempted|unknown)$/.test(parameter);
                    break;
                case 'Exit':
                    return match = /^(time-out|suspend|logout|normal|^)$/.test(parameter);
                    break;
                case 'Credit':
                    return match = /^(credit|no-credit)$/.test(parameter);
                    break;
                case 'Entry':
                    return match = /^(ab-initio|resume|^)$/.test(parameter);
                    break;
                case 'Interaction':
					return match = /^(true-false|choice|fill-in|long-fill-in|matching|performance|sequencing|likert|numeric|other)$/.test(parameter);
                    break;
                case 'Result':
                    return match = /^(correct|incorrect|unanticipated|neutral|(-?([0-9]{1,5})(\.[0-9]{1,7})?))$/.test(parameter);
					break;
                case 'TimeLimitAction':
                    return match = /^(exit,message|exit,no message|continue,message|continue,no message)$/.test(parameter);
					break;
				/** 
 				* SCORM:2004 New element
				*/
				case 'SuccessStatus':
					return match = /^(passed|failed|unknown)$/.test(parameter);
					break;
				/** 
 				* SCORM:2004 New element
				*/
				case 'CompletionStatus':
					return match = /^(completed|incomplete|not attempted|unknown)$/.test(parameter);
					break;
                default:
                    return false;
                    break;
            }


		/** 
 		* SCORM:2004 CMIVocabulary changed to status -->must deleted
		*/

        case 'CMIVocabulary':
            switch (type) {
                case 'Mode':
                    return match = /^(normal|review|browse)$/.test(parameter);
                    break;
                case 'Status':
                    return match = /^(passed|completed|failed|incomplete|browsed|not attempted)$/.test(parameter);
                    break;
                case 'Exit':
                    return match = /^(time-out|suspend|logout|^)$/.test(parameter);
                    break;
                case 'Credit':
                    return match = /^(credit|no-credit)$/.test(parameter);
                    break;
                case 'Entry':
                    return match = /^(ab-initio|resume|^)$/.test(parameter);
                    break;
                case 'Interaction':
                    return match = /^(true-false|choice|fill-in|matching|performance|likert|sequencing|numeric)$/.test(parameter);
                    break;
                case 'Result':
                    return match = /^(correct|wrong|unanticipated|neutral|(-?\d+(\.\d+)?))$/.test(parameter);
                    break;
                case 'TimeLimitAction':
                    return match = /^(exit,message|exit,no message|continue,message|continue,no message)$/.test(parameter);
					break;
				case 'SuccessStatus':
					return match = /^(passed|failed|unknown)$/.test(parameter);
					break;
				case 'CompletionStatus':
					return match = /^(completed|incomplete|not attempted|unknown)$/.test(parameter);
					break;
                default:
                    return false;
                    break;
            }

        default:
            return false;
            break;
    }

}

/**
* This function creates error objects
*/
function myError(errorNumber, errorMessage)
{
    this.errorNumber = errorNumber;
    this.errorMessage = errorMessage;
}

/**
* This function handles errors, by setting appropriate error messages and codes
*/
function myErrorHandler(err)
{
    if (err instanceof myError)
    {
        myErrorNumber = err.errorNumber;
        if (err.errorMessage)
        {
            myErrorDiagnostic = err.errorMessage;
        }
        else
        {
            myErrorDiagnostic = myGetDiagnostic(err.errorNumber);
        }
	}
	else alert(err);
    //else throw err;
}

/**
* Initializes error code and message
*/
function myInitError()
{
    myErrorNumber = '0';
    myErrorDiagnostic = myGetDiagnostic(myErrorNumber);
}

/*
######################### Data Model ####################################

The functions below implement the SCORM data model. Each function is 
initialized to an object, so that data access is done accordingly to 
SCORM notation. For example, if we need to set the cmi.core.student_name 
parameter's value, we will access it at exactly the same way, since all 
parameter elements (cmi, core, student_name) are objects.

#########################################################################
*/







/**
* Cmi data model
            this.max       = new max();
*/
function myCmi()
{

	 this.get = function(param) { throw new myError('401'); }
	 this.set = function(param) { throw new myError('401'); }

    /**
    * cmi.core is made of objects which all SCOs depend on and all LMSs must implement
	*/

	/* 
	* SCORM 2004: Removed the concept of the core data model category.
	*/
   
        /**
        * cmi.core._children is a string that contains all the elements the LMS supports
        *
        * Supported API calls: LMSGetValue()
        * LMS Mandatory: YES
        * Data Type: CMIString255
        * Read Only
        *
        * Initialization: All LMS children, so that an LMSGetValue call will return a comma separated list
        */
        var _children = function()
        {
            this.get = function()      { return value;           }
            this.set = function(param) { throw new myError('402'); }

            var value = 'learner_id, learner_name, location, credit, lesson_status, entry, score, total_time, lesson_mode, exit, session_time';
        }

        /**
        * cmi.core.student_id is an identifier that contains the user (student) id.
        *
        * Supported API calls: LMSGetValue()
        * LMS Mandatory: YES
        * Data Type: CMIIdentifier
        * Read Only
        *
        * Initialization: LMS sets it to the user id.
        */
        var learner_id = function() 
        {
            this.get = function()      { return value;           }
            this.set = function(param) { throw new myError('404'); }

            var value = "<?php $learner_id=$_SESSION['s_login']; echo $learner_id; ?>";
        }

        /**
        * cmi.core.student_name is the user full name
        *
        * Supported API calls: LMSGetValue()
        * LMS Mandatory: YES
        * Data Type: CMIString255
        * Read Only
        *
        * Initialization: LMS sets it to the user full name
        */
        var learner_name = function()
        {
            this.get = function()      { return value;           }
            this.set = function(param) { throw new myError('404'); }

            <?php
                /*Get the user name from the database*/
                $result       = eF_getTableData('users', 'name, surname', 'login="' .$_SESSION['s_login']. '"');
                $learner_name = $result[0]['surname'].' '.$result[0]['name'];
            ?>
            var value = "<?php echo $learner_name; ?>";
        }

        /**
        * cmi.core.location is the point where the user left to SCO. The LMS must store this value and return
        * it to the SCO when the user returns, if the SCO asks for it
        *
        * Supported API calls: LMSGetValue(), LMSSetValue()
        * LMS Mandatory: YES
        * Data Type: CMIString255
        * Read / Write
        *
        * Initialization: LMS sets it to ''. Using it is SCO responsibility.
        */
        var location = function()
        {
            this.get = function()
            {
                //<?php /* if ( isset($LMSToSCOValues['location']) ) echo "value = '".$LMSToSCOValues['location']."';"; */ ?>
                //if (SCOState['location'] && SCOState['location'] != '' && typeof SCOState['location'] != 'undefined')

				if (SCOState['lesson_location'] == null) 
					throw new myError('403');
				else
					return SCOState['lesson_location']; 



                return value;
            }
            this.set = function(param)
            {
                if (!checkDataType(param, 'characterstring', false))
                {
                    throw new myError('405');
                }
                else
                {
                    value = param;
                    //commitArray['lesson_location'] = value;
                    SCOState['lesson_location'] = value;

                    return "true";
                }
            }

            var value = null;
        }

        /**
        * cmi.core.credit siginifies whether the user is beeing tracked by the LMSduring this SCO. 
        * That is, it sets whether the SCO will send data to the LMS, which will be stored
        *
            this.max       = new max();
        * Supported API calls: LMSGetValue()
        * LMS Mandatory: YES
        * Data Type: CMIVocabulary "credit" , "no-credit"
        * Read Only
        *
        * Initialization: LMS responsibility.
        */
        var credit = function()
        {
            this.get = function()
            {
                return value;
            }
            this.set = function(param) { throw new myError('404'); }

            var value = '';
            <?php                                               //If the content is beeing reviews by the professor, we are in no-credit mode
                //if ($_SESSION['s_type'] == 'professor')     //todo
                  //  echo "value = 'no-credit';";
                //else
                    echo "value = 'credit';";
            ?>
        }

           /**
        * cmi.core.lesson_status is the user's status
        *
        * Supported API calls: LMSGetValue(), LMSSetValue()
        * LMS Mandatory: YES
        * Data Type: CMIVocabulary 'passed', 'completed', 'failed', 'incomplete', 'browsed', 'not attempted'
        * Read / Write
        *
        * Initialization: LMS responsibility.
        */
        var completion_status   = function()
        {
            this.get = function()
			{

				this.value = evaluateCompletionStatus(true);
				if(this.value == '') {
					return 'unknown';
				}

				return this.value;	

			}
            this.set = function(param)
            {
                if (!checkDataType(param, 'state', 'CompletionStatus'))
                {
                    throw new myError('405');
                }
                else
                {
                    this.value = param;
                    SCOState['completion_status'] = this.value;
                    return "true";
                }
            }
            this.value        = null;
        }




        /**
        * cmi.core.entry siginfies whether the user has previously visited this SCO
        *
        * Supported API calls: LMSGetValue()
        * LMS Mandatory: YES
        * Data Type: CMIVocabulary "ab-initio" , "resume", ""
        * Read Only
        *
        * Initialization: LMS responsibility.
        */
        var entry = function()
        {
            this.get = function()
			{

				<?php print_r($SCOState) ?>		

                if (SCOState['entry'] != null)                          //Since entry may be just '', we need to check if it is defined                
				{
                    value = SCOState['entry'];
				}
                return value;
            }
            this.set = function(param) { throw new myError('404'); }

			var value = 'ab-initio';
        }

        /*User's performance*/
        var score = function()
        {
            /**
            * cmi.core.score._children is a string that lists all the elements supported by το cmi.core.score
            *
            * Supported API calls: LMSGetValue()
            * LMS Mandatory: YES
            * Data Type: CMIString255
            * Read Only
            *
            * Initialization: All the score's children, so that an LMSGetValue call returns a comma-separated list
            */
            var _children = function()
            {
                this.get = function()      { return value;            }
                this.set = function(param) { throw new myError('404'); }

                var value = 'min,max,scaled,raw';
            }

            /**
            * cmi.core.score.raw is the user's performance in this SCO, and it is represented as number ranging from 0 to 100
            *
            * Supported API calls: LMSGetValue(), LMSSetValue()
            * LMS Mandatory: YES
            * Data Type: CMIDecimal or CMIBlank
            * Read / Write
            *
            * Initialization: The empty string ''
            */
            var raw = function()
            {
				this.get = function() 
				{
					if (this.value==null)
						throw new myError('403');
					else
						return this.value;
				}
                this.set = function(param)
                {
                    if (!checkDataType(param, 'CMIDecimal', false))
					{
						throw new myError('406');
					}
					else if (param < -0 || param > 100)
					{
						throw new myError('407');
					}
					this.value = param;
					SCOState['score'] = this.value;				
					return true;

				}
				this.value = null;				
            }

            /**
            * cmi.core.score.max is the user's maximum possible score
	    *
            * Supported API calls: LMSGetValue(), LMSSetValue()
            * LMS Mandatory: ΝΟ
            * Data Type: CMIDecimal or CMIBlank
            * Read / Write
            *
            * Initialization: The emtpy string ''
            */
/*
Uncomment this and comment below to make it non-implemented
            var max = function()
            {
                this.get = function()      { throw new myError('401')}
                this.set = function(param) { throw new myError('401')}
            }
*/
            var max = function()
            {
				this.get = function()      
				{
					if (this.value==null)
						throw new myError('403');
					else
						return this.value;
				}
                this.set = function(param)
                {
                    if ((!checkDataType(param, 'CMIDecimal', false) || param < 0) && !checkDataType(param, 'CMIBlank', false))
                    {
                        throw new myError('406');
                    }
                    else
                    {
                        this.value = param;
                        SCOState['maxscore'] = this.value;
                        return "true";
                    }
                }

                this.value = null;
            }

            /**
            * cmi.core.score.is the user's minimum possible score
            *
            * Supported API calls: LMSGetValue(), LMSSetValue()
            * LMS Mandatory: ΝΟ
            * Data Type: CMIDecimal or CMIBlank
            * Read / Write
            *
            * Initialization: The empty string ''
            */
/*
Uncomment this and comment below to make it non-implemented
            var min = function()
            {
                this.get = function()      { throw new myError('401')}
                this.set = function(param) { throw new myError('401')}
            }
*/
            var min = function()
            {
				this.get = function()      
				{
					if (this.value==null)
						throw new myError('403');
					else
						return this.value; 
		  		}
                this.set = function(param)
                {
                    if ((!checkDataType(param, 'CMIDecimal', false) || param < 0) && !checkDataType(param, 'CMIBlank', false))
                    {
                        throw new myError('406');
                    }
                    else
                    {
                        this.value = param;
                        SCOState['minscore'] = this.value;
                        return "true";
                    }
                }

                this.value = null;
			}


			/** SCORM 2004: New element
			*/
			var scaled = function()
            {
				this.get = function()      
				{ 
					if (this.value == null) 

						throw new myError('403');
					else
						return this.value; 
				}
                this.set = function(param)
                {
                   	if (!checkDataType(param, 'CMIDecimal', false))
					{
						throw new myError('406');
					}
					else if (param < -1 || param > 1)
					{
						throw new myError('407');
					}
					this.value = param;
					SCOState['score_scaled'] = this.value;
					return "true";
                }

                this.value = null;
			}


            this._children = new _children();
            this.raw       = new raw();
            this.max       = new max();
			this.min       = new min();
			this.scaled    = new scaled();
			
        }

        /**
        * cmi.core.total_time is the total (cumulative) time that the user has spent on this SCO. The time is of the form HHHH:MM:SS.SS
        *
        * Supported API calls: LMSGetValue()
        * LMS Mandatory: YES
        * Data Type: CMITimeSpan
        * Read Only
        *
        * Initialization: To 0000:00:00.00
        */
        var total_time = function()
        {
            this.get = function()
            {
        

					var time_split = SCOState['total_time'].split(':');

					var years = Math.floor(time_split[0]/8760);
					time_split[0] = time_split[0]%8760;

					var months = Math.floor(time_split[0]/720);
					time_split[0] = time_split[0]%720;

					var days = Math.floor(time_split[0]/24);
					time_split[0] = time_split[0]%24;

					var hours = time_split[0];

					var time = 'P' + years + 'Y' + months + 'M' + days + 'D' + 'T' + hours + 'H' + time_split[1] + 'M' + time_split[2] + 'S';

                    value = time;
                

                return value;
            }
            this.set = function(param) { throw new myError('404'); }

            var value = '00:00:00';
        }

        /**
        * cmi.core.lesson_mode is the SCO status.
        *
        * Supported API calls: LMSGetValue()
        * LMS Mandatory: No
        * Data Type: CMIVocabulary "browse", "normal", "review"
        * Read Only
        *
        * Initialization: To normal
        */
/*
Uncomment this and comment below to make it non-implemented
        var lesson_mode = function()
        {
            this.get = function()      { throw new myError('401')}
            this.set = function(param) { throw new myError('401')}
        }
*/
        var mode = function()
        {
            this.get = function()
            {
                if (SCOState['lesson_mode'])
                {
                    value = SCOState['lesson_mode'];
                }
                return value;
            }
            this.set = function(param) { throw new myError('404'); }

            var value = 'normal';
            //allowable values : 'browse', 'normal', 'review'
        }

        /**
        * cmi.core.exit siginfies how or why the user left the SCO.
        *
        * Supported API calls: LMSSetValue()
        * LMS Mandatory: YES
        * Data Type: state "time-out", "suspend", "logout", ""
        * Write Only
        *
        * Initialization: Not needed
        */
        var exit = function()
        {
            this.get = function()      { throw new myError('405'); }
            this.set = function(param)
            {
                count = 0;

                if (!checkDataType(param, 'state', 'Exit'))
                {
                    throw new myError('406');
                }
                else
                {
                    value = param;
                    SCOState['scorm_exit'] = value;
                    return "true";
                }
            }

            var value        = '';
            var legal_values = new Array('time-out', 'suspend', 'logout', 'normal', '');
            //allowable values : 'time-out', 'suspend', 'logout', ''
        }

        /**
        * cmi.core.session_time is the time the user has spent on this SCO during this session (opposed to total_time).
        *
        * Supported API calls: LMSSetValue()
        * LMS Mandatory: YES
        * Data Type: CMITimespan
        * Write Only
        *
        * Initialization: To 0000:00:00.00
        */
        var session_time = function()
        {
            this.get = function()      { throw new myError('405'); }
            this.set = function(param)
            {
                if (!checkDataType(param, 'timeinterval', false)) 
                {
					throw new myError('406');
                }
                else
				{

					var matches = param.match('^P(([0-9]+)Y)?(([0-9]+)M)?(([0-9]+)D)?(T(([0-9]+)H)?(([0-9]+)M)?(([0-9]+(\.[0-9]{1,2})?)?S)?)?');

					var hours = 0;
					var minutes = 0;
					var seconds = 0;

					if(matches[2]!=undefined)
					{
						hours =  parseInt(matches[2], 10)*8760;
					}
					if(matches[4]!=undefined)
					{
						hours += parseInt(matches[4], 10)*720;
					}
					if(matches[6]!=undefined)
					{
						hours += parseInt(matches[6], 10)*24;
					}
					if(matches[9]!=undefined)
					{
						hours += parseInt(matches[9], 10);
					}
					if(matches[11]!=undefined)
					{
						minutes = matches[11];
					}
					if(matches[13]!=undefined)
					{
						seconds = matches[13];
					}
					var time = hours + ':' + minutes + ':' + seconds;

					value = time;
					//alert(value);
                    SCOState['session_time'] = value;
                    return "true";
                }
            }

            var value = '';
	}


		/** SCORM 2004: New element
			*/
			var _version = function()
            {
                this.get = function()      { return value; }
                this.set = function(param)	{ throw new myError('404'); }

                var value = '1.0';
			}

	/**
		*SCORM 2004: New element
		*/ 

        /**
        * cmi.core.success_status indicates whether the learner has mastered the SCO.
        *
        * Supported API calls: LMSGetValue(), LMSSetValue()
        * LMS Mandatory: YES
        * Data Type: state 'passed', 'failed', 'unknown'
        * Read / Write
        *
        * Initialization: LMS responsibility.
        */
        var success_status   = function()
        {
            this.get = function()
			{
				this.value = evaluateSuccessStatus(true);
				if(this.value == '') {
					return 'unknown';
				}

				return this.value;

            }
            this.set = function(param)
            {
                if (!checkDataType(param, 'state', 'SuccessStatus'))
                {
                    throw new myError('405');
                }
                else
                {
                    this.value = param;
                    SCOState['success_status'] = this.value;
                    return "true";
                }
            }
            this.value        = null;
        }

		


	/**
		*SCORM 2004: New element
		*/
        /**
        * cmi.core.lesson_status indicates whether the learner has mastered the SCO.
        *
        * Supported API calls: LMSGetValue(), LMSSetValue()
        * LMS Mandatory: YES
		* Data Type: CMIVocabulary 'completed', 'incomplete', 'not attempted' 'unknown'
        * Read / Write
        *
        * Initialization: LMS responsibility.
        */
        var completion_threshold   = function()
        {
            this.get = function()
			{     
				if(this.value == null)
					throw new myError('403');
                return this.value;
            }
            this.set = function(param)
            {
                 throw new myError('404');
			}

            this.value = null;	
		}


		   /**
        * cmi.student_data.max_time_allowed is the maximum time available to the user, in order for him to finish the unit
        *
        * Supported API calls: LMSGetValue()
        * LMS Mandatory: NO
        * Data Type: CMITimespan
        * Read Only
        *
        * Initialization: Set by the LMS
        */
/*
        var max_time_allowed = function()
        {
            this.get = function()      { throw new myError('401')}
            this.set = function(param) { throw new myError('401')}
        }
*/
        var max_time_allowed = function()
		{
            this.get = function()
			{
				if (SCOState['maxtimeallowed'] == null) 
				{								 
					throw new myError('403');
				}
				else if (SCOState['maxtimeallowed'])
                {
                    value = SCOState['maxtimeallowed'];
                }
                return value;
            }
            this.set = function(param) { throw new myError('404'); }

            value = '';
		}

/** SCORM 2004 new element
 */


	var scaled_passing_score = function()
	{
		this.get = function()
		{
			if (this.value != null)
			{
				return this.value;
			}
			else
			{
				throw new myError('403');
			}
		}
		
        this.set = function(param) { throw new myError('404'); }
		   
		this.value = null;
	}

	var progress_measure = function()
    {
        this.get = function()
		{
			if (this.value != null)
			{
				return this.value;
			}
			else
			{
				throw new myError('403');
			}
        }
        this.set = function(param)
        {
		if (!checkDataType(param, 'CMIDecimal', false))
			{
				throw new myError('406');
			}
			else if (param < 0 || param > 1)
			{
				throw new myError('407');
			}

			this.value = param;
			SCOState['progress_measure'] = param;
			
		
				

            return "true";
        }
      	this.value        = null;
	}

       /**
        * cmi.student_data.time_limit_action is the action that will be performed when the user's available time is up
        *
        * Supported API calls: LMSGetValue()
        * LMS Mandatory: NO
        * Data Type: CMIVocabulary 'exit,message', 'exit,no message', 'continue,message', 'continue,no message'
        * Read Only
        *
        * Initialization: Set by the LMS
        */
/*
        var time_limit_action = function()
        {
            this.get = function()      { throw new myError('401')}
            this.set = function(param) { throw new myError('401')}
        }
*/
        var time_limit_action = function()
        {
            this.get = function()
			{
                if (SCOState['timelimitaction'])
                {
                    value = SCOState['timelimitaction'];
                }
                return value;
            }
            this.set = function(param) { throw new myError('404'); }

            var value = '';
            //legal values = 'exit,message', 'exit,no message', 'continue,message', 'continue,no message'
        }



        this._children				= new _children();
        this.learner_id				= new learner_id();
        this.learner_name			= new learner_name();
        this.location				= new location();
        this.credit					= new credit();
		this.completion_status		= new completion_status();
		this.scaled_passing_score	= new scaled_passing_score();
        this.entry					= new entry();
        this.score					= new score();
        this.total_time				= new total_time();
        this.mode					= new mode();
        this.exit					= new exit();
        this.session_time			= new session_time();
		this.success_status			= new success_status();
		this._version				= new _version(); 
		this.completion_threshold	= new completion_threshold();
		this.max_time_allowed		= new max_time_allowed();
		this.progress_measure		= new progress_measure();
		this.time_limit_action		= new time_limit_action();
			

    /**
    * cmi.suspend_data contains general information that the SCO wishes to store to the LMS. These must
    * be made available on restart
    *
    * Supported API calls: LMSGetValue(), LMSSetValue()
    * LMS Mandatory: YES
    * Data Type: CMIString4096
    * Read / Write
    *
    * Initialization: To ''
    */
    this.suspend_data = new function()
    {
        this.get = function()
        {
            if (SCOState['suspend_data'] != null)
            {
				value = SCOState['suspend_data'];
	            return value;
			}
			else if (value==null)
			{
				throw new myError('403')
			}
			else
			{
	            return value;				
			}

        }
        this.set = function(param)
        {
            if (!checkDataType(param, 'CMIString64000', false))
            {
                throw new myError('405');
            }
            else
            {
                value = param;
                SCOState['suspend_data'] = value;
                return "true";
            }
        }

        var value = null;
    }

    /**
    * cmi.launch_data contains general information which are needed when a SCO starts.
    * LMS reads these information from the "adlcp:datafromlms" manifest field
    *
    * Supported API calls: LMSGetValue()
    * LMS Mandatory: YES
    * Data Type: CMIString4096
    * Read Only
    *
    * Initialization: To ''
    */
    this.launch_data = new function()
    {
        this.get = function()
        {
            if (SCOState['datafromlms'])
            {
                value = SCOState['datafromlms'];
            }

            return value;
        }
        this.set = function(param) { throw new myError('404'); }

        var value = '';
    }

    /**
    * cmi.comments is used in order for the user to be able to send comments to the LMS regarding this SCO.
    *
    * Supported API calls: LMSGetValue(), LMSSetValue()
    * LMS Mandatory: NO
    * Data Type: CMIString4096
    * Read / Write
    *
    * Initialization: To ''
    */
/*
    this.comments = new function()
    {
        this.get = function()      { throw new myError('401')}
        this.set = function(param) { throw new myError('401')}
    }
*/
    this.comments = new function()
    {
        this.get = function()      { return value; }
        this.set = function(param)
        {
            if (!checkDataType(param, 'CMIString4096', false))
            {
                throw new myError('405');
            }
            else
            {
                value += param;
                SCOState['comments'] = value;
                return "true";
            }
        }

        var value = '';
    }

    /**
    * cmi.comments_from_lms is used from the user in order for him to see the user-defined comments set for this SCO
    *
    * Supported API calls: LMSGetValue()
    * LMS Mandatory: NO
    * Data Type: CMIString4096
    * Read Only
    *
    * Initialization: To ''
    */
/*
    this.comments_from_lms = new function()
    {
        this.get = function()      { throw new myError('401')}
        this.set = function(param) { throw new myError('401')}
    }
*/
/*
    this.comments_from_lms = new function()
    {
        this.get = function()
        {
            if (SCOState['comments_from_lms'])
            {
                value = SCOState['comments_from_lms'];
            }
            return value;
        }
        this.set = function(param) { throw new myError('401'); }

        var value = '';
    }
*/
    /*
    * It may adjust SCO depending on user performance
    */
    this.student_data = new function()
    {
        /**
        * cmi.student_data._children is a string that contains a list of all the elements supported by cmi.student_data
        *
        * Supported API calls: LMSGetValue()
        * LMS Mandatory: NO
        * Data Type: CMIString255
        * Read Only
        *
        * Initialization: All the element's children, so that a LMSGetValue returns a comme-separated list
        */
/*
        var _children = function()
        {
            this.get = function()      { throw new myError('401')}
            this.set = function(param) { throw new myError('401')}
        }
*/
        var _children = function()
        {
            this.get = function()      { return value;            }
            this.set = function(param) { throw new myError('402'); }

            var value = 'mastery_score,max_time_allowed,time_limit_action';
        }

        /**
        * cmi.student_data.mastery_score is the score that the user must succeed in order to pass the unit succesfully
        *
        * Supported API calls: LMSGetValue()
        * LMS Mandatory: NO
        * Data Type: CMIDecimal
        * Read Only
        *
        * Initialization: Set by the LMS
        */
/*
        var mastery_score = function()
        {
            this.get = function()      { throw new myError('401')}
            this.set = function(param) { throw new myError('401')}
        }
*/
        var mastery_score = function()
        {
            this.get = function()
            {
                if (SCOState['masteryscore'])
                {
                    value = SCOState['masteryscore'];
                }
                return value;
            }
            this.set = function(param) { throw new myError('403'); }

            value = '';
        }
 

        this._children         = new _children();
        this.mastery_score     = new mastery_score();
    }

    /*Options that may be needed in SCOs*/
    this.learner_preference = new function()
    {
        /**
        * cmi.student_preference._children is a string containing a list of all the elements supported by cmi.student_preference
        *
        * Supported API calls: LMSGetValue()
        * LMS Mandatory: NO
        * Data Type: CMIString255
        * Read Only
        *
        * Initialization: All the element's children, so that a LMSGetValue call returns a comma-separated list
        */
/*
        var _children = function()
        {
            this.get = function()      { throw new myError('401')}
            this.set = function(param) { throw new myError('401')}
        }
*/

		this.get = function()      { throw new myError('401')}
        this.set = function(param) { throw new myError('401')}


        var _children = function()
        {
            this.get = function()      { return value;            }
            this.set = function(param) { throw new myError('404'); }

            var value = 'language,delivery_speed,audio_captioning,audio_level';
        }

        /**
        * cmi.student_preference.audio sets the audio volume
        *
        * Supported API calls: LMSGetValue(), LMSSetValue()
        * LMS Mandatory: NO
        * Data Type: CMIInteger
        * Read / Write
        *
        * Initialization: Set by the LMS to 0
        */
/*
        var audio = function()
        {
            this.get = function()      { throw new myError('401')}
            this.set = function(param) { throw new myError('401')}
        }
*/
        var audio_level = function()
        {
            this.get = function()      { return this.value; }
            this.set = function(param)
            {
                if (!checkDataType(param, 'real', false))
                {
                    throw new myError('406');
				}
				else if (param<0)
				{
                    throw new myError('407');
				}
                else
                {
                    this.value = param;
                    return "true";
                }
            }
            this.value = '1';
        }

        /**
        * cmi.student_preference.language Sets the user preferable language
        *
        * Supported API calls: LMSGetValue(), LMSSetValue()
        * LMS Mandatory: NO
        * Data Type: CMIString255
        * Read / Write
        *
        * Initialization: Set by the LMS to ''
        */
/*
        var language = function()
        {
            this.get = function()      { throw new myError('401')}
            this.set = function(param) { throw new myError('401')}
        }
*/
        var language = function()
        {
            this.get = function()       { return this.value; }
            this.set = function(param)
            {
                if (!checkDataType(param, 'CMIString255', false))
                {
                    throw new myError('405');
                }
                else
                {
                    this.value = param;
                    return "true";
                }
            }
            this.value = '<?php echo $_SESSION["s_language"]?>';
        }

        /**
        * cmi.student_preference.speed sets the content rate
        *
        * Supported API calls: LMSGetValue(), LMSSetValue()
        * LMS Mandatory: NO
        * Data Type: CMISInteger
        * Read / Write
        *
        * Initialization: Set by the LMS to ''
        */
/*
        var speed = function()
        {
            this.get = function()      { throw new myError('401')}
            this.set = function(param) { throw new myError('401')}
        }
*/
        var delivery_speed = function()
        {
            this.get = function()      { return this.value; }
            this.set = function(param)
            {
                if (!checkDataType(param, 'real', false))
                {
                    throw new myError('406');
				}
				else if(param<0)
				{
                    throw new myError('407');				
				}
                else
                {
                    this.value = param;
                    return "true";
                }
            }
            this.value = '1';
        }

        /**
        * cmi.student_preference.text Sets whether the audio is accompanied by text also
        *
        * Supported API calls: LMSGetValue(), LMSSetValue()
        * LMS Mandatory: NO
        * Data Type: CMISInteger
        * Read / Write
        *
        * Initialization: Set by the LMS to ''
        */
/*
        var text = function()
        {
            this.get = function()      { throw new myError('401')}
            this.set = function(param) { throw new myError('401')}
        }
*/
        var audio_captioning = function()
        {
            this.get = function()      { return this.value; }
            this.set = function(param)
            {
                if (!checkDataType(param, 'CMISInteger', false) || (param != "1" && param != "0" && param != "-1"))
                {
                    throw new myError('406');
                }
                else
                {
                    this.value = param;
                    return "true";
                }
            }
            this.value = '0';
        }

        this._children = new _children();
        this.audio_level     = new audio_level();
        this.language  = new language();
        this.delivery_speed     = new delivery_speed();
		this.audio_captioning  = new audio_captioning();
    }





    /*
    * Siginfies the accomplishment level of the user to the SCO objectives
    */
    var objectives = new Array();

    /**
    * Το cmi.objectives._children is a string containing a list of all the elements supported by cmi.objectives
    *
    * Supported API calls: LMSGetValue()
    * LMS Mandatory: NO
    * Data Type: CMIString255
    * Read Only
    *
    * Initialization: A comma-spearated list of elements
    */
/*
    objectives._children = new function()
    {
        this.get = function()      { throw new myError('401')}
        this.set = function(param) { throw new myError('401')}
    }
*/
    objectives._children = new function()
    {
        this.get = function()      { return value;            }
        this.set = function(param) { throw new myError('404'); }
        var value = 'success_status,progress_measure,description,id,completion_status,score';
    }

    /**
    * cmi.objectives._count The number of entries currently in the cmi.objectives list
    *
    * Supported API calls: LMSGetValue()
    * LMS Mandatory: NO
    * Data Type: CMIString255
    * Read Only
    *
    * Initialization: Total number of entries
    */
/*
    objectives._count = new function()
    {
        this.get = function()      { throw new myError('401')}
        this.set = function(param) { throw new myError('401')}
    }
*/
    objectives._count = new function()
    {
        this.get = function()      { return cmi.objectives.length; }
        this.set = function(param) { throw new myError('404'); }
    }

    objectives.get = function()
    {
		throw new myError('401');
	}
    objectives.set = function()
    {
		throw new myError('401');
	}

    //Assign objectives to cmi object
    this.objectives = objectives;

/*
    * Siginfies the accomplishment level of the user to the SCO objectives
    */
    var comments_from_learner = new Array();

    /**
    * Το cmi.objectives._children is a string containing a list of all the elements supported by cmi.objectives
    *
    * Supported API calls: LMSGetValue()
    * LMS Mandatory: NO
    * Data Type: CMIString255
    * Read Only
    *
    * Initialization: A comma-spearated list of elements
    */
/*
    objectives._children = new function()
    {
        this.get = function()      { throw new myError('401')}
        this.set = function(param) { throw new myError('401')}
    }
*/
    comments_from_learner._children = new function()
    {
        this.get = function()      { return value;            }
        this.set = function(param) { throw new myError('404'); }
        var value = 'location,comment,timestamp';
    }

    /**
    * cmi.objectives._count The number of entries currently in the cmi.objectives list
    *
    * Supported API calls: LMSGetValue()
    * LMS Mandatory: NO
    * Data Type: CMIString255
    * Read Only
    *
    * Initialization: Total number of entries
    */
/*
    objectives._count = new function()
    {
        this.get = function()      { throw new myError('401')}
        this.set = function(param) { throw new myError('401')}
    }
*/
    comments_from_learner._count = new function()
    {
        this.get = function()      { return cmi.comments_from_learner.length; }
        this.set = function(param) { throw new myError('404'); }
    }

    comments_from_learner.get = function()
    {
		throw new myError('401');
	}
    comments_from_learner.set = function()
    {
		throw new myError('401');
	}


    //Assign objectives to cmi object
	this.comments_from_learner = comments_from_learner;



	/*
    * Siginfies the accomplishment level of the user to the SCO objectives
    */
    var comments_from_lms = new Array();

    /**
    * Το cmi.objectives._children is a string containing a list of all the elements supported by cmi.objectives
    *
    * Supported API calls: LMSGetValue()
    * LMS Mandatory: NO
    * Data Type: CMIString255
    * Read Only
    *
    * Initialization: A comma-spearated list of elements
    */
/*
    objectives._children = new function()
    {
        this.get = function()      { throw new myError('401')}
        this.set = function(param) { throw new myError('401')}
    }
*/
    comments_from_lms._children = new function()
    {
        this.get = function()      { return value;            }
        this.set = function(param) { throw new myError('404'); }
        var value = 'location,comment,timestamp';
    }

    /**
    * cmi.objectives._count The number of entries currently in the cmi.objectives list
    *
    * Supported API calls: LMSGetValue()
    * LMS Mandatory: NO
    * Data Type: CMIString255
    * Read Only
    *
    * Initialization: Total number of entries
    */
/*
    objectives._count = new function()
    {
        this.get = function()      { throw new myError('401')}
        this.set = function(param) { throw new myError('401')}
    }
*/
    comments_from_lms._count = new function()
    {
        this.get = function()      { return cmi.comments_from_lms.length; }
        this.set = function(param) { throw new myError('404'); }
    }

    comments_from_lms.get = function()
    {
		throw new myError('401');
	}
    comments_from_lms.set = function()
    {
		throw new myError('401');
	}


    //Assign objectives to cmi object
    this.comments_from_lms = comments_from_lms;


    /**
    * cmi.interactions handles user-defined data 
    */
    var interactions = new Array();

    /**
    * cmi.interactions._children is a string containing the elements supported by cmi.interactions
    *
    * Supported API calls: LMSGetValue()
    * LMS Mandatory: NO
    * Data Type: CMIString255
    * Read Only
    *
    * Initialization: A comma-separated list of elements
    */
/*
    interactions._children = new function()
    {
        this.get = function()      { throw new myError('401')}
        this.set = function(param) { throw new myError('401')}
    }
*/
    interactions._children = new function()
    {
        this.get = function()      { return value;            }
        this.set = function(param) { throw new myError('404'); }
        var value = 'description,id,type,objectives,timestamp,result,learner_response,weighting,latency,correct_responses';
    }

    /**
    * cmi.interactions._count contains the number of entries currently in cmi.interactions
    *
    * Supported API calls: LMSGetValue()
    * LMS Mandatory: NO
    * Data Type: CMIInteger
    * Read Only
    *
    * Initialization: The total number of entries
    */
/*
    interactions._count = new function()
    {
        this.get = function()      { throw new myError('401')}
        this.set = function(param) { throw new myError('401')}
    }
*/
    interactions._count = new function()
    {
        this.get = function()      { return cmi.interactions.length; }
        this.set = function(param) { throw new myError('404'); }
    }
  	interactions.get = function()
    {
		throw new myError('401');
	}
    interactions.set = function()
    {
		throw new myError('401');
	}

    //Assign interactions to cmi object
    this.interactions = interactions;

}   //end of cmi



dataObject = function()
{
    this.id = new function()
	{

        this.get = function()
		{
			if (this.value === null)         //means that is not initialized yet
            {
				throw new myError('301');
                return '';             //Return empty string and throw error
            }
            return this.value;
       	}
        this.set = function(param)
		{
		
				throw new myError('404');

		}

		this.value = null;
	}
	this.store = new function()
    {
        this.get = function()
		{

			if(this.read == "false")
			{
				throw new myError('405')
			}
			else if (this.value=='')
			{
				throw new myError('403')
			}
			else
			{
	            return unescape(this.value);				
			}

        }
        this.set = function(param)
		{
//alert(param);

			if(this.write == "false")
			{
                throw new myError('404');
			}
            if (!checkDataType(param, 'CMIString64000', false))
            {
                throw new myError('405');
            }
            else
            {
				this.value = escape(param);
                return "true";
            }
        }
		this.value = '';
		this.write = "true";
		this.read = "true";

    }
}



objectivesObject = function()
{
/*
    this.id = new function()
    {
        this.get = function()      { throw new myError('401')}
        this.set = function(param) { throw new myError('401')}
    }
*/
    this.id = new function()
	{

        this.get = function()
		{
			if (this.value === null)         //means that is not initialized yet
            {
				throw new myError('301');
                return '';             //Return empty string and throw error
            }
            return this.value;
       	}
        this.set = function(param)
		{
		
			param = form_id(param);

			if (this.value != null && param !=this.value )         //means that is not initialized yet
            {
				throw new myError('351');
			}

 
			if (!checkDataType(param, 'CMIIdentifier', false))
			{
				cmi.objectives.splice(_TEMP, 1);
				throw new myError('406');
			}

			if (param.substring(0,4) == 'urn:' && !checkDataType(param.substring(4, param.length), 'CMIIdentifier', false))
			{
				throw new myError('406');
			}

				this.value = param;			
                return true;
		}


		this.set1 = function(param)
		{	

			if (!checkDataType(param, 'CMIIdentifier', false))
			{
				throw new myError('406');
			}
			if (param.substring(0,4) == 'urn:' && !checkDataType(param.substring(4, param.length), 'CMIIdentifier', false))
			{
				throw new myError('406');
			}

			this.value = param;
			return true;
		}

		this.value = null;
	}


/* SCORM 2004 new element
 */
    this.description = new function()
    {
		this.get = function()      
		{
			if (this.value==null)
				throw new myError('403');
			else
				return this.value;
		}
		this.set = function(param) 
		{
			this.value = param;
			return true; 
		}
		this.value = null;
    }


    this.score = new function()
    {
/*
        this._children = new function()
        {
            this.get = function()      { throw new myError('401')}
            this.set = function(param) { throw new myError('401')}
        }
*/
        this._children = new function()
        {
            this.get = function()      { return this.value;            }
            this.set = function(param) { throw new myError('404'); }
            this.value = 'min,max,scaled,raw';
        }
/*
        this.raw = new function()
        {
            this.get = function()      { throw new myError('401')}
            this.set = function(param) { throw new myError('401')}
        }
*/
        this.raw = new function()
        {
			this.get = function()
			{ 
				if (this.value==='')
					throw new myError('403');
				else
					return this.value;
			 }
            this.set = function(param)
            {
                if (!checkDataType(param, 'CMIDecimal', false) && !checkDataType(param, 'CMIBlank', false))
                {
                    throw new myError('406');
                }
                else
                {
                    this.value = param;
                    return "true";
                }
            }

            this.value = '';
        }
/*
        this.max = new function()
        {
            this.get = function()      { throw new myError('401')}
            this.set = function(param) { throw new myError('401')}
        }
*/
        this.max = new function()
        {
			this.get = function()      
			{
				if (this.value==='')
					throw new myError('403');
				else
					return this.value;
			}
            this.set = function(param)
            {
                if ((!checkDataType(param, 'CMIDecimal', false) || param < 0 ) && !checkDataType(param, 'CMIBlank', false))
                {
                    throw new myError('406');
                }
                else
                {
                    this.value = param;
                    return "true";
                }
            }

            this.value = '';
        }
/*
        this.min = new function()
        {
            this.get = function()      { throw new myError('401')}
            this.set = function(param) { throw new myError('401')}
        }
*/
        this.min = new function()
        {
			this.get = function() 
			{ 
				if (this.value==='')
					throw new myError('403');
				else
					return this.value;
			}
            this.set = function(param)
            {
                if ((!checkDataType(param, 'CMIDecimal', false)) && !checkDataType(param, 'CMIBlank', false))
                {
                    throw new myError('406');
                }
                else
                {
                    this.value = param;
                    return "true";
                }
            }

            this.value = '';
		}

/* SCORM 2004 new element
 */

	this.scaled = new function()
    {
        this.get = function()
		{
			if (this.value==='')
				throw new myError('403');
			else
				return this.value;
        }
        this.set = function(param)
		{
			if (!checkDataType(param, 'CMIDecimal', false))
			{
				throw new myError('406');
			}
			else if (param < -1 || param > 1)
			{
				throw new myError('407');
			}
			this.value = param;
			return true;
        }
      	this.value        = '';
    }



	}



/*
    this.status = new function()
    {
        this.get = function()      { throw new myError('401')}
        this.set = function(param) { throw new myError('401')}
    }
*/







/** SCORM 2004
 * status -> succes_status, lesson_status
 *
*/
	this.success_status = new function()
	{
        this.get = function()
		{
			if(this.value==null)
			{	
				return 'unknown';
			}

            return this.value;
        }
        this.set = function(param)
        {
            count = 0;

            if (!checkDataType(param, 'state', 'SuccessStatus'))
			{
                throw new myError('406');
            }
            else
			{
                this.value = param;
                return "true";
            }
        }
		
        this.value = null;
        var legal_values = new Array('passed', 'failed', 'unknown');
    }




	this.completion_status = new function()
    {
        this.get = function()
		{ 
			if(this.value==null)
			{
				return 'unknown';
			}

            return this.value;
        }
        this.set = function(param)
        {
            count = 0;

            if (!checkDataType(param, 'state', 'CompletionStatus'))
            {
                throw new myError('406');
            }
            else
            {
				this.value = param;

                return "true";
            }
        }

        this.value        = null;
        var legal_values = new Array('completed', 'incomplete', 'not attempted', 'uknown');
	}
/** SCORM 2004 new element
 */

	this.progress_measure = new function()
    {
        this.get = function()
		{
			if (this.value==='')
				throw new myError('403');
			else
				return this.value;
        }
        this.set = function(param)
        {
		if (!checkDataType(param, 'CMIDecimal', false))
			{
				throw new myError('406');
			}
			else if (param < 0 || param > 1)
			{
				throw new myError('407');
			}
			this.value = param;
			return true;
        }
      	this.value        = '';
	}


}

interactionsObject = function()
{
/*
    this.id = new function()
    {
        this.get = function()      { throw new myError('401')}
        this.set = function(param) { throw new myError('401')}
    }
*/
    this.id = new function()
    {
		this.get = function()
		{         
			return this.value;
		}
        this.set = function(param)
        {
            if (!checkDataType(param, 'CMIIdentifier', false))
            {
                throw new myError('406');
			}

			if (param.substring(0,4) == 'urn:' && !checkDataType(param.substring(4, param.length), 'CMIIdentifier', false))
			{
				throw new myError('406');
			}

            this.value = param;
            return "true";
        }

        this.value = null;
    }

    var objectives = new Array();
/*
    objectives._count = new function()
    {
        this.get = function()      { throw new myError('401')}
        this.set = function(param) { throw new myError('401')}
    }
*/
    objectives._count = new function()
    {
    //  this.get = function()      { return 1000; }
        this.set = function(param) { throw new myError('404'); }
    }
    objectives._count.get = function()     { return objectives.length; }
    this.objectives = objectives;
/*
    this.time = new function()
    {
        this.get = function()      { throw new myError('401')}
        this.set = function(param) { throw new myError('401')}
    }
*/
    this.timestamp = new function()
    {
		this.get = function()      
		{	
			if (this.value=='')
				throw new myError('403');
			else
				return this.value;
		}
        this.set = function(param)
        {
            if (!checkDataType(param, 'time', false))            //edw 8elei ena RE
            {
                throw new myError('406');
            }
            else
            {
                this.value = param;
                return "true";
            }
        }

        this.value = '';
    }
/*
    this.type = new function()
    {
        this.get = function()      { throw new myError('401')}
        this.set = function(param) { throw new myError('401')}
    }
*/
    this.type = new function()
    {
		this.get = function()      
		{
			if (this.value==='')
			{
				throw new myError('403');
			}
			else
			{
				return this.value;
			}
		}
        this.set = function(param)
		{
			if (!checkDataType(param, 'state', 'Interaction'))          //edw 8elei ena RE
            {
                throw new myError('406');
            }
            else
            {
                this.value = param;
                return "true";
            }
        }

        this.getValue = function() {
            return this.value;
        }

        this.value = '';
	}

   

    var correct_responses = new Array();
/*
    correct_responses._count = new function()
    {
        this.get = function()      { throw new myError('401')}
        this.set = function(param) { throw new myError('401')}
    }
*/
    correct_responses._count = new function()
    {
    //  this.get = function()      { return cmi.objectives.length; }
        this.set = function(param) { throw new myError('402'); }
    }
    correct_responses._count.get = function()      { return correct_responses.length; }
    this.correct_responses = correct_responses;
/*
    this.weighting = new function()
    {
        this.get = function()      { throw new myError('401')}
        this.set = function(param) { throw new myError('401')}
    }
*/
    this.weighting = new function()
    {
		this.get = function()      
		{
			if (this.value=='')
				throw new myError('403');
			else
				return this.value;
		}
        this.set = function(param)
        {
            if (!checkDataType(param, 'CMIDecimal', false))         //edw 8elei ena RE
            {
                throw new myError('406');
            }
            else
            {
                this.value = param;
                return "true";
            }
        }

        this.value = '';
    }
/*
		this.student_response = new function()
    {
        this.get = function()      { throw new myError('401')}
        this.set = function(param) { throw new myError('401')}
    }
*/
    this.learner_response = new function()
    {
		this.get = function()      
		{
			if(this.value==null)
                throw new myError('403');
				
			return this.value;
		}
        this.set = function(param)
		{
			
			if (eval('cmi.interactions[' + _TEMP + '].type.getValue()') == '')
            {
                throw new myError('408');
            }
            if (!checkDataType(param, 'CMIFeedback', eval('cmi.interactions[' + _TEMP + '].type.getValue()')))
            {
                throw new myError('406');
            }
            else
            {
                this.value = param;
                return "true";
            }
        }

        this.value = null;
    }
/*
    this.result = new function()
    {
        this.get = function()      { throw new myError('401')}
        this.set = function(param) { throw new myError('401')}
    }
*/
    this.result = new function()
    {
		this.get = function()
		{
			if (this.value=='')
				throw new myError('403');
			else
				return this.value;
		}
        this.set = function(param)
        {
            if (!checkDataType(param, 'state', 'Result'))           //edw 8elei ena RE
            {
                throw new myError('406');
            }
            else
            {
                this.value = param;
                return "true";
            }
        }

        this.value = '';
	}


 /* SCORM 2004 new element
 */
    this.description = new function()
    {
		this.get = function()      
		{
			if (this.value==null)
				throw new myError('403');
			else
				return decodeURIComponent(this.value);
		}
		this.set = function(param) 
		{
			this.value = param;
			return true; 
		}
		this.value = null;
    }




/*
    this.latency = new function()
    {
        this.get = function()      { throw new myError('401')}
        this.set = function(param) { throw new myError('401')}
    }
*/
    this.latency = new function()
    {
		this.get = function()      
		{
			if (this.value=='')
				throw new myError('403');
			else
				return this.value;
		}
        this.set = function(param)
        {
            if (!checkDataType(param, 'timeinterval', false))            //edw 8elei ena RE
            {
                throw new myError('406');
            }
            else
            {
                this.value = param;
                return "true";
            }
        }

        this.value = '';
    }
}

correct_responsesObject = function()
{
/*
    this.pattern = new function()
    {
        this.get = function()      { throw new myError('401')}
        this.set = function(param) { throw new myError('401')}
    }
*/
    this.pattern = new function()
    {
		this.get = function()      
		{
			if(this.value==null)
			{	
                throw new myError('301');
			}
			return this.value;
		}
        this.set = function(param)
		{

			if (eval('cmi.interactions[' + _TEMP + '].type.getValue()') == '')
            {
                throw new myError('408');
            }


            if (!checkDataType(param, 'CMIFeedback', eval('cmi.interactions[' + _TEMP + '].type.getValue()')))
			{	
				if(this.value == null)
				{
		
					eval('cmi.interactions[' + _TEMP + '].correct_responses.splice(_TEMP2, 1)');

				} 
		
                throw new myError('406');
			}	
            else
            {
                this.value = param;
                return "true";
            }
        }

        this.value = null;
    }
}







comments_from_learnerObject = function()
{
/*
    this.id = new function()
    {
        this.get = function()      { throw new myError('401')}
        this.set = function(param) { throw new myError('401')}
    }
*/
    this.id = new function()
    {
        this.get = function()
        {
            if (value === null)         //means that is not initialized yet
			{
               throw new myError('301');				
               return '';             //Return empty string and throw error
			}
			else
            	return value;
        }
        this.set = function(param)
		{

			if (param === value)
			{
                return "true";
            }
			else if (!checkDataType(param, 'CMIIdentifier', false))
            {
                throw new myError('408');
			}
		
			else if (value != null)         //means that has been initialized
            {
                throw new myError('351');
            }
            else
			{
				value = param;
                return "true";
            }
        }

        var value = null;
	}

/* SCORM 2004 new element
 */
    this.comment = new function()
    {
		this.get = function()      
		{
			if (this.value==null)
				throw new myError('403');
			else
				return this.value;
		}
		this.set = function(param) 
		{

			if(!check_localized_string(param)){
				throw new myError('406');
			}

			this.value = param;
            return "true";
		}
		this.value = null;
	}

/* SCORM 2004 new element
 */
    this.location = new function()
    {
		this.get = function()      
		{
			if (this.value==null)
				throw new myError('403');
			else
				return this.value;
		}
		this.set = function(param) 
		{
			this.value = param;
			return true; 
		}
		this.value = null;
    }
 this.timestamp = new function()
    {
		this.get = function()
		{ 
			if (this.value==null)
				throw new myError('403');
			else
				return this.value;

		}
        this.set = function(param)
		{

	
			if (!checkDataType(param, 'time', false))            //edw 8elei ena RE
            {
                throw new myError('406');
            }
            else
            {
                this.value = param;
                return "true";
            }
        }

        this.value = null;
    }


}




comments_from_lmsObject = function()
{
/*
    this.id = new function()
    {
        this.get = function()      { throw new myError('401')}
        this.set = function(param) { throw new myError('401')}
    }
*/
    this.id = new function()
    {
        this.get = function()
        {
            if (value === null)         //means that is not initialized yet
			{
			   throw new myError('301');				
               return '';             //Return empty string and throw error
			}
			else
            	return value;
        }
        this.set = function(param) { throw new myError('404')}
     
        var value = null;
	}

/* SCORM 2004 new element
 */
    this.comment = new function()
    {
		this.get = function()      
		{
			if (this.value==null)
				throw new myError('403');
			else
				return this.value;
		}
		this.set = function(param) {throw new myError('404')}
		this.value = null;
	}

/* SCORM 2004 new element
 */
    this.location = new function()
    {
		this.get = function()      
		{
			if (this.value==null)
				throw new myError('403');
			else
				return this.value;
		}
		this.set = function(param) {throw new myError('404')}

		this.value = null;
    }
 this.timestamp = new function()
    {
		this.get = function()
		{ 
			if (this.value==null)
				throw new myError('403');
			else
				return this.value;

		}
		this.set = function(param) {throw new myError('404')}
      

        this.value = null;
    }


}


function myADL()
{
  	this.nav = new function()
    {
        var request = function()
        {
            this.get = function()      { return value; }
			this.set = function(param)
			{ 
				value=param;
                SCOState['navigation'] = value;

				return "true";
			}

			var value = '';
		}
		
		var request_valid = function()
        {
            var mycontinue = function()
            {
                this.get = function()      { return value; }
				this.set = function(param) 
				{ 
					value = param;
				}
                var value = 'unknown';
			}

            var previous = function()
            {
                this.get = function()      { return value; }
				this.set = function(param) 
				{ 
					value = param;
				}
                var value = 'unknown';
			}
			this.continue = new mycontinue();
			this.previous = new previous();

			var choice = function()
			{

			

				<?php

					$iterator = new EfrontNodeFilterIterator(new RecursiveIteratorIterator($scoContent -> tree, RecursiveIteratorIterator :: SELF_FIRST));  
					foreach ($iterator as $key => $value) {

					$value = trim(preg_replace('[-|\.]', "", $value['identifier']));
					

					echo "var ".$value." = function(){
					
					this.get = function() { return value;}
					this.set = function(param) 
					{ 
						value = param;
					}
					var value = 'unknown';
			
			
			
				};\n";


				echo "\nthis.".$value." = new ".$value."();";

			}
 
		?>
		}
		this.choice = new choice();

		var jump = function()
			{

		
			
		<?php
		$iterator = new EfrontNodeFilterIterator(new RecursiveIteratorIterator($scoContent -> tree, RecursiveIteratorIterator :: SELF_FIRST));  
					foreach ($iterator as $key => $value) {

					$value = trim(preg_replace('[-|\.]', "", $value['identifier']));
					

					echo "var ".$value." = function(){
					
					this.get = function() { return value;}
					this.set = function(param) 
					{ 
						value = param;
					}
					var value = 'unknown';
			
			
			
				};\n";


					echo "\nthis.".$value." = new ".$value."();";
 
					}	
		?>
		}
		this.jump = new jump();
		}
		this.request       		 = new request();
		this.request_valid       = new request_valid();		
	}
	



	/*
    * Siginfies the accomplishment level of the user to the SCO objectives
    */
    var data = new Array();

    /**
    * Το cmi.objectives._children is a string containing a list of all the elements supported by cmi.objectives
    *
    * Supported API calls: LMSGetValue()
    * LMS Mandatory: NO
    * Data Type: CMIString255
    * Read Only
    *
    * Initialization: A comma-spearated list of elements
    */

    data._children = new function()
    {
        this.get = function()      { return value;            }
        this.set = function(param) { throw new myError('404'); }
        var value = 'id,store';
    }

    /**
    * cmi.objectives._count The number of entries currently in the cmi.objectives list
    *
    * Supported API calls: LMSGetValue()
    * LMS Mandatory: NO
    * Data Type: CMIString255
    * Read Only
    *
    * Initialization: Total number of entries
    */

    data._count = new function()
    {
        this.get = function()      { return adl.data.length; }
        this.set = function(param) { throw new myError('404'); }
    }

    data.get = function()
    {
		throw new myError('401');
	}
    data.set = function()
    {
		throw new myError('401');
	}



    //Assign objectives to cmi object
    this.data = data;


}
<?php

?>
