API_URL = "http://localhost/PHP/api/app/controller/userController.php";

const nombre = document.getElementById("nombre");
const email = document.getElementById("email");
const errorDiv = document.getElementById("divError");
const form = document.getElementById("form");
const tbody = document.getElementById("tbody");

// limpia cadenas a HTML seguro
function cleanHTML(str) {
  return str.replace(/[^\w. @-]/gi, function (e) {
    return "&#" + e.charCodeAt(0) + ";";
  });
}

function validEmail(email) {
  const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  return regex.test(email);
}

function validNombre(nombre) {
  return nombre.length >= 2 && nombre.length <= 20;
}

function getUsers() {
  fetch(API_URL)
    .then((response) => response.json())
    .then((users) => {
      tbody.innerHTML = "";
      users.forEach((user) => {
        const cleanNombre = cleanHTML(user.nombre);
        const cleanEmail = cleanHTML(user.email);
        tbody.innerHTML += `
            <tr data-id="${user.id}">
                <td>${user.id}</td>
                <td>${cleanNombre}</td>
                <td>${cleanEmail}</td>
                <td>
                    <div class="btns">
                        <button class="edit" id="e${user.id}">✏️</button>
                        <button class="delete" id="d${user.id}">🗑️</button>
                        <button class="accept" id="a${user.id}" style="display: none">✅</button>
                        <button class="cancel" id="c${user.id}" style="display: none">❌</button>
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
              .previousElementSibling;
          const emailTd =
            event.target.parentNode.parentNode.previousElementSibling;
          const oldNombre = nombreTd.textContent;
          const oldEmail = emailTd.textContent;

          nombreTd.innerHTML = `<input type="text" id=nombre${id} value="${nombreTd.textContent}">`;
          emailTd.innerHTML = `<input type="email" id=email${id} value="${emailTd.textContent}">`;
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
              const newEmail = document.getElementById(`email${id}`).value;
              nombreTd.innerHTML = `<td>${newNombre}</td>`;
              emailTd.innerHTML = `<td>${newEmail}</td>`;
              updateUser(id, newNombre, newEmail);
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
              emailTd.innerHTML = `<td>${oldEmail}</td>`;
            });
        });
      });

      //   click borrar
      deleteBtns.forEach((button) => {
        button.addEventListener("click", (event) => {
          const row = event.target.closest("tr");
          const id = row.getAttribute("data-id");
          deleteUser(id);
        });
      });
    })
    .catch((error) => {
      errorDiv.textContent = "Error al editar usuario";
    });
}

function createUser(event) {
  event.preventDefault();
  const cleanNombre = cleanHTML(nombre.value);
  const cleanEmail = cleanHTML(email.value);
  const data = {
    nombre: cleanNombre,
    email: cleanEmail,
  };

  if (!validNombre(cleanNombre)) {
    errorDiv.textContent = "Error en el nombre";
    return;
  }

  if (!validEmail(cleanEmail)) {
    errorDiv.textContent = "Error en el email";
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
      if (result["error"] || result["insert"] == "Email ya existente!") {
        errorDiv.textContent = result["insert"];
      }
      getUsers();
    })
    .catch((error) => {
      errorDiv.textContent = "Error al crear usuario";
    });
}

function updateUser(id, nombre, email) {
  const cleanNombre = cleanHTML(nombre);
  const cleanEmail = cleanHTML(email);
  const data = {
    nombre: cleanNombre,
    email: cleanEmail,
  };

  fetch(`${API_URL}?id=${id}`, {
    method: "PUT",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify(data),
  })
    .then((response) => response.json())
    .then((result) => {
      getUsers();
    })
    .catch((error) => {
      errorDiv.textContent = "Error al actualizar usuario";
    });
}

function deleteUser(id) {
  if (confirm("¿Borrar?")) {
    fetch(`${API_URL}?id=${id}`, {
      method: "DELETE",
    })
      .then((response) => response.json())
      .then((result) => {
        getUsers();
      })
      .catch((error) => {
        errorDiv.textContent = "Error al borrar usuario";
      });
  }
}

// ---------------------------------
getUsers();

form.addEventListener("submit", createUser);
