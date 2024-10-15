API_URL = "http://localhost/PHP/api/app/controller/directorController.php";

const nombre = document.getElementById("nombre");
const apellido = document.getElementById("apellido");
const f_nacimiento = document.getElementById("f_nacimiento");
const biografia = document.getElementById("biografia");
const errorDiv = document.getElementById("divError");
const form = document.getElementById("form");
const tbody = document.getElementById("tbody");

// limpia cadenas a HTML seguro
function cleanHTML(str) {
  return str.replace(/[^\w. @-]/gi, function (e) {
    return "&#" + e.charCodeAt(0) + ";";
  });
}

function validNombreApellido(str) {
  return str.length >= 2 && str.length <= 20;
}

function getDirectores() {
  fetch(API_URL)
    .then((response) => response.json())
    .then((directores) => {
      tbody.innerHTML = "";
      directores.forEach((director) => {
        const cleanNombre = cleanHTML(director.nombre);
        const cleanApellido = cleanHTML(director.apellido);
        tbody.innerHTML += `
            <tr data-id="${director.id}">
                <td>${director.id}</td>
                <td>${cleanNombre}</td>
                <td>${cleanApellido}</td>
                <td>${director.f_nacimiento}</td>
                <td>${director.biografia}</td>
                <td>
                    <div class="btns">
                        <button class="edit" id="e${director.id}">✏️</button>
                        <button class="delete" id="d${director.id}">🗑️</button>
                        <button class="accept" id="a${director.id}" style="display: none">✅</button>
                        <button class="cancel" id="c${director.id}" style="display: none">❌</button>
                    </div>
                </td>
            </tr>
        `;
      });
      const editBtns = document.querySelectorAll(".edit");
      const deleteBtns = document.querySelectorAll(".delete");

      //   click editar
      editBtns.forEach((button) => {
        button.addEventListener("click", (event) => {
          const row = event.target.closest("tr");
          const id = row.getAttribute("data-id");
          const nombreTd =
            event.target.parentNode.parentNode.previousElementSibling
              .previousElementSibling.previousElementSibling
              .previousElementSibling;
          const apellidoTd =
            event.target.parentNode.parentNode.previousElementSibling
              .previousElementSibling.previousElementSibling;
          const f_nacimientoTd =
            event.target.parentNode.parentNode.previousElementSibling
              .previousElementSibling;
          const biografiaTd =
            event.target.parentNode.parentNode.previousElementSibling;
          const oldNombre = nombreTd.textContent;
          const oldApellido = apellidoTd.textContent;
          const oldF_nacimiento = f_nacimientoTd.textContent;
          const oldBiografia = biografiaTd.textContent;

          nombreTd.innerHTML = `<input type="text" id=nombre${id} value="${nombreTd.textContent}">`;
          apellidoTd.innerHTML = `<input type="text" id=apellido${id} value="${apellidoTd.textContent}">`;
          f_nacimientoTd.innerHTML = `<input type="date" id=f_nacimiento${id} value="${f_nacimientoTd.textContent}">`;
          biografiaTd.innerHTML = `<textarea id="biografia${id}">${biografiaTd.textContent}</textarea>`;

          document.getElementById(`e${id}`).style.display = "none";
          document.getElementById(`d${id}`).style.display = "none";
          document.getElementById(`a${id}`).style.display = "block";
          document.getElementById(`c${id}`).style.display = "block";

          //   aceptar edicion
          document
            .getElementById(`a${id}`)
            .addEventListener("click", (event) => {
              document.getElementById(`e${id}`).style.display = "block";
              document.getElementById(`d${id}`).style.display = "block";
              document.getElementById(`a${id}`).style.display = "none";
              document.getElementById(`c${id}`).style.display = "none";
              const newNombre = document.getElementById(`nombre${id}`).value;
              const newApellido = document.getElementById(
                `apellido${id}`
              ).value;
              const newF_nacimiento = document.getElementById(
                `f_nacimiento${id}`
              ).value;
              const newBiografia = document.getElementById(
                `biografia${id}`
              ).value;
              nombreTd.innerHTML = `<td>${newNombre}</td>`;
              apellidoTd.innerHTML = `<td>${newApellido}</td>`;
              f_nacimientoTd.innerHTML = `<td>${newF_nacimiento}</td>`;
              biografiaTd.innerHTML = `<td>${newBiografia}</td>`;
              updateDirector(
                id,
                newNombre,
                newApellido,
                newF_nacimiento,
                newBiografia
              );
            });
          //   cancelar edicion
          document
            .getElementById(`c${id}`)
            .addEventListener("click", (event) => {
              document.getElementById(`e${id}`).style.display = "block";
              document.getElementById(`d${id}`).style.display = "block";
              document.getElementById(`a${id}`).style.display = "none";
              document.getElementById(`c${id}`).style.display = "none";
              nombreTd.innerHTML = `<td>${oldNombre}</td>`;
              apellidoTd.innerHTML = `<td>${oldApellido}</td>`;
              f_nacimientoTd.innerHTML = `<td>${oldF_nacimiento}</td>`;
              biografiaTd.innerHTML = `<td>${oldBiografia}</td>`;
            });
        });
      });

      //   click borrar
      deleteBtns.forEach((button) => {
        button.addEventListener("click", (event) => {
          const row = event.target.closest("tr");
          const id = row.getAttribute("data-id");
          deleteDirector(id);
        });
      });
    })
    .catch((error) => {
      errorDiv.textContent = "Error al editar usuario";
    });
}

function createDirector(event) {
  event.preventDefault();
  const cleanNombre = cleanHTML(nombre.value);
  const cleanApellido = cleanHTML(apellido.value);
  const data = {
    nombre: cleanNombre,
    apellido: cleanApellido,
    f_nacimiento: f_nacimiento.value,
    biografia: biografia.value,
  };

  if (!validNombreApellido(cleanNombre)) {
    errorDiv.textContent = "Error en el nombre";
    return;
  }

  if (!validNombreApellido(cleanApellido)) {
    errorDiv.textContent = "Error en el apellido";
    return;
  }

  if (!f_nacimiento.value) {
    errorDiv.textContent = "Error en la fecha de nacimiento";
    return;
  }

  errorDiv.textContent = "";

  fetch(API_URL, {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify(data),
  })
    .then((response) => response.json())
    .then((result) => {
      console.log(result);
      if (result["error"] || result["insert"] == "Director ya existente!") {
        errorDiv.textContent = result["insert"];
      }
      getDirectores();
    })
    .catch((error) => {
      console.log(error);
      errorDiv.textContent = "Error al crear director";
    });
}

function updateDirector(id, nombre, apellido, f_nacimiento, biografia) {
  const cleanNombre = cleanHTML(nombre);
  const cleanApellido = cleanHTML(apellido);
  const data = {
    nombre: cleanNombre,
    apellido: cleanApellido,
    f_nacimiento: f_nacimiento,
    biografia: biografia
  };

  fetch(`${API_URL}?id=${id}`, {
    method: "PUT",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify(data),
  })
    .then((response) => response.json())
    .then((result) => {
      getDirectores();
    })
    .catch((error) => {
      errorDiv.textContent = "Error al actualizar director";
    });
}

function deleteDirector(id) {
  if (confirm("¿Borrar?")) {
    fetch(`${API_URL}?id=${id}`, {
      method: "DELETE",
    })
      .then((response) => response.json())
      .then((result) => {
        getDirectores();
      })
      .catch((error) => {
        errorDiv.textContent = "Error al borrar director";
      });
  }
}

// ---------------------------------
getDirectores();

form.addEventListener("submit", createDirector);
