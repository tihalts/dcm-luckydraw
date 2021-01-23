<template>
    <div class="page-content">

        <div class="container-fluid container-fh">
            <div class="page-content__header">
                <div>
                    <h2 class="page-content__header-heading">Winners</h2>
                </div>
            </div>
            <div class="row" v-if="lucky_draw">
                <div class="col-lg-12">
                    <div class="widget widget-remaining-time">
                        <h3 class="widget-remaining-time__heading">{{ lucky_draw.name}} </h3>
                        <div class="widget-remaining-time__block">
                            <span class="widget-remaining-time__block-text">
                                Total Winners 
                            </span>
                            <span class="widget-remaining-time__block-time"> {{ lucky_draw.no_of_winners}}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="container-fh__content dataset">
                <div class="dataset__body dataset__body--panel">
                    <table id="demo-foo-addrow"
                        class="table table-striped table-bordered m-b-0 tickets-list table-actions-bar dt-responsive nowrap">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Customer Name</th>
                                <th>CPR</th>
                                <th>Mobile</th>
                                <th>UUID</th>
                                <!-- <th>Email</th>
                                <th>Points</th>
                                <th>Position</th> -->
                                <th class="hidden-sm">Action</th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr v-for="(winner , index) in winners" :key="winner.id">
                                <td><b>{{ (index + 1) }}</b></td>
                                <td>
                                    <div class="table__cell-widget">
                                        <router-link href="javascript:void(0)" :to="{ name:'CustomerReportPurchases', params: { 'customer_id': winner.id }}" tag="a" class="table__cell-widget-name">{{ winner.fullname }}</router-link>
                                        <!-- <span class="table__cell-widget-description"><b>Email :</b> {{ customer.email }}</span>
                                        <span class="table__cell-widget-description"><b>CPR :</b> {{ customer.cpr }}</span> -->
                                    </div>
                                </td>
                                <td>{{ winner.cpr }} </td>
                                <td>{{ winner.mobile }} </td>
                                <td>{{ winner.winner.uuid }} </td>
                                <!-- <td>{{ winner.email }} </td>
                                <td>{{ winner.points }} </td>
                                <td>{{ winner.winner.position }} </td> -->
                                <td class="table__cell-actions">
                                    <div class="table__cell-actions-wrap">
                                        <button :id="'winnder_d_' + winner.id" style="margin-right: 10px;" class="btn btn-info" @click="print(winner.winner.id)">Print</button>
                                        <button :id="'winnder_' + winner.id" class="btn btn-success" @click="sendModal(winner.winner.id)">Send Notification</button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-show="!winners.length">
                                <td colspan="7" class="no-data-found-info">No Winners found</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="modal fade" id="sendModal" tabindex="-1" role="dialog" aria-labelledby="addNewLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addNewLabel">Add New</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form>
                        <div class="modal-body">

                            <div class="form-group">
                                <input v-model="mail.email" type="email" name="email" placeholder="Email Address"
                                    class="form-control" >                                
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button @click="sendWinnerMsg()" type="button" class="btn btn-primary">Send</button>
                        </div>

                    </form>

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
                winners: [],
                lucky_draw: {},
                mail: {},
            }
        },
        mounted: function () {
            this.getWinners();
        },
        methods: {
            getWinners: function () {
                axios.get('/winner/list/' + this.$route.params.lucky_draw_id)
                    .then(r => r.data)
                    .then((response) => {
                        this.winners = response.data;
                        this.lucky_draw = response.luck_draw;
                    });
            },
            sendModal: function(id){
                this.mail.id = id;
                $('#sendModal').modal('show');
            },
            sendWinnerMsg: function(){
                axios.get('/sendwinner/notification/' + this.mail.id , { params : this.mail })
                        .then(r => r.data)
                        .then((response) => {
                            $('winnder_' + this.mail.id).prop('disabled', true);
                            notification('Send Notification', 'Send Notification successfull.!');
                             $('#sendModal').modal('hide');
                        }).catch((error) => {
                            $('winnder_' + this.mail.id).prop('disabled', false)
                            notification('Send Notification', 'Send Notification Faild.!');
                             $('#sendModal').modal('hide');
                        });
            },
            print(id){
                let routeData = this.$router.resolve({
                    path: '/winner-print/pdf/' + id,
                    query: {}
                });
                window.open(routeData.href, '_blank');
            },
            fetchWinner: function (id) {
                this.$router.push({
                    path: '/winner/fetch/' + this.$route.params.lucky_draw_id + '/' + id
                });
            }
        }
    }

</script>
