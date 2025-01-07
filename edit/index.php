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

    <script src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js"></script>
</head>

<body>
    <div onclick="document.getElementById('note-editor').focus()" class="content">
        <?php include("../header.php"); ?>

        <div style="margin-left: 10px" class="main">
            <a href="/">Return home</a>
            <p id="last-saved-message"><i>Not saved since opened.</i></p>
            <div class="horizontal-divider"></div>
            <div id="note-content" class="column"></div>
        </div>

        <?php include("../footer.php"); ?>
    </div>

    <script>
        const searchParams = new URLSearchParams(window.location.search);
        var file;
        var content = [];
        var converter = new showdown.Converter();
        var editPosition = 0;

        var editor_autosize = function(e) {
            this.style.height = 'auto';
            this.style.height = `${this.scrollHeight}px`;
        };

        var editor_keydown = function(e) {
            if (e.key === "Enter") {
                let lines = $(this).val().split("\n");
                if ((lines[lines.length - 1] === "" || e.ctrlKey) && lines.length !== 1) {
                    if (editPosition === content.length) {
                        content.push($(this).val());
                    } else {
                        content[editPosition] = $(this).val();
                    }

                    editPosition++;

                    renderContent();
                    saveNote();
                }
            } else if (e.key === "Backspace") {
                if ($(this).val() === "" || e.ctrlKey) {
                    content[editPosition] = $(this).val();

                    if (editPosition !== 0) {
                        editPosition--;
                    }

                    renderContent();
                    saveNote();
                }
            } else if (e.ctrlKey && e.key === "s") {
                e.preventDefault();

                saveNote();
            }
        };

        var renderContent = () => {
            let container = document.getElementById("note-content");
            container.innerHTML = "";
            content.forEach((v, i) => {
                if (i === editPosition) {
                    container.innerHTML += "<textarea id='note-editor'>" + v + "</textarea>";
                } else {
                    //container.innerHTML += converter.makeHtml(v);
                    let latex_split = v.split("$$");
                    if (latex_split.length === 1 || ((latex_split.length % 2) === 0)) {
                        container.innerHTML += converter.makeHtml(v);
                    }
                    else {
                        let html = "<div class='note-content-block'>";
                        for (let i = 0; i < latex_split.length; i++) {
                            if ((i % 2) === 0) html += converter.makeHtml(latex_split[i]);
                            else {
                                html += "<p>$$" + latex_split[i] + "$$</p>";
                            }
                        }

                        container.innerHTML += html + "</div>";
                    }
                }
            });

            if (MathJax !== undefined) {
                MathJax.typeset();
            }

            if (editPosition === content.length) {
                container.innerHTML += "<textarea id='note-editor'></textarea>";
            }

            $("#note-content a").each(function() {
                $(this).attr("target", "_blank");
            });

            $("#note-content").children().each(function() {
                $(this).on("click", function(e) {
                    if (editPosition === $(this).index()) return;

                    content[editPosition] = $("#note-editor").val();
                    editPosition = $(this).index();
                    renderContent();
                });
            });

            $("#note-editor").on("input", editor_autosize);
            $("#note-editor").on("keydown", editor_keydown);

            editor_autosize();

            document.getElementById("note-editor").focus();
        };

        var saveNote = () => {
            if (file === undefined) {
                let blob = new Blob([content.join("\n")], {
                    type: "text/plain"
                });

                file = new File([blob], prompt("Please enter a name for your new note") + ".md", {
                    type: "text/plain"
                });
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
                let blob = new Blob([content.join("\n\n")], {
                    type: "text/plain"
                });

                file = new File([blob], file.name, {
                    type: "text/plain"
                });
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
                    else $("#last-saved-message").html("<i>Last saved " + new Date().toString() + "</i>");
                });
            }
        };

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
                    document.title = file.name;

                    fetch("https://cdn.nathcat.net/cloud/read-notecat.php?file=" + searchParams.get("file"))
                        .then((r) => r.text()).then((r) => {
                            content = r.split("\n\n");

                            let empty_pass = false;
                            while (!empty_pass) {
                                empty_pass = true;
                                for (let i = 0; i < content.length; i++) {
                                    if (content[i].match(/^(\s+|\n+)$|^$/) !== null) {
                                        empty_pass = false;
                                        content.splice(i, 1);
                                        break;
                                    }
                                }
                            }

                            editPosition = content.length;
                            renderContent();
                        });

                } else {
                    alert(r.message);
                }
            });
        } else {
            console.log("New file!");
            document.title = "New file";

            file = undefined;
            renderContent();
        }
    </script>
</body>

</html>