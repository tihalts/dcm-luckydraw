<template>
    <div class="page-content">

        <div class="container-fluid">
            <div class="page-content__header">
                <div>
                    <h2 class="page-content__header-heading">Create Campaign</h2>
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
                        <h3>Campaign Create Form</h3>
                        <form role="form" ref="addCampaignFrom" name="addCampaignFrom"
                            v-on:submit.prevent="createCampaign">
                            <div class="from-block">
                                <div class="form-group">
                                    <label for="campaign_name">Campaign Name</label>
                                    <input type="text" class="form-control" name="campaign_name"
                                        v-model="form.campaign_name" placeholder="Campaign Name" required>
                                </div>
                                <div class="form-group">
                                    <label for="min_amount">Min Amount</label>
                                    <input type="number" min="0" class="form-control" name="amount"
                                        v-model="form.min_amount" placeholder="Min Amount">
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
                                    <button type="submit" class="btn btn-success mb-2 mr-3">Create</button>
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
<style scoped>
    .file-upload__files{
      margin-top: 20px;
    }
    .file-upload__file.file-upload__border{
        padding: 10px;
        border: 1px solid rgba(147,157,170,.2) !important;
    }
    .file-upload {
        display: block;
        text-align: center;
        font-family: Helvetica, Arial, sans-serif;
        font-size: 12px;
    }

    .file-upload .file-select {
        display: block;
        border: 2px solid #dce4ec;
        color: #34495e;
        cursor: pointer;
        height: 40px;
        line-height: 40px;
        text-align: left;
        background: #FFFFFF;
        overflow: hidden;
        position: relative;
    }

    .file-upload .file-select .file-select-button {
        background: #dce4ec;
        padding: 0 10px;
        display: inline-block;
        height: 40px;
        line-height: 40px;
    }

    .file-upload .file-select .file-select-name {
        line-height: 40px;
        display: inline-block;
        padding: 0 10px;
    }

    .file-upload .file-select:hover {
        border-color: #34495e;
        transition: all .2s ease-in-out;
        -moz-transition: all .2s ease-in-out;
        -webkit-transition: all .2s ease-in-out;
        -o-transition: all .2s ease-in-out;
    }

    .file-upload .file-select:hover .file-select-button {
        background: #34495e;
        color: #FFFFFF;
        transition: all .2s ease-in-out;
        -moz-transition: all .2s ease-in-out;
        -webkit-transition: all .2s ease-in-out;
        -o-transition: all .2s ease-in-out;
    }

    .file-upload.active .file-select {
        border-color: #3fa46a;
        transition: all .2s ease-in-out;
        -moz-transition: all .2s ease-in-out;
        -webkit-transition: all .2s ease-in-out;
        -o-transition: all .2s ease-in-out;
    }

    .file-upload.active .file-select .file-select-button {
        background: #3fa46a;
        color: #FFFFFF;
        transition: all .2s ease-in-out;
        -moz-transition: all .2s ease-in-out;
        -webkit-transition: all .2s ease-in-out;
        -o-transition: all .2s ease-in-out;
    }

    .file-upload .file-select input[type=file] {
        z-index: 100;
        cursor: pointer;
        position: absolute;
        height: 100%;
        width: 100%;
        top: 0;
        left: 0;
        opacity: 0;
        filter: alpha(opacity=0);
    }

    .file-upload .file-select.file-select-disabled {
        opacity: 0.65;
    }

    .file-upload .file-select.file-select-disabled:hover {
        cursor: default;
        display: block;
        border: 2px solid #dce4ec;
        color: #34495e;
        cursor: pointer;
        height: 40px;
        line-height: 40px;
        margin-top: 5px;
        text-align: left;
        background: #FFFFFF;
        overflow: hidden;
        position: relative;
    }

    .file-upload .file-select.file-select-disabled:hover .file-select-button {
        background: #dce4ec;
        color: #666666;
        padding: 0 10px;
        display: inline-block;
        height: 40px;
        line-height: 40px;
    }

    .file-upload .file-select.file-select-disabled:hover .file-select-name {
        line-height: 40px;
        display: inline-block;
        padding: 0 10px;
    }

</style>


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
        mounted: function () {},
        methods: {
            createCampaign: function () {
                axios.post('/reward-point-campaign/create' , this.form)
                    .then(r => r.data)
                    .then((response) => {
                        notification('Create Campaign' , "Create Campaign successfully!" , 'success');
                        this.listCampaign();
                    }).catch((error) => {
                        notification('Create Campaign' , "Create Campaign Faild!" , 'danger');
                    });
            },
            AlertFilesize : function(sizeinbytes){               
                var fSExt = new Array('Bytes', 'KB', 'MB', 'GB');
                var fSize = sizeinbytes; 
                var i=0;
                while(fSize>900){
                    fSize/=1024;i++;
                }
                return ((Math.round(fSize*100)/100)+' '+fSExt[i]);
            },
            listCampaign: function () {
                this.$router.push({
                    path: '/reward-point-campaigns'
                });
            }
        }
    }

</script>
