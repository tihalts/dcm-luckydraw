import VueRouter from 'vue-router';
import routes from './config/routes';

export default new VueRouter({
    routes: routes,
    mode: 'history',
    linkExactActiveClass: 'is-active',
    scrollBehavior: function (to, from, savedPosition) {
        return savedPosition || { x: 0, y: 0 }
    }
});