<template>
    <div class="page-content">

        <div class="container-fluid">
            <div class="page-content__header">
                <div>
                    <h2 class="page-content__header-heading">Edit Campaign</h2>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 offset-md-3">
                    <div v-if="errors !== null" class="alert content-alert content-alert--danger mt-4" role="alert">
                        <div class="content-alert__info">
                            <span class="content-alert__info-icon ua-icon-warning"></span>
                        </div>
                        <div class="content-alert__content">
                            <div class="content-alert__heading"></div>
                            <div class="content-alert__message">
                                <ul class="custom-alert__list" v-for="(values , key) in errors" :key="key">
                                    <li v-for="(value , index) in values" :key="index">{{ value }}</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="main-container">
                        <h3>Campaign Edit Form</h3>
                        <hr />
                        <form role="form" ref="editCampaignFrom" name="editCampaignFrom" v-on:submit.prevent="editCampaign">
                            <div class="from-block">
                                <div class="form-group">
                                    <label for="campaign_name">Campaign Name</label>
                                    <input type="text" class="form-control" name="campaign_name"
                                        v-model="form.campaign_name" placeholder="Campaign Name" required>
                                </div>
                                <div class="form-group">
                                    <label for="amount">Min Amount</label>
                                    <input type="number" min="0" class="form-control" name="amount"
                                        v-model="form.amount" placeholder="Min Amount">
                                </div>
                                <div class="form-group">
                                    <label for="customer_limit">Customer Limit</label>
                                    <input type="number" min="0" class="form-control" name="customer_limit"
                                        v-model="form.customer_limit" placeholder="Customer Limit">
                                </div>
                                <div class="form-group">
                                    <label for="day_limit">Campaign Day Limit</label>
                                    <input type="number" min="0" class="form-control" name="day_limit"
                                        v-model="form.day_limit" placeholder="Campaign Day Limit">
                                </div>
                                <div class="form-group">
                                    <label for="max_limit">Maximum Campaign Limit</label>
                                    <input type="number" min="0" class="form-control" name="max_limit"
                                        v-model="form.max_limit" placeholder="Maximum Campaign Limit">
                                </div>
                                <div class="form-group">
                                    <label for="voucher_digits">Voucher Digit Limit</label>
                                    <input type="number" min="0" class="form-control" name="voucher_digits"
                                        v-model="form.voucher_digits" placeholder="Voucher Digits Limit">
                                </div>
                                <div class="form-group">
                                    <label for="start_at">Start At</label>
                                    <flat-pickr class="form-control" :config="config" name="start_at"
                                        v-model="form.start_at" placeholder="Start At"></flat-pickr>
                                </div>
                                <div class="form-group">
                                    <label for="end_at">End At</label>
                                    <flat-pickr class="form-control" :config="config" name="end_at"
                                        v-model="form.end_at" placeholder="End At"></flat-pickr>
                                </div>
                                 <div class="form-group">
                                    <label for="description">Send SMS</label>
                                    <div class="">
                                        <label class="switch mr-3">
                                            <input type="checkbox" name="send_sms" v-bind:true-value="1" v-bind:false-value="0" v-model="form.send_sms">
                                            <span class="switch-slider"></span>
                                        </label>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="description">Send Email</label>
                                    <div class="">
                                        <label class="switch mr-3">
                                            <input type="checkbox" name="send_email" v-bind:true-value="1" v-bind:false-value="0" v-model="form.send_email">
                                            <span class="switch-slider"></span>
                                        </label>
                                    </div>
                                </div>
                                <!-- /.box-body -->
                                <hr />
                                <div class="form-group text-right m-b-0">
                                    <button type="button" @click="listCampaign()"
                                        class="btn btn-default mb-2 mr-3 pull-left">Cancel</button>
                                    <button type="submit" class="btn btn-success mb-2 mr-3">Update</button>
                                </div>
                                <!-- /.box-footer -->
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
    import axios from 'axios';

    export default {
        data() {
            return {
                form: {},
                errors: null,
                config: {
                    wrap: true, 
                    altInput: true,
                    enableTime: true,
                    dateFormat: "Y-m-d H:i",
                }, 
            }
        },
        mounted: function () {
            this.getCampaign();
        },
        methods: {
            getCampaign: function () {
                axios.get('/reward-point-campaign/fetch/' + this.$route.params.campaign_id)
                    .then(r => r.data)
                    .then((response) => {
                        this.form = response.data;
                    });
            },
            editCampaign: function () {
                axios.post('/reward-point-campaign/edit/' + this.$route.params.campaign_id, this.form)
                    .then(r => r.data)
                    .then((response) => {
                        this.errors = null;
                        this.listCampaign();
                    }).catch((error) => {
                        this.errors = typeof error.response.data.errors !== 'undefined' ? error.response.data
                            .errors : null;
                    });;
                hidebtnLoader();
            },
            listCampaign: function () {
                this.$router.push({
                    path: '/reward-point-campaigns'
                });
            }
        }
    }

</script>
