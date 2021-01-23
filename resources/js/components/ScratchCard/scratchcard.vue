<template>
    <div class="page-content">
        <div class="container-fluid container-fh">
            <div class="page-content__header">
                <div>
                    <h2 class="page-content__header-heading">Scratch cards</h2>
                </div>
            </div>
            <div class="container-fh__content dataset">
                <div class="dataset__body dataset__body--panel">
                    <table id="demo-foo-addrow"
                        class="table table-striped table-bordered m-b-0 tickets-list table-actions-bar dt-responsive nowrap">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Scratch Card Code</th>
                                <th>Scratched At</th>
                                <th>Is Winner</th>
                                <th>Created At</th>
                                <th class="hidden-sm">Action</th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr v-for="( card , index ) in cards" :key="card.id">
                                <td><b>{{ (index + 1) + (15 * (currentPage - 1)) }}</b></td>
                                <td>{{ card.code }}</td>
                                <td>
                                    <span v-if="card.scratched_at" >{{ card.redeemed_at }}</span>
                                    <span v-if="!card.scratched_at">Not yet scratched</span>
                                </td>
                                <td>
                                    <span v-if="card.is_winner" class="badge badge-success badge-rounded" >Winner</span>
                                    <span v-if="!card.is_winner" class="badge badge-danger badge-rounded">Not Winner</span>
                                </td>
                                <td>{{ card.created_at }} </td>
                                <td>
                                    <button role="button" :disabled="card.redeemed_at" @click="viewScratchCard(card.id)"
                                        class="btn btn-success">Scratch</button>
                                </td>
                            </tr>
                            <tr v-show="!cards.length">
                                <td colspan="5" class="no-data-found-info">No Scratch Cards found</td>
                            </tr>
                        </tbody>
                    </table>
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
                cards: [],
                totalItems: null,
                currentPage: null,
                filter: {
                    orderby: 'desc'
                },
                form: {},
                totalPages: 0,
                user: {}
            }
        },
        mounted: function () {
            this.getScratchCards();
        },
        methods: {
            getScratchCards: function () {
                axios.get('/scratch/card/list' , { params : {'purchase_id' : this.$route.params.purchase_id}})
                    .then(r => r.data)
                    .then((response) => {
                        this.cards = response.data;
                        this.totalItems = response.totalItems;
                        this.currentPage = response.currentPage;
                        this.totalPages = Math.ceil(this.totalItems / 15);
                        this.filter = response.filter;
                    });
            },
            OrderBy: function (orderby) {
                this.filter.orderby = orderby;
                this.searchScratchCards(1);
            },
            redeemedScratchCard: function (id, index) {
                axios.get('/card/redeemed/' + id)
                    .then(r => r.data)
                    .then((response) => {
                        this.searchScratchCards(this.currentPage);
                    });
            },
            viewScratchCard: function(id){
                let routeData = this.$router.resolve({path: '/scratch/card/'+ id +'/scratched-view' });
                window.open(routeData.href, '_blank');
            }
        }
    }

</script>
