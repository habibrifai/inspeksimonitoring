<!doctype html>
<html>
<head>
    <title></title>
    <?php $base = "http://localhost/inspeksimonitoring/"; ?>
    <!-- <script type="text/javascript" src="script.js"></script> -->

</head>
<body>
    <ul id="output"></ul>

    <script src="<?php echo $base; ?>assets/js/jquery-3.2.1.min.js" type="text/javascript"></script>

<script type="text/javascript">
var ajax = function () 
{
    $.ajax({                                      
        url: 'php_handler.php',                     
        data: "",                             
        dataType: 'json',          
        success: function(data)          
        {
            // $('#output').html("<b>id: </b>"+data);
            // document.write(data);
            // console.log(data);
            for (var i = 0; i < data.length; i++) {
                var counter = data[i];
                console.log(counter);
                $('#output').html("<b>tekanan: </b>"+counter);
            }

        } 
    });
}; 

setInterval(ajax, 1000 * 60 * 0.017);
</script>
</body>
</html>