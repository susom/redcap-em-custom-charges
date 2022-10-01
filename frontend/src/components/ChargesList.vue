<template>
    <div>
        <div ref="el" class="alert alert-danger alert-dismissible fade" :class="{show : showError}" role="alert">
            <div>{{ this.errorMessage }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <table class="table table-hover">
            <thead>
            <tr>
                <th>#</th>
                <th>Project</th>
                <th>External Module</th>
                <th>Is Recurring</th>
                <th>Amount</th>
                <th>Notes</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="config in this.list" :key="config.id">
                <td>{{ config.id }}</td>
                <td>{{ config.project_id }}</td>
                <td>{{ config.module_prefix }}</td>
                <td>{{ config.is_recurring }}</td>
                <td>{{ config.amount }}</td>
                <td>{{ config.notes }}</td>
            </tr>
            </tbody>
        </table>
    </div>
</template>

<script>
import axios from "axios";

export default {
    name: "ChargesList",
    methods: {
        prepareComponent: function () {
            this.loadConfigList()
        },
        loadConfigList: function () {
            var action = 'get_charges'
            axios.get(window.ajaxURL + '&action=' + action).then(response => {
                this.list = response.data
            }).catch(err => {
                this.showError = true
                this.errorMessage = err.response.data.message
            });
        }
    },
    data() {
        return {
            list: [],
            errorMessage: '',
            showError: false
        }
    },
    mounted() {
        this.prepareComponent();
    }
}
</script>

<style scoped>

</style>
