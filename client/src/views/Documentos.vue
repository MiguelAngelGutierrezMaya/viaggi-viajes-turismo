<template>
    <div id="documentos">
        <div class="card">
            <div class="card-body">
                <h4 class="fw-bolder mb-4">Documentos</h4>
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
                        <div v-if="permiso" class="text-end">
                            <button data-bs-toggle="modal" data-bs-target="#nuevo"
                                class="btn btn-success btn-sm btn-rounded btn-icon-text">
                                <i class="fa fa-plus-circle"></i> Nuevo
                            </button>
                        </div>

                        <div v-if="id_carpeta != null && id_carpeta != 0">
                            <div class="mt-2 mb-4">
                                <a @click="back(carpeta)" href="javascript:void(0)">
                                    <i class="fa fa-chevron-left"></i>
                                    Regresar
                                </a>
                            </div>
                            <h4 class="fw-bold text-muted">{{ carpeta.carpeta }}</h4>
                            <small>{{ carpeta.descripcion }}</small>
                            <hr>
                        </div>

                        <div v-if="documentos.length != 0" class="mt-4 row">
                            <div v-for="(documento, index) in documentos" v-bind:key="index" class="col-lg-3">
                                <div class="card card-body card-link border mb-3">
                                    <div @click="go(documento)" class=" card-link mb-0 text-primary">
                                        <i v-if="documento.tipo == 0" class="fa-solid fa-folder fa-2x me-2"></i>
                                        <i v-if="documento.tipo == 1" class="fa-regular fa-file-lines fa-2x me-2"></i>
                                        <small>{{ documento.nombre }}</small>
                                    </div>
                                    <div v-if="permiso" class="text-end  mb-0">
                                        <small>
                                            <a @click="index_sel = index" data-bs-toggle="modal" data-bs-target="#eliminar"
                                                href="javascript:void(0)" class="text-danger">
                                                <i class="fa fa-trash"></i>
                                            </a>
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div v-else>
                            <div class="text-muted">
                                <i class="fa-solid fa-circle-info"></i> No se han agregado
                                documentos.
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
                        <h5 class="modal-title fw-bolder">Nuevo</h5>
                        <button id="close_nuevo" type="button" class="close" data-bs-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">

                        <div class="form-group">
                            <label>Tipo</label>
                            <select v-model="nuevo.tipo" class="form-control">
                                <option value="0">Carpeta</option>
                                <option value="1">Archivo</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Nombre </label>
                            <input v-model="nuevo.nombre" type="text" class="form-control" />
                        </div>

                        <div class="form-group">
                            <label>Descripción</label>
                            <textarea v-model="nuevo.descripcion" class="form-control" />
                        </div>

                        <div v-if="nuevo.tipo == 1" class="form-group">
                            <label>Archivo </label>
                            <input @change="handle_images" type="file" class="form-control" />
                        </div>

                        <div class="text-end">
                            <button v-on:click="set_documento" class="btn btn-success btn-rounded btn-sm btn-icon-text">
                                <i class="fa fa-save"></i> Guardar
                            </button>
                            <button data-bs-dismiss="modal"
                                class="btn btn-outline-dark btn-rounded btn-sm btn-icon-text ms-1">
                                Cancelar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="eliminar">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header text-success">
                        <h5 class="modal-title fw-bolder text-danger">Eliminar documento</h5>
                        <button id="close_eliminar" type="button" class="close" data-bs-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div v-if="index_sel != null" class="modal-body">
                        <div class="text-danger">
                            ¿Desea eliminar
                            <span v-if="documentos[index_sel].tipo == 0">la carpeta</span>
                            <span v-if="documentos[index_sel].tipo == 1">el archivo</span>
                            <strong>
                                {{ documentos[index_sel].nombre }}
                            </strong>?
                            <span v-if="documentos[index_sel].tipo == 0">Esto eliminará las subcarpetas y archivo dentro de
                                ella.</span>
                        </div>
                        <div class="text-end mt-4">
                            <button v-on:click="delete_documento" class="btn btn-danger btn-rounded btn-sm btn-icon-text">
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
    name: "Documentos",
    metaInfo: {
        title: "Documentos - Backoffice",
    },
    data() {
        return {
            loading: true,
            id_carpeta: null,
            carpeta: null,
            documentos: [],
            search: null,
            nuevo: {
                tipo: 0,
                nombre: null,
                descripcion: null,
            },
            file: [],
            hay_archivo: false,
            files: [],
            index_sel: null,
            loading_upload: false,
            permiso: false,
        };
    },
    props: {
        session: Object,
    },
    created: function () {
        this.get_documentos();
        this.permiso = this.session.usuario.usuario.permisos.GSERVICIOS;
    },
    methods: {
        get_documentos: function () {
            const vm = this;


            const headers = {
                "Content-Type": "application/json",
                "x-api-key": vm.$api_key,
                Token: vm.session.usuario.token_session,
            };
            let config = {
                headers: headers,
                params: {
                    page: this.page,
                    id_carpeta: this.id_carpeta,
                },
            };
            axios
                .get(vm.$base_url + "documentos/documentos", config, {
                    headers: headers,
                })
                .then((response) => {
                    if (response.data.status) {
                        vm.carpeta = response.data.data.carpeta;
                        vm.documentos = response.data.data.documentos;
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
        go(documento) {
            if (documento.tipo == 0) {
                this.id_carpeta = documento.id_documento;
                this.get_documentos();
            } else {
                window.open(this.$base_url + documento.url, "_blank");
            }

        },
        back(carpeta) {
            this.id_carpeta = carpeta.id_carpeta;
            this.get_documentos();
        },
        handle_images(e) {
            var files = e.target.files || e.dataTransfer.files;
            if (!files.length) {
                this.hay_archivo = false;
                return;
            }
            this.file = files;
            this.hay_archivo = true;
        },
        set_documento: function () {
            this.hay_archivo = false;
            this.loading_upload = true;
            var formData = new FormData();
            formData.append("files", this.file[0]);
            formData.append("token", this.session.usuario.token_session);
            formData.append("id_carpeta", this.id_carpeta);
            formData.append("tipo", this.nuevo.tipo);
            formData.append("nombre", this.nuevo.nombre);
            formData.append("descripcion", this.nuevo.descripcion);
            const vm = this;
            axios
                .post(this.$base_url + "upload/documento", formData, {
                    headers: {
                        "Content-Type": "multipart/form-data",
                    },
                })
                .then((response) => {
                    if (response.data.status) {
                        vm.hay_archivo = false;
                        vm.$notify({
                            group: "foo",
                            type: "success",
                            title: "Excelente!",
                            text: "Se ha creado el recurso.",
                        });
                        vm.nuevo = {
                            tipo: 0,
                            nombre: null,
                            descripcion: null,
                        };
                        vm.file = [];
                        vm.hay_archivo = false;
                        vm.files = [];
                        vm.loading_upload = false;
                        vm.get_documentos();
                    } else {
                        vm.$notify({
                            group: "foo",
                            type: "error",
                            title: "Ups!",
                            text: response.data.message,
                        });
                        vm.loading_upload = false;
                    }
                })
                .catch((error) => {
                    vm.$notify({
                        group: "foo",
                        type: "error",
                        title: "Ups",
                        text: error,
                    });
                    console.log(error);
                    vm.loading_upload = false;
                });
        },
        delete_documento: function () {
            const vm = this;
            vm.loading = true;
            const headers = {
                "Content-Type": "application/json",
                "x-api-key": this.$api_key,
                Token: vm.session.usuario.token_session,
            };
            let params = {
                id_documento: this.documentos[this.index_sel].id_documento,
            };
            axios
                .post(this.$base_url + "documentos/delete_documento", params, {
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
                            title: "Directorio",
                            text: response.data.message,
                        });
                    }
                    vm.get_documentos();
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
  