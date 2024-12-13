<!DOCTYPE html>
<html>

<head>
    <title>Loading...</title>

    <link rel="stylesheet" href="https://nathcat.net/static/css/new-common.css">
    <link rel="stylesheet" href="/static/styles/editor.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat&display=swap" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cloud.nathcat.net/static/scripts/cloud.js"></script>
    <script src="  https://cdn.jsdelivr.net/npm/showdown@2.1.0/dist/showdown.min.js"></script>
</head>

<body>
    <div onclick="document.getElementById('note-editor').focus()" class="content">
        <?php include("../header.php"); ?>

        <div style="margin-left: 10px" class="main">
            <div id="note-content" class="column"></div>
            <textarea id="note-editor"></textarea>
        </div>

        <?php include("../footer.php"); ?>
    </div>

    <script>
        const searchParams = new URLSearchParams(window.location.search);
        var file;
        var content = [];

        if (searchParams.has("file")) {
            fetch("https://cloud.nathcat.net/get-file.php", {
                method: "POST",
                credentials: "include",
                body: JSON.stringify({
                    "filePath": searchParams.get("file")
                })
            }).then((r) => r.json()).then((r) => {
                if (r.status === "success") {
                    file = r.file;
                    file.filePath = searchParams.get("file");

                    fetch("https://cdn.nathcat.net/cloud/read-notecat?file=" + searchParams.get("file"), { method: "GET", mode: 'no-cors' })
                        .then((r) => r.text()).then((r) => {
                            content = r.split("\n");
                        }
                    );

                } else {
                    alert(r.message);
                }
            });
        } else {
            console.log("New file!");
            document.title = "New file";

            file = undefined;
        }

        var converter = new showdown.Converter();
        var renderContent = () => {
            let container = document.getElementById("note-content");
            container.innerHTML = "";
            content.forEach((v) => {
                container.innerHTML += converter.makeHtml(v);
            });

            $("#note-content a").each(function() {
                $(this).attr("target", "_blank");
            });
        };

        renderContent();

        $("#note-editor").on("input", function(e) {
            this.style.height = 'auto';
            this.style.height = `${this.scrollHeight}px`;
        });

        $("#note-editor").on("keydown", function(e) {
            if (e.key === "Enter") {
                let lines = $(this).val().split("\n");
                if (lines[lines.length - 1] === "") {
                    content.push($(this).val());
                    renderContent();
                    $(this).val("");
                }
            } else if (e.key === "Backspace") {
                let lines = $(this).val().split("\n");
                if (lines[lines.length - 1] === "") {
                    $(this).val(content[content.length - 1]);
                    content.splice(content.length - 1, 1);
                    renderContent();
                }
            } else if (e.ctrlKey && e.key === "s") {
                e.preventDefault();

                if (file === undefined) {
                    let blob = new Blob([content.join("\n")], { type: "text/plain" });

                    file = new File([blob], prompt("Please enter a name for your new note") + ".md", {type: "text/plain"});
                    let path = "/NoteCat";

                    let fd = new FormData();
                    fd.append("file", file);
                    fd.append("displayPath", path);

                    fetch("https://cdn.nathcat.net/cloud/upload.php", {
                        method: "POST",
                        credentials: "include",
                        body: fd
                    }).then((r) => r.json()).then((r) => {
                        if (r.status === "fail") alert(r.message);
                        else location = "?file=" + r.name;
                    });
                } else {
                    let blob = new Blob([content.join("\n")], { type: "text/plain" });

                    file = new File([blob], file.name, {type: "text/plain"});
                    let path = "/NoteCat";

                    let fd = new FormData();
                    fd.append("file", file);
                    fd.append("filePath", searchParams.get("file"));

                    fetch("https://cdn.nathcat.net/cloud/replace-content.php", {
                        method: "POST",
                        credentials: "include",
                        body: fd
                    }).then((r) => r.json()).then((r) => {
                        if (r.status === "fail") alert(r.message);
                        else location.reload();
                    });
                }
            }
        });
    </script>
</body>

</html>