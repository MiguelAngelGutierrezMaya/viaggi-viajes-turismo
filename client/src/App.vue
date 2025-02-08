<template>
  <div id="app">
    <router-view />
    <notifications group="foo" position="top right" />
  </div>
</template>
<script>
export default {
  name: "App",
  created: function () {
    var session = localStorage.getItem("sess_vyt_backoffice");
    if (session == null || session == undefined) {
      if (this.$route.name != "Auth" && this.$route.name != "Restablecer") {
        this.$router.push("/auth");
      }
      return false;
    }
  },
  watch: {
    $route() {
      var session = localStorage.getItem("sess_vyt_backoffice");
      if (session == null || session == undefined) {
        if (this.$route.name != "Auth" && this.$route.name != "Restablecer") {
          this.$router.push("/auth");
        }
        return false;
      }
    },
  },
};
</script>