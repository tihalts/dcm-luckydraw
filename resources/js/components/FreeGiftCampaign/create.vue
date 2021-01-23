<template>
    <div class="page-content">

        <div class="container-fluid">
            <div class="page-content__header">
                <div>
                    <h2 class="page-content__header-heading">Create Campaign</h2>
                </div>
            </div>

            <div class="row">
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
                <div class="col-md-6 offset-md-1">
                    <div class="main-container">
                        <h3>Campaign Create Form</h3>
                        <hr/>
                        <form role="form" ref="addCampaignFrom" name="addCampaignFrom">
                            <div class="from-block">
                                <div class="form-group">
                                    <label for="campaign_name">Campaign Name</label>
                                    <input type="text" class="form-control" name="campaign_name"
                                        v-model="form.campaign_name" placeholder="Campaign Name" required>
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
                                    <label for="end_at">Type</label>
                                    <select name="type" id="type" class="form-control" v-model="form.type">
                                        <option value="month">Month</option>
                                        <option value="year">Year</option>
                                        <option value="date">Date</option>
                                    </select>
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

                                <!-- /.box-footer -->
                            </div>
                        </form>
                    </div>
                    <div class="main-container">
                        <h3>Campaign Limit</h3>
                            <hr/>
                        <div class="from-block">
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
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <!-- <div class="main-container">
                        <h3>Campaign Group</h3>
                        <hr/>
                        <button type="button" @click="AddNew" class="btn btn-success float-right btn-rounded mb-2 mr-3">
                            Add
                        </button>
                        <div class="form-group">
                            <label for="email">Search Campaign Group</label>
                            <v-select label="name" :filterable="false" :options="groups"
                                @search="searchGroup" @change="selectedGroup"
                                v-model="group" style="width:100%;">
                                <template slot="no-options">
                                    type to search group..
                                </template>
                                <template slot="option" slot-scope="option">
                                    <div class="d-center">
                                        {{ option.name }}
                                    </div>
                                </template>
                                <template slot="selected-option" slot-scope="option">
                                    <div class="selected d-center">
                                        {{ option.name }}
                                    </div>
                                </template>
                            </v-select>
                        </div>
                    </div> -->
                    <div v-if="form.type == 'month'" class="main-container">
                        <h3>Months</h3>
                        <hr/>
                        <div class="from-block">
                            <table  class="table table-bordered">
                                <tr>
                                    <th>Month</th>
                                    <th>Remove</th>
                                </tr>
                                <tr v-for="(month , index) in form.months" :key="index">
                                    <td>{{ month }}</td>
                                    <td>
                                        <button type="button" @click="removeMonth(index)" class="btn btn-danger btn-rounded">
                                            <i class="fa fa-remove"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr v-show="!form.months.length">
                                    <td colspan="9" class="no-data-found-info">No data found</td>
                                </tr>
                            </table>
                            <div class="row mt-4">
                                <div class="col-sm-8">
                                    <input type="number" min="1" max="12" class="form-control" name="month"
                                v-model="month" placeholder="Months">
                                </div>
                                <div class="col-sm-4">
                                    <button type="button" @click="addMonth" class="btn btn-success btn-rounded mb-2 mr-3">
                                       Add
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div v-if="form.type == 'year'" class="main-container">
                        <h3>Years</h3>
                        <hr/>
                        <div class="from-block">
                            <table  class="table table-bordered">
                                <tr>
                                    <th>Year</th>
                                    <th>Remove</th>
                                </tr>
                                <tr v-for="(year , index) in form.years" :key="index">
                                    <td>{{ year }}</td>
                                    <td>
                                        <button type="button" @click="removeYear(index)" class="btn btn-danger btn-rounded">
                                            <i class="fa fa-remove"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr v-show="!form.years.length">
                                    <td colspan="9" class="no-data-found-info">No data found</td>
                                </tr>
                            </table>
                            <div class="row mt-4">
                                <div class="col-sm-8">
                                    <input type="number"  class="form-control" name="year"
                                v-model="year" placeholder="Years">
                                </div>
                                <div class="col-sm-4">
                                    <button type="button" @click="addYear" class="btn btn-success btn-rounded mb-2 mr-3">
                                       Add
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div v-if="form.type == 'date'" class="main-container">
                        <h3>Dates</h3>
                        <hr/>
                        <div class="from-block">
                            <table  class="table table-bordered">
                                <tr>
                                    <th>Date (dd-MM)</th>
                                    <th>Remove</th>
                                </tr>
                                <tr v-for="(date , index) in form.dates" :key="index">
                                    <td>{{ date | day-month }}</td>
                                    <td>
                                        <button type="button" @click="removeDate(index)" class="btn btn-danger btn-rounded">
                                            <i class="fa fa-remove"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr v-show="!form.dates.length">
                                    <td colspan="9" class="no-data-found-info">No data found</td>
                                </tr>
                            </table>
                            <div class="row mt-4">
                                <div class="col-sm-4">
                                    <input type="number"  class="form-control" name="date"
                                    v-model="date" min="01" max="31" placeholder="31">
                                </div>
                                <div class="col-sm-4">
                                    <input type="number"  class="form-control" name="day_month"
                                    v-model="day_month" min="01" max="12" placeholder="12">
                                </div>
                                <div class="col-sm-4">
                                    <button type="button" @click="addDate" class="btn btn-success btn-rounded mb-2 mr-3">
                                       Add
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- <div class="main-container">
                        <h3>Dates</h3>
                        <hr/>
                        <div class="from-block">
                            <table  class="table table-bordered">
                                <tr>
                                    <th>Date</th>
                                    <th>Remove</th>
                                </tr>
                                <tr v-for="(date , index) in form.dates" :key="index">
                                    <td>{{ date }}</td>
                                    <td>
                                        <button type="button" @click="removeDate(index)" class="btn btn-danger btn-rounded">
                                            <i class="fa fa-remove"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr v-show="!form.dates.length">
                                    <td colspan="9" class="no-data-found-info">No data found</td>
                                </tr>
                            </table>
                            <div class="row mt-4">
                                <div class="col-sm-8">
                                    <flat-pickr class="form-control" :config="dateconfig" name="dates"
                                        v-model="date" placeholder="Select Date"></flat-pickr>
                                </div>
                                <div class="col-sm-4">
                                    <button type="button" @click="addDate" class="btn btn-success btn-rounded mb-2 mr-3">
                                       Add
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div> -->
                </div>
            </div>
            <div class="row">
                <div class="col-md-10 offset-md-1">
                    <hr />
                    <div class="form-group text-right m-b-0">
                        <button type="button" @click="listCampaign()"
                            class="btn btn-default mb-2 mr-3 pull-left">Cancel</button>
                        <button type="button" @click="createCampaign" class="btn btn-success mb-2 mr-3">Create</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="addNew" tabindex="-1" role="dialog" aria-labelledby="addNewLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" v-show="!editmode" id="addNewLabel">Add New</h5>
                        <h5 class="modal-title" v-show="editmode" id="addNewLabel">Update User's Info</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form @submit.prevent="editmode ? updateGroup() : createGroup()">
                        <div class="modal-body">
                            <div class="form-group">
                                <input v-model="form.name" type="text" name="name" placeholder="Name"
                                    class="form-control" >
                                <!-- <has-error :form="form" field="name"></has-error> -->
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                            <button v-show="editmode" type="submit" class="btn btn-success">Update</button>
                            <button v-show="!editmode" type="submit" class="btn btn-primary">Create</button>
                        </div>

                    </form>

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
                errors: [],
                form: {
                    months: [],
                    years: [],
                    dates: []
                },
                month:null,
                year: null,
                date: null,
                errors: null,
                editmode: false,
                day_month: null,
                config: {
                    wrap: true,
                    altInput: true,
                    enableTime: true,
                    dateFormat: "Y-m-d H:i",
                    maxDate: "today",
                    minDate: "today"
                },
                dateconfig: {
                    wrap: true,
                    altInput: true,
                    enableTime: false,
                    dateFormat: "Y-m-d",
                },
                group: {},
                groups: []
            }
        },
        mounted: function () {
            this.getGroup();
        },
        methods: {
            getGroup: function () {
                axios.get('/campaign-group/fetch/' + this.$route.params.group_id)
                    .then(r => r.data)
                    .then((response) => {
                        //this.form = response.data;
                        this.config.minDate = response.data.start_at;
                        this.config.maxDate = response.data.end_at;
                    });
            },
            createCampaign: function () {
                this.form.group_id = this.$route.params.group_id;
                axios.post('/free-gift-campaign/create' , this.form)
                    .then(r => r.data)
                    .then((response) => {
                        notification('Create Campaign' , "Create Campaign successfully!" , 'success');
                        this.listCampaign();
                    }).catch((error) => {
                        notification('Create Campaign' , "Create Campaign Faild!" , 'danger');
                    });
            },
            addMonth: function(){
                this.form.months.push(this.month);
                this.month = null;
            },
            removeMonth: function(index){
                this.form.months.splice(index, 1);
            },
            addYear: function(){
                this.form.years.push(this.year);
                this.year = null;
            },
            removeYear: function(index){
                this.form.years.splice(index, 1);
            },
            addDate: function(){
                if(!this.day_month || !this.date)return;
                this.form.dates.push(('0' + this.date).slice(-2) + '' +  ('0' + this.day_month).slice(-2));
                this.date = null;
            },
            removeDate: function(index){
                this.form.dates.splice(index, 1);
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
            createGroup: function(){
                axios.post('/free-gift-group/create' , this.form)
                    .then(r => r.data)
                    .then((response) => {
                        this.group = response.data;
                        this.groups.push(response.data);
                        this.form.group_id = response.data.id;
                        $('#addNew').modal('hide');
                        notification('Create Campaign Group' , "Create Campaign Group successfully!" , 'success');
                    }).catch((error) => {
                        notification('Create Campaign Group' , "Create Campaign Group Faild!" , 'danger');
                    });
            },
            updateGroup: function(){

            },
            searchGroup(search, loading) {
                loading(true);
                this.onSearchGroup(loading, search, this);
            },
            onSearchGroup: _.debounce((loading, search, vm) => {
                axios.post('/free-gift-group/list', {
                    'searchText': search
                }).then(r => r.data).then((response) => {
                    vm.groups = response.data;
                    loading(false);
                });
            }, 350),
            selectedGroup: function () {
                this.form.group_id = this.group.id;
            },
            AddNew(){
                $('#addNew').modal('show');
            },
            listCampaign: function () {
                this.$router.push({
                    name: 'FreeGiftCampaigns' , params: {'group_id' : this.$route.params.group_id}
                });
            }
        }
    }

</script>
