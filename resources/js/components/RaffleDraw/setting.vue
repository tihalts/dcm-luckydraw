<template>
    <div class="page-content">

        <div class="container-fluid">
            <div class="page-content__header">
                <div>
                    <h2 class="page-content__header-heading">Raffle Draw Settings</h2>
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
                        <h3>Raffle Draw Setting Form</h3>
                        <form role="form" ref="addRaffleDrawFrom" name="addRaffleDrawFrom" v-on:submit.prevent="updateSetting">
                            <div class="from-block">
                                <div class="form-group">
                                    <label for="min_amount">Min Amount For One Point</label>
                                    <input type="mumber" min="0" class="form-control" name="min_amount"
                                        v-model="form.min_amount" placeholder="Min Amount for one point">
                                </div>
                                <div class="form-group">
                                    <label for="max_points">Max Points</label>
                                    <input type="mumber" min="0" class="form-control" name="max_points"
                                        v-model="form.max_points" placeholder="Max Points">
                                </div>
                                <div class="image" v-if="form.image">
                                    <img :src="form.image" class="img-thumbnail" alt="" srcset="">
                                </div>
                                 <div class="form-group">
                                    <label for="expires_at">Background Image</label>
                                    <div class="file-upload" v-bind:class="{ active: background_img }">
                                        <div class="file-select">
                                            <div class="file-select-button" id="fileName">Choose File</div>
                                            <div class="file-select-name" v-if="!background_img">No file chosen...</div>
                                            <div class="file-select-name" v-if="background_img">{{background_img.name}}</div>
                                            <input type="file" ref="background_img" @change="onBackgroundImgFileChange"
                                                name="background_img" id="background_img">
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
                                <div class="form-group">
                                    <label for="sms">SMS Template</label>
                                    <textarea class="form-control" name="sms" v-model="form.sms"
                                        rows="5"></textarea>
                                </div>
                                <div class="form-group">
                                    <label for="description">Email Template</label>
                                    <textarea class="form-control" name="email" v-model="form.email"
                                        rows="10"></textarea>
                                    <a :href="'raffle-draw-email-preview/' + $route.params.lucky_draw_id" target="_blank" type="button"  class="btn btn-info">Preview</a>
                                    <!-- <ckeditor :editor="editor" v-model="form.email" :config="editorConfig"></ckeditor> -->
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
                                    <button type="button" @click="listRaffleDraw()"
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
    //import ClassicEditor from '@ckeditor/ckeditor5-build-classic';

    export default {
        data() {
            return {
                form: {},
                errors: null,
                background_img: null,
                percentCompleted: 0,
                // editor: ClassicEditor,
                // editorConfig: {}
            }
        },
        mounted: function () {
            this.getRaffleDrawTemplate();
        },
        methods: {
            getRaffleDrawTemplate: function () {
                axios.get('/raffledraw/setting/fetch/' + this.$route.params.lucky_draw_id)
                    .then(r => r.data)
                    .then((response) => {
                        this.form = response.data;
                    });
            },
            updateSetting: function () {
                const formData = new FormData(this.$refs.addRaffleDrawFrom);
                formData.set("send_sms" , this.form.send_sms);
                formData.set("send_email" , this.form.send_email);
                var v = this;
                const config = {
                    onUploadProgress: function (progressEvent) {
                        v.percentCompleted = Math.round((progressEvent.loaded * 100) / progressEvent.total);
                        console.log(v.percentCompleted);
                    }
                };
                axios.post('/raffledraw/setting/update/' + this.$route.params.lucky_draw_id, formData, config)
                    .then(r => r.data)
                    .then((response) => {
                        this.errors = null;
                        this.listRaffleDraw();
                    }).catch((error) => {
                        this.errors = typeof error.response.data.errors !== 'undefined' ? error.response.data
                            .errors : null;
                    });
                hidebtnLoader();
            },
            onBackgroundImgFileChange(e) {
                this.background_img = this.$refs.background_img.files[0];
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
            listRaffleDraw: function () {
                this.$router.push({
                    name: 'RaffleDraws'
                });
            }
        }
    }

</script>
