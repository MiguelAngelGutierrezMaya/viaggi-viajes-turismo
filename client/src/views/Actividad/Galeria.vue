<template>
<div id="galeria_actividad">
    <div v-if="loading"  id="loading" class="loading_full">Loading&#8230;</div>
    <div v-else>
        <h4 class="mb-4 fw-bold text-info">Galería</h4>

        <hr />
        <div v-if="tipo == 0">
            <div v-if="permiso">
                <h5 class="mb-4 fw-bold text-primary">Imágenes</h5>
                <UploadImages @changed="handle_images" :max="5" uploadMsg="Sube las imágenes para la galería" maxError="Solo se permite subir cinco imágenes a la galería." fileError="Solo se permiten imágenes en formato jpg, jpeg o png" clearAll="Quitar todas" />
                <div class="text-end mt-2">
                    <button v-on:click="upload" class="btn btn-success btn-sm" :disabled="files.length == 0">
                        <i class="fa fa-upload"></i> Subir imágenes
                    </button>
                </div>
            </div>

            <div class="mt-4 mb-3">
                <h4 class="fw-bold text-primary">Imágenes en la galería</h4>
                <div v-if="galeria.length != 0" class="row">
                    <div v-for="(imagen, index_galeria) in galeria" v-bind:key="index_galeria" class="col-lg-3 text-center">
                        <div v-if="imagen.tipo == 0">
                            <img class="img-thumbnail" v-bind:src="imagen.url" />
                            <div v-if="permiso" class="text-end">
                                <span style="font-size: 12px">
                                    <a v-if="imagen.principal == 1" v-on:click="set_principal(index_galeria)" href="javascript:void(0)" class="text-warning">
                                        <i class="fa fa-star"></i>
                                    </a>
                                    <a v-if="imagen.principal == 0" v-on:click="set_principal(index_galeria)" href="javascript:void(0)" class="text-muted">
                                        <i class="fa-regular fa-star"></i>
                                    </a>
                                </span>
                                <span class="ms-1" style="font-size: 12px">
                                    <a v-on:click="delete_img(index_galeria)" href="javascript:void(0)" class="text-danger">
                                        <i class="fa fa-trash"></i> Eliminar
                                    </a>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <div v-else class="text-muted mt-4">
                    <i class="fa fa-info-circle"></i> No se han agregado imágenes a la
                    galería.
                </div>
            </div>
        </div>
        <div v-if="tipo == 1">
            <div class="card card-body shadow mb-4">
                <div class="form-group">
                    <label>Agregar video</label>
                    <input v-model="nuevo_video" class="form-control" placeholder="Pega aquí el id del video" />
                </div>
                <div class="text-end">
                    <button v-on:click="set_video()" class="btn btn-success btn-rounded btn-sm" :disabled="nuevo_video == null || nuevo_video.trim().length == 0">
                        <i class="fa fa-plus-circle"></i> Agregar
                    </button>
                </div>
            </div>
            <table v-if="videos.length != 0" class="table table-bordered table-condensed table-hover">
                <thead>
                    <tr>
                        <th class="text-center">#</th>
                        <th>Vídeo</th>
                        <th class="text-center">Opciones</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(video, index_video) in videos" v-bind:key="index_video">
                        <td class="text-center">{{ index_video + 1 }}</td>
                        <td class="text-center">
                            <iframe width="300" height="250" v-bind:src="video.url" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>
                        </td>
                        <td class="text-center">
                            <button v-on:click="eliminar_video(video.id_galeria)" class="btn btn-danger btn-sm">
                                <i class="fa fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
            <div v-else>
                <span class="text-muted"><i class="fa fa-info-circle"></i> No se han agregado videos.</span>
            </div>
        </div>
    </div>
</div>
</template>

<script>
import axios from "axios";
import UploadImages from "vue-upload-drop-images";
import "vue-select/dist/vue-select.css";
export default {
    name: "Galeria_actividad",
    metaInfo: {
        title: "Galeria de la actividad - Backoffice",
    },
    components: {
        UploadImages,
    },
    data() {
        return {
            loading: true,
            loading_upload: false,
            id_servicio: null,
            id_actividad: null,
            galeria: [],
            videos: [],
            files: [],
            tipo: 0,
            nuevo_video: null,
        };
    },
    props: {
        session: Object,
        actividad: Object,
        permiso: Boolean,
    },
    created: function () {
        this.id_servicio = this.$route.params.id_servicio;
        this.id_actividad = this.actividad.id_actividad;
        this.get_galeria();
    },
    methods: {
        get_galeria: function () {
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
                .get(vm.$base_url + "servicios/galeria/" + this.id_servicio, config, {
                    headers: headers,
                })
                .then((response) => {
                    if (response.data.status) {
                        vm.galeria = response.data.data.galeria;
                        vm.videos = response.data.data.videos;
                    } else {
                        vm.$notify({
                            group: "foo",
                            type: "error",
                            title: "Actividad",
                            text: response.data.message,
                        });
                    }
                    vm.loading = false;
                })
                .catch((error) => {
                    vm.$notify({
                        group: "foo",
                        type: "error",
                        title: "Actividad",
                        text: error,
                    });
                });
        },
        handle_images(files) {
            this.files = files;
        },
        upload: function () {
            const vm = this;
            vm.loading = true;
            var formData = new FormData();
            for (let file of this.files) {
                formData.append("files[]", file);
            }
            formData.append("token", vm.session.usuario.token_session);
            formData.append("id_servicio", vm.id_servicio);
            axios
                .post(this.$base_url + "upload/galeria_actividad", formData, {
                    headers: {
                        "Content-Type": "multipart/form-data",
                    },
                })
                .then((response) => {
                    if (response.data.status) {
                        location.reload();
                    } else {
                        vm.$notify({
                            group: "foo",
                            type: "error",
                            title: "Actividades",
                            text: response.data.message,
                        });
                        vm.loading = false;
                    }
                })
                .catch((error) => {
                    vm.$notify({
                        group: "foo",
                        type: "error",
                        title: "Actividades",
                        text: error,
                    });
                    console.log(error);
                    vm.loading_upload = false;
                });
        },
        delete_img: function (index_galeria) {
            const vm = this;
            vm.loading = true;
            const headers = {
                "Content-Type": "application/json",
                "x-api-key": this.$api_key,
                Token: vm.session.usuario.token_session,
            };
            let params = {
                id_galeria: this.galeria[index_galeria].id_galeria,
            };
            axios
                .post(this.$base_url + "servicios/delete_imagen", params, {
                    headers: headers,
                })
                .then((response) => {
                    if (!response.data.status) {
                        vm.$notify({
                            group: "foo",
                            type: "error",
                            title: "Actividades",
                            text: response.data.message,
                        });
                    } else {
                        vm.$notify({
                            group: "foo",
                            type: "success",
                            title: "Actividades",
                            text: "Se ha eliminado la imagen de la galería.",
                        });
                    }
                    vm.get_galeria();
                })
                .catch((error) => {
                    vm.$notify({
                        group: "foo",
                        type: "error",
                        title: "Actividades",
                        text: error,
                    });
                })
                .finally();
        },
        set_principal: function (index_galeria) {
            const vm = this;
            vm.loading = true;
            const headers = {
                "Content-Type": "application/json",
                "x-api-key": this.$api_key,
                Token: vm.session.usuario.token_session,
            };
            let params = {
                id_galeria: this.galeria[index_galeria].id_galeria,
            };
            axios
                .post(this.$base_url + "servicios/set_imagen_principal", params, {
                    headers: headers,
                })
                .then((response) => {
                    if (!response.data.status) {
                        vm.$notify({
                            group: "foo",
                            type: "error",
                            title: "Actividades",
                            text: response.data.message,
                        });
                    } else {
                        vm.$notify({
                            group: "foo",
                            type: "success",
                            title: "Actividades",
                            text: "Se ha definido la imagen como la principal.",
                        });
                    }
                    vm.get_galeria();
                })
                .catch((error) => {
                    vm.$notify({
                        group: "foo",
                        type: "error",
                        title: "Actividades",
                        text: error,
                    });
                })
                .finally();
        },
        set_video: function () {
            const vm = this;
            const headers = {
                "Content-Type": "application/json",
                "x-api-key": this.$api_key,
                Token: vm.session.usuario.token_session,
            };
            let params = {
                id_video: this.nuevo_video,
                id_servicio: this.id_servicio,
            };
            axios
                .post(this.$base_url + "servicios/set_video_galeria", params, {
                    headers: headers,
                })
                .then((response) => {
                    if (!response.data.status) {
                        vm.$notify({
                            group: "foo",
                            type: "error",
                            title: "Actividades",
                            text: response.data.message,
                        });
                    } else {
                        vm.$notify({
                            group: "foo",
                            type: "success",
                            title: "Actividades",
                            text: "Se ha agregado el video a la galería.",
                        });
                    }
                    vm.get_galeria();
                })
                .catch((error) => {
                    vm.$notify({
                        group: "foo",
                        type: "error",
                        title: "Actividades",
                        text: error,
                    });
                })
                .finally();
        },
        eliminar_video: function (id_galeria) {
            const vm = this;
            const headers = {
                "Content-Type": "application/json",
                "x-api-key": this.$api_key,
                Token: vm.session.usuario.token_session,
            };
            let params = {
                id_galeria: id_galeria,
            };
            axios
                .post(this.$base_url + "servicios/eliminar_video_galeria", params, {
                    headers: headers,
                })
                .then((response) => {
                    if (!response.data.status) {
                        vm.$notify({
                            group: "foo",
                            type: "error",
                            title: "Actividades",
                            text: response.data.message,
                        });
                    } else {
                        vm.$notify({
                            group: "foo",
                            type: "success",
                            title: "Actividades",
                            text: "Se ha eliminado el video de la galería.",
                        });
                    }
                    vm.get_galeria();
                })
                .catch((error) => {
                    vm.$notify({
                        group: "foo",
                        type: "error",
                        title: "Actividades",
                        text: error,
                    });
                })
                .finally();
        },
    },
};
</script>
