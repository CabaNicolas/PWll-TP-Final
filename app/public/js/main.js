document.querySelectorAll('.form-lista input[type="radio"]').forEach(radio => {
    radio.addEventListener('change', async (e) => {
        e.preventDefault();

        const respuestaId = e.target.value;
        const formData = new FormData();
        formData.append('respuesta', respuestaId);

        const response = await fetch('/partida/validarRespuesta', {
            method: 'POST',
            body: formData
        });
        const data = await response.json();
        if (data.correcta) {
            document.getElementById(`label_${respuestaId}`).classList.add('respuesta-correcta');
        } else {
            document.getElementById(`label_${respuestaId}`).classList.add('respuesta-incorrecta');
        }

        setTimeout(() => {
            if (data.correcta) {
                window.location.href = '/partida/showPregunta';
            } else {
                window.location.href = '/partida/cerrarPartida';
            }}, 1000);
    });
});

