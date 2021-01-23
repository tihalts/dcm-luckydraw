<template>
    <div class="page-content">

        <div class="container-fluid">
            <div class="page-content__header">
                <div>
                    <h2 class="page-content__header-heading">Campaign Settings</h2>
                </div>
            </div>
            <div class="main-container container-heading-bordered">
                <h2 class="container-heading">Voucher Settings</h2>
                <div class="container-body global-settings">
                    <div class="row global-settings__block">
                        <div class="col-lg-4">
                            <h5 class="global-settings__block-heading">Vouchers</h5>
                            <div class="global-settings__block-desc">
                               Setting for vouchers.
                            </div>
                        </div>
                        <div class="col-lg-8">
                            <form class="global-settings__form">
                                <h6 class="global-settings__form-heading"></h6>
                                <div class="form-group row">
                                    <label for="customer_limit" class="col-sm-3 col-form-label">Customer Limit</label>
                                    <div class="col-sm-8">
                                        <input type="number" min="0" class="form-control" id="customer_limit" placeholder="Customer Limit" v-model="form.customer_limit">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="day_limit" class="col-sm-3 col-form-label">Day Limit</label>
                                    <div class="col-sm-8">
                                        <input type="number" min="0" class="form-control" id="day_limit" placeholder="Per Day Limit" v-model="form.day_limit">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="min_amount" class="col-sm-3 col-form-label">Min Amount</label>
                                    <div class="col-sm-8">
                                        <input type="number" min="0" class="form-control" id="min_amount" placeholder="Min Amount For Voucher" v-model="form.min_amount">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="max_amount" class="col-sm-3 col-form-label">Max Amount</label>
                                    <div class="col-sm-8">
                                        <input type="number" min="0" class="form-control" id="max_amount" placeholder="Max Amount form Voucher" v-model="form.max_amount">
                                    </div>
                                </div>
                                <div class="form-group row global-settings__form-actions  pull-right" style="margin-top:30px;margin-right:70px;">
                                    <div class="col-sm-2">
                                        <button type="button" class="btn btn-info" @click="saveSettings">Save Changes</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <hr>
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
                form:{}               
            }
        },
        mounted: function () {
            this.getSettings();
        },
        methods: {
          getSettings: function () {
                axios.get('/free-gift-campaign/fetch/settings')
                    .then(r => r.data)
                    .then((response) => {
                        this.form = response.data;
                    });
          },
          saveSettings: function () {                
                axios.post('/free-gift-campaign/update/settings', this.form)
                    .then(r => r.data)
                    .then((response) => {
                        this.form = response.data;
                        notification('Setting' , "Update setting successfully.!" , 'success');
                    });
                //hidebtnLoader();
            },
        }
    }

</script>
