<?php
/* This file defines the constants for the module in Greek */
define("_MODULE_QUESTIONS_TESTQUESTIONSUPLOADING","Ανέβασμα ερωτήσεων αξιολογήσεων");
define("_MODULE_QUESTIONS_IMPORTUSERSHISTORYFROMXLSFILE", "Εισαγωγή ερωτήσεων από csv αρχείο");
define("_MODULE_QUESTIONS_HISTORYRECORDS" , "ερωτήσεις αξιολογήσεων");
define("_MODULE_QUESTIONS_LOGINCOLUMNTITLE","Τίτλος στήλης με eFront αναγνωριστικά");
define("_MODULE_QUESTIONS_DATECOLUMNTITLE","Τίτλος στήλης με ημερομηνίες γεγονότων");
define("_MODULE_QUESTIONS_HANDLINGFORNOTEXISTINGLOGINS","Επιλογές για ήδη υπάρχουσες ερωτήσεις");
define("_MODULE_QUESTIONS_IMPORTINTO","Εισαγωγή σε");
define("_MODULE_QUESTIONS_INCLUDECOLUMNTITLESINTOEVENTDESCRIPTION","Χρήση τίτλων στηλών στις περιγραφές των γεγονότων");
define("_MODULE_QUESTIONS_USERHISTORY","Ιστορικό χρηστών");
define("_MODULE_QUESTIONS_USEREVALUATIONS","Αξιολογήσεις χρηστών");
define("_MODULE_QUESTIONS_OMMITRECORDSWHOSELOGINDOESNOTEXIST","Παράκαμψη υπάρχουσων ερωτήσεων");
define("_MODULE_QUESTIONS_ADDNEWRECORDSWHOSELOGINDOESNOTEXIST","Αντικατάσταση υπάρχουσων ερωτήσεων");
define("_MODULE_QUESTIONS_THESUPPLIEDTITLEFORCOLUMNWITHEFRONTUSERLOGINSDOESNOTEXIST", "Ο παρεχόμενος τίτλος στήλης για eFront αναγνωριστικά δεν υπάρχει στην πρώτη γραμμή του εισαγόμενου αρχείου. Παρακαλώ σιγουρευτείτε ότι οι δύο τίτλοι είναι ίδιοι.");
define("_MODULE_QUESTIONS_THERECORDSHAVEBEENOMMITED","Οι ερωτήσεις που δεν ήταν δυνατόν να εισαχθούν είναι");
define("_MODULE_QUESTIONS_THEFOLLOWINGUSERSHAVEBEENINSERT","Οι παρακάτω χρήστες (με κωδικό 'password') εισήχθησαν στο σύστημα");
define("_MODULE_QUESTIONS_THESUPPLIEDTITLEFORCOLUMNDATEDOESNOTEXIST","Ο παρεχόμενος τίτλος για τη στήλη με τις ημερομηνίες γεγονότων δεν υπάρχει στην πρώτη γραμμή του εισαγόμενου αρχείου. Παρακαλώ σιγουρευτείτε ότι οι δύο τίτλοι είναι ίδιοι. <br>Εναλλακτικά, αν δεν υπάρχει σχετική στή λη στο αρχείο, εισάγετε κενό στο σχετικό πεδίο της φόρμας");
define("_MODULE_QUESTIONS_BOTTOMNOTE","Σας συμβουλεύουμε να κρατήσετε backup της βάσης δεδομένων σας πριν την εισαγωγή, ειδικά αν σκοπεύετε να προσθέσετε μη-υπάρχουσες ερωτήσεις στο σύστημα. Παρότι δεν υπάρχει κανένας απολύτως κίνδυνος για τα παρόντα δεδομένα του συστήματος, λάθος ρύθμιση των παραμέτρων μπορεί να οδηγήσει σε λανθασμένη εισαγωγή ερωτήσεων που απαιτούν χειροκίνητη διαγραφή.");
define("_MODULE_QUESTIONS_PLEASECONFIGUREDATE", "Παρακαλώ ρυθμίστε το εισαγόμενο φύλλο εργασίας να παρουσιάζει τις ημερομηνίες στη μορφή ηη/μμ/χχχχ or ηη-μμ-χχχχ");

define("_MODULE_QUESTIONS_QUESTIONTYPEISWRONG", "Ο τύπος ερώτησης είναι λάθος");
define("_MODULE_QUESTIONS_QUESTIONDIFFICULTYISWRONG","Η βαθμός δυσκολίας της ερώτησης είναι λάθος");
define("_MODULE_QUESTIONS_QUESTIONLESSONUNITDOESNOTEXIST","Ο κωδικός προγράμματος ή ενότητας είναι λάθος");
define("_MODULE_QUESTIONS_WRONGQUESTIONTIME","Ο αναμενόμενος χρόνος απάντησης είναι λάθος");
define("_MODULE_QUESTIONS_NOOPTIONSDEFINEDFORMULTIPLECHOICE","Δεν έχουν οριστεί επιλογές για την ερώτηση πολλαπλής επιλογής");
define("_MODULE_QUESTIONS_NOCORRECTANSWERSDEFINEDFORMULTIPLECHOICE", "Δεν έχουν οριστεί σωστές απαντήσης για την ερώτηση πολλαπλής επιλογής");
define("_MODULE_QUESTIONS_NOQUESTIONTEXT","Δεν υπάρχει κείμενο εκφώνησης για την ερώτηση");
define("_MODULE_QUESTIONS_NOCORRECTANSWERSDEFINEDFORTRUEFALSE","Δεν έχει οριστεί σωστά η απάντηση για την ερώτηση τύπου Σωστό/Λάθος");
define("_MODULE_QUESTIONS_NOEMPTYSPACESDEFINEDFOREMPTYSPACEQUESTION","Δεν έχουν οριστεί κενά για ερώτηση συμπλήρωσης κενών");
define("_MODULE_QUESTIONS_WRONGAMOUNTOFANSWERSINEMPTYSPACES","Λανθασμένος αριθμός απαντήσεων για ερώτηση συμπλήρωσης κενού");
define("_MODULE_QUESTIONS_NOANSWERSDEFINEDFOREMPTYSPACES","Δεν έχουν οριστεί απαντήσεις για την ερώτηση συμπλήρωσης κενού");
define("_MODULE_QUESTIONS_SAMPLEANSWERFILENOTFOUND","Δε βρέθηκε το αρχείο με το πρότυπο απάντησης");
define("_MODULE_QUESTIONS_UPLOADAZIPFILEIFYOUWANTTOUPLOADQUESTIONFILES","Πρέπει να ανεβάσετε αρχείο zip αν θέλετε να ανεβάσετε αρχεία για τις ερωτήσεις σας");
define("_MODULE_QUESTIONS_QUESTIONEXISTSALREADY","Η ερώτηση υπάρχει ήδη");
define("_MODULE_QUESTIONS_LINE","Γραμμή");
define("_MODULE_QUESTIONS_WRONGINPUTFILETYPE","Λανθασμένος τύπος αρχείου εισαγωγής ερωτήσεων");
define("_MODULE_QUESTIONS_REPORTALREADYEXISTINGQUESTIONS","Ανάφερε ήδη υπάρχουσες ερωτήσεις");
define("_MODULE_QUESTIONS_QUESTIONREPLACEDPREVIOUSEXISTING","Η ερώτηση αντικατέστησε προηγούμενη με ίδια εκφώνηση");
define("_MODULE_QUESTIONS_PLEASESELECTALESSON","Παρακαλώ επιλέξτε ένα μάθημα");

?>