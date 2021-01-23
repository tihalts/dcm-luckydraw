let routes = [{
        path: '/spin-and-win',
        component: require('../../components/Spinner/spin.vue').default,
        name: 'Dashboard'
    },
    {
        path: '*',
        component: require('../../components/NotFound.vue').default
    },
];
export default routes;
