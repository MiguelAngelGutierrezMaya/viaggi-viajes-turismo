import Vue from "vue";
import App from "./App.vue";
import router from "./router";
Vue.config.productionTip = false;
import axios from "axios";
Vue.use(VueAxios, axios);
import VueAxios from "vue-axios";
import VueMeta from "vue-meta";
Vue.use(VueMeta);
import Notifications from "vue-notification";
Vue.use(Notifications);
import vSelect from "vue-select";
Vue.component("v-select", vSelect);
import VueCurrencyFilter from "vue-currency-filter";
Vue.use(VueCurrencyFilter, {
  symbol: "$",
  thousandsSeparator: ".",
  fractionCount: 0,
  fractionSeparator: ",",
  symbolPosition: "front",
  symbolSpacing: true,
});
import money from "v-money";
Vue.use(money, { precision: 0 });
import vmodal from "vue-js-modal";
Vue.use(vmodal);

// Environment variables
Vue.prototype.$api_key = process.env.VUE_APP_API_KEY;
Vue.prototype.$editor_api_key = process.env.VUE_APP_EDITOR_API_KEY;
Vue.prototype.$base_url = process.env.VUE_APP_BASE_URL;
Vue.prototype.$base_url_shop = process.env.VUE_APP_BASE_URL_SHOP;

console.log(process.env);

new Vue({
  router,
  render: (h) => h(App),
}).$mount("#app");
