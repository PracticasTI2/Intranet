<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Grupo Asmedia</title>

    <!-- Google Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700&display=swap" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.12.1/css/all.css" crossorigin="anonymous">
    <style>
        @font-face {
             font-family: "Poppins", sans-serif;
            font-weight: 500;
            font-style: normal;
        }

        body, html {
            height: 100%;
            margin: 0;
            padding: 0;
            font-family: "Poppins", sans-serif;
            font-weight: 500;
            font-style: normal;
            overflow: hidden; /* Evitar barras de desplazamiento */
        }

        .tablero {
            background-image: url("{{ asset('storage/archivos/fondo.png') }}");
            height: 100%;
            width: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            color: white;

        }

        .content {
            display: flex;
            justify-content: center;
            align-items: center;
            text-align: center;
            width: 100%;
            height: 100%;
            margin-right: auto;
            margin-left: auto;
            position: relative;
        }


        .birthday-card {
            display: flex;
            flex-direction: column;
            align-items: center;
            background-color: rgba(0, 0, 0, 0.1);
            padding: 20px;
            border-radius: 10px;
            max-width: 950px;

            width: 100vw;
            height: 100vh;
            position: center;
            /* width: 95%; */

        }

        .birthday-card h2 {
            display: table;
            height: 5%;
            width: 80%;
            background: linear-gradient(#fff, #bec6cb 50%, #fff);
            border-radius: 20px;
            padding: 10px;
            margin: 10%;
            text-align: center;

            font-size: 40px;
            font-weight: bold;
            margin-bottom: 40px;
            color: #04325a;

        }

        .profile-container {
            display: flex;
            align-items: start;
            width: 100%;
        }

        .profile-img {
            width: 50%;
            max-width: 400px;
            /* height: 75%; */

            margin-right: 20px;
            object-fit: fill;
            border-radius: 96px 10px 95px 10px;
            -webkit-border-radius: 96px 10px 95px 10px;
            -moz-border-radius: 96px 10px 95px 10px;
            border: 5px none #f3e8e6;
        }

        .text-content {
            text-align: left;
            width: 50%;
        }

        .text-content h2 {
            font-size: 38px;
            margin-bottom: 0px;
            color: #74C0FC;
            text-align: center;
            margin-top: 40px;
        }

        .text-content p {
            font-size: 28px;
            margin: 2px 0;
            margin-top: 40px;
        }

        .fullscreen-image {
        width: 95vw; /* Ancho de la ventana de vista */
        height: 95vh; /* Altura de la ventana de vista */
        object-fit: fill; /* Ajusta la imagen para cubrir completamente */
        position: center; /* Posiciona la imagen para cubrir toda la pantalla */
        top: 0;
        left: 0;
    }

        video {
            width: 100vw;
            height: 100vh;
            object-fit: cover;
            background: black;
        }
    </style>
</head>
<body>
    <div class="tablero">
        <div id="content" class="content">
            <!-- El contenido dinámico se insertará aquí -->
        </div>
    </div>

    <script>


    document.addEventListener("DOMContentLoaded", function() {
    let index = 0;
    let cumpleaneros = @json($cumpleaneros->toArray());
    let notas = @json($notas->toArray());

    // Separar el contenido en diferentes categorías
    let videos = notas.filter(item => item.tipo === 'video');
    let imagenes = notas.filter(item => item.tipo === 'imagen');
    let urls = notas.filter(item => item.tipo === 'url');

    // Combinar el contenido de forma aleatoria
    let combinedData = shuffle([...cumpleaneros, ...videos, ...imagenes, ...urls]);

    let displayTime = 15000; // Tiempo en milisegundos para cada elemento

    function shuffle(array) {
        for (let i = array.length - 1; i > 0; i--) {
            const j = Math.floor(Math.random() * (i + 1));
            [array[i], array[j]] = [array[j], array[i]];
        }
        return array;
    }

    function formatFecha(fecha) {
        const meses = [
            'ENERO', 'FEBRERO', 'MARZO', 'ABRIL', 'MAYO', 'JUNIO',
            'JULIO', 'AGOSTO', 'SEPTIEMBRE', 'OCTUBRE', 'NOVIEMBRE', 'DICIEMBRE'
        ];
        let dateObj = new Date(fecha);
        let day = dateObj.getDate();
        let month = meses[dateObj.getMonth()];
        return `${day} DE ${month}`;
    }

    function showNext() {
        if (combinedData.length === 0) return;
        const contentDiv = document.getElementById('content');
        contentDiv.innerHTML = '';
        let currentItem = combinedData[index];

        if (currentItem.hasOwnProperty('idnota') && currentItem.tipo === 'video') {
            contentDiv.innerHTML = `
                <div class="text-center">
                    <video id="videoPlayer" src="{{ asset('storage') }}/${currentItem.archivo}" autoplay muted controls></video>
                </div>
            `;
            const videoPlayer = document.getElementById('videoPlayer');
            videoPlayer.onended = function() {
                index = (index + 1) % combinedData.length;
                if (index === 0) {
                    combinedData = shuffle(combinedData); // Rebarajar después de mostrar todo el contenido
                }
                showNext(); // Mostrar el siguiente contenido después de que termine el video
            };
        } else {
            if (currentItem.hasOwnProperty('nacimiento')) {
                contentDiv.innerHTML = `
                    <div class="birthday-card">
                        <h2 class="text-uppercase">FELIZ CUMPLEAÑOS</h2>
                        <div class="profile-container">
                            <div class="text-content">
                                <p class="text-uppercase">${currentItem.nombre} ${currentItem.apaterno} ${currentItem.amaterno}</p>
                                <p class="text-uppercase">${formatFecha(currentItem.nacimiento)}</p>
                                <p class="text-uppercase">${currentItem.puesto}</p>
                            </div>
                            <img src="{{ asset('storage') }}/${currentItem.foto}" class="profile-img" alt="Foto de ${currentItem.nombre}">
                        </div>
                    </div>
                `;
            } else if (currentItem.hasOwnProperty('idnota') && currentItem.tipo === 'imagen') {
                contentDiv.innerHTML = `
                    <div class="text-center">
                        <img src="{{ asset('storage') }}/${currentItem.archivo}" alt="${currentItem.titulo}" class="fullscreen-image">
                    </div>
                `;
            } else if (currentItem.hasOwnProperty('idnota') && currentItem.tipo === 'url') {
                contentDiv.innerHTML = `
                    <div class="text-center">
                        <iframe src="${currentItem.archivo}" alt="${currentItem.titulo}" class="fullscreen-image" allow="autoplay; fullscreen"></iframe>
                    </div>
                `;
            }
            index = (index + 1) % combinedData.length;
            if (index === 0) {
                combinedData = shuffle(combinedData); // Rebarajar después de mostrar todo el contenido
            }
            setTimeout(showNext, displayTime);
        }
    }

    showNext();

     setInterval(function() {
            location.reload();
        }, 9000000); // Recarga cada 60 segundos

});
    </script>
</body>
</html>
