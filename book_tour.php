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
        <form action="save_order.php" method="post" target="_blank">
            
            <textarea style="width: 400px; height: 500px" name="__payload__"><?=file_get_contents("book_tour.xml")?></textarea>
            
            <button type="submit">Save</button>
        </form>
    </body>
</html>
