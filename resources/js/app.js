require('./bootstrap');
import Vue from "vue";
import PerformerTable from "./components/PerformerTable";
Vue.component('performer-table',PerformerTable);
const app = new Vue({
    el: "#app"
});

