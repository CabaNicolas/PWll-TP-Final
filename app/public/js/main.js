function submitForm() {
    document.querySelector('.partida-form').submit();
}

let respuestas = document.querySelectorAll('.form-lista');

respuestas.forEach(respuesta => {
    respuesta.addEventListener('click', () => {
        submitForm();
    });
});
