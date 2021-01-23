<template>
    <div class="page-content">

        <div class="container-fluid">
            <div class="page-content__header">
                <div>
                    <h2 class="page-content__header-heading">Create Spin & Win</h2>
                </div>
            </div>

            <div class="row">
                <div class="col-md-10 col-sm-12 offset-md-1">
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
                        <h3>Spin & Win Create Form</h3>
                        <hr>
                        <form role="form" ref="addSpinwinFrom" name="addSpinwinFrom"
                            v-on:submit.prevent="createSpinAndWin">
                            <div class="from-block">
                                <div class="row">
                                    <div class="col-md-6 col-sm-12">
                                        <div class="form-group">
                                            <label for="spin_and_win_name">Spin & Win Name</label>
                                            <input type="text" class="form-control" name="spin_and_win_name"
                                                v-model="form.spin_and_win_name" placeholder="Spin and win Name" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="min_amount">Min Amount</label>
                                            <input type="number" min="0" class="form-control" name="min_amount"
                                                v-model="form.min_amount" placeholder="Min Amount">
                                        </div>
                                        <div class="form-group">
                                            <label for="customer_limit">Per Customer Limit</label>
                                            <input type="number" min="0" class="form-control" name="customer_limit"
                                                v-model="form.customer_limit" placeholder="Per Customer Limit">
                                        </div>
                                        <!-- <div class="form-group">
                                            <label for="max_winners">Maximum Winners</label>
                                            <input type="number" min="0" class="form-control" name="max_winners"
                                                v-model="form.max_winners" placeholder="Spinwin maximum winners">
                                        </div> -->
                                    </div>
                                    <div class="col-md-6 col-sm-12">
                                        
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
                                            <label for="customer_gift_limit">Max Gifts Per Customer</label>
                                            <input type="number" min="0" class="form-control" name="customer_gift_limit"
                                                v-model="form.customer_gift_limit" placeholder="Max Gifts Per Customer">
                                        </div>
                                        <div class="form-group">
                                            <label for="probability">Winner Probabilities</label>
                                            <input type="number" min="0" class="form-control" name="probability"
                                                v-model="form.probability" placeholder="Winner Probabilities">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">

                                        <div class="form-group">
                                            <label for="sms">SMS Template</label>
                                            <textarea class="form-control" name="sms" v-model="form.sms"
                                                rows="5"></textarea>
                                        </div>
                                        <div class="form-group">
                                            <label for="description">Send SMS</label>
                                            <div class="">
                                                <label class="switch mr-3">
                                                    <input type="checkbox" name="send_sms" v-bind:true-value="1"
                                                        v-bind:false-value="0" v-model="form.send_sms">
                                                    <span class="switch-slider"></span>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="description">Email Template</label>
                                            <textarea class="form-control" name="sms" v-model="form.email"
                                                rows="10"></textarea>
                                            <a :href="'spin-win-email-preview/' + $route.params.spinner_id"
                                                target="_blank" type="button" class="btn btn-info">Preview</a>
                                            <!-- <ckeditor :editor="editor" v-model="form.email" :config="editorConfig"></ckeditor> -->
                                        </div>
                                        <div class="form-group">
                                            <label for="description">Send Email</label>
                                            <div class="">
                                                <label class="switch mr-3">
                                                    <input type="checkbox" name="send_email" v-bind:true-value="1"
                                                        v-bind:false-value="0" v-model="form.send_email">
                                                    <span class="switch-slider"></span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- /.box-body -->
                                <hr />
                                <div class="form-group text-right m-b-0">
                                    <button type="button" @click="listSpinwin()"
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
            createSpinAndWin: function () {
                //formData.set("send_sms" , this.form.send_sms);
                //formData.set("send_email" , this.form.send_email);
                axios.post('/spinner/create', this.form)
                    .then(r => r.data)
                    .then((response) => {
                        notification('Create Spin and win', "Create Spin and win successfully!", 'success');
                        this.listSpinwin();
                    }).catch((error) => {
                        notification('Create Spin and win', "Create Spin and win Faild!", 'danger');
                    });
            },
            listSpinwin: function () {
                this.$router.push({
                    name: 'SpinAndWins'
                });
            }
        }
    }

</script>
