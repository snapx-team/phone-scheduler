import Vue from "vue";
import Router from "vue-router";
import Kanban from "../components/kanban/Kanban";
import Dashboard from "../components/dashboard/Dashboard";

Vue.use(Router);

export default new Router({
    mode: "history",

    routes: [
        {
            path: "/phone-scheduler/",
            redirect: "phone-scheduler/dashboard"
        },
        {
            path: "/phone-scheduler/dashboard",
            component: Dashboard
        },
        {
            path: "/phone-scheduler/phoneline",
            component: Kanban,
            props: (route) => ({id: Number(route.query.id)})
        },
        {
            path: '*',
            redirect: "phone-scheduler/dashboard"
        }
    ]
});
