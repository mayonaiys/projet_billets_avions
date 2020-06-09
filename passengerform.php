<!doctype html>
<html lang='fr'>
     <head>
         <meta charset='utf-8'>
         <title>Passager</title>
         <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">

         <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
         <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
         <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>

         <link rel="stylesheet" href="css/multirange.css">
         <script src="js/multirange.js"></script>


    </head>
<body style="background-image:url('./datas/backgroung.jpg'); background-size: cover;">

<!--HEADER-->

<header class="header d-flex" style="margin-top: 20px; margin-left: 5%; margin-right: 5%; border-radius: 10px; background-color: rgba(200,200,200,0.6);">
    <div class="container text-center my-auto" style="text-align: center;">
        <h1 class="mb-1"><br>CANADA AIRLINE</h1>
        <h3 class="mb-5">
            <em>The best company in the world !</em>
        </h3>
    </div>

</header>
<br>

<div class="container-fluid " style="width: 90%; margin: auto; border-radius: 10px; background-color: rgba(255, 255, 255,0.9); ">
        <?php
            $nbPassenger=3;

            for($i=1; $i<=$nbPassenger;$i++)
            {
                echo "<br><div style='margin-left: 3%;'><h3>Informations Passager $i</h3><br>";
                $nom='name'.$i;
                $prenom='prenom'.$i;
                $mail='mail'.$i;
                $date='birth'.$i;

                echo "
                    <label>Nom </label>
                    <input id='$nom' style='margin-right: 5%'>
                    
              

                    <label>Prénom </label>
                    <input id='$prenom' style='margin-right: 5%'> 
                                       
                    
                
                    <label>Mail </label>
                    <input id='$mail' style='margin-right: 5%'>
                                        
                    

                    <label>Date de naissance</label>
                    <input type='date' id='$date' value='' >
                
                </div>
                    <br><br>
                ";
            }
            echo "<button type='submit' id='valid' class='btn btn-primary'  style='margin-left: 45%; margin-bottom: 10px;'>Valider</button> "
        ?>

</div>