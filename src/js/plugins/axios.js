import axios from "axios"
import store from '../store'
import { LOADING, WARN_CHANGE } from '../store/mutation-types'
import { toast } from '../helpers'

/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

window.axios = axios;

axios.baseURL = window.APP.baseUrl;
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

/**
 * Next we will register the CSRF Token as a common header with Axios so that
 * all outgoing HTTP requests automatically have it attached. This is just
 * a simple convenience so we don't have to attach every token manually.
 */

let token = document.head.querySelector('meta[name="csrf-token"]');
window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;

axios.interceptors.response.use(function (response) {
    store.commit(LOADING, false);
    if (response.data.message) {
        toast({
            message: response.data.message,
            type: 'is-success',
        })
    }

    if (response.data.warnings) {
        response.data.warnings.forEach((message) => {
            toast({
                message: message,
                type: 'is-warning',
            })
        });
    }

    if (response.config.method === 'post' || response.config.method === 'put') {
        store.commit(WARN_CHANGE, false);
    }

    return response;
}, function (error) {
    store.commit(LOADING, false);

    if (error.response.data.errors) {
        for (let err in error.response.data.errors) {
            if (error.response.data.errors.hasOwnProperty(err)) {
                if (typeof error.response.data.errors[err] === "object") {
                    error.response.data.errors[err].forEach((item) => {
                        toast({
                            message: item,
                            type: 'is-danger',
                        });
                    })
                } else {
                    toast({
                        message: error.response.data.errors[err],
                        type: 'is-danger',
                    });
                }
            }
        }
    } else if (error.response.data.message || error.response.data.error) {
        toast({
            message: error.response.data.message || error.response.data.error || 'Unexpected error connecting with server',
            type: 'is-danger',
        });
    }

    return Promise.reject(error);
});