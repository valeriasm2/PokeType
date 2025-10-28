<!DOCTYPE html>
<html lang="ca">

<head>
    <meta charset="UTF-8">
    <title>Rànking de rècords</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div id="ranking-container">
        <h1>Rànking de rècords</h1>
        <table>
            <thead>
                <tr>
                    <th>Jugador</th>
                    <th>Punts</th>
                </tr>
            </thead>
            <tbody id="ranking-body">
            </tbody>
        </table>
        <a href="index.php">Tornar al joc</a>
    </div>

    <script>
        let ranking = JSON.parse(localStorage.getItem("ranking") || "[]");
        const tbody = document.getElementById("ranking-body");

        ranking.forEach((p, i) => {
            let tr = document.createElement("tr");
            tr.className = i % 2 === 0 ? "even" : "odd";
            tr.innerHTML = `<td>${p.name}</td><td>${p.score}</td>`;
            tbody.appendChild(tr);
        });
    </script>
</body>

</html>