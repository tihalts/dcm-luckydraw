<template>
    <div class="page-content">

        <div class="container-fluid">
            <div class="page-content__header">
                <div>
                    <h2 class="page-content__header-heading">Edit Scratch Card</h2>
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
                        <form role="form" ref="editCampaignFrom" name="editCampaignFrom"
                            v-on:submit.prevent="editCampaign">
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
                                    <label for="day_limit">Per Day Limit</label>
                                    <input type="number" min="0" class="form-control" name="day_limit"
                                        v-model="form.day_limit" placeholder="Per Day Limit">
                                </div>
                                <div class="form-group">
                                    <label for="customer_limit">Per Customer Limit</label>
                                    <input type="number" min="0" class="form-control" name="customer_limit"
                                        v-model="form.customer_limit" placeholder="Per Customer Limit">
                                </div>
                                <div class="form-group">
                                    <label for="max_limit">Maximum Limit</label>
                                    <input type="number" min="0" class="form-control" name="max_limit"
                                        v-model="form.max_limit" placeholder="Campaign maximum scratch card Limit">
                                </div>
                                <!-- <div class="form-group">
                                    <label for="max_winners">Maximum Winners</label>
                                    <input type="number" min="0" class="form-control" name="max_winners"
                                        v-model="form.max_winners" placeholder="Campaign maximum winners">
                                </div> -->
                                <div class="form-group">
                                    <label for="max_winners">Winner Probabilities</label>
                                    <input type="number" min="0" class="form-control" name="probability"
                                        v-model="form.probability" placeholder="Campaign winner Probabilities">
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
                                <div class="image" v-if="form.winner_image_url">
                                    <img :src="form.winner_image_url" class="img-thumbnail" alt="" srcset="">
                                </div>                                
                                <div class="form-group">
                                    <label for="expires_at">Winner Image</label>
                                    <div class="file-upload" v-bind:class="{ active: winner_img }">
                                        <div class="file-select">
                                            <div class="file-select-button" id="fileName">Choose File</div>
                                            <div class="file-select-name" v-if="!winner_img">No file chosen...</div>
                                            <div class="file-select-name" v-if="winner_img">{{winner_img.name}}</div>
                                            <input type="file" ref="winner_img" @change="onWinnerImgFileChange"
                                                name="winner_img" id="winner_img">
                                        </div>
                                    </div>
                                    <div class="file-upload__files" v-if="winner_img">
                                        <div class="file-upload__file file-upload__border">
                                            <div class="file-upload__file-bg-progress"
                                                :style="'width:'+ percentCompleted + '%;'"></div>
                                            <div class="file-upload__file-preview">
                                                <img :src="winner_img.webkitRelativePath" alt="">
                                            </div>
                                            <div class="file-upload__file-info">
                                                <span class="file-upload__file-name">{{ winner_img.name }}</span>
                                                <span
                                                    class="file-upload__file-size">{{ AlertFilesize(winner_img.size) }}</span>
                                            </div>
                                            <span class="file-upload__file-progress">{{percentCompleted}}%</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="image" v-if="form.looser_image_url">
                                    <img :src="form.looser_image_url" class="img-thumbnail" alt="" srcset="">
                                </div>
                                <div class="form-group">
                                    <label for="expires_at">Looser Background Image</label>
                                    <div class="file-upload" v-bind:class="{ active: looser_img }">
                                        <div class="file-select">
                                            <div class="file-select-button" id="fileName">Choose File</div>
                                            <div class="file-select-name" v-if="!looser_img">No file chosen...</div>
                                            <div class="file-select-name" v-if="looser_img">{{looser_img.name}}</div>
                                            <input type="file" ref="looser_img" @change="onLooserImgFileChange"
                                                name="looser_img" id="looser_img">
                                        </div>
                                    </div>
                                    <div class="file-upload__files" v-if="looser_img">
                                        <div class="file-upload__file file-upload__border">
                                            <div class="file-upload__file-bg-progress"
                                                :style="'width:'+ percentCompleted + '%;'"></div>
                                            <div class="file-upload__file-preview">
                                                <img :src="looser_img.webkitRelativePath" alt="">
                                            </div>
                                            <div class="file-upload__file-info">
                                                <span class="file-upload__file-name">{{ looser_img.name }}</span>
                                                <span
                                                    class="file-upload__file-size">{{ AlertFilesize(looser_img.size) }}</span>
                                            </div>
                                            <span class="file-upload__file-progress">{{percentCompleted}}%</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="image" v-if="form.background_image_url">
                                    <img :src="form.background_image_url" class="img-thumbnail" alt="" srcset="">
                                </div>
                                <div class="form-group">
                                    <label for="expires_at">Background Image</label>
                                    <div class="file-upload" v-bind:class="{ active: background_img }">
                                        <div class="file-select">
                                            <div class="file-select-button" id="fileName">Choose File</div>
                                            <div class="file-select-name" v-if="!background_img">No file chosen...</div>
                                            <div class="file-select-name" v-if="background_img">{{background_img.name}}
                                            </div>
                                            <input type="file" name="background_img" ref="background_img"
                                                @change="onBgImgFileChange" id="background_img">
                                        </div>
                                    </div>
                                    <div class="file-upload__files" v-if="background_img">
                                        <div class="file-upload__file file-upload__border">
                                            <div class="file-upload__file-bg-progress"
                                                :style="'width:'+ percentCompleted + '%;'"></div>
                                            <div class="file-upload__file-preview">
                                                <img :src="background_img.webkitRelativePath" alt="">
                                            </div>
                                            <div class="file-upload__file-info">
                                                <span class="file-upload__file-name">{{ background_img.name }}</span>
                                                <span
                                                    class="file-upload__file-size">{{ AlertFilesize(background_img.size) }}</span>
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
<style scoped>
    img.img-thumbnail {
        max-width: 300px;
    }

    .file-upload__files {
        margin-top: 20px;
    }

    .file-upload__file.file-upload__border {
        padding: 10px;
        border: 1px solid rgba(147, 157, 170, .2) !important;
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
                looser_img: null,
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
        mounted: function () {
            this.getCampaign();
        },
        methods: {
            getCampaign: function () {
                axios.get('/scratch-card-campaign/fetch/' + this.$route.params.campaign_id)
                    .then(r => r.data)
                    .then((response) => {
                        this.form = response.data;
                    });
            },
            onWinnerImgFileChange(e) {
                this.winner_img = this.$refs.winner_img.files[0];
            },
            onLooserImgFileChange(e) {
                this.looser_img = this.$refs.looser_img.files[0];
            },
            onBgImgFileChange(e) {
                this.background_img = this.$refs.background_img.files[0];
            },
            editCampaign: function () {
                const formData = new FormData(this.$refs.editCampaignFrom);
                formData.set("send_sms" , this.form.send_sms);
                formData.set("send_email" , this.form.send_email);
                formData.append('dsds', this.$refs.background_img.files[0]);
                // Object.keys(this.form).forEach((value, key) => {
                //     formData.append(key,value);
                // });
                var v = this;
                const config = {
                    onUploadProgress: function (progressEvent) {
                        v.percentCompleted = Math.round((progressEvent.loaded * 100) / progressEvent.total);
                        console.log(v.percentCompleted);
                    }
                };
                axios.post('/scratch-card-campaign/edit/' + this.$route.params.campaign_id, formData, config)
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
            AlertFilesize: function (sizeinbytes) {
                var fSExt = new Array('Bytes', 'KB', 'MB', 'GB');
                var fSize = sizeinbytes;
                var i = 0;
                while (fSize > 900) {
                    fSize /= 1024;
                    i++;
                }
                return ((Math.round(fSize * 100) / 100) + ' ' + fSExt[i]);
            },
            listCampaign: function () {
                this.$router.push({
                    name: 'ScratchCardCampaigns'
                });
            }
        }
    }

</script>
