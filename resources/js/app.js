/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

import auth from './mixins/auth';
import { library } from '@fortawesome/fontawesome-svg-core';
import { faUsers, faPaperPlane, faThumbsUp } from '@fortawesome/free-solid-svg-icons';
import { faThumbsUp as faThumbsUpRegular } from '@fortawesome/free-regular-svg-icons';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';

require('./bootstrap');

window.Vue = require('vue').default;
window.EventBus = new Vue();
library.add(faUsers, faPaperPlane, faThumbsUp, faThumbsUpRegular);

Vue.component('font-awesome-icon', FontAwesomeIcon);
Vue.component('status-form', require('./components/StatusForm').default);
Vue.component('status-list', require('./components/StatusList').default);
Vue.component('friendship-btn', require('./components/FriendshipBtn').default);
Vue.component('accept-friendship-btn', require('./components/AcceptFriendshipBtn').default);


Vue.mixin(auth);

const app = new Vue({
    el: '#app',
});
