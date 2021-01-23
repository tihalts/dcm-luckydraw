<template>
    <div class="page-content">

        <div class="container-fluid">
            <div class="page-content__header">
                <div>
                    <h2 class="page-content__header-heading">Create Raffle Draw</h2>
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
                        <h3>Raffle Draw Create Form</h3>
                        <form role="form" ref="addRaffleDrawFrom" name="addRaffleDrawFrom"
                            v-on:submit.prevent="createRaffleDraw">
                            <div class="from-block">
                                <div class="form-group">
                                    <label for="name">Luck Draw Name</label>
                                    <input type="text" class="form-control" name="name" v-model="form.name"
                                        placeholder="Luck Draw Name" required>
                                </div>                               
                                <div class="form-group">
                                    <label for="description">Description</label>
                                    <textarea class="form-control" name="description" v-model="form.description"
                                        rows="5"></textarea>
                                </div>
                                <div class="form-group">
                                    <label for="no_of_winners">No of winners</label>
                                    <input type="text" class="form-control" name="no_of_winners" v-model="form.no_of_winners"
                                        placeholder="No of winners" required>
                                </div> 
                                <div class="form-group">
                                    <label for="description">Add old participants</label>
                                    <div class="">
                                        <label class="switch mr-3">
                                            <input type="checkbox" name="is_allow_old_winders" v-bind:true-value="1" v-bind:false-value="0" v-model="form.is_allow_old_winders">
                                            <span class="switch-slider"></span>
                                        </label>
                                    </div>
                                </div>
                                 <!-- <div class="form-group">
                                    <label for="description">Allow Repeat User</label>
                                    <div class="">
                                        <label class="switch mr-3">
                                            <input type="checkbox" name="is_allow_repeat_user" v-model="form.is_allow_repeat_user">
                                            <span class="switch-slider"></span>
                                        </label>
                                    </div>
                                </div> -->
                                <div class="form-group">
                                    <label for="start_at">Luck Draw Start Date</label>
                                    <flat-pickr class="form-control" :config="config" name="start_at" v-model="form.start_at" placeholder="Luck Draw Start Date"></flat-pickr>
                                </div> 
                                <div class="form-group">
                                    <label for="no_of_winners">Luck Draw End Date</label>
                                    <flat-pickr class="form-control" :config="config" name="end_at" v-model="form.end_at" placeholder="Luck Draw End Date"></flat-pickr>
                                </div> 

                                <!-- /.box-body -->
                                <hr />
                                <div class="form-group text-right m-b-0">
                                    <button type="button" @click="listRaffleDraw()"
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
            createRaffleDraw: function () {
                axios.post('/lucky-draw/create', this.form)
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
            listRaffleDraw: function () {
                this.$router.push({
                    path: '/lucky-draws'
                });
            }
        }
    }

</script>
