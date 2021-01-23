<template>
    <div class="page-content">

        <div class="container-fluid">
            <div class="page-content__header">
                <div>
                    <h2 class="page-content__header-heading">Import Gifts</h2>
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
                        <h3>Import Gifts Form</h3>
                        <form role="form" ref="addGiftsFrom" name="addGiftsFrom"
                            v-on:submit.prevent="createGifts">
                            <div class="from-block">
                                <div class="form-group">
                                    <label for="expires_at">Gift Excel Form</label>
                                    <div class="file-upload" v-bind:class="{ active: gift_file }">
                                        <div class="file-select">
                                            <div class="file-select-button" id="fileName">Choose File</div>
                                            <div class="file-select-name" v-if="!gift_file">No file chosen...</div>
                                            <div class="file-select-name" v-if="gift_file">{{gift_file.name}}</div>
                                            <input type="file" ref="gift_file" @change="onFileChange"
                                                name="gift_file" id="gift_file">
                                        </div>
                                    </div>
                                    <div class="file-upload__files" v-if="gift_file">
                                        <div class="file-upload__file file-upload__border">
                                            <div class="file-upload__file-bg-progress"
                                                :style="'width:'+ percentCompleted + '%;'"></div>
                                            <div class="file-upload__file-preview">
                                                <img :src="gift_file.webkitRelativePath" alt="">
                                            </div>
                                            <div class="file-upload__file-info">
                                                <span class="file-upload__file-name">{{ gift_file.name }}</span>
                                                <span
                                                    class="file-upload__file-size">{{ AlertFilesize(gift_file.size) }}</span>
                                            </div>
                                            <span class="file-upload__file-progress">{{percentCompleted}}%</span>
                                        </div>
                                    </div>
                                </div>
                                <!-- /.box-body -->
                                <hr />
                                <div class="form-group text-right m-b-0">
                                    <button type="button" @click="listGift()"
                                        class="btn btn-default mb-2 mr-3 pull-left">Cancel</button>
                                    <button type="submit" class="btn btn-success mb-2 mr-3">Upload</button>
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
                gift_file: null,
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
            onFileChange(e) {
                this.gift_file = this.$refs.gift_file.files[0];
            },
            createGifts: function () {
                // this.upload_file = URL.createObjectURL(this.$refs.upload_file.files[0]);
                const formData = new FormData(this.$refs.addGiftsFrom);
                formData.append('spinner_id', this.$route.params.spinner_id );
                var v = this;
                const config = {
                    onUploadProgress: function (progressEvent) {
                        v.percentCompleted = Math.round((progressEvent.loaded * 100) / progressEvent.total);
                        console.log(v.percentCompleted);
                    }
                };
                axios.post('/spinner/gift/imports' , formData, config)
                    .then(r => r.data)
                    .then((response) => {
                        notification('Import Gifts', "Import Gifts successfully!", 'success');
                        this.listGift();
                    }).catch((error) => {
                        notification('Import Gifts', "Import Gifts Faild!", 'danger');
                    });
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
            listGift: function () {
                this.$router.push({
                    name: 'SpinAndWinGifts' , params:{ spinner_id : this.$route.params.spinner_id }
                });
            }
        }
    }

</script>
