<?php

//navigation_request_process('start');

define("S_DEBUG", 1);

class navigation 
{
	var $current_activity;
	var $suspended_activity;
	var $target_activity;
    var $navigation_request; 
	var $sequencing_request;
	var $exception;
	var $termination_request;

	function __construct($navigation_request, $currentContent) {
	
		$result = eF_getTableData("scorm_sequencing_global_state_information", "current_activity, suspended_activity", "users_LOGIN = '".$_SESSION['s_login']."' AND lesson_ID = '".$_SESSION['s_lessons_ID']."'");
	
		if (sizeof($result))
		{
			if($result[0]['current_activity'])
				$this->current_activity	= $currentContent -> seekNode($result[0]['current_activity']);
			else
				$this->current_activity   = null;
			
			if($result[0]['suspended_activity'])
				$this->suspended_activity	= $currentContent -> seekNode($result[0]['suspended_activity']);
			else
				$this->suspended_activity   = null;
		}
		else
		{
			$this->current_activity		= null;            //Initialize current unit
			$this->suspended_activity	= null;
		}

		$this->navigation_request	= $navigation_request;		
		$this->target_activity		= null;
		$this->sequencing_request	= null;
		$this->exception			= null;
		$this->termination_request	= null;
	}

	function setCurrentActivity($activity) {

		if($activity instanceof EfrontUnit)	{
			$this->current_activity = $activity;		
			$activity = $activity->offsetGet('id');
		}
		else {
			$this->current_activity = null;
			$activity = null;			
		}

		eF_insertOrupdateTableData("scorm_sequencing_global_state_information", array('lesson_ID'=>$_SESSION['s_lessons_ID'], 'users_LOGIN'=>$_SESSION['s_login'], 'current_activity'=>$activity), "users_LOGIN = '".$_SESSION['s_login']."' AND lesson_ID=".$_SESSION['s_lessons_ID']);
	}
	
	function setSuspendedActivity($activity) {

		if($activity instanceof EfrontUnit) {
			$this->suspended_activity = $activity;
			$activity = $activity->offsetGet('id');			
		}
		else {
			$this->suspended_activity = null;
			$activity = null;						
		}

		eF_insertOrupdateTableData("scorm_sequencing_global_state_information", array('lesson_ID'=>$_SESSION['s_lessons_ID'], 'users_LOGIN'=>$_SESSION['s_login'], 'suspended_activity'=>$activity), "users_LOGIN = '".$_SESSION['s_login']."' AND lesson_ID=".$_SESSION['s_lessons_ID']);
	}
}



function overall_sequencing_process($navigation, $currentContent)
{

		if(S_DEBUG)
		{
			echo 'Overall Sequencing Process=>';
		}

	$navigation = navigation_request_process($navigation, $currentContent);

	if($navigation->navigation_request)
	{
		if ($navigation->termination_request != null)
		{
			$navigation = termination_request_process($navigation, $currentContent);
		}
	}

	if($navigation->sequencing_request)
	{	
		$navigation = sequencing_request_process($navigation, $currentContent);
	}

	if($navigation->delivery_request)
	{
		$navigation = delivery_request_process($navigation, $currentContent);

		if(!$navigation->delivery_request_valid)
		{
			//behavior not specified
		}
		else
		{   
			$navigation = content_delivery_environment_process($navigation, $currentContent);		
		}
	}

	return $navigation;
}



function navigation_request_process($navigation, $currentContent)
{
	if(S_DEBUG)
	{
		echo 'Navigation Request Process=>';
	}

	$matches = split('[\{\}]', $navigation->navigation_request);
	
	if($matches[0]=='choice')
	{

		$navigation->navigation_request = $matches[0];
	}


	switch($navigation->navigation_request) 
	{
	case 'start':
			if(!$navigation->current_activity)
			{
				$navigation->navigation_request = 'true';
				$navigation->sequencing_request = 'start';
			}
			else
			{
				$navigation->navigation_request = false;
				$navigation->exception = 'NB.2.1-1';
			}
			break;
		case 'resumeAll':
			if(S_DEBUG)
			{
				echo '[resume all]';
			}

			if($navigation->current_activity)
			{
				if($navigation->suspended_activity)
				{
					$navigation->navigation_request = 'true';
					$navigation->sequencing_request = 'resumeAll';
				}
				else
				{
					$navigation->navigation_request = 'false';				
					$navigation->exception = 'NB.2.1-3';
				}
			}
			else
			{
				$navigation->navigation_request = 'false';				
				$navigation->exception = 'NB.2.1-1';				
			}
			break;
		case 'continue':
			
			if(!$navigation->current_activity)
			{
				$navigation->navigation_request = 'false';
				$navigation->exception = 'NB.2.1-1';

				return $navigation;
			}

			if(!$currentContent->isRoot(false, $navigation->current_activity) && $currentContent->seekNode($navigation->current_activity->offsetGet('parent_content_ID'))->offsetGet('flow')=='true')
			{
				if(is_active($navigation->current_activity->offsetGet('id')))
				{
					$navigation->navigation_request = 'true';
					$navigation->termination_request = 'exit';
					$navigation->sequencing_request = 'continue';					
				}
				else
				{
					
					$navigation->navigation_request = 'true';
					$navigation->sequencing_request = 'continue';	
				}
			}
			else
			{
				$navigation->navigation_request = 'false';
				$navigation->exception = 'NB.2.1-4';
			}
			break;
		case 'previous':
			if(!$navigation->current_activity)
			{
				$navigation->navigation_request = 'false';				
				$navigation->exception = 'NB.2.1-3';
			}
			if(!$currentContent->isRoot(false, $navigation->current_activity))
			{

				$parent = $currentContent->seekNode($navigation->current_activity->offsetGet('parent_content_ID'));

				if($parent->offsetGet('flow') != 'false' && $parent->offsetGet('forward_only') != 'true')
				{

					if(is_active($navigation->current_activity->offsetGet('id')))
					{
						$navigation->navigation_request = 'true';
						$navigation->termination_request = 'exit';
						$navigation->sequencing_request = 'previous';	
					}
					else
					{
						$navigation->navigation_request = 'true';
						$navigation->sequencing_request = 'previous';	
					}
				}
				else
				{
					$navigation->navigation_request = 'false';
					$navigation->exception = 'NB.2.1-5';
				}
			}
			else
			{
				$navigation->navigation_request = 'false';
				$navigation->exception = 'NB.2.1-6';			
			}
		case 'forward':
			$navigation->navigation_request = 'false';
			$navigation->exception = 'NB.2.1-7';	
			break;
		case 'backward':
			$navigation->navigation_request = 'false';
			$navigation->exception = 'NB.2.1-7';
			break;
		case 'choice':

			$target = $currentContent->seekNode($matches[1]);

			if(true) //if exists
			{
			//	$target_parent = $currentContent->seekNode($target->offsetGet('parent_content_ID')); 


				if($currentContent->isRoot(false, $target) || $currentContent->seekNode($target->offsetGet('parent_content_ID'))->offsetGet('choice')=='true')
				{
					if(S_DEBUG)
					{
					}

					if(!$navigation->current_activity)
					{
						$navigation->navigation_request = 'true';
						$navigation->sequencing_request = 'choice';
						$navigation->target_activity = $target;

						return $navigation;
					}
					if(!are_siblings($target, $navigation->current_activity))
					{
						
						if(S_DEBUG)
						{
						}

						$ancestors = $currentContent->getNodeAncestors($navigation->current_activity);
						$common_ancestor = $currentContent->findCommonAncestor($ancestors, $target);

						$activity_path = form_activity_path($currentContent, $navigation->current_activity, $common_ancestor, array($common_ancestor)); //todo exclude common ancestor not properly defined in scorm standard(see imsss)


						if (sizeof($activity_path)>0)
						{
							foreach ($activity_path as $value)
							{
								$value = $currentContent->seekNode($value['content_ID']);
								
								if (is_active($value->offsetGet('content_ID')) && $value->offsetGet('choice_exit') == 'false')
								{
									$navigation->navigation_request = 'false';
									$navigation->exception = 'NB.2.1-8';

									return $navigation;
								}
							}
						}
						else
						{

						/*	
							$navigation->navigation_request = 'false';
							$navigation->exception = 'NB.2.1-9';

							return $navigation;
						 */
						}
					}

				if(is_active($navigation->current_activity->offsetGet('content_ID')) && $navigation->current_activity->offsetGet('choice_exit')=='false')
				{
					$navigation->navigation_request = 'false';
					$navigation->exception = 'NB.2.1-8';

					return $navigation;
				}

				if(is_active($navigation->current_activity->offsetGet('content_ID')))
				{
					
					$navigation->navigation_request = 'true';
					$navigation->termination_request = 'exit';
					$navigation->sequencing_request = 'choice';
					$navigation->target_activity = $target;
	
					return $navigation;
				}	
				else
				{
					
					$navigation->navigation_request = 'true';
					$navigation->sequencing_request = 'choice';
					$navigation->target_activity = $target;
					$navigation->exception = 'NB.2.1-10';

					return $navigation;
				}					
			}
			else
			{
				$navigation->navigation_request = 'false';
				$navigation->exception = 'NB.2.1-11';

				return $navigation;
			}	
			
			}
		break;
		//8
		case 'exit':


			if($navigation->current_activity)
			{
				if(is_active($navigation->current_activity->offsetGet('id')))
				{
					$navigation->navigation_request = 'true';
					$navigation->termination_request = 'exit';
					$navigation->sequencing_request = 'exit';
				}
				else
				{
					$navigation->navigation_request = 'false';						
					$navigation->exception = 'NB.2.1-12';							
				}
			}
			else
			{ 
				$navigation->navigation_request = 'false';						
				$navigation->exception = 'NB.2.1-2';
			}
			break;
		//9
		case 'exitAll':
			if($navigation->current_activity)
			{
				$navigation->navigation_request = 'true';
				$navigation->termination_request = 'exitAll';
				$navigation->sequencing_request = 'exit';
			}
			else
			{
				$navigation->navigation_request = 'false';
				$navigation->exception = 'NB.2.1-2';
			}
			break;
		//10
		case 'abandon':
			if($navigation->current_activity)
			{
				if(is_active($navigation->current_activity))
				{
					$navigation->navigation_request = 'true';
					$navigation->termination_request = 'abandon';
					$navigation->sequencing_request = 'exit';
				}	
				else
				{
					$navigation->navigation_request = 'false';
					$navigation->exception = 'NB.2.1-12';
				}
			}
			else
			{
				$navigation->navigation_request = 'false';
				$navigation->exception = 'NB.2.1-12';
			}
			break;
		//11
		case 'abandon_all':
			if($navigation->current_activity)
			{
				$navigation->navigation_request = 'true';
				$navigation->termination_request = 'abandon_all';
				$navigation->sequencing_request = 'exit';
			}
			else
			{
				$navigation->navigation_request = 'false';
				$navigation->exception = 'NB.2.1-2';
			}
			break;
		//12
		case 'suspendAll':
			if($navigation->current_activity)
			{
				$navigation->navigation_request = 'true';
				$navigation->termination_request = 'suspendAll';
				$navigation->sequencing_request = 'exit';
			}
			else
			{
				$navigation->navigation_request = 'false';
				$navigation->exception = 'NB.2.1-2';
			}
			break;
		//13
		default:
			$navigation->navigation_request = 'false';
			$navigation->exception = 'NB.2.1-13';
			break;
	}


	return $navigation;
 
}
 

function termination_request_process($navigation, $currentContent)
{

	if(S_DEBUG)
	{
		echo 'Termination Request Process=>';
	}

	if(!$navigation->current_activity)
	{
		$navigation->termination_request = false;
		$navigation->exception = 'TB.2.3-1';

		return $navigation;
	}

	if(($navigation->termination_request=='exit' || $navigation->termination_request == 'abandon') && !is_active($navigation->current_activity->offsetGet('id')))
	{
		$navigation->termination_request = false;
		$navigation->exception = 'TB.2.3-2';

		return $navigation;
	}

	switch($navigation->termination_request)
	{
		case 'exit':

			echo 'exit1';
			
			end_attempt_process($navigation->current_activity, $navigation, $currentContent);

			$navigation = sequencing_exit_action_rules($navigation, $currentContent);

			do 
			{
				$processed_exit = 'false';
			
				$navigation = sequencing_post_condition_rule_subprocess($navigation, $currentContent);

				if ($navigation->termination_request == 'exitAll')
				{
					//do nothing
					echo '<br>do nothing?<br>';
				}

				echo '<br>--------<br>';
				echo $navigation->termination_request;

				echo $navigation->sequencing_request;
				
				echo '<br>--------<br>';
				

				if ($navigation->termination_request == 'exitParent')
				{
					if(!$currentContent->isRoot(false, $navigation->current_activity))
					{
						$navigation->setCurrentActivity($currentContent->seekNode($navigation->current_activity->offsetGet('parent_content_ID')));
						end_attempt_process($navigation->current_activity, $navigation, $currentContent);

						$processed_exit = 'true';
					}
					else
					{
						$navigation->termination_request = 'false';
						$navigation->exception = 'TB.2.3-4';
						$navigation->sequencing_request = null;

						return $navigation;
					}
				}
				else 
				{
		
					if($currentContent->isRoot(false, $navigation->current_activity) && $navigation->sequencing_request != 'retry')
					{
						$navigation->termination_request = 'true';
						$navigation->sequencing_request = 'exit';
						$navigation->exception = '';

						return $navigation;
					}
				}			
			} while ($processed_exit == 'true');
			 

			$navigation->termination_request = true;
			return $navigation;

			break;
		case 'exitAll':

			echo 'exitAll';

			if(is_active($navigation->current_activity->offsetGet('content_ID')))
			{
				end_attempt_process($navigation->current_activity, $navigation, $currentContent);
			}
			
			$navigation = terminate_descendent_attempts_process($navigation, $currentContent, $currentContent->getFirstNode());
			end_attempt_process($currentContent->getFirstNode(), $navigation, $currentContent);

			$navigation->setCurrentActivity($currentContent->getFirstNode());

			$navigation->termination_request = 'true';
			$navigation->sequencing_request = 'exit';

			return $navigation;

			break;
		case 'suspendAll':
			if(is_active($navigation->current_activity->offsetGet('content_ID')) || is_suspended($navigation->current_activity->offsetGet('content_ID')))
			{
				//todo overall_rollup_process($currentContent, $nagigation);
				$navigation->setSuspendedActivity($navigation->current_activity);
			}
			else
			{
				if(!$currentContent->isRoot(false, $navigation->current_activity))
				{
					$navigation->setSuspendedActivity($navigation->current_activity);
				}
				else
				{
					$navigation->termination_request = 'false';
					$navigation->exception = 'TB.2.3-3';
				}
			}
			$active_path = form_activity_path($currentContent, $navigation->suspended_activity, $currentContent->getFirstNode());

			if(empty($active_path))
			{
				$navigation->termination_request = 'false';
				$navigation->exception = 'TB.2.3-5';
			}
			foreach ($active_path as $value)
			{
				$value = $currentContent->seekNode($value['content_ID']);

				if (is_active($value->offsetGet('content_ID')) && $value->offsetGet('content_ID')=='false')
				{
					eF_insertOrupdateTableData("scorm_sequencing_activity_state_information", array('content_ID'=>$value->offsetGet('content_ID'), 'users_LOGIN'=>$_SESSION['s_login'], 'is_active'=>'false', 'is_suspended'=>'true'), "users_LOGIN = '".$_SESSION['s_login']."' AND content_ID = '".$value->offsetGet('content_ID')."'");
				}
			}

			$navigation->setSuspendedActivity($currentContent->getFirstNode());
			
			$navigation->termination_request = 'true';
			$navigation->sequencing_request = 'exit';

			return $navigation;
			break;

		default:
			$navigation->termination_request = 'false';
			$navigation->exception = 'TB.2.3-7';
			return $navigation;
			break;
	}
	
}

function sequencing_post_condition_rule_subprocess($navigation, $currentContent)
{
	if(S_DEBUG)
	{
		echo 'Sequencing Post Condition Rules Subprocess=>';
	}

	if(is_active($navigation->current_activity->offsetGet('content_ID')))
	{
		return $navigation;
	}
	
	$rule_action = "('exitParent', 'exitAll', 'retry', 'retryAll', 'continue', 'previous')";
	$rule_result = sequencing_rules_check_process($navigation->current_activity, $rule_action);

	if($rule_result)
	{
		$actions = array('retry', 'continue', 'previous');
		if(in_array($rule_result, $actions))
		{ 
			echo '<br>*****************<br>';
			echo $rule_result;
			echo '<br>*****************<br>';
			$navigation->termination_request = null;
			$navigation->sequencing_request = $rule_result;

			return $navigation;
		}

		$actions = array('exitParent', 'exitAll');		
		if(in_array($rule_result, $actions))
		{
			echo '<br>*****************<br>';
			echo $rule_result;
			echo '<br>*****************<br>';
			
			
			$navigation->termination_request = $rule_result;
			$navigation->sequencing_request = null;

			return $navigation;
		}

		$actions = array('retryAll');		
		if(in_array($rule_result, $actions))
		{
			echo '<br>*****************<br>';
			echo $rule_result;
			echo '<br>*****************<br>';
			

			$navigation->termination_request = 'exitAll';
			$navigation->sequencing_request = 'retry';

			return $navigation;
		}
	}

	/* todo see imsss, scorm incompatible
	$navigation->termination_request = null;
	$navigation->sequencing_request = null;
	 */

	return $navigation;
}


function overall_rollup_process($currentContent, $navigation, $activity)
{
	if(S_DEBUG)
	{
		echo 'Overall Rollup Process=>';
	}

	$activity_path = array_reverse(form_activity_path($currentContent, $activity, $currentContent->getFirstNode()));  
	

	if(empty($activity_path))
	{
		return $navigation;
	}

	foreach($activity_path as $value)
	{
		$value = $currentContent->seekNode($value['id']);

		if(!$currentContent->isLeaf($value))
		{
			measure_rollup_process($currentContent, $navigation, $value);
			completion_measure_rollup_process($currentContent, $navigation, $value);
		}


		objective_rollup_using_rules_process($currentContent, $navigation, $value);
		


		/*	objective_rollup_using_measure($currentContent, $navigation, $value);



    scorm_seq_objective_rollup_measure($sco,$userid);
    scorm_seq_objective_rollup_rules($sco,$userid);
	scorm_seq_objective_rollup_default($sco,$userid);
		 */
		
	}
}


function objective_rollup_using_rules_process($currentContent, $navigation, $value)
{
	


}

function rollup_rule_check_subprocess($currentContent, $navigation, $activity, $action)
{
	$result = eF_getTableData("scorm_sequencing_rollup_rules", "*", "users_LOGIN = '".$_SESSION['s_login']."' AND content_ID = '".$value->offsetGet('content_ID')."'"." AND rule_action='".$action."'");

	if(!empty($result))
	{
		foreach($result as $value)
		{

			$children = $currentContent->getNodeChildren($activity);

			foreach($children as $child)
			{
				if ($child instanceof EfrontUnit)
				{
					if($child->offsetGet('tracked')=='true')
					{
						check_child

					}			
				}
			}
		}
	}
}

function check_child($activity, $rule)
{
	$included = 'false';
	
	if($rule['rule_action'] == 'satisfied' || $rule['rule_action'] == 'notSatisfied')
	{
		if($rule['rollup_objective_satisfied'] == 'true')
		{
			$included = 'true';

			if(($rule['rule_action'] == 'satisfied' && $activity->offsetGet('required_for_satisfied') == 'ifNotSuspended') || ($rule['rule_action'] == 'notSatisfied' && $activity->offsetGet('required_for_not_satisfied') == 'ifNotSuspended'))
			{

				$activity_progress_information = eF_getTableData("scorm_sequencing_activity_progress_information", "*", "users_LOGIN = '".$_SESSION['s_login']."' AND content_ID = '".$activity->offsetGet('id')."'");

				if($activity_progress_information[0]['activity_progress_status'] == 'false' || ($activity_progress_information[0]['activity_attempt_count']>0 && is_suspended($activity)))
				{
					$included = 'true';
				}
			}
			else
			{	
				if(($rule['rule_action'] == 'satisfied' && $activity->offsetGet('required_for_satisfied') == 'ifNotAttempted') || ($rule['rule_action'] == 'notSatisfied' && $activity->offsetGet('required_for_not_satisfied') == 'ifAttempted'))
				{
					$activity_progress_information = eF_getTableData("scorm_sequencing_activity_progress_information", "*", "users_LOGIN = '".$_SESSION['s_login']."' AND content_ID = '".$activity->offsetGet('id')."'");
					
					if($activity_progress_information[0]['activity_progress_status'] == 'false' || $activity_progress_information[0]['activity_attempt_count']==0)
					{
						$included = 'false';
					}
				}
				else
				{
					if(($rule['rule_action'] == 'satisfied' && $activity->offsetGet('required_for_satisfied') == 'ifNotSkipped') || ($rule['rule_action'] == 'notSatisfied' && $activity->offsetGet('required_for_not_satisfied') == 'ifNotSkipped')
					{
						$rule_action = "('skip')";
						$rule_result = sequencing_rules_check_process($activity, $rule_action);

						if($rule_result)
						{
							$included = 'false';					
						}
					}
				}
			}
		}
	}

	if($rule['rule_action'] == 'completed' || $rule['rule_action'] == 'incomplete')
	{
		if($activity->offsetGet('rollup_progress_completion') == 'true')
		{
			$included = 'true';

			if(($rule['rule_action'] == 'completed' && $activity->offsetGet('required_for_completed') == 'ifNotSuspended')) ||  )

If (the Rollup Action is Completed And
adlseq:requiredForCompleted is ifNotSuspended) Or (the Rollup
Action is Incomplete And adlseq:requiredForIncomplete is
ifNotSuspended) Then



		}
	}
}




function objective_rollup_using_measure($currentContent, $navigation, $activity)
{



}

function completion_measure_rollup_process($currentContent, $navigation, $activity)
{

	if(S_DEBUG)
	{
		echo 'Completion Measure Rollup Process=>';
	}

	$total_weighted_measure = 0;
	$valid_data = 'false';
	$counted_measures = 0;


	$target_children = $currentContent->getNodeChildren($activity);

	foreach($target_children as $child)
	{
		if ($child instanceof EfrontUnit)
		{
			if($child->offsetGet('tracked')=='true')
			{
				$counted_measures += $child->offsetGet('progress_weight'); //todo import?
				
				if($child->offsetGet('attemt_completion_status') == 'true')          //na bainoun sto unit
				{
					$total_weighted_measure += $child->offsetGet('attempt_completion_amount')*$child->offsetGet('progress_weight');
					$valid_data = 'true';
				}
			}
		}
	}
	if($valid_data == 'false')
	{
		//Set the Attempt Completion Amount Status to False
	}
	else
	{
			if($counted_measures>0)
			{
				$rolled_up_objective['attempt_completion_status'] = 'true';  //todo write to db?
				$rolled_up_objective['attempt_completion_amount'] = $total_weighted_measure/$counted_measures;  //todo write to db?
			}
			else
			{
				$rolled_up_objective['attempt_completion_amount'] = 'false';
			}
	}

	return;
}

function measure_rollup_process($currentContent, $navigation, $activity)
{

	if(S_DEBUG)
	{
		echo 'Measure Rollup Process=>';
	}

	$total_weighted_measure = 0;
	$valid_data = 'false';
	$counted_measures = 0;
	$target_objective = null;

	$result = eF_getTableData("scorm_sequencing_objectives", "*", "content_ID = '".$activity->offsetGet('id')."'");

	foreach($result as $value)
	{
		if($value['is_primary'] == 1)
		{
			$target_objective = $value;
			break;
		}
	}

	if($target_objective)
	{
		$target_children = $currentContent->getNodeChildren($activity);

		foreach($target_children as $child)
		{
			if ($child instanceof EfrontUnit)
			{
				if($child->offsetGet('tracked') == 'true')
				{
					$rolled_up_objective = null;

					$objectives = eF_getTableData("scorm_sequencing_objectives", "*", "content_ID = '".$child->offsetGet('id')."'");

					foreach($objectives as $objective)
					{
						if($value['is_primary'] == 1)
						{
							$rolled_up_objective = $value;
							break;
						}
					}

					if($rolled_up_objective)
					{


						$counted_measures += $child->offsetGet('rollup_objective_measure_weight');

						if($rolled_up_objective['objective_measure_status'] == 'true')
						{
							$total_weighted_measure += $objective_normalized_measure*$child->offsetGet('rollup_objective_measure_weight');
							
							$valid_data ='true';					
						}
					}
					else
					{
						return;
					}
				}


			}
		}

		if($valid_data == 'true')
		{
			$rolled_up_objective['objective_measure_status'] = 'false';  //todo write to db?
		}
		else
		{
			if($counted_measures>0)
			{
				$rolled_up_objective['objective_measure_status'] = 'true';  //todo write to db?
				$rolled_up_objective['objective_normalized_measure'] = $total_weighted_measure/$counted_measures;  //todo write to db?
			}
			else
			{
				$rolled_up_objective['objective_measure_status'] = 'false';  //todo write to db?
			}
		}
	}
	return;
}


function sequencing_request_process($navigation, $currentContent)
{

	if(S_DEBUG)
	{
		echo 'Sequencing Request Process=>';
	}

	switch($navigation->sequencing_request)
	{

		case 'start':
			$navigation = start_sequencing_request_process($navigation, $currentContent);
			break;
		case 'continue':
			$navigation =  continue_sequencing_request_process($navigation, $currentContent);
			break;
		case 'previous':
			$navigation =  previous_sequencing_request_process($navigation, $currentContent);			
			break;
		case 'choice':
			$navigation = choice_sequencing_request_process($navigation, $currentContent);
			break;
		case 'exit':
			$navigation = exit_sequencing_request_process($navigation, $currentContent);
			break;
		case 'resumeAll':
			$navigation = resumeAll_sequencing_request_process($navigation, $currentContent);
			break;
		case 'suspendAll':
			$navigation = suspendAll_sequencing_request_process($navigation, $currentContent);
			break;
		default:
			break;

	}
		return $navigation;
}

function resumeAll_sequencing_request_process($navigation, $currentContent)
{

	if(S_DEBUG)
	{
		echo 'Resume All Sequencing Request Process=>';
	}

	if(!$navigation->current_activity)
	{
		$navigation->delivery_request = null;
		$navigation->exception = 'SB.2.6-1';

		return $navigation;
	}

	if(!$navigation->suspended_activity)
	{
		$navigation->delivery_request = null;
		$navigation->exception = 'SB.2.6-2';
	}

	$navigation->delivery_request = $navigation->suspended_activity;
	$navigation->exception = '';

	return $navigation;

}

function exit_sequencing_request_process($navigation, $currentContent)
{

	if(S_DEBUG)
	{
		echo 'Exit Sequencing Request Process=>';
	}


	if(!$navigation->current_activity)
	{
		$navigation->end_sequencing_session = 'false';
		$navigation->exception = 'SB.2.11-1';
		
		return $navigation;
	}

	if(is_active($navigation->current_activity->offsetGet('content_ID')))
	{
		$navigation->end_sequencing_session = 'false';
		$navigation->exception = '';
		
		return $navigation;
	}

	if($currentContent->isRoot(false, $currentContent->getFirstNode()))
	{

		//when sequencing session ends set current_activity to undefined
		//eF_insertOrupdateTableData("scorm_sequencing_global_state_information", array('lesson_ID'=>$_SESSION['s_lessons_ID'], 'users_LOGIN'=>$_SESSION['s_login'], 'current_activity'=>null), "users_LOGIN = '".$_SESSION['s_login']."' AND lesson_ID=".$_SESSION['s_lessons_ID']);
	
		$navigation->end_sequencing_session = 'true';
		$navigation->exception = 'SB.2.11-2';
		
		return $navigation;
	}

	$navigation->end_sequencing_session = 'false';
	$navigation->exception = '';

	return $navigation;
	
}


function choice_sequencing_request_process($navigation, $currentContent)
{
	if(S_DEBUG)
	{
		echo 'Choice Sequencing Request Process=>';
	}

	if(!$navigation->target_activity)
	{
		$navigation->delivery_request = null;
		$navigation->exception = 'SB.2.9-1';

		return $navigation;
	}

	$ancestors = $currentContent->getNodeAncestors($navigation->current_activity);
	$common_ancestor = $currentContent->findCommonAncestor($ancestors, $navigation->target_activity);
	$activity_path = form_activity_path($currentContent, $common_ancestor, $navigation->target_activity); //to do: exclusive target 

	$activity_path = form_activity_path($currentContent, $currentContent->getFirstNode(), $navigation->target_activity);



	foreach($activity_path as $value)
	{
		$value = $currentContent->seekNode($value['id']);
		
		if($currentContent->isRoot(false, $value))
		{
			//todo
		}
		 

		$rule_action = "('hiddenFromChoice')";
		$rule_result = sequencing_rules_check_process($value, $rule_action);


		if($rule_result)
		{
			$navigation->delivery_request = null;
			$navigation->exception = 'SB.2.9-3';

			return $navigation;

		}
	}

	if(!$currentContent->isRoot(false, $navigation->target_activity))
	{
		$target_parent = $currentContent->seekNode($navigation->target_activity->offsetGet('parent_content_ID'));


		if($target_parent->offsetGet('choice')=='false')
		{
			$navigation->delivery_request=null;
			$navigation->exception = 'SB.2.9-4';
			return $navigation;
		}
	}


	if($navigation->current_activity)
	{
		$ancestors = $currentContent->getNodeAncestors($navigation->current_activity);
		$common_ancestor = $currentContent->findCommonAncestor($ancestors, $navigation->target_activity);
	}
	else
	{
		$common_ancestor = $currentContent->getFirstNode();
	}

	if($navigation->current_activity->offsetGet('content_ID')==$navigation->target_activity->offsetGet('id'))
	{
		if(S_DEBUG)
		{
			echo 'Case #1';
		}
		//do nothing
	}
	else if (are_siblings($navigation->current_activity, $navigation->target_activity))
	{
		if(S_DEBUG)
		{
			echo 'Case #2';
		}

		//Form the activity list
		$target_parent = $currentContent->seekNode($navigation->target_activity->offsetGet('parent_content_ID'));
		

		if($navigation->current_activity->offsetGet('content_ID') > $navigation->target_activity->offsetGet('content_ID'))
		{
			$limit1 = $navigation->current_activity->offsetGet('content_ID');
			$limit2 = $navigation->target_activity->offsetGet('content_ID');

			$traversal_direction = 'backward';
		}
		else
		{
			$limit2 = $navigation->current_activity->offsetGet('content_ID');
			$limit1 = $navigation->target_activity->offsetGet('content_ID');

			$traversal_direction = 'forward';			
		}

	
		foreach($target_parent as $value)
		{
			if($value instanceof EfrontUnit && $value->offsetGet('content_ID')>$limit2 && $value->offsetGet('content_ID')<=$limit1)
			{
					$activity_list[]=$value;
			}
		}

		if(empty($activity_list))
		{
			$navigation->delivery_request=null;
			$navigation->exception='';
			return $navigation;
		}

		foreach($activity_list as $value)
		{
			$value = $currentContent->seekNode($value['content_ID']);
			$navigation = choice_activity_traversal_subprocess($navigation, $currentContent, $value, $traversal_direction);

			if($navigation->reachable == 'false')
			{
				$navigation->delivery_request = null;
				return $navigation;
			}
		}
	}

	else if(($navigation->current_activity->offsetGet('content_ID')==$common_ancestor->offsetGet('content_ID')) || !$navigation->current_activity)
	{
		if(S_DEBUG)
		{
			echo 'Case #3';
		}

		$exclude_activities[] = $navigation->target_activity;
		$activity_path = form_activity_path($currentContent, $common_ancestor, $navigation->target_activity, $exclude_activities); //to do: exclusive target 

		if(empty($activity_path))
		{
			$navigation->delivery_request =null;
			$navigation->exception='SB.2.9-5';
		}
		else
		{

			foreach ($activity_path as $value)
			{
				$value = $currentContent->seekNode($value['content_ID']);
				
				$navigation = choice_activity_traversal_subprocess($navigation, $currentContent, $value, 'forward');
			
				if($navigation->reachable == 'false')
				{
					$navigation->delivery_request=null;
					return $navigation;
				}

				//todo prevent activation
				if(!is_active($value->offsetGet('id')) && $value->offsetGet('content_ID') != $common_ancestor->offsetGet('content_ID') && $value->offsetGet('prevent_activation')=='true')
				{
					$navigation->delivery_request = null;
					$navigation->exception = 'SB.2.9-6';

					return $navigation;	
				}
			}
		}
	}

	else if($common_ancestor->offsetGet('content_ID') == $navigation->target_activity->offsetGet('content_ID')) //todo rethink this
	{
		if(S_DEBUG)
		{
			echo 'Case #4';
		}

		$activity_path = form_activity_path($currentContent, $navigation->current_activity, $navigation->target_activity);

		if(empty($activity_path))
		{
			$navigation->delivery_request = null;
			$navigation->exception = 'SB.2.9-5';

			return $navigation;
		}

		foreach($activity_path as $value)
		{

			$cnt++;

			$value = $currentContent->seekNode($value['content_ID']);
			
			if($cnt<sizeof($activity_path))
			{
				if($value->offsetGet('choice_exit')=='false')
				{
					$navigation->delivery_request = null;
					$navigation->exception = 'SB.2.9-7';

					return $navigation;
				}

			}
		}	
	}

	else if($navigation->target_activity->offsetGet('id')>$common_ancestor->offsetGet('id'))
	{

		if(S_DEBUG)
		{
			echo 'Case #5';
		}

		$activity_path = form_activity_path($currentContent, $navigation->current_activity, $common_ancestor, array($common_ancestor)); //todo exclude

		if(empty($activity_path))
		{

			$navigation->delivery_request=null;
			$navigation->exception='SB.2.9-5';

			return $navigation;
		}

		$constrained_activity = null;

		foreach($activity_path as $value)
		{
			if($value['choice_exit'] == 'false')	
			{
				
				$navigation->delivery_request = null;
				$navigation->exception = 'SB.2.9-7';
			
				return $navigation;
			}
			if(!$constrained_activity)
			{
				//todo
				
			}
		}

		
		if($constrained_activity)
		{
			if($navigation->target_activity->offsetGet('content_ID')>$constrained_activity->offsetGet('content_ID'))
			{
				$traversal_direction = 'forward';
			}
			else
			{
				$traversal_direction = 'backward';
			}

			$navigation=choice_flow_subprocess($navigation, $currentContent, $constrained_activity, 'forward');

			$activity_to_consider  = $navigation->identified_activity;

			//todo
		}

		$activity_path = form_activity_path($currentContent, $common_ancestor, $navigation->target_activity, array($navigation->target_activity)); //to do: exclusive target

		if(empty($activity_path))
		{
			
			$navigation->delivery_request=null;
			$navigation->exception = 'SB.2.9-5';

			return $navigation;
		}
		if($navigation->target_activity->offsetGet('content_ID')>$navigation->current_activity->offsetGet('content_ID'))
		{
			foreach($activity_path as $value)
			{
				$value = $currentContent->seekNode($value['content_ID']);
				
				$navigation = choice_activity_traversal_subprocess($navigation, $currentContent, $value, 'forward');
		
				if($navigation->reachable == 'false')
				{
					$navigation->delivery_request = null;

					return $navigation;
				}

				if(!is_active($value->offsetGet('id') && $value!=$common_ancestor) && $value->offsetGet('precent_activation')=='true') 
				{
					
					$navigation->delivery_request = null;
					$navigation->exception='SB.2.9-6';

					return $navigation;
				}
			}

		}
		else
		{

			
			$activity_path[] = $navigation->target_activity;


			foreach($activity_path as $value)
			{
				$value=$currentContent->seekNode($value['id']);

				if(!is_active($value->offsetGet('content_ID')) && $value!=$common_ancestor && $value->offsetGet('prevent_activation') == 'true')
				{
					$navigation->delivery_request = null;
					$navigation->exception = 'SB.2.9-6';

					return $navigation;
				}		
			}
		}
	}


	if($currentContent->isLeaf($navigation->target_activity))
	{
		$navigation->delivery_request = $navigation->target_activity;
		$navigation->exception='';

		return $navigation;
	}

	$navigation = flow_subprocess($navigation, $currentContent, $navigation->target_activity, 'forward', 'true', $previous_traversal_direction);
	

	if(!$navigation->identified_activity)
	{
	
		$navigation = terminate_descendent_attempts_process($navigation, $currentContent, $common_ancestor);
		end_attempt_process($common_ancestor, $navigation, $currentContent);

		$navigation->current_activity = $navigation->target_activity;  //todo write to db?
		$navigation->delivery_request = null;

		$navigation->exception = 'SB.2.9-9';
	}
	else
	{
		$navigation->exception='';
		$navigation->delivery_request = $navigation->identified_activity;

		return $navigation;
	}	
}

function choice_flow_subprocess($navigation, $currentContent, $activity, $traversal_direction)
{
	if(S_DEBUG)
	{
		echo 'Choice Flow SubProcess=>';
	}
	$navigation=choice_flow_tree_traversal_subprocess($navigation, $currentContent, $value, 'forward');

	if($navigation->next_activity == null)
	{
		$navigation->identified_activity = $acitivity;

		return $navigation;
	}
	else
	{
		$navigation->identified_activity = $navigation->next_activity;

		return $navigation;
	}
}


function choice_flow_tree_traversal_subprocess($navigation, $currentContent, $activity, $traversal_direction)
{
	if(S_DEBUG)
	{
		echo 'Choice Flow Tree Traversal  SubProcess=>';
	}
	if($traversal_direction == 'forward')
	{
		if($activity==$currentContent->getLastNode() || $currentContent->isRoot(false, $activity))
		{
			$navigation->next_activity = null;
			return $navigation;
		}

		if($currentContent->isLastChild($activity))
		{
			$navigation=choice_flow_subprocess($navigation, $currentContent, $currentContent->seekNode($activity->offsetGet('parent_content_ID')), 'forward');

			return $navigation;
		}
		else
		{
			$next_sibling_node = $currentContent->getNextSiblingNode($activity);
			$navigation->next_activity = $next_sibling_node;

			return $navigation;
		}
	}
	if($traversal_direction == 'backward')
	{
		if($currentContent->isRoot(false, $activity))
		{
			$navigation->next_activity=null;
			return $navigation;
		}

		$parent_activity = $currentContent->seekNode($activity->offsetGet('parent_content_ID'));
		$first_activity = $currentContent->getNextNode($parent);

		if($activity===$first_activity)
		{
			$navigation = choice_flow_tree_traversal_subprocess($navigation, $currentContent, $currentContent->seekNode($activity->offsetGet('parent_content_ID')), 'backward');
			return $navigation;
		}
		else
		{
			$previous_sibling_node = $currentContent->getPreviousSiblingNode($activity);


			$navigation->next_activity = $previous_sibling_node;
		
			return $navigation;
		}
	}
}


function choice_activity_traversal_subprocess($navigation, $currentContent, $activity, $traversal_direction)
{
	if(S_DEBUG)
	{
		echo 'Choice Activity Traversal SubProcess=>';
	}
	if($traversal_direction == 'forward')
	{
		$rule_action = "('stopForwardTraversal')";
		$rule_result = sequencing_rules_check_process($activity, $rule_action);

		if($rule_result)
		{
			$navigation->reachable = 'false';
			$navigation->exception = 'SB.2.4-1';

			return $navigation;
		}

		$navigation->reachable = 'true';
		$navigation->exception = '';

		return $navigation;
	}


	if($traversal_direction == 'backward')
	{
		//if the activity has a parent?
		if(!$currentContent->isRoot(false, $activity))
		{
			$parent = $currentContent->seekNode($activity->offsetGet('parent_content_ID'));

			if($parent->offsetGet('forward_only') == 'true')
			{
				$navigation->reachable='false';
				$navigation->exception='SB.2.4-2';

				return $navigation;
			}
		}
		else
		{
			$navigation->reachable = 'false';
			$navigation->exception = 'SB.2.4-3';

			return $navigation;
		}

		$navigation->reachable = 'true';
		$navigation->exception = '';
	
		return $navigation;
	}
}


function previous_sequencing_request_process($navigation, $currentContent)
{

	if(S_DEBUG)
	{
		echo 'Previous Sequencing Request Process=>';
	}

	if(!$navigation->current_activity)
	{
		$navigation->delivery_request = null;
		$navigation->exception = 'SB.2.8-1';

		return $navigation;
	}

	if(!$currentContent->isRoot(false, $navigation->current_activity))
	{
		$parent_activity = $currentContent->seekNode($navigation->current_activity->offsetGet('parent_content_ID'));

		if($parent_activity->offsetGet('flow')=='false')
		{
			$navigation->delivery_request = null;
			$navigation->exception = 'SB.2.8-2';

			return $navigation;
		}
	}
 

	$navigation = flow_subprocess($navigation, $currentContent, $navigation->current_activity, 'backward', 'false', $previous_traversal_direction);

	if(!$navigation->identified_activity)
	{
		$navgation->delivery_request = null;
		return $navgation;
	}
	else
	{
		$navigation->delivery_request = $navigation->identified_activity;
		$navigation->exception = null;

		return $navigation;	
	}

}



function continue_sequencing_request_process($navigation, $currentContent)
{

	if(S_DEBUG)
	{
		echo 'Continue Sequencing Request Process=>';
	}

	if(!$navigation->current_activity)
	{
		$navigation->delivery_request = null;
		$navigation->exception = 'SB.2.7-1';

		return $navigation;
	}

	if(!$currentContent->isRoot(false, $navigation->current_activity))
	{
		$parent_activity = $currentContent->seekNode($navigation->current_activity->offsetGet('parent_content_ID'));

		if($parent_activity->offsetGet('flow')=='false')
		{
			$navigation->delivery_request = null;
			$navigation->exception = 'SB.2.7-2';

			return $navigation;
		}
	}
 

	$navigation = flow_subprocess($navigation, $currentContent, $navigation->current_activity, 'forward', 'false', $previous_traversal_direction);

	if(!$navigation->identified_activity)
	{
		$navgation->delivery_request = null;
		return $navgation;
	}
	else
	{
		$navigation->delivery_request = $navigation->identified_activity;
		$navigation->exception = null;
		return $navigation;	
	}

}

function start_sequencing_request_process($navigation, $currentContent)
{

	if(S_DEBUG)
	{
		echo 'Start Sequencing Request Process=>';
	}

	if($navigation->current_activity)
	{
		$navigation->delivery_request = null;
		$navigation->exception = 'SB.2.5-1';

		return $navigation;
	}

	if($currentContent->isLeaf($node = $currentContent->getFirstNode()))
	{
		$navigation->delivery_request = $node;
		$navigation->exception = 'SB.2.5-1';

		return $navigation;
	}
	else
	{
		$activity = $currentContent->getFirstNode();

		$traversal_direction = 'forward';
		$children_flag = 'true';
		$navigation = flow_subprocess($navigation, $currentContent, $activity, $traversal_direction, $children_flag, $previous_traversal_direction);

		if($navigation->identified_activity)
		{
			$navigation->delivery_request = $navigation->identified_activity;
			$navigation->exception = null;
		}

		return $navigation;
	}
}

function flow_subprocess($navigation, $currentContent, $activity, $traversal_direction, $children_flag, $previous_traversal_direction)
{

	if(S_DEBUG)
	{
		echo 'Flow SubProcess=>';
	}

	$candidate_activity = $activity;

	$navigation = flow_tree_traversal_subprocess($navigation, $currentContent, $activity, $traversal_direction, $children_flag, null);

	if($navigation->next_activity == null)
	{
		$navigation->identified_activity=$candidate_activity;
		$navigation->deliverable=false;

		return $navigation;


	}
	else
	{
		$candidate_activity = $navigation->next_activity;

		$navigation = flow_activity_traversal_subprocess($navigation, $currentContent, $candidate_activity, $traversal_direction, null);

		$navigation->identified_activity = $navigation->next_activity;

		return $navigation;

	}

	return $navigation;
}



function flow_tree_traversal_subprocess($navigation, $currentContent, $activity, $traversal_direction, $children_flag, $previous_traversal_direction)
{

	if(S_DEBUG)
	{
		echo 'Flow Tree Traversal SubProcess=>';
	}

	$navigation->reversed = 'false';

	if($previous_traversal_direction && $previous_traversal_direction == 'backward' && $currentContent->isLastChild($activity))
	{
		
		$navigation->reversed = 'true';
		$navigation->traversal_direction = 'backward';

		$activity = $currentContent->getNextNode($activity->offsetGet('parent_content_ID'));
	}

	if($traversal_direction == 'forward')
	{
		if ($currentContent->getLastNode() === $activity || ($currentContent->getFirstNode() === $activity && $children_flag=='false') )
		{
			//$navigation = terminate_descendent_attempts_process($navigation, $currentContent);
			//todo
			//echo 'aaaaaaaaaaaaaa';

			$navigation->next_activity=null;
			$navigation->exception=null;
			return $navigation;
		}

		if($currentContent->isLeaf($activity) || $children_flag == 'false')
		{
			if($currentContent->isLastChild($activity))	
			{
				$parent_activity = $currentContent->seekNode($activity->offsetGet('parent_content_ID'));
				$navigation = flow_tree_traversal_subprocess($navigation, $currentContent, $parent_activity, $traversal_direction, 'false', null);
			}
			else
			{
				$next_sibling_node = $currentContent->getNextSiblingNode($activity);

				$navigation->next_activity = $next_sibling_node;
				$navigation->exception=null;

				return $navigation;
			}
		}
		else
		{
			if(!$currentContent->isLeaf($activity))
			{			

				$navigation->next_activity = $currentContent->getNextNode($activity);

				return $navigation;
			}
			else
			{
				echo 'ooooooooooooooooooooooooooooo';
				//todo
			}
		}
	}




	if($traversal_direction == 'backward')
	{
		if($currentContent->isRoot(false, $activity))
		{
			$navigation->next_activity = null;
			$navigation->traversal_direction = null;
			$navigation->exception = 'SB.2.1-3';
			
			return $navigation;
		}

		if($currentContent->isLeaf($activity) || $children_flag == 'false')
		{
			if($navigation->reversed=='false')
			{
				$parent = $currentContent->seekNode($activity->offsetGet('parent_content_ID'));
				
				if($parent->offsetGet('forward_only')=='true')
				{
					$navigation->next_activity = null;
					$navigation->traversal_direction = null;
					$navigation->exception = 'SB.2.1-4';

					return $navigation;
				}
			}

			$parent_activity = $currentContent->seekNode($activity->offsetGet('parent_content_ID'));

			$first_activity = $currentContent->getNextNode($parent);

		
			if($activity===$first_activity)
			{

				$navigation = flow_tree_traversal_subprocess($navigation, $currentContent, $parent_activity, 'backward', 'false', null);
			
				return $navigation;
			}
			else
			{
				
				$previous_sibling_node = $currentContent->getPreviousSiblingNode($activity);


				$navigation->next_activity = $previous_sibling_node;
				$navigation->exception=null;
				$navigation->traversal_direction=$traversal_direction;

				return $navigation;

			}
		}
		else
		{
			if(!$currentContent->isLeaf($activity))
			{
				if($activity->offsetGet('forward_only')=='true')
				{
					$navigation->next_activity = $currentContent->getNextNode($activity);
					$navigation->traversal_direction = 'forward';
					$navigation->exception = null;

					return $navigation;
				}				
				else
				{
					$navigation->next_activity = $currentContent->getLastChild($activity);
					$navigation->exception = null;
					
					return $navigation;
				}
			}
			else
			{
				$navigation->next_activity = null;
				$navigation->traversal_direction = null;
				$navigation->exception ='SB.2.1-2';
				
				return $navigation;
			}
		}
	}
	



	return $navigation;

}

function flow_activity_traversal_subprocess($navigation, $currentContent, $activity, $traversal_direction, $previous_traversal_direction)
{

	if(S_DEBUG)
	{
		echo 'Flow Activity Traversal SubProcess';
	}

	
	$parent = $currentContent->seekNode($activity->offsetGet('id'));


	if($activity->offsetGet('flow')=='false')
	{
		//todo
		$navigation->deliverable = false;
		$navigation->next_activity = $activity;
	}

	
	$rule_action = "('skip')";
	$rule_result = sequencing_rules_check_process($activity, $rule_action);

	if($rule_result)
	{
		echo 'RULEZZZ';
		$navigation = flow_tree_traversal_subprocess($navigation, $currentContent, $activity, $traversal_direction, 'false', $previous_traversal_direction);

		if($navigation->next_activity == null)
		{
			$navigation->deliverable = false;
			$navigation->next_activity = $activity;

			return $navigation;
		}
		else
		{
			if($previous_traversal_direction == 'backward' && $navigation->traversal_direction == 'backward')
			{
				$navigation = flow_activity_traversal_subprocess($navigation, $currentContent, $navigation->next_activity, $traversal_direction, null);
			}
			else
			{
				$navigation = flow_activity_traversal_subprocess($navigation, $currentContent, $navigation->next_activity, $traversal_direction, $previous_traversal_direction);
				
			}
			return $navigation;

		}



	}

	$check_activity_result = check_activity_process($activity);

	if($check_activity_result)
	{
		$navigation->deliverable = false;
		$navigation->next_activity = $activity;
		$navigation->exception = 'SB.2.2-2';
		
		return $navigation;
	}


	if(!$currentContent->isLeaf($activity))
	{ 
		$navigation = flow_tree_traversal_subprocess($navigation, $currentContent, $activity, $traversal_direction, 'true', null);

		if($navigation->next_activity == null)
		{
			$navigation->deliverable = false;
			$navigation->next_activity = $activity;
		
			return $navigation;			
		}
		else
		{
			if ($traversal_direction == 'backward' && $navigation->traversal_direction == 'forward')
			{
				$navigation = flow_activity_traversal_subprocess($navigation, $currentContent, $navigation->next_activity, 'forward', 'backward');
			}
			else
			{
				echo $traversal_direction;
				$navigation = flow_activity_traversal_subprocess($navigation, $currentContent, $navigation->next_activity, $traversal_direction, null);				
			}
				return $navigation;
			
		}
	}


	$navigation->deliverable = true;
	$navigation->next_activity = $activity;
	$navigation->exception=null;

	return $navigation;
}


function delivery_request_process($navigation, $currentContent)
{

	if(S_DEBUG)
	{
		echo 'Delivery Request Process=>';
	}

	if(!$currentContent->isLeaf($navigation->delivery_request))
	{
			$navigation->delivery_request_valid = false;
			$navigation->exception = 'DB.1.1-1';
	}

	$activity_path = $currentContent->getNodeAncestors($navigation->delivery_request);

	if(empty($activity_path))
	{
		$navigation->delivery_request_valid = false;
		$navigation->exception = 'DB.1.1-2';
	}

	foreach ($active_path as $value)
	{
		$found_disallowed_node = check_activity_process($value);

		if ($found_disallowed_node)
		{
			$navigation->delivery_request_valid = false;
			$navigation->exception = 'DB.1.1-3';
		}
	}

	$navigation->delivery_request_valid = true;
	$navigation->exception = null;

	return $navigation;
}

function check_activity_process($activity)
{
	if(S_DEBUG)
	{
		echo 'Check Activity Process';
	}

	//sequencing_rules_check_process();

	return false;
}

function content_delivery_environment_process($navigation, $currentContent)
{
	if(S_DEBUG)
	{
		echo 'Content Delivery Environment Process=>';
	}



	if($navigation->current_activity && is_active($navigation->current_activity->offsetGet('content_ID'))) //current activity is not defined when start request process
	{ 
		//echo 'aaaaaa';
		$navigation->exception = 'DB.2-1';
	}

	if($navigation->delivery_request != $navigation->suspended_activity)
	{
		$navigation = clear_suspended_activity_subprocess($navigation, $currentContent);
	}

	$navigation = terminate_descendent_attempts_process($navigation, $currentContent, $navigation->delivery_request);

	$activity_path = $currentContent->getNodeAncestors($navigation->delivery_request);

	foreach($activity_path as $value)
	{

		$result = eF_getTableData("scorm_sequencing_activity_state_information", "*", "users_LOGIN = '".$_SESSION['s_login']."' AND content_ID = '".$value['id']."'");

		if(!is_active($value['content_ID']))
		{
			if($value['tracked']=='true')     //den ginetai import
			{
				$activity_attempt_count = eF_getTableData("scorm_sequencing_activity_progress_information", "activity_attempt_count", "users_LOGIN = '".$_SESSION['s_login']."' AND content_ID = '".$value['id']."'");

				$activity_attempt_count = $activity_attempt_count[0]['activity_attempt_count'];

				if(is_suspended($value['id']))
				{
					eF_insertOrupdateTableData("scorm_sequencing_activity_state_information", array('is_suspended'=>'true'), "users_LOGIN = '".$_SESSION['s_login']."' AND content_ID = '".$value['id']."'");	
				}	
				else
				{
					$activity_attempt_count = $activity_attempt_count + 1;

					eF_insertOrupdateTableData("scorm_sequencing_activity_progress_information", array('content_ID'=>$value['id'], 'users_LOGIN'=>$_SESSION['s_login'], 'activity_attempt_count'=>$activity_attempt_count), "users_LOGIN = '".$_SESSION['s_login']."' AND content_ID = '".$value['id']."'");

					if ($activity_attempt_count == 1)
					{
						eF_updateTableData("scorm_sequencing_activity_progress_information", array('activity_progress_status'=>'true'), "users_LOGIN = '".$_SESSION['s_login']."' AND content_ID = '".$value['id']."'");
					}

					//todo
					//		

					eF_insertOrupdateTableData("scorm_sequencing_attempt_progress_information", array('content_ID'=>$value['id'], 'users_LOGIN'=>$_SESSION['s_login'], 'attempt_progress_status'=>'false' , 'attempt_completion_status'=>'false', 'attempt_absolute_duration'=>'0.0', 'attempt_experienced_duration'=>'0.0', 'attempt_completion_amount'=>'0.0'),"users_LOGIN = '".$_SESSION['s_login']."' AND content_ID = '".$value['id']."'");

				}
			}

			eF_insertOrupdateTableData("scorm_sequencing_activity_state_information", array('content_ID'=>$value['id'], 'users_LOGIN'=>$_SESSION['s_login'], 'is_active'=>'true'), "users_LOGIN = '".$_SESSION['s_login']."' AND content_ID = '".$value['id']."'");

		}
	}

	$navigation->setCurrentActivity($navigation->delivery_request);
	$navigation->setSuspendedActivity(null);

	//to do The delivery environment is assumed to deliver the content resources associated with the identified activity. While the activity is assumed to be active, the sequencer may track learner status.

	if($navigation->current_activity->offsetGet('tracked') == 'true')
	{
		//todo 8.1.1, 8.1.2
	}


	return $navigation;
}

function clear_suspended_activity_subprocess($navigation, $currentContent)
{

	if(S_DEBUG)
	{
		echo 'Clear Suspend Activity SubProcess=>';
	}

	if($navigation->suspended_activity)
	{

		$ancestors = $currentContent->getNodeAncestors($navigation->activity);
		$common_ancestor = $currentContent->findCommonAncestor($navigation->suspended_activity, $ancestors);

		$activity_path = $currentContent->getNodeAncestors($common_ancestor);

		foreach($activity_path as $value)
		{
			if($currentContent->isLeaf($value))
			{
				$result = eF_getTableData("scorm_sequencing_activity_state_information", "*", "content_ID=".$value['id']." AND users_LOGIN='". $_SESSION['s_login']."'");

				if (sizeof($result) > 0) 
				{
					eF_updateTableData("scorm_data", array('suspended'=>'true'), "content_ID=".$value['id']." AND users_LOGIN='". $_SESSION['s_login']."'");       
				}	
				else
				{
					eF_insertTableData("scorm_data", array('content_ID'=>$value['id'],'suspended'=>'true'));       
				}
			}
			else
			{
				$children = $currentContent->getNodeChildren($navigation->activity);

				foreach ($children as $value)
				{
					//tod0
					echo 'fffffffffffffffffffffffffffffffffffffffffffff';
				}
			}
		}
		$navigation->suspended_activity = null;	
	}

	return $navigation;

}

function terminate_descendent_attempts_process($navigation, $currentContent, $activity)
{

	if(S_DEBUG)
	{
		echo 'Terminate Descendant Attempts Process=>';
	}


	if($navigation->current_activity != null) //borei na einai null? otan baineis gia 1h fora?
	{
		$ancestors = $currentContent->getNodeAncestors($navigation->current_activity);	

		$common_ancestor = $currentContent->findCommonAncestor($ancestors, $activity);

		$activity_path = form_activity_path($currentContent, $navigation->current_activity, $common_ancestor);

		foreach	($activity_path as $value)
		{
			$activity = $currentContent->seekNode($value['id']);  //todo array of arrays, convert to EfrontUnit
			end_attempt_process($activity, $navigation, $currentContent);
		}
	}

	return $navigation;

}

function end_attempt_process($activity, $navigation, $currentContent)
{
	if(S_DEBUG)
	{
		echo 'End Attempt Process=>';
	}

	if($currentContent->isLeaf($activity))
	{
		if($activity->offsetGet('tracked') == 'true')
		{
			if($activity->offsetGet('completion_set_by_content')!='true')
			{
				$result = eF_getTableData("scorm_sequencing_attempt_progress_information", "*", "users_LOGIN = '".$_SESSION['s_login']."' AND content_ID = ".$activity->offsetGet('content_ID'));	
				if($result[0]['attempt_progress_status']!='true')
				{
					eF_updateTableData("scorm_sequencing_attempt_progress_information", array('attempt_progress_status'=>'true', 'attempt_completion_status'=>'true'), "content_ID=".$activity->offsetGet('content_ID')." AND users_LOGIN='". $_SESSION['s_login']."'");

				}
			}

			if($activity->objective_set_by_content!='true')
			{
				$result = eF_getTableData("scorm_sequencing_objectives", "*", "content_ID = ".$activity->offsetGet('id'). " AND is_primary=1");

				// primary objective may not expliticly defined
				if(empty($result))
				{
					eF_insertOrupdateTableData("scorm_sequencing_objective_progress_information", array('content_ID'=>$activity->offsetGet('id'), 'users_LOGIN'=>$_SESSION['s_login'], 'objective_progress_status'=>'true', 'objective_satisfied_status'=>'true', 'objective_ID'=>null), "users_LOGIN = '".$_SESSION['s_login']."' AND content_ID = ".$activity->offsetGet('id'));
				}
				else
				{
					eF_insertOrupdateTableData("scorm_sequencing_objective_progress_information", array('content_ID'=>$value[0]['content_ID'], 'users_LOGIN'=>$_SESSION['s_login'], 'objective_progress_status'=>'true', 'objective_satisfied_status'=>'true', 'objective_ID'=>$value[0]['objective_ID']), "users_LOGIN = '".$_SESSION['s_login']."' AND content_ID = ".$value[0]['id']." AND objective_ID = '".$value[0]['objective_ID']);
				}
			}

		/*
	foreach ($result as $value)
				{
					if($activity->offsetGet('objective_progress_status') != 'true')
					{
						if($value['is_primary']==1)
						{
							eF_insertOrupdateTableData("scorm_sequencing_objective_progress_information", array('content_ID'=>$value['content_ID'], 'users_LOGIN'=>$_SESSION['s_login'], 'objective_progress_status'=>'true', 'objective_satisfied_status'=>'true', 'objective_ID'=>$value['objective_ID']), "users_LOGIN = '".$_SESSION['s_login']."' AND content_ID = ".$value['id']." AND objective_ID = '".$value['objective_ID']);
						}
					}
				}
*/

		}
	}
	else
	{
		//todo
		
	}

	eF_updateTableData("scorm_sequencing_activity_state_information", array('is_active'=>'false'), "content_ID=".$activity->offsetGet('content_ID')." AND users_LOGIN='". $_SESSION['s_login']."'");

	overall_rollup_process($currentContent, $navigation, $activity);


}

function sequencing_rules_check_process($activity, $rule_actions)
{
	if(S_DEBUG)
	{
		echo 'Sequencing Rules Check Process=>';
	}
	$result = eF_getTableData("scorm_sequencing_rules", "*", "content_ID=".$activity->offsetGet('id')." AND rule_action in ".$rule_actions);

	foreach($result as $value)
	{
		echo 'DDI';

		$rule_result = sequencing_rule_check_process($value);
		
		if($rule_result)
		{
			return $value['rule_action'];
		}
	}

	return null;
}

function sequencing_rule_check_process($rule)
{
	if(S_DEBUG)
	{
		echo 'Sequencing Rule Check Process=>';
	}
	$rule_condition_bag = array();

	$result = eF_getTableData("scorm_sequencing_rule, scorm_sequencing_rules", "*", "scorm_sequencing_rules_ID=".$rule['id'] . " AND scorm_sequencing_rules_ID = scorm_sequencing_rules.id");

	foreach($result as $value)
	{	
		//Evaluate the rule condition by applying the appropriate tracking information for the activity to the Rule Condition?

		$evaluation = scorm_sequencing_evaluate_condition($value);

		if($value['operator'] == 'not')
		{
			$rule_condition_bag[] = !$evaluation;
		}
		else
		{
			$rule_condition_bag[] = $evaluation;
		}
	}

	if (empty($rule_condition_bag))
	{
        $condition = 'unknown';
        return false;      //???
	}

	if($rule['condition_combination']=='any')
	{
		foreach($rule_condition_bag as $value)
		{   
			$combined_rule = $combined_rule || $value;
		}
	}
	else	
	{
		$combined_rule = true;
		pr($rule_condition_bag);
		foreach($rule_condition_bag as $value)
		{
			$combined_rule = $combined_rule && $value;
		}
	}

	return $combined_rule;
}

function is_suspended($nodeID, $referenced_objective)
{
	$result = eF_getTableData("scorm_sequencing_activity_state_information", "*", "content_ID=".$nodeID." AND users_LOGIN='". $_SESSION['s_login']."'");

	if (sizeof($result) > 0) 
	{
		if($result[0]['is_suspended'] == 'true')
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	else
	{
		return false;
	}
}

function is_active($nodeID)
{
	$result = eF_getTableData("scorm_sequencing_activity_state_information", "*", "content_ID=".$nodeID." AND users_LOGIN='". $_SESSION['s_login']."'");

	if (sizeof($result) > 0) 
	{
		if($result[0]['is_active'] == 'true')
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	else
	{
		return false;
	}
}


function form_activity_path($currentContent, $startNode, $endNode, $exclude_activities=array())
{
	$startNodeAncestors = $currentContent->getNodeAncestors($startNode);
	$startNode = $currentContent->filterOutChildren($startNode);
	
	$endNodeAncestors = $currentContent->getNodeAncestors($endNode);
	$endNode = $currentContent->filterOutChildren($endNode);

	if($pos = array_search($startNode, $endNodeAncestors) !== false && is_array($endNodeAncestors))
	{
		
		$activity_path =array_slice($endNodeAncestors, $pos-2);

		foreach($exclude_activities as $exclude)
		{
			foreach($activity_path as $key=>$activity)
			{
				if($activity['id']==$exclude->offsetGet('id'))
				{
					unset($activity_path[$key]);
				}
			}
		}
		return array_reverse($activity_path);
	}

	if($pos = array_search($endNode, $startNodeAncestors) !== false && is_array($startNodeAncestors))
	{
		$activity_path = array_slice($startNodeAncestors, $pos-2);

		foreach($exclude_activities as $exclude)
		{
			foreach($activity_path as $key=>$activity)
			{
				if($activity['id']==$exclude->offsetGet('id'))
				{
					unset($activity_path[$key]);
				}
			}
		}

		return $activity_path;
	}

	return array();	
}



function scorm_sequencing_evaluate_condition ($rule){

	if(S_DEBUG)
	{
		echo 'Scorm Sequencing Evaluate Condition=>';
	}
            switch ($rule['rule_condition']){
                
                case 'satisfied':
					$result = eF_getTableData("scorm_sequencing_objective_progress_information", "*", "users_LOGIN = '".$_SESSION['s_login']."' AND content_ID = '".$rule['content_ID']."' AND objective_ID = '".$rule['referenced_objective']."'");

					
					if(sizeof($result)==0)
					{
						$result = eF_getTableData("scorm_sequencing_objectives", "*", "content_ID=".$rule['content_ID']." AND is_primary=1");  

						$result = eF_getTableData("scorm_sequencing_objective_progress_information", "*", "users_LOGIN = '".$_SESSION['s_login']."' AND content_ID = '".$rule['content_ID']."' AND objective_ID = '".$result[0]['objective_ID']."'");

					}

					if($result[0]['objective_satisfied_status']=='true')
					{
						return true;
					}
					break;
				case 'attempted':
					echo '??????????';

					$result = eF_getTableData("scorm_sequencing_activity_progress_information", "*", "users_LOGIN = '".$_SESSION['s_login']."' AND content_ID = ".$rule['content_ID']);

					pr($result);

					if($result[0]['activity_attempt_count']>0)
					{
						echo 'attempted=true';
						return true;
					}

					break;
				case 'completed':
					echo '!!!!!!!!!!!';
					$result = eF_getTableData("scorm_sequencing_attempt_progress_information", "*", "users_LOGIN = '".$_SESSION['s_login']."' AND content_ID = ".$rule['content_ID']);
					

					if($result[0]['attempt_completion_status']=='true')
					{
						echo 'completed=true';
						return true;
					}

					break;

				default:
					break;

			}

	return false;
}

function are_siblings($activity1, $activity2)
{

	if($activity1->offsetGet('parent_content_ID') == $activity2->offsetGet('parent_content_ID'))
	{
		return true;
	}

	return false;

}

function sequencing_exit_action_rules($navigation, $currentContent)
{

	if(S_DEBUG)
	{
		echo 'Sequencing Exit Action Rules=>';
	}

	$activity_path = form_activity_path($currentContent, $currentContent->getFirstNode(), $navigation->current_activity);
	$activity_path = array_reverse($activity_path);    //todo 

	$exit_target = null;

	echo '<br>------**-------<br>';
	//pr($activity_path);
	
	echo '<br>------**-------<br>';
	

	foreach($activity_path as $value)
	{
		$value = $currentContent->seekNode($value['id']);

		$rule_action = "('exit')";
		$rule_result = sequencing_rules_check_process($value, $rule_action);	

		if($rule_result)
		{
			$exit_target = $value;
			break;
		}
	}

	if($exit_target)
	{

		$navigation = terminate_descendent_attempts_process($navigation, $currentContent, $exit_target);

		end_attempt_process($exit_target, $navigation, $currentContent);
		
		$navigation->setCurrentActivity($exit_target);

	}

	return $navigation;
}		

?>
