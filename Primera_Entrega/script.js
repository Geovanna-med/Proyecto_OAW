    var feeds = [];
    const sortByDateBtn = document.getElementById("sortByDateBtn");
    const sortByTitleBtn = document.getElementById("sortByTitleBtn");
    const sortByURLBtn = document.getElementById("sortByUrlBtn");
    const sortByDescriptionBtn = document.getElementById("sortByDescriptionBtn");
    const sortByCategoriesBtn = document.getElementById("sortByCategoriesBtn");
    const updateNewsBtn = document.getElementById("updateNewsBtn");
    const addUrlBtn = document.getElementById("addUrlBtn");
    const searchNewsBtn = document.getElementById("btnSearch");
    
    sortByDateBtn.addEventListener("click", () => sortColumn(0));
    sortByTitleBtn.addEventListener("click", () => sortColumn(1));
    sortByURLBtn.addEventListener("click", () =>sortColumn(2));
    sortByDescriptionBtn.addEventListener("click", () => sortColumn(3));
    sortByCategoriesBtn.addEventListener("click", () => sortColumn(4));
    updateNewsBtn.addEventListener("click", actualizarNoticias);
    addUrlBtn.addEventListener("click", addUrl);
    searchNewsBtn.addEventListener("click", searchNews);

    function addUrl() {
        var feedUrl = document.getElementById("feedUrl").value;
        if (feedUrl.trim() !== "" && !feeds.includes(feedUrl.trim()) && isUrl(feedUrl)) {
            feeds.push(feedUrl);
            urlAdd(feedUrl);
            document.getElementById("feedUrl").value = ""; 
        } else {
            alert("La URL ingresada no es válida");
        }
    }

    function urlAdd(newUrl) {
        var linksAddedContainer = document.querySelector(".linksadded");
        var item = document.createElement("p");
        item.textContent = newUrl;
        linksAddedContainer.appendChild(item);
    }

    function actualizarNoticias() {
        var xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function () {
            if (this.readyState == 4 && this.status == 200) {
                var responseData = JSON.parse(this.responseText);
                if (responseData.length > 0) {
                    fillTable(responseData);
                } else {
                    document.getElementById("tableFeeds").innerHTML = "0 Resultados";
                }
            }
        };
        var queryString = feeds.map((url) => "urls[]=" + encodeURIComponent(url)).join("&");
        xhttp.open("GET", "read_feed.php?" + queryString);
        xhttp.send();
    }

    function searchNews() {
        var searchTerm = document.getElementById("searchNews").value.toLowerCase();
        var xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function () {
            if (this.readyState == 4 && this.status == 200) {
                var responseData = JSON.parse(this.responseText);
                if (responseData.length > 0) {
                    fillTable(responseData);
                } else {
                    document.getElementById("tableFeeds").innerHTML = "0 Resultados";
                }
            }
        };
        xhttp.open("GET","search_feed.php?searchString=" + encodeURIComponent(searchTerm));
        xhttp.send();
    }

    function fillTable(data){
        var html = "";
        data.forEach(function (newsItem) {
            html += "<tr><td><h2>Fecha:<span class='fecha'>" + newsItem.date + "</span></h2><br>" 
                    + "<h1><span class='titulo'>" + newsItem.title + "</span></h1><br>"
                    + "<h2>URL:</h2> <a href='" + newsItem.url + "'><span class='url'>" + newsItem.url + "</span></a><br>"
                    + "<h2>Descripción:</h2> <span class='descripcion'>" + newsItem.description + "</span><br>"
                    + "<h2>Categorías:</h2> <br><span class='categoria'>" + newsItem.category + "</span><br></td><td>";
            if (!(newsItem.image_url === null || typeof newsItem.image_url === 'undefined')) {
                html += "<img src='" + newsItem.image_url + "' alt='imagen' class='newsImage' style='max-width: 100%; height: auto;' loading='lazy'>";
            } else {
                html += "<img src='no_image.jpg' alt='imagen predeterminada' class='newsImage' style='max-width: 100%; height: auto;'>";
            }
            html += '</td><tr>';
        });
        document.getElementById("tableFeeds").innerHTML = html;
    }

    function isUrl(url) {
        try {
            new URL(url);
            return true;
        } catch (error) {
            return false;
        }
    }

    function byDate() {
        sortColumn(0);
    }

    function byTitle() {
        sortColumn(1);
    }

    function byURL() {
        sortColumn(2);
    }

    function byDescription() {
        sortColumn(3);
    }

    function byCategories() {
        sortColumn(4);
    }

    function sortColumn(index) {
        let registros = document.querySelectorAll(".table tbody tr");
        registros = Array.from(registros).filter(tr => tr.innerHTML.trim() !== ''); 
        registros.sort(function (a, b) {
            let valorCelda1, valorCelda2;
            if (index === 0) {
                let fechaCelda1 = a.querySelector(".fecha");
                let fechaCelda2 = b.querySelector(".fecha");
                if (fechaCelda1 && fechaCelda2) {
                    valorCelda1 = new Date(fechaCelda1.textContent.trim());
                    valorCelda2 = new Date(fechaCelda2.textContent.trim());
                }
            } else if (index === 1) {
                let valor = a.querySelector(".titulo").textContent.trim().toLowerCase();
                valorCelda1 = a.querySelector(".titulo").textContent.trim().toLowerCase();
                valorCelda2 = b.querySelector(".titulo").textContent.trim().toLowerCase();
            } else if (index === 2) {
                valorCelda1 = a.querySelector(".url").textContent.trim().toLowerCase();
                valorCelda2 = b.querySelector(".url").textContent.trim().toLowerCase();
            } else if (index === 3) {
                valorCelda1 = a.querySelector(".descripcion").textContent.trim().toLowerCase();
                valorCelda2 = b.querySelector(".descripcion").textContent.trim().toLowerCase();
            } else if (index === 4) {
                let categoriasCelda1 = a.querySelectorAll(".categoria");
                let categoriasCelda2 = b.querySelectorAll(".categoria");
                valorCelda1 = categoriasCelda1.length > 0 ? categoriasCelda1[0].textContent.trim().toLowerCase(): "";
                valorCelda2 = categoriasCelda2.length > 0 ? categoriasCelda2[0].textContent.trim().toLowerCase(): "";
            }
            if (index === 0) {
                return valorCelda2 - valorCelda1; 
            } else {
                return valorCelda1.localeCompare(valorCelda2);
            }
        });
        let tabla = document.querySelector(".table tbody");
        while (tabla.firstChild) {
            tabla.removeChild(tabla.firstChild);
        }
        registros.forEach(function (registro) {
            tabla.appendChild(registro);
        });
    }

    document.addEventListener("DOMContentLoaded", function () {
        getNoticias();
    });

    function getNoticias() {
        var xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function () {
            if (this.readyState == 4 && this.status == 200) {
                var responseData = JSON.parse(this.responseText);
                if (responseData.length > 0) {
                    fillTable(responseData);
                } else {
                    document.getElementById("tableFeeds").innerHTML = "0 Resultados";
                }
            }
        };
        xhttp.open("GET", "load_feed.php");
        xhttp.send();
    }