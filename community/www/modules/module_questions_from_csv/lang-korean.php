<?php
define("_MODULE_QUESTIONS_TESTQUESTIONSUPLOADING","업로드 시험 문제");//Test questions uploading
define("_MODULE_QUESTIONS_IMPORTUSERSHISTORYFROMXLSFILE","csv 파일에서 가져오기 시험 문제");//Import test questions from csv file
define("_MODULE_QUESTIONS_HISTORYRECORDS" , "test questions");
define("_MODULE_QUESTIONS_LOGINCOLUMNTITLE","eFront 사용자 로그인을 사용하여 열의 제목");//Title for column with eFront user logins
define("_MODULE_QUESTIONS_DATECOLUMNTITLE","이벤트 날짜와 칼럼 제목");//Title for column with event dates
define("_MODULE_QUESTIONS_HANDLINGFORNOTEXISTINGLOGINS","기존의 질문에 대한 옵션");//Options for existing questions
define("_MODULE_QUESTIONS_IMPORTINTO","가져오기로");//Import into
define("_MODULE_QUESTIONS_INCLUDECOLUMNTITLESINTOEVENTDESCRIPTION","이벤트 설명에 포함 칼럼 제목");//Include column titles into event description
define("_MODULE_QUESTIONS_USERHISTORY","사용자 역사");//User history
define("_MODULE_QUESTIONS_USEREVALUATIONS","사용자 평가");//User evaluations
define("_MODULE_QUESTIONS_OMMITRECORDSWHOSELOGINDOESNOTEXIST","Ommit 질문을 기존의");//Ommit existing questions
define("_MODULE_QUESTIONS_ADDNEWRECORDSWHOSELOGINDOESNOTEXIST","교체 질문을 기존의");//Replace existing questions
define("_MODULE_QUESTIONS_THESUPPLIEDTITLEFORCOLUMNWITHEFRONTUSERLOGINSDOESNOTEXIST","eFront 사용자 로그인을 사용하여 칼럼을위한 공급 제목은 파일의 첫 번째 행에 존재하지 않습니다. 이 제목은 똑같은되어 있는지 확인하시기 바랍니다.");//The supplied title for column with eFront user logins does not exist in the first row of your file. Please make sure that the two titles are exactly the same.
define("_MODULE_QUESTIONS_THERECORDSHAVEBEENOMMITED","가져올 수 없습니다 질문들입니다");//The questions that could not be imported are
define("_MODULE_QUESTIONS_THEFOLLOWINGUSERSHAVEBEENINSERT","다음과 같은 사용자 (암호 &#39;비밀 번호&#39;)를 시스템에 삽입되었습니다");//The following users (with password 'password') have been inserted into the system
define("_MODULE_QUESTIONS_THESUPPLIEDTITLEFORCOLUMNDATEDOESNOTEXIST","이벤트 날짜와 칼럼을위한 공급 제목은 파일의 첫 번째 행에 존재하지 않습니다. 이 제목은 똑같은되어 있는지 확인하시기 바랍니다. <br> 그렇지 않으면, 그런 열의가 존재하는 경우, 삽입 빈 값");//The supplied title for column with event dates does not exist in the first row of your file. Please make sure that the two titles are exactly the same. <br>Otherwise, if no such column exists, insert a blank value
define("_MODULE_QUESTIONS_BOTTOMNOTE","당신은 당신이 시스템에 존재하지 않는 질문을 추가할 계획 특히, 가져오기 전에 데이터베이스를 백업하는 것이 좋습니다. 현재 시스템 데이터에 대한 어떠한 위험가 존재지만, 잘못된 매개 변수 정의는 다음 수동으로 제거를해야한다고, 잘못된 데이터 삽입을 초래할 수도 있습니다.");//You are adviced to backup your database before the import, especially if you are planning to add non-existing questions into the system. Though there exists no danger whatsoever to current system data, wrong parameter definitions might lead to wrong data insertions, that would then require manual removal.
define("_MODULE_QUESTIONS_PLEASECONFIGUREDATE","양식에 날짜가 표시 DD 형식으로 스프레드 시트를 구성하십시오 / mm / 또는 DD 형식 - mm - yyyy로 yyyy로");//Please configure the spreadsheet to show dates in the form dd/mm/yyyy or dd-mm-yyyy
define("_MODULE_QUESTIONS_QUESTIONTYPEISWRONG","질문 유형이 잘못인가");//The question type is wrong
define("_MODULE_QUESTIONS_QUESTIONDIFFICULTYISWRONG","질문 어려움이 잘못인가");//The question difficulty is wrong
define("_MODULE_QUESTIONS_QUESTIONLESSONUNITDOESNOTEXIST","수업 단위 조합이 존재하지 않습니다");//The lesson-unit combination does not exist
define("_MODULE_QUESTIONS_WRONGQUESTIONTIME","질문이 잘못 시간 값");//Wrong question time value
define("_MODULE_QUESTIONS_NOOPTIONSDEFINEDFORMULTIPLECHOICE","객관식 질문에 대해 정의된 옵션 없음");//No options defined for multiple choice question
define("_MODULE_QUESTIONS_NOCORRECTANSWERSDEFINEDFORMULTIPLECHOICE","객관식 질문에 대해 정의된 없음 정답");//No correct answers defined for multiple choice question
define("_MODULE_QUESTIONS_NOQUESTIONTEXT","라는 질문에 대해 정의된 텍스트 없음");//No text defined for the question
define("_MODULE_QUESTIONS_NOCORRECTANSWERSDEFINEDFORTRUEFALSE","참 / 거짓 유형의 질문에 대해 정의된 없음 정답");//No correct answers defined for true/false type question
define("_MODULE_QUESTIONS_NOEMPTYSPACESDEFINEDFOREMPTYSPACEQUESTION","빈 공간 질문에 대해 정의된 없음 빈 공간");//No empty spaces defined for empty space question
define("_MODULE_QUESTIONS_WRONGAMOUNTOFANSWERSINEMPTYSPACES","빈 공간에 대한 해답의 잘못된 금액 질문");//Wrong amount of answers for empty spaces question
define("_MODULE_QUESTIONS_NOANSWERSDEFINEDFOREMPTYSPACES","빈 공간에 대해 정의된 없음 질문 답변");//No answers defined for empty spaces question
define("_MODULE_QUESTIONS_SAMPLEANSWERFILENOTFOUND","샘플 파일을 찾을 대답");//Sample answer file not found
define("_MODULE_QUESTIONS_UPLOADAZIPFILEIFYOUWANTTOUPLOADQUESTIONFILES","당신은 질문이 파일을 업로드하려는 경우 당신은 zip 파일을 업로드해야");//You need to upload a zip file if you want to upload question files
define("_MODULE_QUESTIONS_QUESTIONEXISTSALREADY","질문이 이미 존재합니다");//Question already exists
define("_MODULE_QUESTIONS_LINE","라인");//Line
define("_MODULE_QUESTIONS_WRONGINPUTFILETYPE","잘못된 입력 파일 형식");//Wrong input file type
define("_MODULE_QUESTIONS_REPORTALREADYEXISTINGQUESTIONS","보고서는 이미 질문을 기존의");//Report already existing questions
define("_MODULE_QUESTIONS_QUESTIONREPLACEDPREVIOUSEXISTING","질문이 동일한 텍스트로 이전 기존 교체");//Question replaced previous existing one with the same text
define("_MODULE_QUESTIONS_PLEASESELECTALESSON","강의를 선택하십시오");//Please select a lesson
define("_MODULE_QUESTIONS_NOQUESTIONSWEREINSERTED","가 질문을 삽입했다");//No questions were inserted
?>
