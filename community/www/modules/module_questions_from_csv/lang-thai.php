<?php
define("_MODULE_QUESTIONS_TESTQUESTIONSUPLOADING","คำถามทดสอบอัพโหลด");//Test questions uploading
define("_MODULE_QUESTIONS_IMPORTUSERSHISTORYFROMXLSFILE","คำถามทดสอบจากไฟล์ csv");//Import test questions from csv file
define("_MODULE_QUESTIONS_HISTORYRECORDS" , "test questions");
define("_MODULE_QUESTIONS_LOGINCOLUMNTITLE","ชื่อสำหรับคอลัมน์ที่มีการเข้าสู่ระบบผู้ใช้ eFront");//Title for column with eFront user logins
define("_MODULE_QUESTIONS_DATECOLUMNTITLE","ชื่อสำหรับคอลัมน์ที่มีวันที่เหตุการณ์");//Title for column with event dates
define("_MODULE_QUESTIONS_HANDLINGFORNOTEXISTINGLOGINS","ตัวเลือกสำหรับคำถามที่มีอยู่");//Options for existing questions
define("_MODULE_QUESTIONS_IMPORTINTO","นำเข้าสู่");//Import into
define("_MODULE_QUESTIONS_INCLUDECOLUMNTITLESINTOEVENTDESCRIPTION","รวมชื่อคอลัมน์เป็นคำอธิบายเหตุการณ์");//Include column titles into event description
define("_MODULE_QUESTIONS_USERHISTORY","ประวัติของผู้ใช้");//User history
define("_MODULE_QUESTIONS_USEREVALUATIONS","การประเมินผู้ใช้");//User evaluations
define("_MODULE_QUESTIONS_OMMITRECORDSWHOSELOGINDOESNOTEXIST","Ommit คำถามที่มีอยู่");//Ommit existing questions
define("_MODULE_QUESTIONS_ADDNEWRECORDSWHOSELOGINDOESNOTEXIST","คำถามมีอยู่แทนที่");//Replace existing questions
define("_MODULE_QUESTIONS_THESUPPLIEDTITLEFORCOLUMNWITHEFRONTUSERLOGINSDOESNOTEXIST","ชื่อเรื่องมาสำหรับคอลัมน์ที่มีการเข้าสู่ระบบผู้ใช้ eFront ไม่อยู่ในแถวแรกของแฟ้มของคุณ กรุณาตรวจสอบให้แน่ใจว่าทั้งสองชื่อจะว่ากัน");//The supplied title for column with eFront user logins does not exist in the first row of your file. Please make sure that the two titles are exactly the same.
define("_MODULE_QUESTIONS_THERECORDSHAVEBEENOMMITED","คำถามที่ไม่สามารถนำเข้าได้");//The questions that could not be imported are
define("_MODULE_QUESTIONS_THEFOLLOWINGUSERSHAVEBEENINSERT","ผู้ใช้ต่อไปนี้ (กับ&#39;รหัสผ่าน&#39;รหัสผ่าน) ได้รับการแทรกลงในระบบ");//The following users (with password 'password') have been inserted into the system
define("_MODULE_QUESTIONS_THESUPPLIEDTITLEFORCOLUMNDATEDOESNOTEXIST","ชื่อเรื่องมาสำหรับคอลัมน์ที่มีวันที่เหตุการณ์ไม่อยู่ในแถวแรกของแฟ้มของคุณ กรุณาตรวจสอบให้แน่ใจว่าทั้งสองชื่อจะว่ากัน <br> มิฉะนั้นถ้าไม่มีคอลัมน์ดังกล่าวมีอยู่แล้วให้ใส่ค่าว่าง");//The supplied title for column with event dates does not exist in the first row of your file. Please make sure that the two titles are exactly the same. <br>Otherwise, if no such column exists, insert a blank value
define("_MODULE_QUESTIONS_BOTTOMNOTE","ขอแนะนำให้สำรองฐานข้อมูลของคุณก่อนที่จะนำเข้าโดยเฉพาะถ้าคุณวางแผนที่จะเพิ่มคำถามที่มีอยู่แล้วไม่เข้าสู่ระบบ แม้ว่าจะไม่มีอันตรายใด ๆ มีอยู่ไปยังข้อมูลของระบบปัจจุบันคำจำกัดความพารามิเตอร์ที่ไม่ถูกต้องอาจนำไปสู่การแทรกข้อมูลผิดที่แล้วจะต้องมีการกำจัดคู่มือ");//You are adviced to backup your database before the import, especially if you are planning to add non-existing questions into the system. Though there exists no danger whatsoever to current system data, wrong parameter definitions might lead to wrong data insertions, that would then require manual removal.
define("_MODULE_QUESTIONS_PLEASECONFIGUREDATE","กรุณาตั้งค่ากระดาษคำนวณเพื่อแสดงวันที่ในรูปแบบ dd / mm / yyyy หรือ DD - MM - yyyy");//Please configure the spreadsheet to show dates in the form dd/mm/yyyy or dd-mm-yyyy
define("_MODULE_QUESTIONS_QUESTIONTYPEISWRONG","ประเภทของคำถามที่ไม่ถูกต้อง");//The question type is wrong
define("_MODULE_QUESTIONS_QUESTIONDIFFICULTYISWRONG","ปัญหาในคำถามที่ไม่ถูกต้อง");//The question difficulty is wrong
define("_MODULE_QUESTIONS_QUESTIONLESSONUNITDOESNOTEXIST","รวมบทเรียนหน่วยไม่มี");//The lesson-unit combination does not exist
define("_MODULE_QUESTIONS_WRONGQUESTIONTIME","คำถามค่าผิดเวลา");//Wrong question time value
define("_MODULE_QUESTIONS_NOOPTIONSDEFINEDFORMULTIPLECHOICE","ไม่มีตัวเลือกที่กำหนดไว้สำหรับคำถามแบบเลือกตอบ");//No options defined for multiple choice question
define("_MODULE_QUESTIONS_NOCORRECTANSWERSDEFINEDFORMULTIPLECHOICE","ไม่มีคำตอบที่ถูกต้องที่กำหนดไว้สำหรับคำถามแบบเลือกตอบ");//No correct answers defined for multiple choice question
define("_MODULE_QUESTIONS_NOQUESTIONTEXT","ข้อความที่กำหนดไว้สำหรับคำถามไม่มี");//No text defined for the question
define("_MODULE_QUESTIONS_NOCORRECTANSWERSDEFINEDFORTRUEFALSE","ไม่มีคำตอบที่ถูกต้องที่กำหนดไว้สำหรับความจริงคำถามพิมพ์ / เท็จ");//No correct answers defined for true/false type question
define("_MODULE_QUESTIONS_NOEMPTYSPACESDEFINEDFOREMPTYSPACEQUESTION","ไม่มีพื้นที่ว่างที่กำหนดไว้สำหรับคำถามที่ว่าง");//No empty spaces defined for empty space question
define("_MODULE_QUESTIONS_WRONGAMOUNTOFANSWERSINEMPTYSPACES","จำนวนเงินไม่ถูกต้องของคำตอบสำหรับคำถามของพื้นที่ว่าง");//Wrong amount of answers for empty spaces question
define("_MODULE_QUESTIONS_NOANSWERSDEFINEDFOREMPTYSPACES","ไม่มีคำตอบใดที่กำหนดไว้สำหรับพื้นที่ว่างคำถาม");//No answers defined for empty spaces question
define("_MODULE_QUESTIONS_SAMPLEANSWERFILENOTFOUND","ตัวอย่างคำตอบไม่พบไฟล์");//Sample answer file not found
define("_MODULE_QUESTIONS_UPLOADAZIPFILEIFYOUWANTTOUPLOADQUESTIONFILES","คุณจำเป็นต้องอัปโหลดไฟล์ซิปหากคุณต้องการอัปโหลดไฟล์คำถาม");//You need to upload a zip file if you want to upload question files
define("_MODULE_QUESTIONS_QUESTIONEXISTSALREADY","คำถามมีอยู่แล้ว");//Question already exists
define("_MODULE_QUESTIONS_LINE","สาย");//Line
define("_MODULE_QUESTIONS_WRONGINPUTFILETYPE","ใส่ไฟล์พิมพ์ผิด");//Wrong input file type
define("_MODULE_QUESTIONS_REPORTALREADYEXISTINGQUESTIONS","รายงานที่มีอยู่แล้วคำถาม");//Report already existing questions
define("_MODULE_QUESTIONS_QUESTIONREPLACEDPREVIOUSEXISTING","คำถามหนึ่งที่มีอยู่ก่อนหน้าแทนที่ด้วยข้อความเดียวกัน");//Question replaced previous existing one with the same text
define("_MODULE_QUESTIONS_PLEASESELECTALESSON","โปรดเลือกบทเรียน");//Please select a lesson
define("_MODULE_QUESTIONS_NOQUESTIONSWEREINSERTED","คำถามที่ไม่มีการแทรก");//No questions were inserted
?>
