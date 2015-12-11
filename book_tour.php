<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
        <style>
            label {display: block; margin-bottom: 10px};
        </style>
    </head>
    <body>
        <h4>Save Order Information</h4>
        <form action="save_order.php" method="post">            
            <textarea name="__payload__" style="width: 800px; height: 200px"><?=  file_get_contents("book_tour.xml")?></textarea>
            <button type="submit">Save</button>
        </form>
    </body>
</html>
