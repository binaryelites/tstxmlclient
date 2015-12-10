<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
        <style>
            label {display: block; margin-bottom: 10px};
        </style>
    </head>
    <body>
        <h3>Save order item</h3>
        <form action="save_order_item.php" method="post" target="_blank">
            
            <textarea style="width: 400px; height: 300px" name="__payload__"><?=file_get_contents("save_order_item.xml")?></textarea>
            
            <button type="submit">Save</button>
        </form>
    </body>
</html>
