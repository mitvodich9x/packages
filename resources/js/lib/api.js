import axios from "axios";

axios.defaults.withCredentials = true;
axios.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest";
axios.defaults.xsrfCookieName = "XSRF-TOKEN";
axios.defaults.xsrfHeaderName = "X-XSRF-TOKEN";

let csrfReady = false;
async function prepareCsrf() {
    if (!csrfReady) {
        await axios.get("/sanctum/csrf-cookie");
        csrfReady = true;
    }
}

axios.interceptors.request.use(async (config) => {
    const needCsrf = ["post", "put", "patch", "delete"].includes(
        (config.method || "get").toLowerCase()
    );
    if (needCsrf) await prepareCsrf();
    return config;
});

export default axios;
