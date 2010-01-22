<?php
    $path = "../libraries/";
    define("NO_OUTPUT_BUFFERING", true);
    require_once($path."configuration.php");
?>
<html>
<head>
 
<script type="text/javascript" src="charts/js/swfobject.js"></script>
<script type="text/javascript">
<?php
    $id = $_GET['id'];
    switch ($id) {
        case 2:
            ?>
            swfobject.embedSWF("charts/open-flash-chart.swf", "my_chart", 
            "700", "400", "9.0.0", "expressInstall.swf", {"data-file": "chart_data.php?id=2%26test_id=<?php echo $_GET['test_id'];?>"} );
            <?php
            break;
        case 3:
            ?>
            swfobject.embedSWF("charts/open-flash-chart.swf", "my_chart", 
            "700", "400", "9.0.0", "expressInstall.swf", {"data-file": "chart_data.php?id=3%26test_id=<?php echo $_GET['test_id'];?>"} );
            <?php
            break;
        case 4:
            ?>
            swfobject.embedSWF("charts/open-flash-chart.swf", "my_chart", 
            "700", "400", "9.0.0", "expressInstall.swf", {"data-file": "chart_data.php?id=4"} );
            <?php
            break;
        case 5:
            ?>
            swfobject.embedSWF("charts/open-flash-chart.swf", "my_chart", 
            "700", "400", "9.0.0", "expressInstall.swf", {"data-file": "chart_data.php?id=5%26lesson_id=<?php echo $_GET['lesson_id'];?>%26from=<?php echo $_GET['from'];?>%26to=<?php echo $_GET['to'];?>"} );
            <?php
            break;
        case 6:
            ?>
            swfobject.embedSWF("charts/open-flash-chart.swf", "my_chart", 
            "700", "400", "9.0.0", "expressInstall.swf", {"data-file": "chart_data.php?id=6%26logins=<?php echo $_GET['logins'];?>%26seconds=<?php echo $_GET['seconds'];?>"} );
            <?php
            break;
        case 8:
            ?>
            swfobject.embedSWF("charts/open-flash-chart.swf", "my_chart", 
            "700", "400", "9.0.0", "expressInstall.swf", {"data-file": "chart_data.php?id=8%26lesson_id=<?php echo $_GET['lesson_id'];?>%26from=<?php echo $_GET['from'];?>%26to=<?php echo $_GET['to'];?>"} );
            <?php
            break;
        case 9:
            ?>
            swfobject.embedSWF("charts/open-flash-chart.swf", "my_chart", 
            "700", "400", "9.0.0", "expressInstall.swf", {"data-file": "chart_data.php?id=9%26from=<?php echo $_GET['from'];?>%26to=<?php echo $_GET['to'];?>"} );
            <?php
            break;
        case 10:
            ?>
            swfobject.embedSWF("charts/open-flash-chart.swf", "my_chart", 
            "700", "400", "9.0.0", "expressInstall.swf", {"data-file": "chart_data.php?id=10%26lesson_id=<?php echo $_GET['lesson_id'];?>%26from=<?php echo $_GET['from'];?>%26to=<?php echo $_GET['to'];?>%26login=<?php echo $_GET['login'];?>"} );
            <?php
            break;
        case 11:
            ?>
            swfobject.embedSWF("charts/open-flash-chart.swf", "my_chart", 
            "700", "400", "9.0.0", "expressInstall.swf", {"data-file": "chart_data.php?id=11%26from=<?php echo $_GET['from'];?>%26to=<?php echo $_GET['to'];?>%26login=<?php echo $_GET['login'];?>"} );
            <?php
            break;
        case 12:
            ?>
            swfobject.embedSWF("charts/open-flash-chart.swf", "my_chart", 
            "765", "430", "9.0.0", "expressInstall.swf", {"data-file": "chart_data.php?id=12%26tests_ID=<?php echo $_GET['tests_ID'];?>%26categories=<?php echo $_GET['categories'];?>"} );
            <?php
            break; 
        case 13:
            ?>
            swfobject.embedSWF("charts/open-flash-chart.swf", "my_chart", 
            "765", "430", "9.0.0", "expressInstall.swf", {"data-file": "chart_data.php?id=13%26tests_ID=<?php echo $_GET['tests_ID'];?>%26categories=<?php echo $_GET['categories'];?>"} );
            <?php
            break;                         
    }
?>
</script>
</head>
<body>
<div id="my_chart"></div> 
</body>
</html>