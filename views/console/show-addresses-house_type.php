<body>
<div id="map" style="width: 1200px; height: 800px"></div>
<script type="text/javascript">
    var map = new ymaps.Map("map", {
        center: [55.76, 37.64],
        zoom: 7
    });
</script>
</body>




<script type="text/javascript">
    ymaps.ready(init);
    var myMap,
        myPlacemark;

    function init(){
        myMap = new ymaps.Map ("map", {
            center: [58.53, 31.22],
            zoom: 13
        });
        <? if ($addresses) {
        foreach ($addresses as $address) {
            if ($address->house_type == 0) {
                echo "myPlacemark$address->id = new ymaps.Placemark([$address->coords_y,$address->coords_x], {
            hintContent: '$address->address, $address->year, $address->floorcount, $address->house_type'
        });
         myMap.geoObjects.add(myPlacemark$address->id);
         myPlacemark$address->id.events.add('click', function () 
         {
         var house_type = prompt(\"Please enter house_type\");
         var id_address = ".$address->id.";
         $.ajax({
        url : '/addresses/set-housetype-and-fix-it',
        data: {id_address: id_address, house_type: house_type},
        type: 'get',
        success: function(data){
        this.display='none';


      },

        error: function() {alert('error')}
        });
           
          });
        ";
            }
            else echo "
            
         myGeoObject".$address->id." = new ymaps.GeoObject({
           
            geometry: {
                type: \"Point\",
                coordinates: [".$address->coords_y.", ".$address->coords_x."]
            },
            
            properties: {iconContent: '".$address->house_type."'}
        }, {
          
            preset: 'islands#blackStretchyIcon',
           
            draggable: true
        });

    myMap.geoObjects
        .add(myGeoObject".$address->id.")
        
        ;";



        }
    }
        ?>




    }
</script>
