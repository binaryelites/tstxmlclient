<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
    </head>
    <body style="width: 100%">
        <div style="margin: 0 auto; width: 1024px">
            <?php
            include("menu.php");
            ?>
            <form action="search.php" method="post">
                <b>Search Tours</b><br />                    
                <input type="text" value="" name="tour_name_like"  />
                <button type="submit">Search</button>
            </form>
        </div>
    </body>
</html>
