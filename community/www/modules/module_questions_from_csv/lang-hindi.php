<?php
define("_MODULE_QUESTIONS_TESTQUESTIONSUPLOADING","परीक्षण अपलोड सवाल");//Test questions uploading
define("_MODULE_QUESTIONS_IMPORTUSERSHISTORYFROMXLSFILE","आयात csv फ़ाइल से परीक्षा प्रश्न");//Import test questions from csv file
define("_MODULE_QUESTIONS_HISTORYRECORDS" , "test questions");
define("_MODULE_QUESTIONS_LOGINCOLUMNTITLE","eFront उपयोगकर्ता लॉगिन के साथ स्तंभ के लिए शीर्षक");//Title for column with eFront user logins
define("_MODULE_QUESTIONS_DATECOLUMNTITLE","शीर्षक घटना तारीखों के साथ स्तंभ के लिए");//Title for column with event dates
define("_MODULE_QUESTIONS_HANDLINGFORNOTEXISTINGLOGINS","मौजूदा प्रश्नों के लिए विकल्प");//Options for existing questions
define("_MODULE_QUESTIONS_IMPORTINTO","में आयात");//Import into
define("_MODULE_QUESTIONS_INCLUDECOLUMNTITLESINTOEVENTDESCRIPTION","घटना में शामिल विवरण में स्तंभ शीर्षक");//Include column titles into event description
define("_MODULE_QUESTIONS_USERHISTORY","उपयोगकर्ता का इतिहास");//User history
define("_MODULE_QUESTIONS_USEREVALUATIONS","उपयोगकर्ता मूल्यांकनों");//User evaluations
define("_MODULE_QUESTIONS_OMMITRECORDSWHOSELOGINDOESNOTEXIST","Ommit प्रश्न मौजूदा");//Ommit existing questions
define("_MODULE_QUESTIONS_ADDNEWRECORDSWHOSELOGINDOESNOTEXIST","बदलें प्रश्न मौजूदा");//Replace existing questions
define("_MODULE_QUESTIONS_THESUPPLIEDTITLEFORCOLUMNWITHEFRONTUSERLOGINSDOESNOTEXIST","eFront उपयोगकर्ता लॉगिन के साथ स्तंभ शीर्षक के लिए आपूर्ति की अपनी फ़ाइल की पहली पंक्ति में मौजूद नहीं है. कृपया सुनिश्चित करें कि दो खिताब बिल्कुल वही कर रहे हैं.");//The supplied title for column with eFront user logins does not exist in the first row of your file. Please make sure that the two titles are exactly the same.
define("_MODULE_QUESTIONS_THERECORDSHAVEBEENOMMITED","सवाल है कि आयात नहीं किया जा सकता है");//The questions that could not be imported are
define("_MODULE_QUESTIONS_THEFOLLOWINGUSERSHAVEBEENINSERT","निम्नलिखित उपयोगकर्ता (पासवर्ड &#39;पासवर्ड&#39; के साथ) सिस्टम में डाला गया है");//The following users (with password 'password') have been inserted into the system
define("_MODULE_QUESTIONS_THESUPPLIEDTITLEFORCOLUMNDATEDOESNOTEXIST","घटना तारीखों के साथ स्तंभ के लिए आपूर्ति की शीर्षक अपनी फ़ाइल की पहली पंक्ति में मौजूद नहीं है. कृपया सुनिश्चित करें कि दो खिताब बिल्कुल वही कर रहे हैं. <br> अन्यथा यदि ऐसी कोई स्तंभ मौजूद है, सम्मिलित कोई रिक्त मान");//The supplied title for column with event dates does not exist in the first row of your file. Please make sure that the two titles are exactly the same. <br>Otherwise, if no such column exists, insert a blank value
define("_MODULE_QUESTIONS_BOTTOMNOTE","आप आयात करने से पहले बैकअप अपने डेटाबेस के लिए सलाह दी जाती है, खासकर यदि आप सिस्टम में गैर मौजूदा प्रश्नों जोड़ने की योजना बना रहे हैं. हालांकि कोई खतरा मौजूद है वर्तमान प्रणाली डेटा के लिए जो भी, गलत पैरामीटर परिभाषा गलत डेटा सम्मिलन के लिए सीसा, जो फिर हाथ से हटाने की आवश्यकता होगी हो सकता है.");//You are adviced to backup your database before the import, especially if you are planning to add non-existing questions into the system. Though there exists no danger whatsoever to current system data, wrong parameter definitions might lead to wrong data insertions, that would then require manual removal.
define("_MODULE_QUESTIONS_PLEASECONFIGUREDATE","स्प्रेडशीट कॉन्फ़िगर करने के लिए दिखाने के रूप में दिनांक dd कृपया / मिमी / yyyy या dd-MM-yyyy");//Please configure the spreadsheet to show dates in the form dd/mm/yyyy or dd-mm-yyyy
define("_MODULE_QUESTIONS_QUESTIONTYPEISWRONG","प्रश्न प्रकार गलत है");//The question type is wrong
define("_MODULE_QUESTIONS_QUESTIONDIFFICULTYISWRONG","सवाल कठिनाई गलत है");//The question difficulty is wrong
define("_MODULE_QUESTIONS_QUESTIONLESSONUNITDOESNOTEXIST","सबक इकाई संयोजन मौजूद नहीं है");//The lesson-unit combination does not exist
define("_MODULE_QUESTIONS_WRONGQUESTIONTIME","गलत प्रश्न समय मूल्य");//Wrong question time value
define("_MODULE_QUESTIONS_NOOPTIONSDEFINEDFORMULTIPLECHOICE","कोई बहु विकल्प प्रश्न के लिए निर्धारित विकल्प");//No options defined for multiple choice question
define("_MODULE_QUESTIONS_NOCORRECTANSWERSDEFINEDFORMULTIPLECHOICE","कोई सही बहु विकल्प प्रश्न के लिए परिभाषित जवाब");//No correct answers defined for multiple choice question
define("_MODULE_QUESTIONS_NOQUESTIONTEXT","कोई प्रश्न के लिए परिभाषित किया गया पाठ");//No text defined for the question
define("_MODULE_QUESTIONS_NOCORRECTANSWERSDEFINEDFORTRUEFALSE","कोई सही सही / गलत प्रकार के लिए परिभाषित सवाल जवाब");//No correct answers defined for true/false type question
define("_MODULE_QUESTIONS_NOEMPTYSPACESDEFINEDFOREMPTYSPACEQUESTION","कोई खाली खाली जगह प्रश्न के लिए निर्धारित स्थान");//No empty spaces defined for empty space question
define("_MODULE_QUESTIONS_WRONGAMOUNTOFANSWERSINEMPTYSPACES","खाली स्थान के लिए उत्तर की गलत राशि सवाल");//Wrong amount of answers for empty spaces question
define("_MODULE_QUESTIONS_NOANSWERSDEFINEDFOREMPTYSPACES","कोई खाली स्थान के लिए परिभाषित जवाब सवाल");//No answers defined for empty spaces question
define("_MODULE_QUESTIONS_SAMPLEANSWERFILENOTFOUND","नमूना फाइल जवाब नहीं मिला");//Sample answer file not found
define("_MODULE_QUESTIONS_UPLOADAZIPFILEIFYOUWANTTOUPLOADQUESTIONFILES","आप एक ज़िप फ़ाइल अपलोड करने की आवश्यकता अगर तुम सवाल फ़ाइलें अपलोड करना चाहते हैं");//You need to upload a zip file if you want to upload question files
define("_MODULE_QUESTIONS_QUESTIONEXISTSALREADY","सवाल पहले से मौजूद है");//Question already exists
define("_MODULE_QUESTIONS_LINE","लाइन");//Line
define("_MODULE_QUESTIONS_WRONGINPUTFILETYPE","गलत इनपुट फ़ाइल प्रकार");//Wrong input file type
define("_MODULE_QUESTIONS_REPORTALREADYEXISTINGQUESTIONS","रिपोर्ट पहले ही प्रश्न मौजूदा");//Report already existing questions
define("_MODULE_QUESTIONS_QUESTIONREPLACEDPREVIOUSEXISTING","सवाल वही पाठ के साथ पिछले मौजूदा एक जगह");//Question replaced previous existing one with the same text
define("_MODULE_QUESTIONS_PLEASESELECTALESSON","एक सबक चुनें");//Please select a lesson
define("_MODULE_QUESTIONS_NOQUESTIONSWEREINSERTED","कोई सवाल नहीं डाला गया");//No questions were inserted
?>
