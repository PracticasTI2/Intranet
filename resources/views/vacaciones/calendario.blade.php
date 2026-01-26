@extends('layouts/contentNavbarLayout')

@section('title', 'Calendario - Vacaciones')

@section('vendor-style')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/apex-charts/apex-charts.css') }}">
@endsection

@section('vendor-script')
    <script src="{{ asset('assets/vendor/libs/apex-charts/apexcharts.js') }}"></script>
@endsection

@section('page-script')
    <script src="{{ asset('assets/js/dashboards-analytics.js') }}"></script>
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.9/index.global.min.js'></script>
    <script src="{{ asset('assets/js/moment.js') }}"></script>
    <script src="{{ asset('assets/js/sweetalert2.all.min.js') }}"></script>
    <script src="{{ asset('assets/js/es.global.js') }}"></script>
@endsection

@section('content')
    <!-- Bootstrap JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous">
    </script>

    <div class="row justify-content-center" id="calendar-container" data-sam="{{ $sam }}"
        data-rol="{{ $rol }}" data-iduser="{{ $iduser }}" data-app-url="{{ url('/') }}"
        data-csrf-token="{{ csrf_token() }}">

        <div class="col-md-10 col-lg-10 col-xl-10 mb-4">
            <!-- Leyenda de estatus -->
            <div class="row mb-4">
                <div class="col">
                    <h4 class="text-center">Estatus:</h4>
                </div>
                <div class="col">
                    <p class="text-white text-center" style="background-color: #808080;">Pendiente</p>
                </div>
                <div class="col">
                    <p class="text-white text-center" style="background-color: #276621;">Autorizado</p>
                </div>
                <div class="col">
                    <p class="text-white text-center" style="background-color: #FF0000;">Rechazado</p>
                </div>
            </div>

            <!-- Tarjeta principal con calendario -->
            <div class="card">
                <div class="card-body">
                    <div id='calendar'></div>
                </div>
            </div>

            <!-- Depuración: mostrar información del usuario -->
            <div class="mt-3 text-center small text-muted">
                <p>Usuario ID: {{ $iduser }} | Rol: {{ $rol }} | Nombre: {{ $sam }}</p>
            </div>

            <!-- Modal para registrar/modificar vacaciones -->
            <div class="modal fade" id="myModal" data-backdrop="static" data-keyboard="false" tabindex="-1"
                aria-labelledby="myModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-simple modal-add-new-cc">
                    <div class="modal-content">
                        <div class="modal-header bg-secondary">
                            <h5 class="modal-title text-white" id="titulo">Registro de Vacaciones</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>

                        <form id="formulario" action="">
                            @csrf
                            <div class="modal-body">
                                <input type="hidden" id="id" name="id">

                                <div class="form-floating mb-3">
                                    @if ($rol == 2)
                                        <!-- Si es usuario normal -->
                                        <input type="text" class="form-control pe-none" id="nombre_user"
                                            name="nombre_user" value="{{ $ldapName }}" readonly>
                                        <label for="nombre_user">Usuario</label>
                                    @else
                                        <!-- Si es admin -->
                                        <input type="text" class="form-control" id="nombre_user" name="nombre_user"
                                            value="" required>
                                        <label for="nombre_user">Nombre del Usuario</label>
                                    @endif
                                </div>

                                <!-- Fecha del calendario -->

                                <div class="form-floating mb-3">
                                    <input type="date" class="form-control" id="fechain" name="fechain"
                                        min="{{ date('Y-m-d') }}">
                                    <label for="fechain">Fecha de Inicio *</label>
                                    <div class="invalid-feedback" id="fechain-error">
                                        No se pueden seleccionar fechas anteriores a hoy.
                                    </div>
                                </div>

                                <div class="form-floating mb-3">
                                    <input type="date" class="form-control" id="fechafin" name="fechafin"
                                        min="{{ date('Y-m-d') }}">
                                    <label for="fechafin">Fecha Final *</label>
                                    <div class="invalid-feedback" id="fechafin-error">
                                        No se pueden seleccionar fechas anteriores a hoy.
                                    </div>
                                </div>

                            </div>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                <a class="btn btn-danger text-white d-none" id="btnEliminar" name="btnEliminar"
                                    style="background-color: #d33;">Eliminar</a>
                                <button class="btn btn-primary" id="btnAccion" type="submit">Registrar</button>

                                @if ($rol != 2)
                                    <!-- Solo admin puede autorizar -->
                                    <a class="btn btn-success text-white d-none" id="btnAutorizar"
                                        name="btnAutorizar">Autorizar</a>
                                @endif
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Obtener configuración de data attributes
            const container = document.getElementById('calendar-container');
            const config = {
                sam: container.dataset.sam,
                rol: parseInt(container.dataset.rol),
                iduser: parseInt(container.dataset.iduser),
                APP_URL: container.dataset.appUrl,
                csrfToken: container.dataset.csrfToken
            };

            // Depuración en consola
            console.log('=== CONFIGURACIÓN DEL CALENDARIO ===');
            console.log('Config:', config);
            console.log('Usuario ID:', config.iduser, 'Tipo:', typeof config.iduser);
            console.log('Rol:', config.rol, 'Tipo:', typeof config.rol);

            // Inicializar modal de Bootstrap
            var myModal = new bootstrap.Modal(document.getElementById('myModal'));
            let frm = document.getElementById('formulario');
            var eliminar = document.getElementById('btnEliminar');
            var autorizar = document.getElementById('btnAutorizar');

            // ========== FUNCIONES AUXILIARES ==========

            // Establecer fecha mínima en los inputs al cargar la página
            const fechaActual = obtenerFechaActual();
            document.getElementById('fechain').min = fechaActual;
            document.getElementById('fechafin').min = fechaActual;

            // ========== CALENDARIO ==========

            // Inicializar calendario
            var calendarEl = document.getElementById('calendar');
            var calendar = new FullCalendar.Calendar(calendarEl, {
                timeZone: 'America/Mexico_City',
                initialView: 'dayGridMonth',
                locale: 'es',
                headerToolbar: {
                    left: 'prev, next, today',
                    center: 'title',
                    right: 'dayGridMonth, timeGridWeek, listWeek'
                },
                events: config.APP_URL + '/listar',

                // Al hacer clic en una fecha
                dateClick: function(info) {
                    console.log('Clic en fecha:', info.dateStr);

                    const fechaClic = info.dateStr;
                    const fechaActual = obtenerFechaActual();

                    // Validar que la fecha no sea anterior a hoy
                    if (fechaClic < fechaActual) {
                        Swal.fire({
                            title: 'Fecha no válida',
                            text: 'No puedes seleccionar fechas pasadas. Por favor, selecciona una fecha de hoy en adelante.',
                            icon: 'warning',
                            confirmButtonText: 'Aceptar'
                        });
                        return; // No abrir el modal
                    }

                    // Resetear formulario
                    frm.reset();
                    document.getElementById('id').value = "";
                    document.getElementById('btnEliminar').classList.add('d-none');

                    // Ocultar botón Autorizar si existe
                    if (autorizar) {
                        autorizar.classList.add('d-none');
                    }

                    // Establecer fechas en el formulario
                    document.getElementById('fechain').value = info.dateStr;
                    document.getElementById('fechafin').value = info.dateStr;

                    // Configurar título y botón
                    document.getElementById('titulo').textContent = 'Registro de Vacaciones';
                    document.getElementById('btnAccion').textContent = 'Registrar';
                    document.getElementById('btnAccion').classList.remove('d-none');

                    // Si el usuario es normal, establecer su nombre
                    if (config.rol === 2) {
                        document.getElementById('nombre_user').value = config.sam;
                    } else {
                        // Si es admin, limpiar el campo
                        document.getElementById('nombre_user').value = '';
                    }

                    // Mostrar modal
                    myModal.show();
                },

                // Al hacer clic en un evento
                eventClick: function(info) {
                    console.log('=== CLIC EN EVENTO ===');
                    console.log('Evento completo:', info.event);
                    console.log('Extended Props:', info.event.extendedProps);

                    var iduserCreador = parseInt(info.event.extendedProps.iduser);
                    var iduserActual = config.iduser;

                    console.log('Comparando IDs - Creador:', iduserCreador, 'Actual:', iduserActual);
                    console.log('Rol usuario:', config.rol);

                    // Verificar permisos
                    if (iduserActual === iduserCreador || config.rol === 1) {
                        console.log('PERMITIDO: Usuario puede editar');

                        // Mostrar botones según permisos
                        document.getElementById('btnEliminar').classList.remove('d-none');
                        if (config.rol === 1 && autorizar) {
                            autorizar.classList.remove('d-none');
                        }

                        // Configurar formulario con datos del evento
                        document.getElementById('titulo').textContent = 'Modificar Vacaciones';
                        document.getElementById('id').value = info.event.id;

                        // Obtener nombre del evento (eliminando la parte de "Creado el")
                        var eventTitle = info.event.title || '';
                        var nombreUsuario = eventTitle.split(' - Creado el')[0] || '';
                        document.getElementById('nombre_user').value = nombreUsuario;

                        // Formatear fechas
                        var fechaInicio = info.event.startStr.split('T')[0];
                        var fechaFin = '';

                        if (info.event.endStr) {
                            // Restar un día porque FullCalendar muestra fecha_fin + 1 día
                            var fechaFinDate = new Date(info.event.endStr);
                            fechaFinDate.setDate(fechaFinDate.getDate() - 1);
                            fechaFin = fechaFinDate.toISOString().split('T')[0];
                        } else {
                            fechaFin = fechaInicio;
                        }

                        document.getElementById('fechain').value = fechaInicio;
                        document.getElementById('fechafin').value = fechaFin;

                        // Configurar botón según estatus
                        var estatus = parseInt(info.event.extendedProps.estatus);
                        if (estatus === 1) { // Autorizado
                            document.getElementById('btnAccion').classList.add('d-none');
                        } else {
                            document.getElementById('btnAccion').classList.remove('d-none');
                            document.getElementById('btnAccion').textContent = 'Modificar';
                        }

                        // Mostrar modal
                        myModal.show();
                    } else {
                        console.log('DENEGADO: Usuario NO puede editar');
                        Swal.fire({
                            title: 'Error',
                            text: 'No tienes permiso para modificar o borrar este evento.',
                            icon: 'error',
                            confirmButtonText: 'Aceptar'
                        });
                    }
                },

                eventContent: function(arg) {
                    return {
                        html: '<div class="m-2"><b>' + arg.event.title + '</b></div>',
                    };
                }
            });

            // ========== EVENT LISTENERS PARA VALIDACIÓN EN TIEMPO REAL ==========

            // Validar fecha de inicio mientras se escribe/selecciona
            document.getElementById('fechain').addEventListener('change', function() {
                validarFechaNoPasada(this.value, 'fechain');

                // Si fecha fin es anterior a la nueva fecha inicio, actualizar
                const fechaFin = document.getElementById('fechafin').value;
                if (fechaFin && fechaFin < this.value) {
                    document.getElementById('fechafin').value = this.value;
                }

                // Actualizar min de fecha fin
                document.getElementById('fechafin').min = this.value;
            });

            // Validar fecha final mientras se escribe/selecciona
            document.getElementById('fechafin').addEventListener('change', function() {
                validarFechaNoPasada(this.value, 'fechafin');
                validarRangoFechas(
                    document.getElementById('fechain').value,
                    this.value
                );
            });

            calendar.render();

            // ========== FUNCIONES DE VALIDACIÓN DE FECHAS ==========

            // Función para obtener la fecha actual en formato YYYY-MM-DD
            function obtenerFechaActual() {
                const hoy = new Date();
                const año = hoy.getFullYear();
                const mes = String(hoy.getMonth() + 1).padStart(2, '0');
                const dia = String(hoy.getDate()).padStart(2, '0');
                return `${año}-${mes}-${dia}`;
            }

            // Función para validar que la fecha no sea anterior a hoy
            function validarFechaNoPasada(fecha, campoId) {
                const fechaActual = obtenerFechaActual();
                const inputCampo = document.getElementById(campoId);

                if (fecha < fechaActual) {
                    inputCampo.classList.add('is-invalid');
                    return false;
                } else {
                    inputCampo.classList.remove('is-invalid');
                    return true;
                }
            }

            // Función para validar que fecha fin no sea menor que fecha inicio
            function validarRangoFechas(fechaInicio, fechaFin) {
                const inputFin = document.getElementById('fechafin');

                if (fechaFin < fechaInicio) {
                    inputFin.classList.add('is-invalid');
                    document.getElementById('fechafin-error').textContent =
                        'La fecha final no puede ser anterior a la fecha de inicio.';
                    return false;
                } else {
                    inputFin.classList.remove('is-invalid');
                    return true;
                }
            }

            // Función para validar todo el formulario
            function validarFormulario() {
                const fechaInicio = document.getElementById('fechain').value;
                const fechaFin = document.getElementById('fechafin').value;

                // Validar fecha de inicio
                const inicioValido = validarFechaNoPasada(fechaInicio, 'fechain');

                // Validar fecha final
                const finValida = validarFechaNoPasada(fechaFin, 'fechafin');

                // Validar rango
                const rangoValido = validarRangoFechas(fechaInicio, fechaFin);

                return inicioValido && finValida && rangoValido;
            }

            // ========== FUNCIONES DE FORMULARIO ==========

            // Función para registrar o modificar
            function realizarRegistroOModificacion() {
                const formData = new FormData(frm);

                // Agregar iduser al formData si no está presente
                if (config.rol === 2) {
                    formData.append('iduserev', config.iduser);
                }

                console.log('Enviando datos:', {
                    id: document.getElementById('id').value,
                    nombre_user: document.getElementById('nombre_user').value,
                    fechain: document.getElementById('fechain').value,
                    fechafin: document.getElementById('fechafin').value,
                    iduserev: config.rol === 2 ? config.iduser : ''
                });

                fetch(config.APP_URL + '/registrar', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': config.csrfToken
                        }
                    })
                    .then(response => {
                        console.log('Respuesta recibida, estado:', response.status);
                        if (response.status >= 200 && response.status < 300) {
                            return response.json();
                        } else {
                            throw new Error('Error en la red. Estado: ' + response.status);
                        }
                    })
                    .then(data => {
                        console.log('Datos recibidos:', data);
                        myModal.hide();
                        Swal.fire({
                            title: 'Aviso',
                            text: data.msg,
                            icon: data.tipo === 'success' ? 'success' : 'warning',
                            confirmButtonText: 'Aceptar'
                        });
                        calendar.refetchEvents();
                    })
                    .catch(error => {
                        console.error('Error en fetch:', error.message);
                        myModal.hide();
                        Swal.fire({
                            title: 'Error',
                            text: 'Ha ocurrido un error al procesar su solicitud',
                            icon: 'error',
                            confirmButtonText: 'Aceptar'
                        });
                    });
            }

            // Evento para eliminar
            eliminar.onclick = function() {
                const idEvento = document.getElementById('id').value;

                // Cerrar primero el modal de Bootstrap
                myModal.hide();

                Swal.fire({
                    title: '¿Estás seguro?',
                    text: "¡No podrás revertir esto!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch(config.APP_URL + '/eliminarEvento/' + idEvento, {
                                method: 'DELETE',
                                headers: {
                                    'X-CSRF-TOKEN': config.csrfToken
                                }
                            })
                            .then(response => {
                                if (response.status >= 200 && response.status < 300) {
                                    return response.json();
                                } else {
                                    throw new Error('Error en la red. Estado: ' + response.status);
                                }
                            })
                            .then(data => {
                                myModal.hide();
                                Swal.fire({
                                    title: 'Aviso',
                                    text: data.msg,
                                    icon: data.tipo === 'success' ? 'success' : 'error',
                                    confirmButtonText: 'Aceptar'
                                });
                                calendar.refetchEvents();
                            })
                            .catch(error => {
                                myModal.hide();
                                console.error('Error al eliminar:', error.message);
                                Swal.fire({
                                    title: 'Error',
                                    text: 'Ha ocurrido un error al eliminar el evento',
                                    icon: 'error',
                                    confirmButtonText: 'Aceptar'
                                });
                            });
                    }
                });
            }

            // Evento para autorizar (solo admin)
            if (autorizar) {
                autorizar.onclick = function() {
                    const idEvento = document.getElementById('id').value;

                    const swalWithBootstrapButtons = Swal.mixin({
                        customClass: {
                            confirmButton: "btn btn-success mr-2",
                            cancelButton: "btn btn-danger mx-4"
                        },
                        buttonsStyling: false
                    });

                    myModal.hide();

                    swalWithBootstrapButtons.fire({
                        title: "¿Autorizar vacaciones?",
                        text: "No podrás revertir esto",
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonText: "Sí, autorizar",
                        cancelButtonText: "No, rechazar",
                        reverseButtons: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            const confirmadoData = {
                                confirmado: true,
                                iduserActual: config.iduser
                            };

                            fetch(config.APP_URL + '/autorizar/' + idEvento, {
                                    method: 'POST',
                                    headers: {
                                        'X-CSRF-TOKEN': config.csrfToken,
                                        'Content-Type': 'application/json',
                                    },
                                    body: JSON.stringify(confirmadoData),
                                })
                                .then(response => {
                                    if (response.status >= 200 && response.status < 300) {
                                        return response.json();
                                    } else {
                                        throw new Error('Error en la red. Estado: ' + response
                                            .status);
                                    }
                                })
                                .then(data => {
                                    Swal.fire({
                                        title: 'Aviso',
                                        text: data.msg,
                                        icon: data.tipo === 'success' ? 'success' : 'error',
                                        confirmButtonText: 'Aceptar'
                                    });
                                    calendar.refetchEvents();
                                })
                                .catch(error => {
                                    console.error('Error al autorizar:', error.message);
                                    Swal.fire({
                                        title: 'Error',
                                        text: 'Ha ocurrido un error al autorizar',
                                        icon: 'error',
                                        confirmButtonText: 'Aceptar'
                                    });
                                });
                        } else if (result.dismiss === Swal.DismissReason.cancel) {
                            fetch(config.APP_URL + '/autorizar/' + idEvento, {
                                    method: 'POST',
                                    headers: {
                                        'X-CSRF-TOKEN': config.csrfToken,
                                        'Content-Type': 'application/json',
                                    },
                                })
                                .then(response => {
                                    if (response.status >= 200 && response.status < 300) {
                                        return response.json();
                                    } else {
                                        throw new Error('Error en la red. Estado: ' + response
                                            .status);
                                    }
                                })
                                .then(data => {
                                    Swal.fire({
                                        title: 'Aviso',
                                        text: data.msg,
                                        icon: data.tipo === 'success' ? 'success' : 'error',
                                        confirmButtonText: 'Aceptar'
                                    });
                                    calendar.refetchEvents();
                                })
                                .catch(error => {
                                    console.error('Error al rechazar:', error.message);
                                    Swal.fire({
                                        title: 'Error',
                                        text: 'Ha ocurrido un error al procesar',
                                        icon: 'error',
                                        confirmButtonText: 'Aceptar'
                                    });
                                });
                        }
                    });
                };
            }

            // Evento submit del formulario
            frm.addEventListener('submit', function(e) {
                e.preventDefault();

                // Validar fechas antes de enviar
                if (!validarFormulario()) {
                    Swal.fire({
                        title: 'Error en fechas',
                        text: 'Por favor, corrige los errores en las fechas antes de continuar.',
                        icon: 'error',
                        confirmButtonText: 'Aceptar'
                    });
                    return;
                }

                console.log('Formulario enviado');
                realizarRegistroOModificacion();
            });

        });
    </script>
@endsection
