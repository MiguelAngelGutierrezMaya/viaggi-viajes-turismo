import Vue from "vue";
import VueRouter from "vue-router";

Vue.use(VueRouter);

const routes = [
  {
    path: "/auth",
    name: "Auth",
    component: () => import("../views/Auth.vue"),
  },
  {
    path: "/restablecer",
    name: "Restablecer",
    component: () => import("../views/Restablecer.vue"),
  },
  {
    path: "/",
    name: "Main",
    component: () => import("../views/Main.vue"),
    children: [
      {
        path: "/dashboard",
        redirect: "/reservas/1",
      },
      {
        path: "/actividades",
        redirect: "/actividades/1",
      },
      {
        path: "/actividades/:page",
        name: "Actividades",
        component: () => import("../views/Actividades.vue"),
      },
      {
        path: "/actividad/:id_servicio",
        name: "Actividad",
        component: () => import("../views/Actividad.vue"),
        redirect: "/actividad/:id_servicio/basica",
        children: [
          {
            path: "/actividad/:id_servicio/basica",
            name: "Actividad_basica",
            component: () => import("../views/Actividad/Basica.vue"),
          },
          {
            path: "/actividad/:id_servicio/horarios",
            name: "Actividad_horarios",
            component: () => import("../views/Actividad/Horarios.vue"),
          },
          {
            path: "/actividad/:id_servicio/puntos_salida",
            name: "Actividad_puntos_salida",
            component: () => import("../views/Actividad/Puntos_salida.vue"),
          },
          {
            path: "/actividad/:id_servicio/modalidades",
            name: "Actividad_modalidades",
            component: () => import("../views/Actividad/Modalidades.vue"),
          },
          {
            path: "/modalidad_actividad/:id_servicio/:id_modalidad",
            name: "Modalidad_actividad",
            component: () =>
              import("../views/Actividad/Modalidad_actividad.vue"),
          },
          {
            path: "/actividad/:id_servicio/tarifas",
            name: "Tarifas_actividad",
            component: () => import("../views/Actividad/Tarifas.vue"),
          },
          {
            path: "/actividad/:id_servicio/galeria",
            name: "Galeria_actividad",
            component: () => import("../views/Actividad/Galeria.vue"),
          },
          {
            path: "/actividad/:id_servicio/recomendadas",
            name: "Recomendadas",
            component: () => import("../views/Actividad/Recomendadas.vue"),
          },
        ],
      },
      {
        path: "/tipos_actividad",
        name: "Tipos_actividad",
        component: () => import("../views/Tipos_actividad.vue"),
      },
      {
        path: "/tarifas_actividades",
        redirect: "/tarifas_actividades/1",
      },
      {
        path: "/tarifas_actividades/:page",
        name: "Tarifas_actividades",
        component: () => import("../views/Tarifas_actividades.vue"),
      },
      {
        path: "/paquetes",
        redirect: "/paquetes/1",
      },
      {
        path: "/paquetes/:page",
        name: "Paquetes",
        component: () => import("../views/Paquetes.vue"),
      },
      {
        path: "/paquete/:id_servicio",
        name: "Paquete",
        component: () => import("../views/Paquete.vue"),
        redirect: "/paquete/:id_servicio/basica",
        children: [
          {
            path: "/paquete/:id_servicio/basica",
            name: "Paquete_basica",
            component: () => import("../views/Paquete/Basica.vue"),
          },
          {
            path: "/paquete/:id_servicio/galeria",
            name: "Galeria_paquete",
            component: () => import("../views/Paquete/Galeria.vue"),
          },
          {
            path: "/paquete/:id_servicio/imagen",
            name: "Imagen_paquete",
            component: () => import("../views/Paquete/Imagen.vue"),
          },
        ],
      },
      {
        path: "/usuarios",
        redirect: "/usuarios/1",
      },
      {
        path: "/usuarios/:page",
        name: "Usuarios",
        component: () => import("../views/Usuarios.vue"),
      },
      {
        path: "/cupones",
        redirect: "/cupones/1",
      },
      {
        path: "/cupones/:page",
        name: "Cupones",
        component: () => import("../views/Cupones.vue"),
      },
      {
        path: "/proveedores",
        redirect: "/proveedores/1",
      },
      {
        path: "/proveedores/:page",
        name: "Proveedores",
        component: () => import("../views/Proveedores.vue"),
      },
      {
        path: "/reservas",
        redirect: "/reservas/1",
      },
      {
        path: "/reservas/:page",
        name: "Reservas",
        component: () => import("../views/Reservas.vue"),
      },
      {
        path: "/cotizaciones",
        redirect: "/cotizaciones/1",
      },
      {
        path: "/cotizaciones/:page",
        name: "Cotizaciones",
        component: () => import("../views/Cotizaciones.vue"),
      },
      {
        path: "/reserva/:id_reserva",
        name: "Reserva",
        component: () => import("../views/Reserva.vue"),
      },
      {
        path: "/nueva_reserva",
        name: "Nueva_reserva",
        component: () => import("../views/Nueva_reserva.vue"),
      },
      {
        path: "/perfil",
        name: "Perfil",
        component: () => import("../views/Perfil.vue"),
      },
      {
        path: "/parametros",
        name: "Parametros",
        component: () => import("../views/Parametros.vue"),
      },
      {
        path: "/popups",
        name: "Popups",
        component: () => import("../views/Popups.vue"),
      },
      {
        path: "/reviews",
        name: "Reviews",
        component: () => import("../views/Reviews.vue"),
      },
      {
        path: "/recursos",
        name: "Recursos",
        component: () => import("../views/Recursos.vue"),
      },
      {
        path: "/links",
        name: "Links",
        component: () => import("../views/Links.vue"),
      },
      {
        path: "/documentos",
        name: "Documentos",
        component: () => import("../views/Documentos.vue"),
      },
      {
        path: "/agenda",
        name: "Agenda",
        component: () => import("../views/Agenda.vue"),
      },
    ],
    redirect: "/dashboard",
  },
];

const router = new VueRouter({
  routes,
});

export default router;
