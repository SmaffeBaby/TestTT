document.addEventListener("DOMContentLoaded", function () {
    let filialsLink = document.querySelector(".navigation-text a:nth-child(3)");
    let container = document.getElementById("organization-container");

    filialsLink.addEventListener("click", function (e) {
        e.preventDefault();

        if (container.innerHTML.trim() === "") {
            let xhr = new XMLHttpRequest();

            xhr.open("GET", "/local/templates/GazICD/include/ajax/organization_list.php", true);

            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    container.innerHTML = xhr.responseText;
                }
            };
            xhr.send();
        } else {
            container.innerHTML = "";
        }
    });
});


document.addEventListener("DOMContentLoaded", function () {
    let navLinks = document.querySelectorAll(".navigation-text a");
    let container = document.getElementById("organization-container");

    navLinks.forEach(function (link) {
        link.addEventListener("click", function (e) {
            e.preventDefault();

            // Убираем active у всех ссылок
            navLinks.forEach(l => l.classList.remove("active"));
            // Добавляем active к текущей ссылке
            this.classList.add("active");

            // Проверяем, если это "Филиалы и организации"
            if (this.textContent.trim() === "Филиалы и организации") {
                if (container.innerHTML.trim() === "") {
                    let xhr = new XMLHttpRequest();
                    xhr.open("GET", "/local/templates/GazICD/include/ajax/organization_list.php", true);

                    xhr.onreadystatechange = function () {
                        if (xhr.readyState === 4 && xhr.status === 200) {
                            container.innerHTML = xhr.responseText;
                        }
                    };
                    xhr.send();
                } else {
                    container.innerHTML = "";
                }
            } else {
                container.innerHTML = "";
            }
        });
    });
});
