$.ajax({                                  
    url: 'php_handler.php',                     
    data: "",                             
    dataType: 'json',          
    success: function(data)          
    {
        // for (var i = 0; i < data.length; i++) {
        //     var counter = data[i];
            // console.log(counter);
            $('#output').html("<b>tekanan: </b>"+data);
        // }
        
        // var lineChart = new Chart(speedCanvas, {
        //     type: 'line',
        //     data: {
        //         labels: 
        //         [1,2,3,4,5],
        //         datasets: [{
        //             label: "Tekanan",
        //             data: 
        //             [data+','],
        //         }]},
        //     options: chartOptions
        // });    
    }
});

var dataTekanan = {
                  labels: 
                  [

                  <?php 
                  $no = 1;
                  for ($i=0; $i < count($arr); $i++) { 
                    echo $no.',';
                    $no++;
                }
                ?>

                ],
                datasets: [{
                    label: "Tekanan",
                    data: 
                    [ 

                    <?php
                    
                    for ($j=0; $j < count($arr); $j++) { 
                        echo $arr[$j]['tekanan'].',';
                    }

                    ?>

                    ],
                }]
            };