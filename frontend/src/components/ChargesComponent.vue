<template>
    <div class="container-fluid">
        <div :class="{overlay: isLoading}">
            <!-- Button trigger modal -->
            <div class="row">
                <div class="col-2">
                    <button :disabled=isButtonDisabled type="button" class="btn btn-primary" data-bs-toggle="modal"
                            data-bs-target="#chargeModal" @click="this.resetCharge">
                        Add Custom Charge
                    </button>

                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <small v-if="isButtonDisabled">Button is disabled because NO RMA found for this project. Please
                        create a RMA
                        to create custom charges. </small>
                </div>
            </div>
            <div ref="el" class="alert alert-info alert-dismissible fade" :class="{show : showError}" role="alert">
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
                    <th></th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="item in this.list" :key="item.id">
                    <td>{{ item.id }}</td>
                    <td>{{ item.project_id }}</td>
                    <td>{{ item.module_prefix }}</td>
                    <td>{{ item.is_recurring }}</td>
                    <td>${{ item.amount }}</td>
                    <td>{{ item.notes }}</td>
                    <td>
                        <div class="row">
                            <div class="col-1">
                                <a href="#" @click="deleteCharge(item.id)">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                         class="bi bi-trash" viewBox="0 0 16 16">
                                        <path
                                            d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z"/>
                                        <path fill-rule="evenodd"
                                              d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z"/>
                                    </svg>
                                </a>
                            </div>
                            <div class="col-1">
                                <a href="#" @click="editCharge(item)">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                         class="bi bi-pencil" viewBox="0 0 16 16">
                                        <path
                                            d="M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168l10-10zM11.207 2.5 13.5 4.793 14.793 3.5 12.5 1.207 11.207 2.5zm1.586 3L10.5 3.207 4 9.707V10h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.293l6.5-6.5zm-9.761 5.175-.106.106-1.528 3.821 3.821-1.528.106-.106A.5.5 0 0 1 5 12.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.468-.325z"/>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </td>
                </tr>
                </tbody>
            </table>


            <!-- Modal -->
            <div class="modal fade" id="chargeModal" ref="chargeModal" tabindex="-1" aria-labelledby="chargeModalLabel"
                 aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Add/Edit Charge</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div ref="el" class="alert alert-danger alert-dismissible fade" :class="{show : showError}"
                                 role="alert">
                                <div>{{ this.errorMessage }}</div>
                            </div>
                            <form action="#">
                                <input type="hidden" name="id" id="id" v-model="charge.id">
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
                                <div class="mb-3">
                                    <div class="row">
                                        <div class="col-3"><label for="external-module" class="form-label">External
                                            Module</label>
                                        </div>
                                        <div class="col-9">
                                            <select class="form-select" name="module_prefix"
                                                    v-model="charge.module_prefix">
                                                <option v-for="item in this.modules_list" :key="item.prefix"
                                                        :value="item.prefix">{{ item.name }}
                                                </option>
                                            </select>
                                        </div>
                                    </div>

                                </div>
                                <div class="mb-3">
                                    <div class="row">
                                        <div class="col-3"><label for="exampleFormControlTextarea1"
                                                                  class="form-label">Notes</label></div>
                                        <div class="col-9"><textarea name="notes" class="form-control"
                                                                     id="exampleFormControlTextarea1" rows="3"
                                                                     v-model="charge.notes"></textarea></div>
                                    </div>


                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="button" :disabled='isDisabled' class="btn btn-primary"
                                    @click="this.saveCharge">Save changes
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="spinner-border" role="status" :class="{show: isLoading, hide: !isLoading}">
        </div>
    </div>
</template>

<script>
import axios from "axios";
import {Modal} from 'bootstrap'

var ajaxCalls = [];

export default {
    name: "ChargesComponent",
    created() {
        axios.interceptors.request.use((config) => {
            // trigger 'loading=true' event here
            this.showNoneDismissibleAlert = false
            this.showDismissibleAlert = false
            ajaxCalls.push(config)
            if (this.isLoading != undefined) {
                this.isLoading = true
            }
            this.isDisabled = true
            return config;
        }, (error) => {
            // trigger 'loading=false' event here
            this.isLoading = false
            return Promise.reject(error);
        });

        axios.interceptors.response.use((response) => {
            // trigger 'loading=false' event here
            ajaxCalls.pop()
            if (ajaxCalls.length === 0) {
                this.isLoading = false
            }
            this.isDisabled = false
            return response;
        }, (error) => {
            // trigger 'loading=false' event here
            this.isLoading = false
            return Promise.reject(error);
        });
    },
    methods: {
        resetCharge: function () {
            this.charge = []
        },
        prepareComponent: function () {
            this.loadChargesList()
            this.loadModulesPrefixes()
        },
        loadModulesPrefixes: function () {
            axios.get(window.ajaxURL + '&action=' + window.MODULE_LIST).then(response => {
                this.modules_list = response.data.records
            }).catch(err => {
                this.showError = true
                if (err.response !== undefined) {
                    this.errorMessage = err.response.data.message
                } else {
                    this.errorMessage = err
                }
            });
        },
        loadChargesList: function () {
            axios.get(window.ajaxURL + '&action=' + window.GET_CHARGES).then(response => {
                this.list = response.data.records
            }).catch(err => {
                this.showError = true
                if (err.response !== undefined) {
                    this.errorMessage = err.response.data.message
                } else {
                    this.errorMessage = err
                }
            });
        },
        deleteCharge: function (chargeId) {
            if (confirm('Are you sure you want to delete this Charge?')) {
                axios.get(window.ajaxURL + '&action=' + window.DELETE_CHARGE + '&id=' + chargeId).then(response => {
                    this.loadChargesList()
                    this.showError = true
                    this.variant = 'success'
                    this.errorMessage = response.data.message
                }).catch(err => {
                    this.showError = true
                    if (err.response !== undefined) {
                        this.errorMessage = err.response.data.message
                    } else {
                        this.errorMessage = err
                    }
                });
            }
        },
        editCharge: function (charge) {
            charge['is_recurring'] = charge['is_recurring'] === 'Yes' ? 1 : 0
            this.charge = charge
            this.modal.show()
        },
        saveCharge: function () {

            var data = {
                id: this.charge.id,
                amount: this.charge.amount,
                is_recurring: this.charge.is_recurring,
                notes: this.charge.notes,
                module_prefix: this.charge.module_prefix
            }
            data['redcap_csrf_token'] = window.csrf_token

            axios.post(window.ajaxURL + '&action=' + window.SAVE_CHARGE, data).then(() => {
                this.modal.hide()
                this.loadChargesList()
            }).catch(err => {
                this.showError = true
                this.isDisabled = false
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
            isLoading: true,
            isDisabled: false,
            variant: 'danger',
            modal_button: '',
            modules_list: [],
            errorMessage: '',
            showError: false,
            modal: null,
            charge: {
                id: '',
                amount: '',
                is_recurring: false,
                notes: '',
                module_prefix: ''
            }
        }
    },
    computed: {
        isButtonDisabled() {
            // evaluate whatever you need to determine disabled here...
            return window.HAS_RMA !== '1';
        }
    },
    mounted() {
        this.prepareComponent();
        this.modal = new Modal(this.$refs.chargeModal)
    }
}
</script>

<style scoped>
.overlay {
    display: flex;
    justify-content: center;
    align-items: center;
    position: absolute;
    z-index: 2;
    opacity: 0;
    background: rgba(39, 42, 43, 0.8);
    transition: opacity 200ms ease-in-out;
    border-radius: 4px;
    margin: -15px 0 0 -15px;
}

</style>
