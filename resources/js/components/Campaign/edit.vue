<template>
    <div class="page-content">

        <div class="container-fluid">
            <div class="page-content__header">
                <div>
                    <h2 class="page-content__header-heading">Edit Customer</h2>
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
                        <h3>Customer Edit Form</h3>
                        <hr />
                        <form role="form" ref="editCampaignFrom" name="editCampaignFrom" v-on:submit.prevent="editCampaign">
                            <div class="from-block">
                                <div class="form-group">
                                    <label for="campaign_name">Campaign Name</label>
                                    <input type="text" class="form-control" name="campaign_name" v-model="form.campaign_name"
                                        placeholder="Campaign Name" required>
                                </div>
                                <div class="form-group">
                                    <label for="min_amount">Min Amount</label>
                                    <input type="number" class="form-control" name="min_amount" v-model="form.min_amount"
                                        placeholder="Min Amount" required>
                                </div>
                                <div class="form-group">
                                    <label for="max_amount">Max Amount</label>
                                    <input type="mumber" class="form-control" name="max_amount" v-model="form.max_amount"
                                        placeholder="Max Amount" required>
                                </div>
                                <div class="form-group">
                                    <label for="per_day_limit">Per Day Limit</label>
                                    <input type="mumber" class="form-control" name="per_day_limit" v-model="form.per_day_limit"
                                        placeholder="Per Day Limit" required>
                                </div>
                                <div class="form-group">
                                    <label for="per_customer_limit">Per Customer Limit</label>
                                    <input type="mumber" class="form-control" name="per_customer_limit" v-model="form.per_customer_limit"
                                        placeholder="Per Customer Limit" required>
                                </div>
                                <div class="form-group">
                                    <label for="email">Campaign Type</label>
                                    <select name="campaign_type" id="campaign_type" class="form-control" v-model="form.campaign_type" required>
                                        <option value="scratch_card">Scratch Card</option>
                                        <option value="voucher">Vouchers</option>
                                    </select>
                                    
                                </div>
                                <div class="form-group">
                                    <label for="expires_at">Expires At</label>
                                    <flat-pickr class="form-control" :config="config" name="expires_at" v-model="form.expires_at" placeholder="Expires At"></flat-pickr>
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
                axios.get('/campaign/fetch/' + this.$route.params.campaign_id)
                    .then(r => r.data)
                    .then((response) => {
                        this.form = response.data;
                    });
            },
            editCampaign: function () {
                axios.post('/campaign/edit/' + this.$route.params.campaign_id, this.form)
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
                    path: '/campaigns'
                });
            }
        }
    }

</script>
