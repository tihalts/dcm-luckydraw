<template>
    <div class="page-content">

        <div class="container-fluid">
            <div class="page-content__header">
                <div>
                    <h2 class="page-content__header-heading">Create Customer</h2>
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
                        <h3>Customer Create Form</h3>
                        <form role="form" ref="addCampaignFrom" name="addCampaignFrom"
                            v-on:submit.prevent="createCampaign">
                            <div class="from-block">
                                <div class="form-group">
                                    <label for="name">Campaign Group Name</label>
                                    <input type="text" class="form-control" name="name"
                                        v-model="form.name" placeholder="Campaign Group Name" required>
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

<script>
    import axios from 'axios';

    export default {
        data() {
            return {
                form: {},
                errors: null,
                winner_img: null,
                background_img: null,
                percentCompleted: 0,
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
            createCampaigns: function () {
                axios.post('/campaign-group/create', this.form)
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
            onWinnerImgFileChange(e) {                
                this.winner_img = this.$refs.winner_img.files[0];
            },
            onBgImgFileChange(e) {                
                this.background_img = this.$refs.background_img.files[0];
            },
            createCampaign: function () {
                axios.post('/campaign-group/create' , this.form)
                    .then(r => r.data)
                    .then((response) => {
                        notification('Create Campaign' , "Create Campaign successfully!" , 'success');
                        this.listCampaign();
                    }).catch((error) => {
                        notification('Create Campaign' , "Create Campaign Faild!" , 'danger');
                    });
            },
            listCampaign: function () {
                this.$router.push({
                    name: 'CampaignGroups'
                });
            }
        }
    }

</script>
