<template>
    <div class="page-content">

        <div class="container-fluid">
            <div class="page-content__header">
                <div>
                    <h2 class="page-content__header-heading">Scratch Card Template</h2>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-12 col-md-8 offset-md-1">
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
                        <h3>Scratch Card Template Form</h3>
                        <form role="form" ref="addBusinessTypeFrom" name="addBusinessTypeFrom" v-on:submit.prevent="createBusinessType">
                            <div class="from-block">
                                <div class="form-group">
                                    <label for="sms">SMS Template</label>
                                    <textarea class="form-control" name="sms" v-model="form.sms"
                                        rows="5"></textarea>
                                </div>
                                <div class="form-group">
                                    <label for="description">Email Template</label>
                                     <textarea class="form-control" name="sms" v-model="form.email"
                                        rows="10"></textarea>
                                        <a :href="'voucher-email-preview/' + $route.params.campaign_id" target="_blank" type="button"  class="btn btn-info">Preview</a>
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
    //import ClassicEditor from '@ckeditor/ckeditor5-build-classic';

    export default {
        data() {
            return {
                form: {},
                errors: null,
                //editor: ClassicEditor,
                // editorData: '',
                // editorConfig: {}
            }
        },
        mounted: function () {
            this.getCampaignTemplate();
        },
        methods: {
            getCampaignTemplate: function () {
                axios.get('/campaign/template/fetch/' + this.$route.params.campaign_id)
                    .then(r => r.data)
                    .then((response) => {
                        this.form = response.data;
                        //this.editorData = this.form.email;
                    });
            },
            createBusinessType: function () {
                axios.post('/campaign/template/update/' + this.$route.params.campaign_id, this.form)
                    .then(r => r.data)
                    .then((response) => {
                        this.errors = null;
                        this.listCampaign();
                    }).catch((error) => {
                        this.errors = typeof error.response.data.errors !== 'undefined' ? error.response.data
                            .errors : null;
                    });
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
