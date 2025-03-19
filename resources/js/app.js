import '../css/app.css'
import { createApp, h } from 'vue'
import { createInertiaApp, router } from '@inertiajs/vue3'
import VueTheMask from 'vue-the-mask'
import { formatNumber } from './helpers.js'
import store from './Store'
import VueToast from 'vue-toast-notification';
import 'vue-toast-notification/dist/theme-default.css';
import 'core-js/stable';
import 'regenerator-runtime/runtime';
import axios from 'axios';
import { setZXingModuleOverrides } from "vue-qrcode-reader";
// Вебсокеты
import '@/echo';

// Патч для плагина привязки qr, чтобы файл zxing_reader.wasm оставался локальным
import wasmFile from "../../node_modules/zxing-wasm/dist/reader/zxing_reader.wasm?url";
setZXingModuleOverrides({
    locateFile: (path, prefix) => {
        const match = path.match(/_(.+?).wasm$/);
        if (match) {
            return wasmFile;
        }
        return prefix + path;
    },
});


// fetch('/sanctum/csrf-cookie', {
//     credentials: 'include'
// }).then(() => {
//     // console.log('CSRF token refreshed!');
// });

// Принудительно удалить XSRF-TOKEN
// document.addEventListener("DOMContentLoaded", function () {
//     document.cookie = "XSRF-TOKEN=; path=/; domain=yagoda.team; expires=Thu, 01 Jan 1970 00:00:00 UTC;";
//     // document.cookie = "XSRF-TOKEN=; path=/; domain=.yagoda.team; expires=Thu, 01 Jan 1970 00:00:00 UTC;";
//
//     document.cookie = "yagodateam_session=; expires=Thu, 01 Jan 1970 00:00:00 GMT; path=/; domain=yagoda.team;";
//     document.cookie = "yagodateam_session=; expires=Thu, 01 Jan 1970 00:00:00 GMT; path=/; domain=.yagoda.team;";
//
//     document.cookie = "yagoda_cookie_session=; path=/; domain=.yagoda.team; expires=Thu, 01 Jan 1970 00:00:00 GMT;";
//     document.cookie = "yagoda_cookie_session=; path=/; domain=yagoda.team; expires=Thu, 01 Jan 1970 00:00:00 GMT;";
//     document.cookie = "yagoda_cookie_session=; path=/; expires=Thu, 01 Jan 1970 00:00:00 GMT;";
//
//     // console.log("Удалены все XSRF-TOKEN");
//
//     // После удаления - запрашиваем новый токен
//     axios.get('/sanctum/csrf-cookie').then(() => {
//         // console.log("Новый CSRF-токен получен");
//     }).catch(err => {
//         // console.error("Ошибка при получении CSRF-токена", err);
//     });
// });

// Включаем передачу cookies в axios запросы
axios.defaults.withCredentials = true;

// Добавляем CSRF-токен во все запросы
axios.interceptors.request.use(config => {
    let token = document.head.querySelector('meta[name="csrf-token"]');
    if (token) {
        config.headers['X-XSRF-TOKEN'] = token.content;
    }
    return config;
}, error => {
    return Promise.reject(error);
});

// Включаем передачу cookies в fetch запросы
const originalFetch = window.fetch;
window.fetch = (url, options = {}) => {
    options = {
        ...options,
        headers: {
            ...(options.headers || {}),
        },
        credentials: 'include',
    };

    // Проверяем, идёт ли запрос на наш домен (или по относительному пути)
    const sameDomain = url.startsWith('/')
        || url.includes('yagoda.team')
        || url.startsWith(window.location.origin);

    if (sameDomain) {
        // Добавляем X-XSRF-TOKEN только для своих запросов
        const token = document.head.querySelector('meta[name="csrf-token"]');
        if (token) {
            options.headers['X-XSRF-TOKEN'] = token.content;
        }
    }

    return originalFetch(url, options);
};





createInertiaApp({
  resolve: name => {
    const pages = import.meta.glob('./Pages/**/*.vue', { eager: true })
    return pages[`./Pages/${name}.vue`]
  },
  title: title => title ? `${title} - Yagoda team` : 'Yagoda team',
  setup({ el, App, props, plugin }) {
    // createApp({ render: () => h(App, props) })
    //   .use(plugin)
    //     .use(VueTheMask)
    //     .use({
    //         install(app) {
    //             app.config.globalProperties.$formatNumber = formatNumber
    //         }
    //     })
    //   .mount(el)

      const application = createApp({render: () => h(App, props)})
          .use(plugin)
          .use(VueTheMask)
          .use(store)
          .use(VueToast)
          .use({
              install(app) {
                  app.config.globalProperties.$formatNumber = formatNumber;
                  app.config.compilerOptions.comments = true;

              }
          })
          .mount(el);

      delete el.dataset.page

      return application;

  },
    preserveState: true,
    preserveScroll: true,
})
