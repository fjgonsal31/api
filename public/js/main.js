
const div = document.getElementById("div");

fetch("../../app/controller/mainController.php", {
  method: "GET",
  headers: {
    "Content-Type": "application/x-www-form-urlencoded",
  },
})
  .then((response) => {
    if (!response.ok) {
      throw new Error("Error en la solicitud: " + response.status);
    }
    return response.json();
  })
  .then((data) => {
    data.forEach((element) => {
        console.log(element);
        if (element.imagen == null) {
            element.imagen = '/PHP/api/public/images/ops.png';
        }
      div.innerHTML += `
        <div class="card">
            <img src="${element.imagen}" alt="Sin imagen">
            <div class="info">
                <div class="title">${element.titulo}</div>
                <div class="prize">${element.precio} €</div>
                <div class="director">${element.nombre} ${element.apellido}</div>
            </div>
        </div>
        `;
    });
  })
  .catch((error) => {
    console.error("Hubo un problema con la solicitud fetch:", error);
  });
