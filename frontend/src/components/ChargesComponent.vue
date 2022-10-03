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
        <!-- Button trigger modal -->
        <div class="row">
            <div class="col-4">
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#configModal">
                    Add Custom Charge
                </button>
            </div>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="configModal" ref="configModal" tabindex="-1" aria-labelledby="configModalLabel"
             aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Modal title</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div ref="el" class="alert alert-danger alert-dismissible fade" :class="{show : showError}"
                             role="alert">
                            <div>{{ this.errorMessage }}</div>
                        </div>
                        <form action="#">
                            <div class="mb-3">
                                <div class="row">
                                    <div class="col-3"><label for="amount" class="form-label">Amount</label></div>
                                    <div class="col-9"><input type="number" class="form-control" id="amount"
                                                              aria-describedby="amount"
                                                              v-model="charge.amount"></div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <div class="row">
                                    <div class="col-3"><label for="exampleInputEmail1" class="form-label">Is
                                        Recurring?</label></div>
                                    <div class="col-9">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="is_recurring"
                                                   id="is_recurring1"
                                                   value="1" checked v-model="charge.is_recurring">
                                            <label class="form-check-label" for="is_recurring1">
                                                Yes
                                            </label>
                                        </div>

                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="is_recurring"
                                                   id="is_recurring2"
                                                   value="0" v-model="charge.is_recurring">
                                            <label class="form-check-label" for="is_recurring2">
                                                No
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3 form-check">
                                <div class="row">
                                    <div class="col-3"><label for="external-module" class="form-label">External
                                        Module</label>
                                    </div>
                                    <div class="col-9"><input type="text" class="form-control"
                                                              v-model="charge.module_prefix"
                                                              id="external-module" aria-describedby="external-module">
                                    </div>
                                </div>

                            </div>
                            <div class="mb-3">
                                <div class="row">
                                    <div class="col-3"><label for="exampleFormControlTextarea1"
                                                              class="form-label">Noes</label></div>
                                    <div class="col-9"><textarea name="notes" class="form-control"
                                                                 id="exampleFormControlTextarea1" rows="3"
                                                                 v-model="charge.notes"></textarea></div>
                                </div>


                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" @click="this.saveCharge">Save changes</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from "axios";
import {Modal} from 'bootstrap'

export default {
    name: "ChargesComponent",
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
                if (err.response !== undefined) {
                    this.errorMessage = err.response.data.message
                } else {
                    this.errorMessage = err
                }
            });
        },
        saveCharge: function () {
            var data = this.charge
            data['redcap_csrf_token'] = window.csrf_token
            axios.post(window.ajaxURL + '&action=save_charge', data).then(() => {
                this.modal.hide()
                this.loadConfigList()

            }).catch(err => {
                console.log(err)
                this.showError = true
                if (err.response !== undefined) {
                    this.errorMessage = err.response.data.message
                } else {
                    this.errorMessage = err
                }

            });
        }
    },
    data() {
        return {
            list: [],
            errorMessage: '',
            showError: false,
            modal: null,
            charge: {
                amount: '',
                is_recurring: false,
                notes: '',
                module_prefix: ''
            }
        }
    },
    mounted() {
        this.prepareComponent();
        this.modal = new Modal(this.$refs.configModal)
    }
}
</script>

<style scoped>

</style>
