     * @param $returnObjects whether to return event objects or not

     * @param $avatarSize the normalization size for the avatar images

     * @param $limit maximum number of events to return 

     * @return boolean true/false

     * @since 3.6.0

     * @access public

     */
    public function getEvents($topic_ID = false, $returnObjects = false, $avatarSize, $limit = false) {
  if (!($GLOBALS['configuration']['social_modules_activated'] & SOCIAL_FUNC_LESSON_TIMELINES)) {
      return array();
  }
     if ($topic_ID) {
      // only current lesson users
      $users = $this -> getUsers();
      $users_logins = array_keys($users); // don't mix with course events - with courses_ID = $this->lesson['id']		
      $related_events = eF_getTableData("events", "*", "type = '".EfrontEvent::NEW_POST_FOR_LESSON_TIMELINE_TOPIC. "' AND entity_ID = '".$topic_ID."' AND lessons_ID = '". $this->lesson['id']."' AND users_LOGIN IN ('".implode("','", $users_logins)."') AND (type < 50 OR type >74)", "timestamp desc");
        } else {
      // only current lesson users
      $users = $this -> getUsers();
      $users_logins = array_keys($users);
//    		if ($limit) {
//    			$related_events = eF_getTableData("events", "*", "lessons_ID = '". $this->lesson['id']."' AND users_LOGIN IN ('".implode("','", $users_logins)."')", "timestamp desc LIMIT " . $limit);
//    			
//    		} else {
      $related_events = eF_getTableData("events", "*", "lessons_ID = '". $this->lesson['id']."' AND users_LOGIN IN ('".implode("','", $users_logins)."')  AND (type < 50 OR type >74)	", "timestamp desc");
//    		}
        }
     if (!isset($avatarSize) || $avatarSize <= 0) {
      $avatarSize = 25;
     }
     $prev_event = false;
     $count = 0;
     $filtered_related_events = array();
     foreach($related_events as $key => $event) {
   $user = $users[$event['users_LOGIN']];
   // Logical combination of events
   if ($prev_event) {
    // since we have decreasing chronological order we now that $event['timestamp'] < $prev_event['timestamp']
    if ($event['users_LOGIN'] == $prev_event['event']['users_LOGIN'] && $event['type'] == $prev_event['event']['type'] && $prev_event['event']['timestamp'] - $event['timestamp'] < EfrontEvent::SAME_USER_INTERVAL) {
     unset($filtered_related_events[$prev_event['key']]);
     $count--;
    }
   }
   $filtered_related_events[$key] = $event;
         try {
             $file = new EfrontFile($user['avatar']);
             $filtered_related_events[$key]['avatar'] = $user['avatar'];
             list($filtered_related_events[$key]['avatar_width'], $filtered_related_events[$key]['avatar_height']) = eF_getNormalizedDims($file['path'],$avatarSize, $avatarSize);
         } catch (EfrontfileException $e) {
             $filtered_related_events[$key]['avatar'] = G_SYSTEMAVATARSPATH."unknown_small.png";
             $filtered_related_events[$key]['avatar_width'] = $avatarSize;
             $filtered_related_events[$key]['avatar_height'] = $avatarSize;
         }
         $prev_event = array("key"=>$key, "event"=>$event);
   if ($limit && ++$count == $limit) {
    break;
   }
     }
     if ($returnObjects) {
            $eventObjects = array();
            foreach ($filtered_related_events as $event) {
                $eventObjects[] = new EfrontEvent($event);
            }
            return $eventObjects;
        } else {
            return $filtered_related_events;
        }
    }
}
?>
