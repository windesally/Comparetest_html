<?php
$basePaths = [
    "PKASM011" => "C:\\Users\\nxg13764\\Desktop\\Project compare\\Python\\TEST\\PKASM011\\mc\\bind",
    "PKASM012" => "\\\\192.168.76.58\\data\\05_ EE\\Pathomphat S\\TEST compare project\\PKASM\\PKASM012\\mc\\bind",
    "PKASM013" => "\\\\192.168.76.58\\data\\05_ EE\\Pathomphat S\\TEST compare project\\PKASM\\PKASM013\\mc\\bind",
    "PKASM014" => "\\\\192.168.76.58\\data\\05_ EE\\Pathomphat S\\TEST compare project\\PKASM\\PKASM014\\mc\\bind"
];
$machines = array_keys($basePaths);
$recipeList = [];

if (!empty($_GET['machine']) && isset($basePaths[$_GET['machine']])) {
    $path = $basePaths[$_GET['machine']];
    if (is_dir($path)) {
        foreach (glob($path . DIRECTORY_SEPARATOR . "*.cat") as $file) {
            $recipeList[] = basename($file);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Site-page</title>
    <link rel="stylesheet" href="site-page-admin.css">
</head>
<body>
<header>
    <div style="text-align: center;">
        <img width="250" height="100" src="https://upload.wikimedia.org/wikipedia/commons/thumb/5/50/NXP_Semiconductors_logo_2023.svg/2560px-NXP_Semiconductors_logo_2023.svg.png" alt="NXP logo">
    </div>
    <a class="logoutbtn" style="color: white;" href="logout.php">Logout</a>        
</header>

<main>
    <section class="container">
        <!-- เลือก machine -->
        <div class="machine">
            <p class="choose-machine">Choose machine</p>
            <form method="GET" id="machineForm">
                <select class="choose" name="machine" id="machineSelect" onchange="this.form.submit()">
                    <option value="">Select Machine</option>
                    <?php
                    foreach ($machines as $machine) {
                        $selected = (isset($_GET['machine']) && $_GET['machine'] === $machine) ? 'selected' : '';
                        echo "<option value='$machine' $selected>$machine</option>";
                    }
                    ?>
                </select>
                <button type="button" id="clearBtn">Clear</button>
            </form>
        </div>

        <!-- เลือก recipe + อัพโหลด -->
        <div class="recipe-machine">
            <p class="choose-recipe">Choose recipe</p>
            <form id="compareForm" enctype="multipart/form-data">
                <input type="hidden" name="machine" value="<?php echo isset($_GET['machine']) ? $_GET['machine'] : ''; ?>">
                <select class="choose-recipe-machine" name="recipe" id="recipeSelect">
                    <option value="">Select Recipe</option>
                    <?php
                    foreach ($recipeList as $recipe) {
                        echo "<option value='" . htmlspecialchars(pathinfo($recipe, PATHINFO_FILENAME)) . "'>$recipe</option>";
                    }
                    ?>
                </select>

                <div class="selected_file">
                    <p class="label-upload-file">Upload file</p>
                    <div class="upload-box">
                        <input class="files" type="file" name="file">
                        <button type="submit" class="comparebtn">Compare</button>
                    </div>    
                </div>
            </form>
        </div>

        <!-- แสดงผล -->
        <div class="result">
            <h3 class="resulttitle">Result</h3>
            <textarea style="resize: none;" id="resultbox" readonly></textarea>
        </div>

        <div class="btn_sendgmail">
            <a target="_blank" href="http://thgbnklak1ms105/ewfm_web/Pages/CCRecipes.aspx?AREA=FT"><button class="btn_mail">Send_Gmail</button></a>
        </div>
    </section>
</main>

<script>
document.getElementById("clearBtn").addEventListener("click", function() {
    document.getElementById("machineSelect").value = "";
    document.getElementById("recipeSelect").innerHTML = '<option value="">Select Recipe</option>';
    document.getElementById("resultbox").value = ""; // ล้างข้อความใน Result ด้วย
});

// AJAX ส่งไป compare_action.php
document.getElementById("compareForm").addEventListener("submit", function(e) {
    e.preventDefault();

    let formData = new FormData(this);

    fetch("compare_action.php", {
        method: "POST",
        body: formData
    })
    .then(res => res.text())
    .then(data => {
        document.getElementById("resultbox").value = data;
    })
    .catch(err => {
        document.getElementById("resultbox").value = "❌ Error: " + err;
    });
});
</script>
</body>
</html>
