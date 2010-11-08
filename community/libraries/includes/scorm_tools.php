<?php
//This file cannot be called directly, only included.
if (str_replace(DIRECTORY_SEPARATOR, "/", __FILE__) == $_SERVER['SCRIPT_FILENAME']) {
    exit;
}

/**

* H synarthsh afth dhmiourgei tis synarthseis Javascript pou einai aparaithtes gia na tre3ei se scorm-enabled perivallon

*/
function get_APIFunctions()
{
    $func1 = '
        var startDate;
        var exitPageStatus;
        function loadPage()
        {
           var result = doLMSInitialize();
           var status = doLMSGetValue( "cmi.core.lesson_status" );

           if (status == "not attempted")
           {
              // the student is now attempting the lesson
              doLMSSetValue( "cmi.core.lesson_status", "incomplete" );
           }

           exitPageStatus = false;
           startTimer();
        }


        function startTimer()
        {
           startDate = new Date().getTime();
        }

        function computeTime()
        {
           if ( startDate != 0 )
           {
              var currentDate = new Date().getTime();
              var elapsedSeconds = ( (currentDate - startDate) / 1000 );
              var formattedTime = convertTotalSeconds( elapsedSeconds );
           }
           else
           {
              formattedTime = "00:00:00.0";
           }

           doLMSSetValue( "cmi.core.session_time", formattedTime );
        }

        function doBack()
        {
           doLMSSetValue( "cmi.core.exit", "suspend" );

           computeTime();
           exitPageStatus = true;

           var result;

           result = doLMSCommit();

            // NOTE: LMSFinish will unload the current SCO.  All processing
            //       relative to the current page must be performed prior
            //       to calling LMSFinish.

           result = doLMSFinish();

        }

        function doContinue( status )
        {
           // Reinitialize Exit to blank
           doLMSSetValue( "cmi.core.exit", "" );

           var mode = doLMSGetValue( "cmi.core.lesson_mode" );

           if ( mode != "review" && mode != "browse" )
           {
              doLMSSetValue( "cmi.core.lesson_status", status );
           }

           computeTime();
           exitPageStatus = true;

           var result;
           result = doLMSCommit();
            // NOTE: LMSFinish will unload the current SCO.  All processing
            //       relative to the current page must be performed prior
            //       to calling LMSFinish.

           result = doLMSFinish();

        }

        function doQuit( status )
        {
           computeTime();
           exitPageStatus = true;

           var result;

           result = doLMSCommit();

           result = doLMSSetValue("cmi.core.lesson_status", status);

            // NOTE: LMSFinish will unload the current SCO.  All processing
            //       relative to the current page must be performed prior
            //       to calling LMSFinish.

           result = doLMSFinish();
        }

        function unloadPage( status )
        {

            if (exitPageStatus != true)
            {
                doQuit( status );
            }


        }

        /*******************************************************************************

        ** this function will convert seconds into hours, minutes, and seconds in

        ** CMITimespan type format - HHHH:MM:SS.SS (Hours has a max of 4 digits &

        ** Min of 2 digits

        *******************************************************************************/
        function convertTotalSeconds(ts)
        {
           var sec = (ts % 60);
           ts -= sec;
           var tmp = (ts % 3600); //# of seconds in the total # of minutes
           ts -= tmp; //# of seconds in the total # of hours
           // convert seconds to conform to CMITimespan type (e.g. SS.00)
           sec = Math.round(sec*100)/100;
           var strSec = new String(sec);
           var strWholeSec = strSec;
           var strFractionSec = "";
           if (strSec.indexOf(".") != -1)
           {
              strWholeSec = strSec.substring(0, strSec.indexOf("."));
              strFractionSec = strSec.substring(strSec.indexOf(".")+1, strSec.length);
           }

           if (strWholeSec.length < 2)
           {
              strWholeSec = "0" + strWholeSec;
           }
           strSec = strWholeSec;

           if (strFractionSec.length)
           {
              strSec = strSec+ "." + strFractionSec;
           }


           if ((ts % 3600) != 0 )
              var hour = 0;
           else var hour = (ts / 3600);
           if ( (tmp % 60) != 0 )
              var min = 0;
           else var min = (tmp / 60);

           if ((new String(hour)).length < 2)
              hour = "0"+hour;
           if ((new String(min)).length < 2)
              min = "0"+min;

           var rtnVal = hour+":"+min+":"+strSec;

           return rtnVal;
        }
    ';

    $func2 = '

        var _Debug = false; // set this to false to turn debugging off
                             // and get rid of those annoying alert boxes.

        // Define exception/error codes
        var _NoError = 0;
        var _GeneralException = 101;
        var _ServerBusy = 102;
        var _InvalidArgumentError = 201;
        var _ElementCannotHaveChildren = 202;
        var _ElementIsNotAnArray = 203;
        var _NotInitialized = 301;
        var _NotImplementedError = 401;
        var _InvalidSetValue = 402;
        var _ElementIsReadOnly = 403;
        var _ElementIsWriteOnly = 404;
        var _IncorrectDataType = 405;


        // local variable definitions
        var apiHandle = null;
        var API = null;
        var findAPITries = 0;


        function doLMSInitialize()
        {
           var api = getAPIHandle();
           if (api == null)
           {
              alert("Unable to locate the LMS API Implementation.\nLMSInitialize was not successful.");
              return "false";
           }

           var result = api.LMSInitialize("");

           if (result.toString() != "true")
           {
              var err = ErrorHandler();
           }

           return result.toString();
        }

        function doLMSFinish()
        {
           var api = getAPIHandle();
           if (api == null)
           {
              alert("Unable to locate the LMS API Implementation.\nLMSFinish was not successful.");
              return "false";
           }
           else
           {
              // call the LMSFinish function that should be implemented by the API

              var result = api.LMSFinish("");
              if (result.toString() != "true")
              {
                 var err = ErrorHandler();
              }

           }

           return result.toString();
        }

        function doLMSGetValue(name)
        {
           var api = getAPIHandle();
           if (api == null)
           {
              alert("Unable to locate the LMS API Implementation.\nLMSGetValue was not successful.");
              return "";
           }
           else
           {
              var value = api.LMSGetValue(name);
              var errCode = api.LMSGetLastError().toString();
              if (errCode != _NoError)
              {
                 // an error was encountered so display the error description
                 var errDescription = api.LMSGetErrorString(errCode);
                 alert("LMSGetValue("+name+") failed. \n"+ errDescription);
                 return "";
              }
              else
              {

                 return value.toString();
              }
           }
        }

        function doLMSSetValue(name, value)
        {
           var api = getAPIHandle();
           if (api == null)
           {
              alert("Unable to locate the LMS API Implementation.\nLMSSetValue was not successful.");
              return;
           }
           else
           {
              var result = api.LMSSetValue(name, value);
              if (result.toString() != "true")
              {
                 var err = ErrorHandler();
              }
           }

           return;
        }

        function doLMSCommit()
        {
           var api = getAPIHandle();
           if (api == null)
           {
              alert("Unable to locate the LMS API Implementation.\nLMSCommit was not successful.");
              return "false";
           }
           else
           {
              var result = api.LMSCommit("");
              if (result != "true")
              {
                 var err = ErrorHandler();
              }
           }

           return result.toString();
        }

        function doLMSGetLastError()
        {
           var api = getAPIHandle();
           if (api == null)
           {
              alert("Unable to locate the LMS API Implementation.\nLMSGetLastError was not successful.");
              return _GeneralError;
           }

           return api.LMSGetLastError().toString();
        }

        function doLMSGetErrorString(errorCode)
        {
           var api = getAPIHandle();
           if (api == null)
           {
              alert("Unable to locate the LMS API Implementation.\nLMSGetErrorString was not successful.");
           }

           return api.LMSGetErrorString(errorCode).toString();
        }

        function doLMSGetDiagnostic(errorCode)
        {
           var api = getAPIHandle();
           if (api == null)
           {
              alert("Unable to locate the LMS API Implementation.\nLMSGetDiagnostic was not successful.");
           }

           return api.LMSGetDiagnostic(errorCode).toString();
        }

        function LMSIsInitialized()
        {

           var api = getAPIHandle();
           if (api == null)
           {
              alert("Unable to locate the LMS API Implementation.\nLMSIsInitialized() failed.");
              return false;
           }
           else
           {
              var value = api.LMSGetValue("cmi.core.student_name");
              var errCode = api.LMSGetLastError().toString();
              if (errCode == _NotInitialized)
              {
                 return false;
              }
              else
              {
                 return true;
              }
           }
        }

        function ErrorHandler()
        {
           var api = getAPIHandle();
           if (api == null)
           {
              alert("Unable to locate the LMS API Implementation.\nCannot determine LMS error code.");
              return;
           }

           // check for errors caused by or from the LMS
           var errCode = api.LMSGetLastError().toString();
           if (errCode != _NoError)
           {
              // an error was encountered so display the error description
              var errDescription = api.LMSGetErrorString(errCode);

              if (_Debug == true)
              {
                 errDescription += "\n";
                 errDescription += api.LMSGetDiagnostic(null);
                 // by passing null to LMSGetDiagnostic, we get any available diagnostics
                 // on the previous error.
              }

              alert(errDescription);
           }

           return errCode;
        }

        function getAPIHandle()
        {
           if (apiHandle == null)
           {
              apiHandle = getAPI();
           }

           return apiHandle;
        }


        function findAPI(win)
        {
           while ((win.API == null) && (win.parent != null) && (win.parent != win))
           {
              findAPITries++;
              // Note: 7 is an arbitrary number, but should be more than sufficient
              if (findAPITries > 7)
              {
                 alert("Error finding API -- too deeply nested.");
                 return null;
              }

              win = win.parent;

           }
           return win.API;
        }



        function getAPI()
        {
           var theAPI = findAPI(window);
           if ((theAPI == null) && (window.opener != null) && (typeof(window.opener) != "undefined"))
           {
              theAPI = findAPI(window.opener);
           }
           if (theAPI == null)
           {
              alert("Unable to find an API adapter");
           }
           return theAPI
        }
    ';

    return array($func1, $func2);
}

 /**

* ���� �� �������� ��� manifest ��� ���� �� �� ��� ����������� array

*/
function eF_local_parseManifest($path)
{
    $filename = $path."/imsmanifest.xml";
    $data = iconv("ISO-8859-7", "UTF-8", implode("", file($filename)));
    $parser = xml_parser_create();
    xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
    xml_parse_into_struct($parser, $data, $tagContents, $tags);
    xml_parser_free($parser);

    $currentParent = array(0 => 0);

    for ($i = 0; $i < sizeof($tagContents); $i++) {
        if ($tagContents[$i]['type'] != 'close') {
            $tagArray[$i] = array('parent_index' => end($currentParent),
                                  'tag' => $tagContents[$i]['tag'],
                                  'value' => $tagContents[$i]['value'],
                                  'attributes' => $tagContents[$i]['attributes'],
                                  'children' => array()
                            );
            array_push($tagArray[end($currentParent)]['children'], $i);
        }
        if ($tagContents[$i]['type'] == 'open') {
            array_push($currentParent, $i);
        } else if ($tagContents[$i]['type'] == 'close') {
            array_pop($currentParent);
        }

    }
    return $tagArray;
}

function eF_local_buildDirectories($new_absolute_dir, $scorm_dir)
{
    $current_dir = getcwd();
    chdir($scorm_dir);
    $path_array = eF_local_readDirRecursive();

    chdir($new_absolute_dir);

    foreach ($path_array as $value) {
        $path_parts = explode("/", $value);
        $this_dir = $new_absolute_dir;
        for ($i = 1; $i < sizeof($path_parts); $i++) {
            if (!file_exists($this_dir.$path_parts[$i]."/")) {
                chdir($this_dir);
                mkdir($path_parts[$i], 0755);
            }
            $this_dir .= $path_parts[$i]."/";
        }
        chdir($current_dir);
    }
}

function eF_local_readDirRecursive($path = '.')
{
    $count = 0;
    $path_array[0] = $path;
    while (isset($path_array[$count])) {
        $handle = opendir($path_array[$count]);
        while (false !== ($file = readdir($handle))) {
            if ($file != '.' && $file != '..') {
                if (is_dir($path_array[$count]."/".$file)) {
                    array_push($path_array, $path_array[$count]."/".$file);
                }
            }
        }
        closedir($handle);
        $count++;
    }
    return $path_array;
}


function build_tarfile($lessons_id, $path)
{
    $main_dirname = $path."/lesson" . $lessons_id . "/";
    $filelist = eF_getDirContents($main_dirname);
    $tarname = $main_dirname . "../scorm_lesson.tgz";
    $tar = new Archive_Tar($tarname, true);
    $tar -> createModify($filelist, "", $main_dirname);
}

/**

*

*/
function get_asset_metadata($el)
{
    $metadata = '<?xml version="1.0" encoding="ISO-8859-1"?>
        <lom xmlns="http://www.imsglobal.org/xsd/imsmd_rootv1p2p1"
             xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
             xsi:schemaLocation="http://www.imsglobal.org/xsd/imsmd_rootv1p2p1 imsmd_rootv1p2p1.xsd">

            <general>
                <identifier>
                </identifier>
                <title>
                </title>
                <catalogentry>
                    <catalog>
                    </catalog>
                    <entry>
                    </entry>
                </catalogentry>
                <language>
                </language>
                <description>
                </description>
                <keyword>
                </keyword>
                <coverage>
                </coverage>
                <structure>
                </structure>
                <aggregationlevel>
                </aggregationlevel>
            </general>

            <lifecycle>
                <version>
                </version>
                <status>
                </status>
                <contribute>
                    <role>
                    </role>
                    <centity>
                    </centity>
                    <date>
                    </date>
                </contribute>
            </lifecycle>

            <metametadata>
                <identifier>
                </identifier>
                <catalogentry>
                    <catalog>
                    </catalog>
                    <entry>
                    </entry>
                </catalogentry>
                <contribute>
                    <role>
                    </role>
                    <centity>
                    </centity>
                    <date>
                    </date>
                </contribute>
                <metadatascheme>
                </metadatascheme>
                <language>
                </language>
            </metametadata>

            <technical>
                <format>
                </format>
                <size>
                </size>
                <location>
                </location>
                <requirement>
                    <type>
                    </type>
                    <name>
                    </name>
                    <minimumversion>
                    </minimumversion>
                    <maximumversion>
                    </maximumversion>
                </requirement>
                <installationremarks>
                </installationremarks>
                <otherplatformrequirements>
                </otherplatformrequirements>
                <duration>
                </duration>
            </technical>

            <educational>
                <interactivitytype>
                </interactivitytype>
                <learningresourcetype>
                </learningresourcetype>
                <interactivitylevel>
                </interactivitylevel>
                <semanticdensity>
                </semanticdensity>
                <intendedenduserrole>
                </intendedenduserrole>
                <context>
                </context>
                <typicalagerange>
                </typicalagerange>
                <difficulty>
                </difficulty>
                <typicallearningtime>
                </typicallearningtime>
                <description>
                </description>
                <language>
                </language>
            </educational>

            <rights>
                <cost>
                </cost>
                <copyrightandotherrestrictions>
                </copyrightandotherrestrictions>
                <description>
                </description>
            </rights>

            <relation>
                <kind>
                </kind>
                <resource>
                    <identifier>
                    </identifier>
                    <description>
                    </description>
                    <catalogentry>
                        <catalog>
                        </catalog>
                        <entry>
                        </entry>
                    </catalogentry>
                </resource>
            </relation>

            <annotation>
                <person>
                </person>
                <date>
                </date>
                <description>
                </description>
            </annotation>

            <classification>
                <purpose>
                </purpose>
                <taxonpath>
                    <source>
                    </source>
                    <taxon>
                        <id>
                        </id>
                        <entry>
                        </entry>
                        <taxon>
                            <id>
                            </id>
                            <entry>
                            </entry>
                            <taxon>
                            </taxon>
                        </taxon>
                    </taxon>
                </taxonpath>
                <description>
                </description>
                <keyword>
                </keyword>
            </classification>


        </lom>

    ';

    return $metadata;
}


function create_manifest($lessons_id, $lesson_entries, $filelist, $path)
{
    $GLOBALS['configuration']['G_DELIMITER'] = '/';

    $first_dir_token = "lesson" . $lessons_id;
    $second_dir_token = "html";
    $third_dir_token = "content/lessons/$lessons_id";

    $main_dirname = $path ."/". $first_dir_token;
    $html_dirname = $main_dirname ."/". $second_dir_token;
    $files_dirname = $html_dirname ."/". $third_dir_token;

    mkdir($main_dirname, 0755);
    mkdir($html_dirname, 0755);
    $dir_parts = explode("/", $files_dirname);
    $cur_dir = getcwd();
    foreach ($dir_parts as $value) {
        if (!is_dir($value)) {
            mkdir($value, 0755);
        }
        chdir($value);
    }
    chdir($cur_dir);

    /*Gia ka8e periexomeno, ftia3e th lista me ta arxeia pou perilamvanei, to arxeio html pou to periexei, kai ta metadata*/
    for ($i = 0; $i < sizeof($lesson_entries); $i++) {
        $content = $lesson_entries[$i]['data'];
        $count = 0;
//pr($filelist);

        foreach ($filelist as $value) {
            $file_count = 0;
            /*replace back-slash "\" with forward-slash "/"   */
            if ($GLOBALS['configuration']['G_DELIMITER'] == '\\') $value = strtr($value, "\\", "/");

            $pattern = "#\"content/lessons/$lessons_id/$value\s*\"#";
            if (preg_match($pattern, $content, $matches)) {
                $lesson_entries[$i]['files'][$count++] = basename($value);
                $content = preg_replace($pattern, '"'.$third_dir_token.'/'.$value.'"', $content);
            }
        }


        $content = create_html_files($content);
        if ($fp = fopen($html_dirname . "/" . $lesson_entries[$i]['name'] . ".html", "wb")) {
          fwrite($fp, $content);
        }
        fclose($fp);

        $metadata = get_asset_metadata($lesson_entries[$i]);
        if ($fp = fopen($html_dirname . "/" . $lesson_entries[$i]['name'] . ".xml", "wb")) {
            fwrite($fp, $metadata);
        }
        fclose($fp);
    }

    $questions = ef_getTableData("questions q, content c", "q.*", "q.content_id = c.id and c.lessons_id=$lessons_id");
    for ($i = 0; $i < sizeof($questions); $i++){
        $data = $questions[$i]['text'];
        /*Gia ka8e arxeio, an afto fainetai stin erotisi, alla3e to path kai vale to onoma toy sth lista tou periexomenou*/
        foreach ($filelist as $value) {
            $file_count = 0;
            /*replace back-slash "\" with forward-slash "/"   */
            if ($GLOBALS['configuration']['G_DELIMITER'] == '\\') $value = strtr($value, "\\", "/");

            $pattern = "#\"content/lessons/$lessons_id/$value\s*\"#";
            if (preg_match($pattern, $data, $matches)) {
                $data = preg_replace($pattern, '"'.$third_dir_token.'/'.$value.'"', $data);
            }
        }
        if ($fp = fopen($html_dirname . "/question" . $questions[$i]['id'] . ".html", "wb")) {
          fwrite($fp, $data);
        }
        fclose($fp);
    }

    //create the test files
    $tests = ef_getTableData("tests t, content c", "t.*", "t.content_id = c.id and c.lessons_id=$lessons_id");
    for ($i = 0; $i < sizeof($tests); $i++){
        $data = $tests[$i]['description'];
        /*Gia ka8e arxeio, an afto fainetai stin erotisi, alla3e to path kai vale to onoma toy sth lista tou periexomenou*/
        foreach ($filelist as $value) {
            $file_count = 0;
            /*replace back-slash "\" with forward-slash "/"   */
            if ($GLOBALS['configuration']['G_DELIMITER'] == '\\') $value = strtr($value, "\\", "/");

            $pattern = "#\"\s*((http://.*)|(\.\./))?content/lessons/$lessons_id/$value\s*\"#";
            if (preg_match($pattern, $data, $matches)) {
                $data = preg_replace($pattern, '"'.$third_dir_token.'/'.$value.'"', $data);
            }
        }
        if ($fp = fopen($html_dirname . "/test" . $tests[$i]['id'] . ".html", "wb")) {
          fwrite($fp, $data);
        }
        fclose($fp);
    }

    $file_count = 0;
    foreach ($filelist as $key => $value) {
        if ($GLOBALS['configuration']['G_DELIMITER'] == '\\') $value = strtr($value, "\\", "/");

        /*Recursively create directories*/
        if (!is_dir($files_dirname)) {
         mkdir($files_dirname, 0755, true);
        }
        if (!is_dir($files_dirname.'/'.dirname($value))) {
         mkdir($files_dirname.'/'.dirname($value), 0755, true);
        }
        if (is_file(G_LESSONSPATH.$lessons_id.$GLOBALS['configuration']['G_DELIMITER'].$value)) {
         copy(G_LESSONSPATH.$lessons_id.$GLOBALS['configuration']['G_DELIMITER'].$value, $files_dirname . "/" . $value);
        }
        $fp = fopen($files_dirname . "/" . $value . ".xml", "wb");
        fwrite($fp, $metadata);
        fclose($fp);
    }


    /*Create manifest*/
    $prerequisites = get_prerequisites();
    $organizations_str = build_manifest_organizations($lessons_id, $prerequisites);
    $resources_str = build_manifest_resources($lesson_entries, $tests, $questions, $second_dir_token."/".$third_dir_token);
    $metadata_str = build_manifest_metadata(0);
    $manifest = build_manifest_main($metadata_str . $organizations_str . $resources_str);

    if ($fp = fopen($main_dirname . "/imsmanifest.xml", "wb")) {
        fwrite ($fp, $manifest);
    }
    fclose($fp);

    /*Create functions files*/
    list($func1, $func2) = get_APIFunctions();
    if ($fp = fopen($main_dirname . "/APIWrapper.js", "wb")) {
        fwrite($fp, $func1);
        fclose($fp);
    }
    if ($fp = fopen($main_dirname . "/SCOFunctions.js", "wb")) {
        fwrite($fp, $func2);
        fclose($fp);
    }
}

/**

* H parakatw synarthsh analamvanei na metasxhmatisei tous kanones toy ma8hmatos se prerequisites katallhla

* gia export se scorm.

*/
function get_prerequisites()
{
    /*Pare olous tous kanones toy typoy "an den exei dei thn enothta"*/
    $rules = eF_getTableData("rules", "content_ID,rule_content_ID", "rule_type='hasnot_seen'");
    /*Vale tous se ena pinakaki, pou 8a xrhsimopoih8ei argotera*/
    foreach ($rules as $value) {
        $prerequisites[$value['content_ID']] = $value['rule_content_ID'];
    }
    return $prerequisites;
}
/**

*       www.php.net

*/
function deldir($dir)
{
    if (isset($dir) & $dir != '' & $dir != G_LESSONSPATH) {
        $current_dir = opendir($dir);
        while($entryname = readdir($current_dir)) {
            if(is_dir("$dir/$entryname") and ($entryname != "." and $entryname!="..")) {
                deldir("${dir}/${entryname}");
            }
            elseif($entryname != "." and $entryname!="..") {
                unlink("${dir}/${entryname}");
            }
        }
        closedir($current_dir);
        rmdir("${dir}");
    }
}

/**

*

*/
function build_manifest_metadata($metadata)
{
    $schema = isset($metadata['schema']) ? $metadata['schema'] : 'ADL SCORM';
    $schemaversion = isset($metadata['schemaversion']) ? $metadata['schemaversion'] : '1.2';
    $adlcp_location = isset($metadata['adlcp_location']) ? $metadata['adlcp_location'] : '';
    $str='
        <metadata>
            <schema>'.
                $schema
            .'</schema>
            <schemaversion>'.
                $schemaversion
            .'</schemaversion>
            <adlcp:location>'.
                $adlcp_location
            .'</adlcp:location>
        </metadata>';
    return $str;
}

function build_manifest_resources($lesson_entries, $tests, $questions, $files_dir)
{

    $resource_str = '';
    $dependency_str = '';

    for ($i = 0 ; $i < sizeof($lesson_entries) ; $i++) {
        $resource_str .= '<resource identifier="' . $lesson_entries[$i]['id'] . '" type="webcontent" adlcp:scormtype="sco" href="html/' . $lesson_entries[$i]['name'] . '.html">';
        $resource_str .= '<metadata></metadata>';
        $resource_str .= '<file href="html/' . $lesson_entries[$i]['name'] . '.html"/>';
        $resource_str .= '<dependency identifierref="dep_SPECIAL"/>';

        for ($j = 0 ; $j < sizeof($lesson_entries[$i]['files']) ; $j++) {
            $resource_str .= '<dependency identifierref="dep_' . $i . '_' . $j . '"/>';
            $dependency_str .= '<resource identifier="dep_' . $i . '_' . $j . '" type="webcontent" adlcp:scormtype="asset" href="'.$files_dir.'/'. $lesson_entries[$i]['files'][$j] .'">';
            $dependency_str .= '<metadata></metadata>';
            $dependency_str .= '<file href="'.$files_dir.'/' . $lesson_entries[$i]['files'][$j] . '"/>';
            $dependency_str .= '</resource>';
        }
        $resource_str .= '</resource>';
    }

    for ($i = 0; $i < sizeof($tests); $i++){
        $resource_str .= '<resource identifier="t' . $tests[$i]['id'] . '" type="webcontent" adlcp:scormtype="sco" href="html/test' . $tests[$i]['id'] . '.html">';
        $resource_str .= '<metadata></metadata>';
        $resource_str .= '<file href="html/test' . $tests[$i]['id'] . '.html"/>';
        $resource_str .= '<dependency identifierref="dep_SPECIAL"/>';
        $resource_str .= '</resource>';
    }

    for ($i = 0; $i < sizeof($questions); $i++){
        $resource_str .= '<resource identifier="q' . $questions[$i]['id'] . '" type="webcontent" adlcp:scormtype="sco" href="html/question' . $questions[$i]['id'] . '.html">';
        $resource_str .= '<metadata></metadata>';
        $resource_str .= '<file href="html/question' . $questions[$i]['id'] . '.html"/>';
        $resource_str .= '<dependency identifierref="dep_SPECIAL"/>';
        $resource_str .= '</resource>';
    }

    $SPECIAL_str = '<resource identifier="dep_SPECIAL" adlcp:scormtype="asset"
                    type="webcontent">
             <file href="SCOFunctions.js"/>
             <file href="APIWrapper.js"/>
          </resource>';

    $final_str = '<resources>' . $resource_str . $dependency_str . $SPECIAL_str. '</resources>';
    return $final_str;

}

/**

*

*/
function build_manifest_organizations($lessons_id, $prerequisites)
{
    $tree = eF_getContentTree($nouse, $lessons_id, 0);
    //$cTree = new EfrontContentTree($lessons_id);
    for ($i = 0 ; $i < sizeof($tree) ; $i++) {
        $levels[$i] = $tree[$i]['level'];
    }
    for ($i = max($levels) ; $i>= 0 ; $i--) {
        for ($j = 0 ; $j < sizeof($tree) ; $j++) {
            if ($tree[$j]["level"] == $i && $tree[$j]["ctg_type"] != "tests" && $tree[$j]["ctg_type"] != "scorm" && $tree[$j]["ctg_type"] != "scorm_test") {
                $tree[$j]["string"] = "\n<item identifier=\"item" . $tree[$j]["id"] . "\" identifierref=\"" . $tree[$j]["id"] . "\">\n\t<title>" . ($tree[$j]["name"]) . "</title>\n";
                /*An to antikeimeno exei prerequisites, pros8ese tis katallhles grammes*/
                if ($prerequisites[$tree[$j]["id"]]) {
                    //echo "<br> A".$tree[$j]["id"];
                    $tree[$j]["string"] .= "\n<adlcp:prerequisites type=\"aicc_script\">item" . $prerequisites[$tree[$j]["id"]] . "</adlcp:prerequisites>";
                }

                if (isset($tree[$j]["children"])) {
                    for ($k = 1 ; $k <= sizeof($tree[$j]["children"]) ; $k++) {
                        $tree[$j]["string"] .= $tree[$j]["children"][$k];
                    }
                }
                $tree[$j]["string"] .= "\n</item>";
                if ($tree[$j]["parent_id"] == 0) {
                    $final_str .= $tree[$j]["string"];
                } else {
                    for ( $m = 0 ; $m < sizeof($tree) ; $m++) {
                        if ($tree[$m]["id"] == $tree[$j]["parent_id"]) {
                            $tree[$m]["children"][sizeof($tree[$m]["children"]) + 1] = "\t" . $tree[$j]["string"];
                        }
                    }
                }
            }
        }
    }

    //write out the questions
    /*$questions = ef_getTableData("questions q, content c", "q.*", "q.content_id = c.id and c.lessons_id=$lessons_id");

    for ($i = 0; $i < sizeof($questions); $i++){

        $final_str .= "<item identifier='question".$questions[$i]['id']."' identifierref='q".$questions[$i]['id']."' type='question'><title>question ".($i+1)."</title></item>";

    }*/
    //write the tests
    /*$tests = ef_getTableData("tests t, content c", "c.name, t.*", "t.content_id = c.id and c.lessons_id=$lessons_id");

    for ($i = 0; $i < sizeof($tests); $i++){

        $final_str .= "<item identifier='test".$tests[$i]['id']."' identifierref='t".$tests[$i]['id']."' type='test'><title>".$tests[$i]['name']."</title></item>";

    }*/
    //write the projects
    /*$projects = ef_getTableData("projects","*", "lessons_id=$lessons_id");

    for ($i = 0; $i < sizeof($projects); $i++){

        $final_str .= "<item identifier='project".$projects[$i]['id']."' identifierref='p".$projects[$i]['id']."' type='project'><title>project ".($i+1)."</title></item>";

    }*/
    $content .= "\t" . '<organizations default="org1">' . "\n";
    $content .= "\t<organization identifier=\"Org\" structure=\"hierarchical\"><title>default</title>" . "\n";
    $content .= $final_str . "\t" . '</organization></organizations>' . "\n";
    return $content;
}
/**

*

*/
function build_manifest_main($str)
{
    $manifest = '<?xml version="1.0" encoding="ISO-8859-7"?>
    <manifest identifier="SingleCourseManifest" version="1.1"
              xmlns="http://www.imsproject.org/xsd/imscp_rootv1p1p2"
              xmlns:adlcp="http://www.adlnet.org/xsd/adlcp_rootv1p2"
              xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
              xsi:schemaLocation="http://www.imsproject.org/xsd/imscp_rootv1p1p2 imscp_rootv1p1p2.xsd
                                  http://www.imsglobal.org/xsd/imsmd_rootv1p2p1 imsmd_rootv1p2p1.xsd
                                  http://www.adlnet.org/xsd/adlcp_rootv1p2 adlcp_rootv1p2.xsd">
        ';
    $manifest .= $str;
    $manifest .= '</manifest>';
    return $manifest;
}
/**

* H synarthsh afth dhmiourgei to outline enos arxeiou html kai emvy8izei mesa to periexomeno.

*/
function create_html_files($content)
{
    $page_script = '
        var apiHandle = null;
        var API = null;
        var findAPITries = 0;
        function getAPIHandle()
        {
           if (apiHandle == null)
           {
              apiHandle = getAPI();
           }
           return apiHandle;
        }
        function findAPI(win)
        {
           while ((win.API == null) && (win.parent != null) && (win.parent != win))
           {
              findAPITries++;
              // Note: 7 is an arbitrary number, but should be more than sufficient
              if (findAPITries > 7)
              {
                 alert("Error finding API -- too deeply nested.");
                 return null;
              }
              win = win.parent;
           }
           return win.API;
        }
        function getAPI()
        {
           var theAPI = findAPI(window);
           if ((theAPI == null) && (window.opener != null) && (typeof(window.opener) != "undefined"))
           {
              theAPI = findAPI(window.opener);
           }
           if (theAPI == null)
           {
              alert("Unable to find an API adapter");
           }
           return theAPI
        }

        function my_finish()
        {
            alert(getAPIHandle().LMSGetLastError());
            getAPIHandle().LMSSetValue("cmi.core.lesson_status","completed");
            alert(getAPIHandle().LMSGetLastError());
            getAPIHandle().LMSFinish("");
            alert(getAPIHandle().LMSGetLastError());
        }

        function my_start()
        {
            alert(getAPIHandle().LMSGetLastError());
            getAPIHandle().LMSInitialize("");
            alert(getAPIHandle().LMSGetLastError());
        }


    ';
    $page_head = '<html><head><script language=javascript src="../APIWrapper.js"></script><script language=javascript src="../SCOFunctions.js"></script></head><body onunload="return unloadPage(\'incomplete\')">';
    $page_tail = '
        <script language="javascript">
        loadPage();
        //var   studentName = "!";
        //var   lmsStudentName = doLMSGetValue(  "cmi.core.student_name" );

        //if ( lmsStudentName  != "" )
        //{
        //   studentName = " " + lmsStudentName +   "!";
        //}

        //document.write(studentName);
        </script>
    <br><br>
    <input type = "button" value = "  OK  " onClick = "doQuit(\'completed\')" id=button2 name=button2>
    ';

    $overall = $page_head . $content . $page_tail;

    return $overall;
}
