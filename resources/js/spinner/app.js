/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

//require('./bootstrap');

import Vue from 'vue';
import VueRouter from 'vue-router';
import axios from 'axios';

import router from './router';
import './config/interceptors';

Vue.use(axios);

Vue.use(VueRouter);

import VueSweetalert2 from 'vue-sweetalert2';
 
const options = {
  confirmButtonColor: '#41b882',
  cancelButtonColor: '#ff7674'
}
 
Vue.use(VueSweetalert2, options);

const app = new Vue({
    el: '#app',
    router
});

Vue.directive('title', {
    inserted: (el, binding) => document.title = binding.value,
    update: (el, binding) => document.title = binding.value
});

Vue.filter('upText', function (text) {
    return text.charAt(0).toUpperCase() + text.slice(1);
});

Vue.filter('myDate', function (created) {
    return moment(created).format('MMMM Do YYYY');
});

Vue.filter('capitalize', function (value) {
    if (!value) return '';
    value = value.toString();
    return value.charAt(0).toUpperCase() + value.slice(1);
});

Vue.filter('UpperCase', function (value) {
    if (!value) 
       return '';
    return value.toString().toUpperCase(); 
});

Vue.directive('tooltip', function(el, binding){
    $(el).tooltip({
             title: binding.value,
             placement: binding.arg,
             trigger: 'hover'             
         })
});