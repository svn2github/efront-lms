<?php

$declared_classes = get_declared_classes();
if (!in_array('FUZE_CryptXOR',$declared_classes)) require_once 'FUZE_classes/transport/FUZE_CryptXOR.php';
if (!in_array('RequestAdapterAbstract',$declared_classes)) require_once 'FUZE_classes/transport/RequestAdapterAbstract.php';
if (!in_array('RequestAdapterCurl',$declared_classes)) require_once 'FUZE_classes/transport/RequestAdapterCurl.php';
if (!in_array('RequestAdapterFile',$declared_classes)) require_once 'FUZE_classes/transport/RequestAdapterFile.php';
if (!in_array('RequestFactory',$declared_classes)) require_once 'FUZE_classes/transport/RequestFactory.php';

if (!in_array('FUZE_AbstractClass',$declared_classes)) require_once 'FUZE_classes/FUZE_AbstractClass.php';
if (!in_array('FUZE_AbstractDAO',$declared_classes)) require_once 'FUZE_classes/FUZE_AbstractDAO.php';
if (!in_array('FUZE_DAOFactory',$declared_classes)) require_once 'FUZE_classes/FUZE_DAOFactory.php';
if (!in_array('FUZE_TODefault',$declared_classes)) require_once 'FUZE_classes/FUZE_TODefault.php';
if (!in_array('FUZE_TOFactory',$declared_classes)) require_once 'FUZE_classes/FUZE_TOFactory.php';

if (!in_array('FUZE_Account',$declared_classes)) require_once 'FUZE_classes/FUZE_Account.php';
if (!in_array('FUZE_AccountDAO',$declared_classes)) require_once 'FUZE_classes/FUZE_AccountDAO.php';
if (!in_array('FUZE_Meeting_Attendee',$declared_classes)) require_once 'FUZE_classes/FUZE_Meeting_Attendee.php';
if (!in_array('FUZE_Meeting_AttendeeDAO',$declared_classes)) require_once 'FUZE_classes/FUZE_Meeting_AttendeeDAO.php';
if (!in_array('FUZE_Meeting',$declared_classes)) require_once 'FUZE_classes/FUZE_Meeting.php';
if (!in_array('FUZE_MeetingDAO',$declared_classes)) require_once 'FUZE_classes/FUZE_MeetingDAO.php';
if (!in_array('FUZE_User',$declared_classes)) require_once 'FUZE_classes/FUZE_User.php';
if (!in_array('FUZE_UserDAO',$declared_classes)) require_once 'FUZE_classes/FUZE_UserDAO.php';
if (!in_array('FUZE_User_Manager',$declared_classes)) require_once 'FUZE_classes/FUZE_User_Manager.php';
if (!in_array('FUZE_User_ManagerDAO',$declared_classes)) require_once 'FUZE_classes/FUZE_User_ManagerDAO.php';
if (!in_array('FUZE_System_User',$declared_classes)) require_once 'FUZE_classes/FUZE_System_User.php';
if (!in_array('FUZE_System_UserDAO',$declared_classes)) require_once 'FUZE_classes/FUZE_System_UserDAO.php';

if (!in_array('FUZE_Tools',$declared_classes)) require_once 'FUZE_classes/FUZE_Tools.php';
