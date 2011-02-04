<?php

define('_TEXTME', 'TextMe');

/* admin_lessons.tpl && admin_lessons.tpl */
define('_TEXTME_LESSONSACCOUNTSTAB', 'Λογαριασμοί μαθημάτων');
define('_TEXTME_SENDERID', 'Ταυτότητα αποστολέα');
define('_TEXTME_CREDITS', 'Μονάδες');
define('_TEXTME_CREDITSREMAINING', 'Υπόλοιπες μονάδες');
define('_TEXTME_CREDITSSPENT', 'Καταναλωμένες μονάδες');
define('_TEXTME_UNLIMITEDCREDITS', 'απεριόριστες');
define('_TEXTME_MODULEDISABLED', 'module απενεργοποιημένο');
define('_TEXTME_EDITLESSONACCOUNT', 'Επεξεργασία λογαριασμού');
define('_TEXTME_NOLESSONSACCOUNTS', 'Δεν υπάρχουν μαθήματα');
define('_TEXTME_BACKTOLESSONSACCOUNTS', 'Πίσω στους λογαριασμούς μαθημάτων');
define('_TEXTME_LEAVEBLANKFORUNLIMITEDCREDITS', 'Αφήστε το κενό για απεριόριστες μονάδες');
define('_TEXTME_CREDITSFIELDBLANKORNUMBER', 'Το πεδίο "Μονάδες" θα πρέπει είτε να περιέχει μόνο νούμερα ή να είναι κενό');
define('_TEXTME_LESSONACCOUNUPDATED', 'Λογαριασμοί μαθημάτων');

/* admin_providers.tpl && admin_providers.tpl */
define('_TEXTME_SMSGATEWAYSTAB', 'Sms gateways');
define('_TEXTME_SMSGATEWAYURL', 'Gateway');
define('_TEXTME_PARAMETERS', 'Παράμετροι');
define('_TEXTME_ADDSMSGATEWAY', 'Προσθήκη sms gateway');
define('_TEXTME_EDITSMSGATEWAY', 'Επεξεργασία sms gateway');
define('_TEXTME_DELETESMSGATEWAY', 'Διαγραφή sms gateway');
define('_TEXTME_TESTSMSGATEWAY', 'Δοκιμή sms gateway');
define('_TEXTME_NOSMSGATEWAYS', 'Δεν υπάρχουν sms gateways');
define('_TEXTME_BACKTOSMSGATEWAYS', 'Πίσω στα sms gateways');
define('_TEXTME_PARAMETERSEXPLANATION', 'Παράδειγμα:<br/>username:efront<br/>password:efront<br/>mobile:306900123456789');
define('_TEXTME_SMSGATEWAYADDED', 'Sms gateway προστέθηκε');
define('_TEXTME_SMSGATEWAYUPDATED', 'Sms gateway ενημερώθηκε');
define('_TEXTME_SMSGATEWAYDELETED', 'Sms gateway διαγράφηκε');
define('_TEXTME_TESTMESSAGESENT', 'Ένα δοκιμαστικό sms στάλθηκε στο %s');
define('_TEXTME_NOSMSGATEWAYS', 'No sms gateways');
define('_TEXTME_NOMOBILEPARAMETERDEFINED', 'Το sms δεν εστάλλει. Πρέπει να ορίσετε την παράμετρο "mobile" γι\' αυτό το sms gateway.');
define('_TEXTME_NOTIFICATIONSONLYLOCALLY', 'Δεν έχετε ενεργοποιήσει κάποιο sms gateway ακόμα. Το TextMe δεν μπορεί να αποστείλει ενημερώσεις μέσω sms.');
define('_TEXTME_THEFOLLOWINGPARAMETERSAREMANDATORYFORGATEWAY', 'Οι ακόλουθοι παράμετροι είναι υποχρεωτικές για το sms gateway: %s');

/* cpanel_widget.tpl */
define('_TEXTME_SMSNOTIFICATIONSENABLEDANDROUTED', 'Οι ειδοποιήσεις sms προωθούνται μέσω του gateway <strong style="vertical-align: top;">%s\'s</strong>. (Λογαριασμός: %s)');
define('_TEXTME_SMSNOTIFICATIONNOTSENABLEDANDROUTED', 'Δένς έχετε ενεργοποιήσει κάποιο sms gateway.<br/>Οι ειδοποιήσεις sms αποστέλλονται μόνο τοπικά.<br/>Πατήστε <a href="%s" >εδώ</a> to για να ενεργοποιήσετε κάποιο gateway.');
define('_TEXTME_LESSONSTHATHAVEENABLEDTEXTME', '%s από τα %s μαθήματα <strong style="vertical-align: top;">έχουν ενεργοποιήσει</strong> το TextMe.');
define('_TEXTME_USERSTHATHAVEENABLEDTEXTME', '%s χρήστες έχουν <strong style="vertical-align: top;">εγγραφεί</strong> στις υπηρεσίες του TextMe.');

/* GENERAL */
define('_TEXTME_ANERROROCCURED', 'Συνέβει κάποιο σφάλμα');

/* inbox.tpl && inbox.php */

define('_TEXTME_INBOX', 'Εισερχόμενα μηνύματα');
define('_TEXTME_COMPOSEMESSAGE', 'Σύνθεση μηνύματος');
define('_TEXTME_MESSAGE', 'Μήνυμα');
define('_TEXTME_DELETEMESSAGE', 'Διαγραφή μηνύματος');
define('_TEXTME_UNDOSEND', 'Αναίρεση αποστολής');
define('_TEXTME_ACCOUNTBALANCE', 'Έχετε %s μονάδες ακόμα.');
define('_TEXTME_RECIPIENTS', 'Παραλήπτες');
define('_TEXTME_EVERYONE', '΄Όλοι');
define('_TEXTME_PROFESSORSONLY', 'Καθηγητές μόνο');
define('_TEXTME_STUDENTSONLY', 'Μαθητές μόνο');
define('_TEXTME_SELECT', 'Επιλέξτε...');
define('_TEXTME_NOW', 'Άμεσα');
define('_TEXTME_SELECTDATE', 'Επιλογή ημερομηνίας...');
define('_TEXTME_USERS', 'Χρήστες');
define('_TEXTME_GROUPS', 'Ομάδες');
define('_TEXTME_BACKTOINBOX', 'Πίσω στα εισερχόμενα μηνύματα');
define('_TEXTME_RECIPIENT', 'Παραλήπτης');
define('_TEXTME_VIEWMESSAGE', 'Εμφάνιση μηνύματος');
define('_TEXTME_SMSSTATUS', 'Κατάσταση sms');
define('_TEXTME_LOCALSTATUS', 'Κατάσταση τοπικά');
define('_TEXTME_NOTROUTED', 'Not routed');
define('_TEXTME_MESSAGEDELIVERED', 'Εστάλλει');
define('_TEXTME_MESSAGEPENDING', 'Εκκρεμεί');
define('_TEXTME_MESSAGEFAILED', 'Σφάλμα αποστολής');
define('_TEXTME_MESSAGEUNREAD', 'Το μήνυμα διαβάστηκε');
define('_TEXTME_MESSAGEREAD', 'Το μήνυμα δεν έχει διαβαστεί');
define('_TEXTME_MESSAGEDELETED', 'Το μήνυμα διαγράφτηκε');
define('_TEXTME_MESSAGESENT', 'Το μήνυμα απεστάλλει');
define('_TEXTME_MESSAGESENTPROBLEM', 'Σφάλμα! Το μήνυμα εστάλλει μόνο τοπικά για κάποιους από τους παραλήπτες.');
define('_TEXTME_NOMESSAGES', 'Δεν υπάρχουν μηνύματα');
define('_TEXTME_NORECIPIENTS', 'Δεν υπάρχουν παραλήπτες');
define('_TEXTME_INVALIDDATE', 'Μη έγκυρη ημερομηνία ή ημερομηνία στο παρελθόν');

/* professor_users.tpl && professor_users.php */

define('_TEXTME_USERSANDGROUPS', 'Χρήστες και ομάδες');
define('_TEXTME_USER', 'Χρήστης');
define('_TEXTME_GROUP', 'Ομάδα');
define('_TEXTME_ADDGROUP', 'Προσθήκη ομάδας');
define('_TEXTME_EDITGROUP', 'Επεξεργασία ομάδας');
define('_TEXTME_DELETEGROUP', 'Διαγραφή ομάδας');
define('_TEXTME_BACKTOUSERSANDGROUPS', 'Πίσω στους χρήστες και ομάδες');
define('_TEXTME_ALERTSSTATUS', 'Κατάσταση TextMe');
define('_TEXTME_ROLE', 'Ρόλος');
define('_TEXTME_HASENABLEDALERTS', 'Ο χρήστης έχει ενεργοποιήσεις το TextMe');
define('_TEXTME_HASNOTENABLEDALERTS', 'Ο χρήστης δεν έχει ενεργοποιήσεις το TextMe');
define('_TEXTME_NOUSERS', 'Δεν υπάρχουν χρήστες');
define('_TEXTME_NOGROUPS', 'Δεν υπάρχουν ομάδες');
define('_TEXTME_NUMBEROFUSERS', 'Αριθμός χρηστών');
define('_TEXTME_INGROUPUSERS', 'Χρήστες ενός ομάδας');
define('_TEXTME_OUTOFGROUPUSERS', 'Χρήστες εκτός ομάδας');
define('_TEXTME_GROUPDELETED', 'Η ομάδα διαγράφτηκε');
define('_TEXTME_GROUPUPDATED', 'Η ομάδα ενημερώθηκε');
define('_TEXTME_GROUPADDED', 'Η ομάδα προστέθηκε');

/* professor_configuration.tpl && professor_configuration.php */
define('_TEXTME_CONFIGURATION', 'Παραμετροποίηση');
define('_TEXTME_SENDERIDUPDATED', 'Η ταυτότητα αποστολέα ενημερώθηκε');
define('_TEXTME_SENDERIDINVALIDCHARACTERS', 'Το πεδίο "Ταυτότητα αποστολέα" μπορεί να περιέχει μονο λατινικούς χαρακτήρες ή/και νούμερα.');

/* subscribe.tpl && subscribe.php */

define('_TEXTME_SUBSCRIBE', 'Εγγραφή');
define('_TEXTME_UNSUBSCRIBE', 'Απεγγραφή');
define('_TEXTME_CHANGEMOBILE', 'Αλλαγή αριθμού');
define('_TEXTME_REQUESTVERIFICATIONCODE', 'Αίτηση κωδικού επιβεβαίωσης');
define('_TEXTME_MOBILE', 'Κινητό');
define('_TEXTME_BACK', 'Πίσω');
define('_TEXTME_NOLESSONS', 'Δεν υπάρχουν μαθήματα');
define('_TEXTME_VERIFICATIONCODEWASSENT', 'Ο κωδικός επιβεβαίωσης εστάλλει στο %s');
define('_TEXTME_VERIFICATIONCODEWASNOTSENT', 'Ο κωδικός επιβεβαίωσης δεν εστάλλει. Παρακαλώ δοκιμάστε αργότερα.');
define('_TEXTME_YOUCANSUBSCRIBETOTHEFOLLOWINGLESSONS', 'Μπορείτε να εγγραφείτε για ειδοποιήσεις μέσω sms για τα παρακάτω μαθήματα');
define('_TEXTME_VERIFICATIONCODE', 'Κωδικός επιβεβαίωσης');
define('_TEXTME_INVALIDVERIFICATIONCODE', 'Ο κωδικός επιβεβαίωσης δεν είναι έγκυρος');
define('_TEXTME_WHATTEXTMEIS', 'Το TextMe είναι ένα σύστημα ειδοποιήσεων μέσω sms.<br/>Σας κρατάει ενήμερους για σημαντικά θέματα σχετικά με τα μαθήματα που παρακολουθείτε.<br/>');
define('_TEXTME_THEFOLLOWINGLESSONSPROVIDEALERTS', 'The following lessons provide sms alerts to their users.');
define('_TEXTME_YOUHAVENOTYETSUBSCRIBED', 'You have not yet subscribed to Textme\'s sms alerts.<br/>Please provide us with your mobile phone number.');
define('_TEXTME_MOBILEDESCRIPTION', 'Θα πρέπει εισάγετε τον αριθμό του κινητού σας βάζοντας ως πρόθεμα τον διεθνή κωδικό κλήσης της χώρας σας.<br/>Παράδειγμα. Αν το κινητό σας είναι 690123456xx τότε για Ελλάδα θα πρέπει να εισάγετε 30690123456xx.');
define('_TEXTME_YOURMOBILEIS', 'Ο αριθμός του κινητού σας είναι <strong>%s</strong>');
define('_TEXTME_VERIFICATIONCODEDESCRIPTION', 'Σε λίγα λεπτα θα λάβετε έναν κωδικό επιβεβαίωσης στο κινητό σας.<br/>Παρακαλώ εισάγετε τον κωδικό αυτό στην παρακάτω φόρμα.');
define('_TEXTME_AREYOUSUREYOUWANTTOUNSUBSCRIBE', 'Θέλετε σίγουρα να απενεργοποιήσετε τις ειδοποιήσεις μέσω sms?');
define('_TEXTME_AREYOUSUREYOUWANTTOCHANGEMOBILE', 'Θέλετε σίγουρα να αλλάξετε τον αριθμό κινητού σας?');
define('_TEXTME_RESENDCODE', 'Θέλετε σίγουρα να γίνει επαναποστολή του κωδικού επιβεβαίωσης;');

define('_TEXTME_GOTOPAGE', 'Μετάβαση στο TextMe');


/* lesson_widget.tpl && lesson_widget.php */
define('_TEXTME_WARNING', 'Προσοχή');
define('_TEXTME_YOUCANNOTRECEIVEALERTS', 'Δεν μπορείτε να λάβετε ειδοποιήσεις μέσω sms.');
define('_TEXTME_YOUHAVENOTYETSUBSCRIBEDTOTEXTME', 'Δεν έχετε εισάγει τον αριθμό κινητού σας.');
define('_TEXTME_YOUHAVENOTYETVERIFIEDYOURMOBILE', 'Πρέπει να εισάγετε τον κωδικό επιβεβαίωσης.');
define('_TEXTME_YOUDONTHAVEANYMESSAGES', 'Δεν υπάρχουν μη ανεγνωσμένα μηνύματα.');
define('_TEXTME_YOUHAVEMESSAGES', 'Έχετε <a href="%s%s">%s μή ανεγνωσμένα</a> μηνύματα.');

?>
