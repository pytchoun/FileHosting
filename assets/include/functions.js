function Request() {
  var xmlhttp = null;
  if (window.XMLHttpRequest) {
    xmlhttp = new XMLHttpRequest();
  } else {
    xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
  }
  return xmlhttp;
}

function bankDetails(choice) {
  var xmlhttp = Request();
  xmlhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
      document.getElementById("show-bank-details").innerHTML = this.responseText;
    }
  };
  xmlhttp.open("POST", "../assets/php/bankdetails.php", true);
	xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xmlhttp.send("value=" + choice);
}

function showBill(number, page) {
  var xmlhttp = Request();
  xmlhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
      document.getElementById("show-bill").innerHTML = this.responseText;
    }
  };
  xmlhttp.open("POST", "../assets/php/billcount.php", true);
	xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xmlhttp.send("counter=" + number + "&pagination=" + page);
}

function showDirectory(directory) {
  var xmlhttp = Request();
  xmlhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
      document.getElementById("show-directory").innerHTML = this.responseText;
    }
  };
  xmlhttp.open("POST", "../assets/MyStorage/showdirectory.php", true);
	xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xmlhttp.send("directory=" + directory);
}

function showHostDirectory(directory, myGuestEmail) {
  var xmlhttp = Request();
  xmlhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
      document.getElementById("show-host-directory").innerHTML = this.responseText;
    }
  };
  xmlhttp.open("POST", "../assets/MyHost/showdirectory.php", true);
	xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xmlhttp.send("directory=" + directory + "&myGuestEmail=" + myGuestEmail);
}

function showSharedDirectory(directory, myHostName) {
  var xmlhttp = Request();
  xmlhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
      document.getElementById("show-shared-directory").innerHTML = this.responseText;
    }
  };
  xmlhttp.open("POST", "../assets/MyShared/showdirectory.php", true);
	xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xmlhttp.send("directory=" + directory + "&myHostName=" + myHostName);
}

function uploadFile(directory) {
  var xmlhttp = Request();
  xmlhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
      showDirectory(directory);
      document.getElementById("storage-notification").innerHTML = this.responseText;
    }
  };
  var file_input = document.getElementById('upload-file');
  var file = file_input.files[0];
  var formData = new FormData();
  formData.append('upload-file', file);
  formData.append('folder', directory);
  xmlhttp.open("POST", "../assets/MyStorage/uploadfile.php", true);
	xmlhttp.send(formData);
}

function createFolder(name, directory) {
  var xmlhttp = Request();
  xmlhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
      showDirectory(directory);
      document.getElementById("storage-notification").innerHTML = this.responseText;
    }
  };
  xmlhttp.open("POST", "../assets/MyStorage/createfolder.php", true);
	xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xmlhttp.send("name=" + name + "&folder=" + directory);
}

function shareFile(email, file, folder) {
  var xmlhttp = Request();
  xmlhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
      document.getElementById("storage-notification").innerHTML = this.responseText;
    }
  };
  xmlhttp.open("POST", "../assets/MyStorage/sharefile.php", true);
	xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xmlhttp.send("email=" + email + "&file=" + file + "&folder=" + folder);
}

function shareFolder(email, folder, myParentFolder) {
  var xmlhttp = Request();
  xmlhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
      document.getElementById("storage-notification").innerHTML = this.responseText;
    }
  };
  xmlhttp.open("POST", "../assets/MyStorage/sharefolder.php", true);
	xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xmlhttp.send("email=" + email + "&folder=" + folder + "&myParentFolder=" + myParentFolder);
}

function deleteFile(file, directory) {
  var xmlhttp = Request();
  xmlhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
      showDirectory(directory);
      document.getElementById("storage-notification").innerHTML = this.responseText;
    }
  };
  xmlhttp.open("POST", "../assets/MyStorage/deletefile.php", true);
	xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xmlhttp.send("file=" + file);
}

function deleteSharedFile(file, directory) {
  var xmlhttp = Request();
  xmlhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
      showSharedDirectory(directory);
      document.getElementById("storage-notification").innerHTML = this.responseText;
    }
  };
  xmlhttp.open("POST", "../assets/MyShared/deletefile.php", true);
	xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xmlhttp.send("file=" + file);
}

function deleteHostFile(file, userGuest, directory) {
  var xmlhttp = Request();
  xmlhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
      showHostDirectory(directory);
      document.getElementById("storage-notification").innerHTML = this.responseText;
    }
  };
  xmlhttp.open("POST", "../assets/MyHost/deletefile.php", true);
	xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xmlhttp.send("file=" + file + "&userGuest=" + userGuest);
}

function deleteFolder(folder, directory) {
  var xmlhttp = Request();
  xmlhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
      showDirectory(directory);
      document.getElementById("storage-notification").innerHTML = this.responseText;
    }
  };
  xmlhttp.open("POST", "../assets/MyStorage/deletefolder.php", true);
	xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xmlhttp.send("folder=" + folder);
}

function deleteSharedFolder(folder, directory) {
  var xmlhttp = Request();
  xmlhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
      showSharedDirectory(directory);
      document.getElementById("storage-notification").innerHTML = this.responseText;
    }
  };
  xmlhttp.open("POST", "../assets/MyShared/deletefolder.php", true);
	xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xmlhttp.send("folder=" + folder);
}

function deleteHostFolder(folder, userGuest, directory) {
  var xmlhttp = Request();
  xmlhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
      showHostDirectory(directory);
      document.getElementById("storage-notification").innerHTML = this.responseText;
    }
  };
  xmlhttp.open("POST", "../assets/MyHost/deletefolder.php", true);
	xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xmlhttp.send("folder=" + folder + "&userGuest=" + userGuest);
}

function editFile(file, name, description, directory) {
  var xmlhttp = Request();
  xmlhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
      showDirectory(directory);
      document.getElementById("storage-notification").innerHTML = this.responseText;
    }
  };
  xmlhttp.open("POST", "../assets/MyStorage/editfile.php", true);
	xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xmlhttp.send("file=" + file + "&name=" + name + "&description=" + description);
}

function editFolder(folder, name, directory) {
  var xmlhttp = Request();
  xmlhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
      showDirectory(directory);
      document.getElementById("storage-notification").innerHTML = this.responseText;
    }
  };
  xmlhttp.open("POST", "../assets/MyStorage/editfolder.php", true);
	xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xmlhttp.send("folder=" + folder + "&name=" + name);
}

function moveFile(file, newFolder, directory) {
  var xmlhttp = Request();
  xmlhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
      showDirectory(directory);
      document.getElementById("storage-notification").innerHTML = this.responseText;
    }
  };
  xmlhttp.open("POST", "../assets/MyStorage/movefile.php", true);
	xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xmlhttp.send("file=" + file + "&newFolder=" + newFolder);
}

function moveFolder(folder, newFolder, directory) {
  var xmlhttp = Request();
  xmlhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
      showDirectory(directory);
      document.getElementById("storage-notification").innerHTML = this.responseText;
    }
  };
  xmlhttp.open("POST", "../assets/MyStorage/movefolder.php", true);
	xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xmlhttp.send("folder=" + folder + "&newFolder=" + newFolder);
}

function downloadFolder(folder, name) {
  var xmlhttp = Request();
  var path = "/FileHosting/uploads/";
  var name = path.concat(name);
  var zip = ".zip";
  var zip = name.concat(zip);
  xmlhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
      location.href = zip;
    }
  };
  xmlhttp.open("POST", "../assets/MyStorage/downloadfolder.php", true);
	xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xmlhttp.send("folder=" + folder);
}

function sortFileSize(direction, page, directory, userIdentity) {
  var str = "../assets/php/";
  var page = str.concat(page);
  var xmlhttp = Request();
  xmlhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
      document.getElementById("sort-file-size-storage").innerHTML = this.responseText;
    }
  };
  xmlhttp.open("POST", page, true);
	xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xmlhttp.send("direction=" + direction + "&directory=" + directory + "&userIdentity=" + userIdentity);
}

function sortTable(n) {
  var table, rows, switching, i, x, y, shouldSwitch, direction, switchcount = 0;
  table = document.getElementById("storageTable");
  switching = true;
  // On commence avec la direction en ASC.
  direction = "asc";
  // On fait une boucle tant qu'il y a un changement
  while (switching) {
    // On commence par dire qu'il n y a pas eu de changement
    switching = false;
    rows = table.getElementsByTagName("tr");
    // On fait une boucle à travers toutes les lignes du tableau
    for (i = 1; i < (rows.length - 1); i++) {
      // On commence par dire qu'il ne doit pas avoir eu de changement
      shouldSwitch = false;
      // On prends deux éléments à comparer, la ligne actuelle et la ligne suivante
      x = rows[i].getElementsByTagName("td")[n];
      y = rows[i + 1].getElementsByTagName("td")[n];
      // On regarde si les deux lignes doivent changer de place selon la direction
      if (direction == "asc") {
        if (x.innerHTML.toLowerCase() > y.innerHTML.toLowerCase()) {
          // Si oui, on signale ce changement et on sort de la boucle
          shouldSwitch = true;
          break;
        }
      } else if (direction == "desc") {
        if (x.innerHTML.toLowerCase() < y.innerHTML.toLowerCase()) {
          // Si oui, on signale ce changement et on sort de la boucle
          shouldSwitch = true;
          break;
        }
      }
    }
    if (shouldSwitch) {
      // Si un changement a été marqué, on le fait et on dit qu'un changement a été fait
      rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
      switching = true;
      switchcount ++;
    } else {
      // Si aucun changement a été fait et que la direction est ASC, on met la direction en DESC et on refait la boucle
      if (switchcount == 0 && direction == "asc") {
        direction = "desc";
        switching = true;
      }
    }
  }
}

// Récupérons toutes les lignes correspondantes à notre recherche
function fileSearch() {
  var input, filter, table, tr, td, i;
  input = document.getElementById("fileSearch");
  filter = input.value.toUpperCase();
  table = document.getElementById("storageTable");
  tr = table.getElementsByTagName("tr");

  // Pour toutes les lignes du tableau
  for (i = 0; i < tr.length; i++) {
    // On récupère la colonne avec le nom
    td = tr[i].getElementsByTagName("td")[0];
    if (td) {
      // La saisie utilisateur apparaît-elle dans le mot
      if (td.innerHTML.toUpperCase().indexOf(filter) > -1) {
        tr[i].style.display = "";
      // La saisie utilisateur n'apparaît pas, on cache la ligne
      } else {
        tr[i].style.display = "none";
      }
    }
  }
}
