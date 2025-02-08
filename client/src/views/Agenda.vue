<template>
    <div id="links">
        <div class="card">
            <div class="card-body">
                <h4 class="fw-bolder mb-4">Agenda de videollamadas</h4>
                <div class="text-right"></div>
                <div v-if="loading" class="text-center">
                    <div class="loadingio-spinner-ripple-6armq16mc04">
                        <div class="ldio-2tzb8vdk7pu">
                            <div></div>
                            <div></div>
                        </div>
                    </div>
                </div>
                <div v-else>
                    <div>
                        <div class="text-end">
                            <button v-on:click="(nuevo_success = false), (nuevo_link = null)" data-bs-toggle="modal"
                                data-bs-target="#nuevo" class="btn btn-success btn-sm btn-rounded btn-icon-text">
                                <i class="fa fa-plus-circle"></i> Nueva
                            </button>
                        </div>
                        <div class="input-group mt-2 mb-2 mr-sm-2">
                            <input v-model="fecha" v-on:change="get_agenda" type="date" class="form-control" />
                        </div>
                        <div v-if="agenda.length != 0" class="mt-3">
                            <div class="card card-body border shadow mb-3 p-2" v-for="(reunion, index) in agenda"
                                v-bind:key="index">
                                <div class="row mt-2">
                                    <div class="col-lg-1 pt-3  text-center">
                                        <i class="fa fa-calendar fw-bold text-primary" style="font-size: 1.3em"></i>
                                    </div>
                                    <div class="col-lg-4">
                                        <small>Cliente</small>
                                        <h6 class="text-primary mb-0">{{ reunion.cliente }}</h6>
                                        <small>Asesor</small>
                                        <h6 class="text-primary mb-0">{{ reunion.usuario }}</h6>
                                    </div>
                                    <div class="col-lg-4">
                                        <small>Fecha</small>
                                        <h6 class="text-primary mb-0">{{ reunion.fecha_format }}</h6>
                                    </div>
                                    <div class="col-lg-3">
                                        <small>Hora</small>
                                        <h6 class="text-primary mb-0">{{ reunion.hora }}</h6>
                                    </div>
                                </div>
                                <div class="text-end mt-1">
                                    <a v-if="parametros != null" :href="reunion.url"
                                        class="btn btn-info btn-rounded btn-xs ms-1" target="_blank">
                                        <i class="fa fa-link"></i>
                                    </a>
                                    <button @click="index_sel = index" data-bs-toggle="modal" data-bs-target="#editar"
                                        class="btn btn-primary btn-rounded btn-xs ms-1" title="Editar">
                                        <i class="fa fa-edit"></i>
                                    </button>
                                    <button @click="index_sel = index" data-bs-toggle="modal" data-bs-target="#eliminar"
                                        class="btn btn-danger btn-rounded btn-xs ms-1" title="Eliminar">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div v-else>
                            <div class="text-muted">
                                <i class="fa-solid fa-circle-info"></i> No se ha agregado reuniones.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="nuevo">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header text-success">
                        <h5 class="modal-title fw-bolder">Nueva reunión</h5>
                        <button id="close_nuevo" type="button" class="close" data-bs-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div v-if="cliente == null">
                            <div v-if="!crear_cliente">
                                <div v-if="!cliente_encontrado">
                                    <div class="form-group">
                                        <label>Número de identificación</label>
                                        <input v-on:keyup.enter="search_cliente" v-model="identificacion_cliente"
                                            type="text" class="form-control" />
                                    </div>
                                    <div class="text-end">
                                        <button v-on:click="search_cliente" :disabled="identificacion_cliente == null"
                                            class="btn btn-primary btn-sm rounded-pill">
                                            Siguiente <i class="fa fa-chevron-right"></i>
                                        </button>
                                    </div>
                                </div>
                                <div v-else>
                                    <div class="card card-body mb-4">
                                        <div class="mb-3">
                                            <h6 class="text-primary fw-bold">Cliente encontrado</h6>
                                        </div>
                                        <div>
                                            <small>Cliente</small>
                                            <h6 class="fw-bold">
                                                {{ cliente_sel.nombres }} {{ cliente_sel.apellidos }}
                                            </h6>
                                            <small>Email</small>
                                            <h6 class="fw-bold">
                                                {{ cliente_sel.email }}
                                            </h6>
                                        </div>
                                    </div>
                                    <div class="float-start">
                                        <button v-on:click="cliente_encontrado = false"
                                            class="btn btn-primary btn-sm rounded-pill">
                                            <i class="fa fa-chevron-left"></i> Atrás
                                        </button>
                                    </div>
                                    <div class="float-end">
                                        <button v-on:click="select_cliente" class="btn btn-success btn-sm rounded-pill">
                                            <i class="fa fa-save"></i> Seleccionar cliente
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div v-else>
                                <div class="mb-3">
                                    <h6 class="text-primary fw-bold">Registar nuevo cliente</h6>
                                </div>
                                <div class="form-group">
                                    <label>Tipo de identificación</label>
                                    <select v-model="nuevo_cliente.tipo_identificacion" class="form-control">
                                        <option value="CC">CC</option>
                                        <option value="CE">CE</option>
                                        <option value="PAS">PAS</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Nombres</label>
                                    <input v-model="nuevo_cliente.nombres" type="text" class="form-control" />
                                </div>
                                <div class="form-group">
                                    <label>Apellidos</label>
                                    <input v-model="nuevo_cliente.apellidos" type="text" class="form-control" />
                                </div>
                                <div class="form-group">
                                    <label>Email</label>
                                    <input v-model="nuevo_cliente.email" type="text" class="form-control" />
                                </div>
                                <div class="form-group">
                                    <label>Teléfono</label>
                                    <input v-model="nuevo_cliente.telefono" type="text" class="form-control" />
                                </div>
                                <div class="float-start">
                                    <button v-on:click="crear_cliente = false" class="btn btn-primary btn-sm rounded-pill">
                                        <i class="fa fa-chevron-left"></i> Atrás
                                    </button>
                                </div>
                                <div class="float-end">
                                    <button v-on:click="set_cliente" class="btn btn-success btn-sm rounded-pill">
                                        <i class="fa fa-save"></i> Guardar
                                    </button>
                                    <button data-bs-dismiss="modal" class="btn btn-outline-dark btn-sm rounded-pill ms-1">
                                        Cancelar
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div v-else>
                            <small>Cliente</small>
                            <h6 class="fw-bold">
                                {{ cliente.nombres }} {{ cliente.apellidos }}
                            </h6>
                            <div class="form-group">
                                <label>Fecha</label>
                                <input v-model="nuevo.fecha" class="form-control" type="date">
                            </div>
                            <div class="form-group">
                                <label>Hora</label>
                                <input v-model="nuevo.hora" class="form-control" type="time">
                            </div>
                            <div class="form-group">
                                <label>Url</label>
                                <input v-model="nuevo.url" class="form-control" type="text">
                            </div>
                            <div class="form-group">
                                <label>Observaciones</label>
                                <textarea v-model="nuevo.observaciones" class="form-control"></textarea>
                            </div>
                            <div class="text-end">
                                <button @click="set_reunion" class="btn btn-primary btn-sm rounded-pill">
                                    <i class="fa fa-save"></i> Guardar
                                </button>
                                <button @click="cliente = null, cliente_encontrado = false, identificacion_cliente = null"
                                    data-bs-dismiss="modal" class="btn btn-outline-dark btn-sm rounded-pill ms-1">
                                    Cancelar </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="editar">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header text-success">
                        <h5 class="modal-title fw-bolder">Editar reunión</h5>
                        <button id="close_editar" type="button" class="close" data-bs-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div v-if="index_sel != null" class="modal-body">
                        <small>Cliente</small>
                        <h6 class="fw-bold">
                            {{ agenda[index_sel].nombres }} {{ agenda[index_sel].apellidos }}
                        </h6>
                        <div class="form-group">
                            <label>Fecha</label>
                            <input v-model="agenda[index_sel].fecha" class="form-control" type="date">
                        </div>
                        <div class="form-group">
                            <label>Hora</label>
                            <input v-model="agenda[index_sel].hora" class="form-control" type="time">
                        </div>
                        <div class="form-group">
                            <label>Url</label>
                            <input v-model="agenda[index_sel].url" class="form-control" type="text">
                        </div>
                        <div class="form-group">
                            <label>Observaciones</label>
                            <textarea v-model="agenda[index_sel].observaciones" class="form-control"></textarea>
                        </div>
                        <div class="text-end">
                            <button @click="update_reunion" class="btn btn-primary btn-sm rounded-pill">
                                <i class="fa fa-save"></i> Guardar
                            </button>
                            <button data-bs-dismiss="modal" class="btn btn-outline-dark btn-sm rounded-pill ms-1">
                                Cancelar </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="eliminar">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header text-success">
                        <h5 class="modal-title fw-bolder text-danger">Eliminar link</h5>
                        <button id="close_eliminar" type="button" class="close" data-bs-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div v-if="index_sel != null" class="modal-body">
                        <div class="text-danger">
                            ¿Desea eliminar la reunión con el cliente
                            <strong>
                                {{ agenda[index_sel].cliente }}
                            </strong>
                            ?
                        </div>
                        <div class="text-end mt-4">
                            <button v-on:click="delete_link" class="btn btn-danger btn-rounded btn-sm btn-icon-text">
                                Sí
                            </button>
                            <button data-bs-dismiss="modal"
                                class="btn btn-outline-dark btn-rounded btn-sm btn-icon-text ms-1">
                                No
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import axios from "axios";
import "vue-select/dist/vue-select.css";
export default {
    name: "Agenda",
    metaInfo: {
        title: "Agenda - Backoffice",
    },
    data() {
        return {
            loading: true,
            agenda: [],
            fecha: null,
            identificacion_cliente: null,
            cliente: null,
            cliente_encontrado: false,
            cliente_sel: null,
            crear_cliente: false,
            nuevo_cliente: {
                tipo_identificacion: null,
                nombres: null,
                apellidos: null,
                email: null,
            },
            nuevo: {
                id_cliente: null,
                fecha: this.fecha,
                hora: null,
                url: null,
                observaciones: null,
            },
            nuevo_success: false,
            index_sel: null,
            parametros: null,
            permiso: false,
        };
    },
    props: {
        session: Object,
    },
    created: function () {
        this.permiso = this.session.usuario.usuario.permisos.GSERVICIOS;
        let date = new Date()
        let day = date.getDate()
        let month = date.getMonth() + 1
        let year = date.getFullYear()
        month < 10 ? this.fecha = (`${year}-0${month}-${day}`) : (`${year}-${month}-${day}`);
        this.nuevo.fecha = this.fecha;
        this.get_agenda();
    },
    methods: {
        search_cliente: function () {
            const vm = this;
            const headers = {
                "Content-Type": "application/json",
                "x-api-key": vm.$api_key,
                Token: vm.session.usuario.token_session,
            };
            let config = {
                headers: headers,
                params: {
                    identificacion: this.identificacion_cliente,
                },
            };
            axios
                .get(vm.$base_url + "clientes/cliente_identificacion", config, {
                    headers: headers,
                })
                .then((response) => {
                    if (response.data.status) {
                        vm.cliente_sel = response.data.data.cliente;
                        vm.cliente_encontrado = true;
                    } else {
                        vm.crear_cliente = true;
                    }
                    vm.loading = false;
                })
                .catch((error) => {
                    vm.$notify({
                        group: "foo",
                        type: "error",
                        title: "Nueva reserva",
                        text: error,
                    });
                    vm.loading = false;
                });
        },
        set_cliente: function () {
            const vm = this;
            var valida = true;
            if (this.nuevo_cliente.nombres == null) {
                vm.$notify({
                    group: "foo",
                    type: "error",
                    title: "Crear reserva",
                    text: "Digite los nombres del cliente",
                });
                valida = false;
            }
            if (this.nuevo_cliente.apellidos == null) {
                vm.$notify({
                    group: "foo",
                    type: "error",
                    title: "Crear reserva",
                    text: "Digite los nombres del cliente",
                });
                valida = false;
            }
            if (this.nuevo_cliente.email == null) {
                vm.$notify({
                    group: "foo",
                    type: "error",
                    title: "Crear reserva",
                    text: "Digite el correo electrónico del cliente",
                });
                valida = false;
            }
            if (this.nuevo_cliente.telefono == null) {
                vm.$notify({
                    group: "foo",
                    type: "error",
                    title: "Crear reserva",
                    text: "Digite el teléfono del cliente",
                });
                valida = false;
            }
            if (!valida) {
                return false;
            }
            const headers = {
                "Content-Type": "application/json",
                "x-api-key": this.$api_key,
                Token: vm.session.usuario.token_session,
            };
            let params = {
                tipo_identificacion: this.nuevo_cliente.tipo_identificacion,
                identificacion: this.identificacion_cliente,
                nombres: this.nuevo_cliente.nombres,
                apellidos: this.nuevo_cliente.apellidos,
                email: this.nuevo_cliente.email,
                telefono: this.nuevo_cliente.telefono,
            };
            axios
                .post(this.$base_url + "clientes/set_cliente", params, {
                    headers: headers,
                })
                .then((response) => {
                    if (!response.data.status) {
                        vm.$notify({
                            group: "foo",
                            type: "error",
                            title: "Error",
                            text: response.data.message,
                        });
                    } else {
                        vm.$notify({
                            group: "foo",
                            type: "success",
                            title: "Crear reserva",
                            text: response.data.message,
                        });
                        vm.cliente = response.data.data.cliente;
                        localStorage.setItem("cliente", JSON.stringify(vm.cliente));
                        document.getElementById("close_modal_cliente").click();
                    }
                })
                .catch((error) => {
                    vm.$notify({
                        group: "foo",
                        type: "error",
                        title: "Error",
                        text: error,
                    });
                })
                .finally();
        },
        select_cliente: function () {
            this.cliente = this.cliente_sel;
        },
        get_agenda: function () {
            this.nuevo.fecha = this.fecha;
            const vm = this;
            const headers = {
                "Content-Type": "application/json",
                "x-api-key": vm.$api_key,
                Token: vm.session.usuario.token_session,
            };
            let config = {
                headers: headers,
                params: {
                    fecha: this.fecha,
                },
            };
            axios
                .get(vm.$base_url + "agenda/agenda", config, {
                    headers: headers,
                })
                .then((response) => {
                    if (response.data.status) {
                        vm.agenda = response.data.data.agenda;
                        vm.get_parametros();
                    } else {
                        vm.$notify({
                            group: "foo",
                            type: "error",
                            title: "Error",
                            text: response.data.message,
                        });
                    }
                    vm.loading = false;
                })
                .catch((error) => {
                    vm.$notify({
                        group: "foo",
                        type: "error",
                        title: "Error",
                        text: error,
                    });
                    vm.loading = false;
                });
        },
        get_parametros: function () {
            const vm = this;
            const headers = {
                "Content-Type": "application/json",
                "x-api-key": vm.$api_key,
                Token: vm.session.usuario.token_session,
            };
            let config = {
                headers: headers,
            };
            axios
                .get(vm.$base_url + "admin/parametros", config, {
                    headers: headers,
                })
                .then((response) => {
                    if (response.data.status) {
                        vm.parametros = response.data.data.parametros;
                    } else {
                        vm.$notify({
                            group: "foo",
                            type: "error",
                            title: "Ajustes",
                            text: response.data.message,
                        });
                    }
                    vm.loading = false;
                })
                .catch((error) => {
                    vm.$notify({
                        group: "foo",
                        type: "error",
                        title: "Ajustes",
                        text: error,
                    });
                    vm.loading = false;
                });
        },
        set_reunion: function () {
            const vm = this;
            vm.nuevo_success = false;
            var valida = true;

            if (this.nuevo.fecha == null) {
                vm.$notify({
                    group: "foo",
                    type: "error",
                    title: "Error",
                    text: "Selecciona la fecha.",
                });
                valida = false;
            }

            if (this.nuevo.hora == null) {
                vm.$notify({
                    group: "foo",
                    type: "error",
                    title: "Error",
                    text: "Selecciona la hora.",
                });
                valida = false;
            }

            if (!valida) {
                return false;
            }
            const headers = {
                "Content-Type": "application/json",
                "x-api-key": this.$api_key,
                Token: vm.session.usuario.token_session,
            };
            let params = {
                id_cliente: this.cliente.id_cliente,
                fecha: this.nuevo.fecha,
                hora: this.nuevo.hora,
                url: this.nuevo.url,
                observaciones: this.nuevo.observaciones
            };
            axios
                .post(this.$base_url + "agenda/set_reunion", params, {
                    headers: headers,
                })
                .then((response) => {
                    if (!response.data.status) {
                        vm.$notify({
                            group: "foo",
                            type: "error",
                            title: "Error",
                            text: response.data.message,
                        });
                    } else {
                        vm.$notify({
                            group: "foo",
                            type: "success",
                            title: "Agenda",
                            text: response.data.message,
                        });
                        vm.nuevo = {
                            id_cliente: null,
                            fecha: null,
                            hora: null,
                            observaciones: null,
                        };
                        vm.get_agenda();
                    }
                })
                .catch((error) => {
                    vm.error_manager(error);
                })
                .finally();
        },
        update_reunion: function () {
            const vm = this;
            vm.loading = true;
            const headers = {
                "Content-Type": "application/json",
                "x-api-key": this.$api_key,
                Token: vm.session.usuario.token_session,
            };
            var params = {
                id_agenda: this.agenda[this.index_sel].id_agenda,
                fecha: this.agenda[this.index_sel].fecha,
                hora: this.agenda[this.index_sel].hora,
                url: this.agenda[this.index_sel].url,
                observaciones: this.agenda[this.index_sel].observaciones
            };
            axios
                .post(this.$base_url + "agenda/update_reunion", params, {
                    headers: headers,
                })
                .then((response) => {
                    if (!response.data.status) {
                        vm.$notify({
                            type: "error",
                            group: "foo",
                            title: "Error",
                            text: response.data.message,
                        });
                    } else {
                        vm.$notify({
                            type: "success",
                            group: "foo",
                            title: "Bien!",
                            text: response.data.message,
                        });
                        vm.get_agenda();
                        document.getElementById("close_editar").click();
                    }
                    vm.loading = false;
                })
                .catch((error) => {
                    vm.error_manager(error);
                })
                .finally();
        },
        delete_link: function () {
            const vm = this;
            vm.loading = true;
            const headers = {
                "Content-Type": "application/json",
                "x-api-key": this.$api_key,
                Token: vm.session.usuario.token_session,
            };
            let params = {
                id_agenda: this.agenda[this.index_sel].id_agenda,
            };
            axios
                .post(this.$base_url + "agenda/delete_reunion", params, {
                    headers: headers,
                })
                .then((response) => {
                    if (!response.data.status) {
                        vm.$notify({
                            group: "foo",
                            type: "error",
                            title: "Error",
                            text: response.data.message,
                        });
                    } else {
                        vm.$notify({
                            group: "foo",
                            type: "success",
                            title: "Reunión eliminada",
                            text: response.data.message,
                        });
                    }
                    vm.get_agenda();
                    vm.index_sel = null;
                    vm.loading = false;
                    document.getElementById("close_eliminar").click();
                })
                .catch((error) => {
                    vm.error_manager(error);
                })
                .finally();
        },
        error_manager: function (error) {
            let vm = this;
            let message = error;
            if (error.response.status != undefined && error.response.status == 401) {
                message = error.response.data.message;
                localStorage.removeItem("sess_vyt_backoffice");
                location.reload();
            }
            vm.$notify({
                group: "foo",
                type: "error",
                title: "Error",
                text: message,
            });
        },
    },
};
</script>
  