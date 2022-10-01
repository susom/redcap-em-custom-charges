<template>
    <div>
        <!-- Button trigger modal -->
        <div class="row">
            <div class="col-4">
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">
                    Add Custom Charge
                </button>
            </div>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
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
                                <label for="amount" class="form-label">Amount</label>
                                <input type="number" class="form-control" id="amount" aria-describedby="amount"
                                       v-model="charge.amount">
                                <div id="amount" class="form-text">Amount to be charged</div>
                            </div>
                            <div class="mb-3">
                                <label for="exampleInputEmail1" class="form-label">Is Recurring?</label>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="is_recurring" id="is_recurring1"
                                           value="1" checked v-model="charge.is_recurring">
                                    <label class="form-check-label" for="is_recurring1">
                                        Yes
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="is_recurring" id="is_recurring2"
                                           value="0" v-model="charge.is_recurring">
                                    <label class="form-check-label" for="is_recurring2">
                                        No
                                    </label>
                                </div>
                            </div>
                            <div class="mb-3 form-check">
                                <label for="external-module" class="form-label">External Module</label>
                                <input type="text" class="form-control" v-model="charge.module_prefix"
                                       id="external-module" aria-describedby="external-module">
                            </div>
                            <div class="mb-3">
                                <label for="exampleFormControlTextarea1" class="form-label">Noes</label>
                                <textarea name="notes" class="form-control" id="exampleFormControlTextarea1" rows="3"
                                          v-model="charge.notes"></textarea>
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
import {Modal} from 'bootstrap'
import axios from "axios";

export default {
    name: "ConfigModal",
    data() {
        return {
            modal: null,
            errorMessage: '',
            showError: false,
            charge: {
                amount: '',
                is_recurring: false,
                notes: '',
                module_prefix: ''
            }
        }
    },
    methods: {
        saveCharge: function () {
            var data = this.charge
            axios.get(window.ajaxURL + '&action=save_charge', {
                params: data
            }).then(response => {
                // TODO reload charges
            }).catch(err => {
                this.showError = true
                this.errorMessage = err.response.data.message
            });
        }
    },
    mounted() {
        this.modal = new Modal(this.$refs.exampleModal)
    }
}
</script>

<style scoped>

</style>
