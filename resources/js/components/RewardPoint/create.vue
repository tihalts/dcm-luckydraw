<template>
    <div class="page-content">

        <div class="container-fluid">
            <div class="page-content__header">
                <div>
                    <h2 class="page-content__header-heading">Create Scratch Card</h2>
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
                                    <label for="campaign_name">Campaign Name</label>
                                    <input type="text" class="form-control" name="campaign_name"
                                        v-model="form.campaign_name" placeholder="Campaign Name" required>
                                </div>
                                <div class="form-group">
                                    <label for="min_amount">Min Amount</label>
                                    <input type="number" min="0" class="form-control" name="min_amount"
                                        v-model="form.min_amount" placeholder="Min Amount">
                                </div>
                                <div class="form-group">
                                    <label for="max_amount">Max Amount</label>
                                    <input type="mumber" min="0" class="form-control" name="max_amount"
                                        v-model="form.max_amount" placeholder="Max Amount">
                                </div>
                                <div class="form-group">
                                    <label for="per_day_limit">Per Day Limit</label>
                                    <input type="mumber" min="0" class="form-control" name="per_day_limit"
                                        v-model="form.per_day_limit" placeholder="Per Day Limit">
                                </div>
                                <div class="form-group">
                                    <label for="per_customer_limit">Per Customer Limit</label>
                                    <input type="mumber" min="0" class="form-control" name="per_customer_limit"
                                        v-model="form.per_customer_limit" placeholder="Per Customer Limit">
                                </div>
                                <div class="form-group">
                                    <label for="email">Campaign Type</label>
                                    <select name="campaign_type" id="campaign_type" class="form-control"
                                        v-model="form.campaign_type" required>
                                        <option value="scratch_card">Scratch Card</option>
                                        <option value="voucher">Vouchers</option>
                                    </select>

                                </div>
                                <div class="form-group">
                                    <label for="expires_at">Expires At</label>
                                    <flat-pickr class="form-control" :config="config" name="expires_at"
                                        v-model="form.expires_at" placeholder="Expires At"></flat-pickr>
                                </div>
                                <div class="form-group">
                                    <label for="expires_at">Winner Image</label>
                                    <div class="file-upload" v-bind:class="{ active: winner_img }">
                                        <div class="file-select">
                                            <div class="file-select-button" id="fileName">Choose File</div>
                                            <div class="file-select-name" v-if="!winner_img">No file chosen...</div>
                                            <div class="file-select-name" v-if="winner_img">{{winner_img.name}}</div>
                                            <input type="file" ref="winner_img" @change="onWinnerImgFileChange" name="chooseFile" id="chooseFile">
                                        </div>
                                    </div>
                                    <div class="file-upload__files" v-if="winner_img">
                                        <div class="file-upload__file file-upload__border">
                                            <div class="file-upload__file-bg-progress" :style="'width:'+ percentCompleted + '%;'"></div>
                                            <div class="file-upload__file-preview">
                                                <img :src="winner_img.webkitRelativePath" alt="">
                                            </div>
                                            <div class="file-upload__file-info">
                                                <span class="file-upload__file-name">{{ winner_img.name }}</span>
                                                <span class="file-upload__file-size">{{ AlertFilesize(winner_img.size) }}</span>
                                            </div>
                                            <span class="file-upload__file-progress">{{percentCompleted}}%</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="expires_at">Background Image</label>
                                    <div class="file-upload" v-bind:class="{ active: background_img }">
                                        <div class="file-select">
                                            <div class="file-select-button" id="fileName">Choose File</div>
                                            <div class="file-select-name" v-if="!background_img">No file chosen...</div>
                                            <div class="file-select-name" v-if="background_img">{{background_img.name}}</div>
                                            <input type="file" ref="background_img" @change="onBgImgFileChange" name="chooseFile" id="chooseFile">
                                        </div>
                                    </div>
                                    <div class="file-upload__files" v-if="background_img">
                                        <div class="file-upload__file file-upload__border">
                                            <div class="file-upload__file-bg-progress" :style="'width:'+ percentCompleted + '%;'"></div>
                                            <div class="file-upload__file-preview">
                                                <img :src="background_img.webkitRelativePath" alt="">
                                            </div>
                                            <div class="file-upload__file-info">
                                                <span class="file-upload__file-name">{{ background_img.name }}</span>
                                                <span class="file-upload__file-size">{{ AlertFilesize(background_img.size) }}</span>
                                            </div>
                                            <span class="file-upload__file-progress">{{percentCompleted}}%</span>
                                        </div>
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
                axios.post('/campaign/create', this.form)
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
                // this.upload_file = URL.createObjectURL(this.$refs.upload_file.files[0]);
                const formData = new FormData(this.$refs.addCampaignFrom);
                formData.append('winner_img', this.$refs.winner_img.files[0]);
                formData.append('background_img', this.$refs.background_img.files[0]);
                var v = this;
                const config = {
                    onUploadProgress: function(progressEvent) {
                        v.percentCompleted = Math.round( (progressEvent.loaded * 100) / progressEvent.total );
                        console.log(v.percentCompleted );
                    }
                };
                axios.post('/campaign/create' , formData , config)
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
                    path: '/campaigns'
                });
            }
        }
    }

</script>
