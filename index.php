<!DOCTYPE html>
<html>

<head>
    <title>NoteCat</title>

    <link rel="stylesheet" href="https://nathcat.net/static/css/new-common.css">
    <link rel="stylesheet" href="https://cloud.nathcat.net/static/styles/browser.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat&display=swap" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cloud.nathcat.net/static/scripts/cloud.js"></script>
</head>

<body>
    <div class="content">
        <?php include("header.php"); ?>
        
        <div class="main">
            <div class="row align-center">
                <button onclick="location = '/edit';" style="width: 90%; padding: 20px;">Create new note</button>
            </div>

            <span class="horizontal-divider"></span>

            <div id="dir-contents" class="column"></div>
        </div>

        <script>
            cloud_get_dir_contents("/NoteCat", (dir) => {
                let container = document.getElementById("dir-contents");
                let files = dir["."];

                for (const file in files) {
                    container.innerHTML += "<div class='row align-center'><div onclick=\"location='https://note.nathcat.net/edit/?file=" + files[file].filePath + "'\" class='file'><img src='/static/images/iconmonstr-file-thin.svg'><h3>" + files[file].name + "</h3></div><span class='spacer'></span><button onclick=\"cloud_delete_file('" + files[file].filePath + "', '" + files[file].name + "');\">Delete</button></div>";
                }

            }, () => {
                $("#dir-contents").html("<h3><i><b>You have no notes!</b></i></h3>");
            });
        </script>

        <?php include("footer.php"); ?>
    </div>
</body>

</html>