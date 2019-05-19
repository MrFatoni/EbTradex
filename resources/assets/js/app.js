import 'viewerjs/dist/viewer.css'
import VueViewer from 'v-viewer';

require('./bootstrap');
window.Vue = require('vue');

Vue.use(VueViewer, {defaultOptions: {zIndex: 9999}});

