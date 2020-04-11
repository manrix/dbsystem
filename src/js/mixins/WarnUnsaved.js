import {WARN_CHANGE} from "../store/mutation-types";

export default {
    data() {
        return {
            confirmMessage: "Do you want to leave the page without save ?"
        }
    },

    mounted() {
        this.$store.commit(WARN_CHANGE, false);
        let inputFields = document.getElementById('page-content').querySelectorAll("input,select,textarea");
        Array.from(inputFields).forEach((el) => {
            el.addEventListener("change", (event) => {
                this.$store.commit(WARN_CHANGE, true);
            });
        });
    },

    beforeRouteLeave (to, from, next) {
        if (this.$store.state.warning.warnBeforeChange) {
            if (confirm(this.confirmMessage)) {
                next();
            } else {
                next(false);
            }
        } else {
            next();
        }
    }
};